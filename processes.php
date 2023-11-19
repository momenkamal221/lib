<?php

/*
ID			processe
0			add lib
1			toggle post like
2			get stores
3			get latest comments
4			get previous comments
5			get posts
6			lib cover
7			add to list
8			remove book from list
9			remove list
10			add book
11			add list
12			add post
13			toggle read
14			getBook cover
15			add comment
16			start chat
17			send chat
18			get chat
19			edit bio
20			get categories
21			is notifications seen
22			User saw notifications
23			get last notifications
*/
include_once 'includes/dbh.inc.php';
include_once 'init.php';
header('Access-Control-Allow-Origin: *');
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['process'] == 0) {
		include_once 'includes/processes/add-lib.php';
	} elseif ($_POST['process'] == 1) {
		include_once 'includes/processes/like-post.php';
	} elseif ($_POST['process'] == 2) {
		include_once 'includes/processes/get-stores.php';
	} elseif ($_POST['process'] == 3) {
		include_once 'includes/processes/get-last-comments.php';
	} elseif ($_POST['process'] == 4) {
		include_once 'includes/processes/get-pre-comments.php';
	} elseif ($_POST['process'] == 5) {
		include_once 'includes/processes/get-posts.php';
	} elseif ($_POST['process'] == 6) {
		include_once 'includes/processes/img.php';
	} elseif ($_POST['process'] == 7) {
		include_once 'includes/processes/add-book-to-list.php';
	} elseif ($_POST['process'] == 8) {
		include_once 'includes/processes/remove-book.php';
	} elseif ($_POST['process'] == 9) {
		include_once 'includes/processes/remove-list.php';
	} elseif ($_POST['process'] == 10) {
		include_once 'includes/processes/add-book.php';
	} elseif ($_POST['process'] == 11) {
		include_once 'includes/processes/add-list.php';
	} elseif ($_POST['process'] == 12) {
		include_once 'includes/processes/add-post.php';
	} elseif ($_POST['process'] == 13) {
		include_once 'includes/processes/read-toggle.php';
	} elseif ($_POST['process'] == 14) {
		include_once 'includes/processes/get-book.php';
	} elseif ($_POST['process'] == 15) {
		include_once 'includes/processes/add-comment.php';
	} elseif ($_POST['process'] == 16) {
		include_once 'includes/processes/start-chat.php';
	} elseif ($_POST['process'] == 17) {
		include_once 'includes/processes/send-msg.php';
	} elseif ($_POST['process'] == 18) {
		include_once 'includes/processes/get-msgs.php';
	} elseif ($_POST['process'] == 19) {
		include_once 'includes/processes/edit-bio.php';
	} elseif ($_POST['process'] == 20) {
		include_once 'includes/processes/get-categories.php';
	}elseif ($_POST['process'] == 21) {
		include_once 'includes/processes/is-notification-seen.php';
	}elseif ($_POST['process'] == 22) {
		include_once 'includes/processes/notifications-is-seen.php';
	}elseif ($_POST['process'] == 23) {
		include_once 'includes/processes/get-last-notifictions.php';
	}
	
}
