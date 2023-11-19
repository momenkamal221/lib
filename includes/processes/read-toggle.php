<?php
include_once $backTpl . 'header.php';
$libID = $_POST['lib_id'];
if(own($conn,$libID)){
	exit();
}	
$libReaders=fetch($conn,'SELECT readers_count FROM libraries WHERE lib_id = '.$libID.';')[0]['readers_count'];
$mainLibReadings=fetch($conn,'SELECT reading_count FROM libraries WHERE lib_id = '.getMainLib().';')[0]['reading_count'];
if (read($conn, $libID)) {
	$sql = 'DELETE FROM readers WHERE reader = ' . $_SESSION['main_lib_id'] . ' AND reading = '.$libID . ';';
	runQuery($conn,$sql);
	$libReaders--;
	$mainLibReadings--;
	echo json_encode(array('state'=>1));
} else {
	$sql = "INSERT INTO readers (reader,reading) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	mysqli_stmt_bind_param($stmt, "is", $_SESSION['main_lib_id'], $libID);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	$libReaders++;
	$mainLibReadings++;
	echo json_encode(array('state'=>2));
}
$sql='UPDATE libraries SET readers_count='. $libReaders.' WHERE lib_id = '.$libID.';';
runQuery($conn,$sql);
$sql='UPDATE libraries SET reading_count='. $mainLibReadings.' WHERE lib_id = '. getMainLib().';';
runQuery($conn,$sql);
