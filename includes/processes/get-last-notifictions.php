<?php
include_once $backTpl . 'header.php';
$lastNotiID=$_POST['last_noti'];
$notifications = fetch($conn, 'SELECT * FROM notifications WHERE to_lib_id = ' . $_SESSION['main_lib_id'] . ' AND noti_id > '.$lastNotiID.' ORDER BY noti_id DESC ;');

if ($notifications) {
	foreach ($notifications as $notification) {

		include $frontTpl . 'notification.phtml';
	}
}