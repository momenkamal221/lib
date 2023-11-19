<?php

$comment=cleanInput($_POST['comment']);
if(empty($comment)){
	exit();
}
$postID=$_POST['post_id'];
$sql = "INSERT INTO comments (post_id,lib_id,comment,_date) VALUES (?,?,?,?);";
$stmt = mysqli_stmt_init($conn);
$date= time();
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "iisi", $postID, $_SESSION['main_lib_id'],$comment,$date);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$toLibID=getPostedLibID($conn,$postID);
$commentID=fetch($conn,'SELECT post_id FROM posts WHERE post_id = LAST_INSERT_ID();')[0]['post_id'];
if(!ownPost($conn,$postID))
notifyUser($conn,$_SESSION['main_lib_id'],'comment',$toLibID,$commentID,$postID,NULL);