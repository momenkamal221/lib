<?php
include_once 'includes/dbh.inc.php';
$errs = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$fname = ucfirst(cleanInput(trim($_POST['fname'])));
	$lname = ucfirst(cleanInput(trim($_POST['lname'])));
	$email = trim($_POST['email']);
	$pwd = trim($_POST['pwd']);
	$day = intval(trim($_POST['day']));
	$month = intval(trim($_POST['month']));
	$year = intval(trim($_POST['year']));
	if (empty($email)) {
		exit();
	}
	if (!preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $email)) {
		exit();
	} elseif (emailExists($conn, $email)) {
		array_push($errs, 2);
	}
	if (empty($fname) || empty($lname)) {
		exit();
	}
	if (strlen($pwd) <= 8) {
		array_push($errs, 4);
	}

	if (is_int($month) && is_int($day) && is_int($year)) {
		if (checkdate($month, $day, $year)) {
			$birthDate = mktime(0, 0, 0, $month, $day, $year);
		} else {
			array_push($errs, 3);
		}
	}
} else {
	header("location: index.php");
}
if ($errs) {
	echo json_encode(array('errs' => $errs));
	exit();
}

$sql = "INSERT INTO users (email,f_name,l_name,password,birthdate) VALUES (?,?,?,?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "sssss", $email,$fname,$lname,$pwd,$birthDate);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


$sql = "INSERT INTO libraries (user_id,lib_title,main) VALUES (?,?,?);";
$main = 1;
$lib_title= $fname . " " . $lname;
if ($userData=fetch($conn,'SELECT * FROM users where email = "'. $email .'";')){
	$user_id=$userData[0]['user_id'];
}
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "isi", $user_id,$lib_title,$main);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
login($email, $pwd, $conn);

// the lib should read itself
$sql = "INSERT INTO readers (reader,reading) VALUES (?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "ii", $_SESSION['main_lib_id'],$_SESSION['main_lib_id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo json_encode(array('errs' => [-1]));

