<?php
include_once 'includes/dbh.inc.php';
$errs = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = cleanInput(trim($_POST['email']));
	$pwd = cleanInput(trim($_POST['pwd']));
	if (empty($email)) {
		exit();
	}
	// Check if password is empty
	if (empty($pwd)) {
		exit();
	}
	if (login($email, $pwd, $conn)) {
		echo json_encode(array('errs' => [-1]));
	} else {
		echo json_encode(array('errs' => [5]));
	} 
}
