<?php
include_once 'includes/dbh.inc.php';
session_start();
logout();
if(isloggedin()){
	echo json_encode(false);
	exit();
}
echo json_encode(true);
