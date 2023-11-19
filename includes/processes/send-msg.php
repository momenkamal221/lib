<?php
$libID=$_SESSION['main_lib_id'];
$msg=cleanInput($_POST['msg']);
$chattedLibID=$_POST['chatted_lib_id'];
$date=time();
include_once $backTpl . 'header.php';
if(!main($conn,$chattedLibID || !read($conn,$chattedLibID))){
	exit();
}
if(strpos($msg,'\n')||stripos(strrev($msg), 'n\\') === 0){
	exit();
}
$sql = "INSERT INTO chat (lib_id,to_lib_id,_message,_date) VALUES (?,?,?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "iisi", $libID,$chattedLibID,$msg,$date);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
$lastNotifications=fetch($conn,'SELECT * FROM notifications WHERE to_lib_id = '.$chattedLibID.' ORDER BY noti_id DESC LIMIT '.$notificationsLimit.';');
$notifiedBefore=false;
foreach($lastNotifications as $notification){
	if($notification['_action']=='msg_send'&&$notification['lib_id']==$libID&&$notification['to_lib_id']==$chattedLibID){
		$notifiedBefore=true;
		break;
	}
}
if(!$notifiedBefore){
	notifyUser($conn,$libID,'msg_send',$chattedLibID,NULL,NULL,NULL);
}
