<?php
include_once $backTpl . 'header.php';
$post_id = $_POST['post_id'];
$lastCommentID = $_POST['last_comment_id'];
if ($lastCommentID == 0) {
	$sql = 'SELECT comments.comment_id,comments.comment, comments.lib_id,comments._date , libraries.lib_title, libraries.lib_pic FROM comments JOIN libraries ON comments.lib_id=libraries.lib_id  WHERE post_id = ' . $post_id . ' ORDER BY comment_id DESC LIMIT 5;';
} else {
	$sql = 'SELECT comments.comment_id,comments.comment, comments.lib_id,comments._date , libraries.lib_title, libraries.lib_pic FROM comments JOIN libraries ON comments.lib_id=libraries.lib_id  WHERE post_id = ' . $post_id . ' AND comment_id > ' . $lastCommentID . ' ORDER BY comment_id DESC LIMIT 5;';
}
if($comments=fetch($conn, $sql)){

	$comments = array_reverse($comments);
}

echo json_encode($comments);
