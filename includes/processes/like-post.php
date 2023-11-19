<?php
include_once $backTpl . 'header.php';
$post_id=$_POST['post_id'];
//libID here is the main lib of the user
$libID=getMainLib();
$likesCount=0;
$postedLibID=getPostedLibID($conn,$post_id);
if(fetch($conn,'SELECT post_id,lib_id FROM post_likes WHERE lib_id = '. $libID .' AND post_id = ' . $post_id . ';')){
	//delete that the lib has liked this post
	$sql='DELETE FROM post_likes WHERE post_id = ' . $post_id .' AND lib_id = ' . $libID . ';';
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	// sub 1 from likes count
	$likesCount = fetch($conn, 'SELECT likes_count FROM posts WHERE post_id = ' . $post_id . ';')[0]['likes_count'];
	$likesCount--;
	$sql='UPDATE posts SET likes_count= ' . $likesCount . ' WHERE post_id = ' . $post_id . ';';
	runQuery($conn,$sql);
	echo json_encode(array('likes_count'=>$likesCount,'liked'=>false));
	$sql='DELETE FROM notifications WHERE post_id = ' . $post_id . '  AND to_lib_id = ' . $postedLibID . ' AND _action=\'like_post\';';
	runQuery($conn,$sql);
}else{
	// add that this lib liked a post
	$sql = 'INSERT INTO post_likes (post_id,lib_id) VALUES (?,?);';
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $post_id, $libID);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	//get likes count then add 1
	$likesCount = fetch($conn, 'SELECT likes_count FROM posts WHERE post_id = ' . $post_id . ';')[0]['likes_count'];
	$likesCount++;
	$sql='UPDATE posts SET likes_count= '. $likesCount . ' WHERE post_id = ' . $post_id . ';';
	runQuery($conn,$sql);
	notifyUser($conn,$libID,'like_post',$postedLibID,NULL,$post_id,NULL);
	// fetch return false if there is no data then there is no likes
	echo json_encode(array('likes_count'=>$likesCount,'liked'=>true));
}
