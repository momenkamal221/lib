<?php
include_once $backTpl . 'header.php';

$title = cleanInput(preg_replace('/\s+/', ' ', trim($_POST['title'])));
if(empty($title)){
	header('location: library.php?id='.$_POST['lib_id']);
	exit();
}
$libID = $_POST['lib_id'];
if(!own($conn,$libID)){
	exit();
}

$categories=explode(',',$_POST['categories']);
if(count($categories)>10){
	exit();
}
$sql = "INSERT INTO book_lists (lib_id,title) VALUES (?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}

mysqli_stmt_bind_param($stmt, "is", $libID , $title);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$last_id = mysqli_insert_id($conn);

foreach($categories as $category){
	$sql = "INSERT INTO list_categories (category,list_id) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "si", $category,$last_id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}