<?php
include_once $backTpl . 'header.php';

$lib_title = cleanInput(preg_replace('/\s+/', ' ', trim($_POST['lib_title'])));
if(empty($lib_title)){
	exit();
}
$user_id = $_SESSION['user_id'];
$sql = "INSERT INTO libraries (user_id,lib_title) VALUES (?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "is", $user_id, $lib_title);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
$userLibs = fetch($conn, 'SELECT lib_id FROM libraries WHERE user_id = ' . $_SESSION['user_id'] . ' ORDER BY lib_id;');
$lastLibID=$userLibs[count($userLibs) - 1]['lib_id'];
$mainLibID=getMainLibOf($conn,$lastLibID);
$sql = "INSERT INTO readers (reader,reading) VALUES (?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "ii",$mainLibID , $lastLibID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo json_encode($userLibs[count($userLibs) - 1]);
