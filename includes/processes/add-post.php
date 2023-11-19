<?php
include_once $backTpl . 'header.php';
$content = cleanInput($_POST['content']);
$libID = $_POST['lib_id'];
if(!own($conn,$libID)){
	exit();
}
if(empty($content)){
	exit();
}
if ($_POST['book_id'] == 0) {
	$bookID = NULL;
} else {
	$bookID = $_POST['book_id'];
}
$date=time();
$sql = 'INSERT INTO posts (lib_id,content,book_id,_date) VALUES (?,?,?,?);';
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "isii", $libID, $content, $bookID,$date);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
$postID=fetch($conn,'SELECT post_id FROM posts WHERE post_id = LAST_INSERT_ID();')[0]['post_id'];
notifyUser($conn,$libID,'post','all',NULL,$postID,NULL);