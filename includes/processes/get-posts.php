<?php
include_once $backTpl . 'header.php';
$mainLibID = $_SESSION['main_lib_id'];
$libID = $_POST['lib_id'];
$page= explode('/',$_SERVER['HTTP_REFERER']);
$page=explode('?',$page[count($page)-1])[0];
if ($page != 'library.php') {
	if (isset($_POST['last_post_id'])) {
		$lastPostID = $_POST['last_post_id'];
		if ($lastPostID == 0) {
			$sql = 'SELECT 
			posts.post_id,posts.lib_id,posts.book_id,posts.content,posts.likes_count,posts._date,libraries.lib_title,libraries.lib_pic,post_likes.post_id \'liked\'
			FROM posts 
			LEFT JOIN post_likes ON posts.post_id=post_likes.post_id
			INNER JOIN libraries ON posts.lib_id=libraries.lib_id
			WHERE libraries.lib_id 
			IN (SELECT lib_id FROM libraries 
				WHERE libraries.lib_id 
				IN (SELECT reading FROM readers WHERE reader = ' . $mainLibID . ' )
			   ) ORDER BY post_id DESC LIMIT 5;';
		} else {
			$sql = 'SELECT 
			posts.post_id,posts.lib_id,posts.book_id,posts.content,posts.likes_count,posts._date,libraries.lib_title,libraries.lib_pic,post_likes.post_id \'liked\'
			FROM posts 
			LEFT JOIN post_likes ON posts.post_id=post_likes.post_id
			INNER JOIN libraries ON posts.lib_id=libraries.lib_id
			WHERE libraries.lib_id 
			IN (SELECT lib_id FROM libraries 
				WHERE libraries.lib_id 
				IN (SELECT reading FROM readers WHERE reader = ' . $mainLibID . ')
			   ) AND posts.post_id > ' . $lastPostID . ' ORDER BY post_id DESC LIMIT 5;';
		}
		if ($posts = fetch($conn, $sql)) {
			$posts = array_reverse($posts);
		}
	} elseif (isset($_POST['pre_post_id'])) {
		$prePostID = $_POST['pre_post_id'];
		$sql = 'SELECT 
		posts.post_id,posts.lib_id,posts.book_id,posts.content,posts.likes_count,posts._date,libraries.lib_title,libraries.lib_pic,post_likes.post_id \'liked\'
		FROM posts 
		LEFT JOIN post_likes ON posts.post_id=post_likes.post_id
		INNER JOIN libraries ON posts.lib_id=libraries.lib_id
		WHERE libraries.lib_id 
		IN (SELECT lib_id FROM libraries 
			WHERE libraries.lib_id 
				IN (SELECT reading FROM readers WHERE reader = ' . $mainLibID . ')
				) AND posts.post_id < ' . $prePostID . ' ORDER BY post_id DESC LIMIT 5;';
		$posts = fetch($conn, $sql);
	}
} else{
	if (isset($_POST['last_post_id'])) {
		$lastPostID = $_POST['last_post_id'];
		if ($lastPostID == 0) {
			$sql = 
			'SELECT posts.post_id,posts.lib_id,posts.book_id,posts.content,posts.likes_count,posts._date,libraries.lib_title,libraries.lib_pic,post_likes.post_id \'liked\'
			FROM posts
			LEFT JOIN post_likes ON posts.post_id=post_likes.post_id
			INNER JOIN libraries ON posts.lib_id=libraries.lib_id
			WHERE libraries.lib_id = '.$libID.' ORDER BY post_id DESC LIMIT 5;';
		} else {
			$sql = 'SELECT 
			posts.post_id,posts.lib_id,posts.book_id,posts.content,posts.likes_count,posts._date,libraries.lib_title,libraries.lib_pic,post_likes.post_id \'liked\' 
			FROM posts 
			LEFT JOIN post_likes ON posts.post_id=post_likes.post_id 
			INNER JOIN libraries ON posts.lib_id=libraries.lib_id 
			WHERE libraries.lib_id = ' . $libID .
			' AND posts.post_id > ' . $lastPostID . ' ORDER BY post_id DESC LIMIT 5;';
		}
		if ($posts = fetch($conn, $sql)) {
			$posts = array_reverse($posts);
		}
	} elseif (isset($_POST['pre_post_id'])) {
		$prePostID = $_POST['pre_post_id'];
		$sql = 'SELECT 
		posts.post_id,posts.lib_id,posts.book_id,posts.content,posts.likes_count,posts._date,libraries.lib_title,libraries.lib_pic,post_likes.post_id \'liked\'
		FROM posts 
		LEFT JOIN post_likes ON posts.post_id=post_likes.post_id 
		INNER JOIN libraries ON posts.lib_id=libraries.lib_id 
		WHERE libraries.lib_id = ' . $libID .
			' AND posts.post_id < ' . $prePostID . ' ORDER BY post_id DESC LIMIT 5;';
		$posts = fetch($conn, $sql);
	}
}

echo json_encode($posts);
