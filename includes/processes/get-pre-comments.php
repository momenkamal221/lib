<?php
include_once $backTpl . 'header.php';

$post_id = $_POST['post_id'];
$preCommentID = $_POST['pre_comment_id'];
$sql = 'SELECT comments.comment_id,comments.comment, comments.lib_id,comments._date, libraries.lib_title, libraries.lib_pic FROM comments JOIN libraries ON comments.lib_id=libraries.lib_id WHERE post_id = ' . $post_id . ' AND comment_id < ' . $preCommentID . ' ORDER BY comment_id DESC LIMIT 5;';
$comments = fetch($conn, $sql);
echo json_encode($comments);
