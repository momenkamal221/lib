<?php
include_once $backTpl . 'header.php';
$sql='SELECT * FROM unseen_notifications WHERE lib_id = '.$_SESSION['main_lib_id'].';';
if(fetch($conn,$sql)){
	echo 'false';
}else{
	echo 'true';
}