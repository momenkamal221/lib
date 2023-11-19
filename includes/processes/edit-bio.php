<?php
include_once $backTpl . 'header.php';
$bio=cleanInput($_POST['bio']);
$libID=$_POST['lib_id'];
if(!own($conn,$libID)){
	exit();
}
$sql='UPDATE libraries SET about_lib = \'' .$bio .'\'WHERE lib_id = '.$libID.';';
runQuery($conn,$sql);