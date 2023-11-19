<?php

include_once $backTpl . 'header.php';
if (!isset($_POST['lib_id'])) {
	header('location: ./');
}

$title = cleanInput($_POST['title']);
$description = cleanInput($_POST['description']);
$pages = intval($_POST['pages']);
$publisher = cleanInput($_POST['publisher']);
$language = cleanInput($_POST['language']);
$author=cleanInput($_POST['author']);
$libID = $_POST['lib_id'];
if(!own($conn,$libID)){
	header('location: ./');
}
if(empty($pages)||!isset($pages) || $pages < 0 || !is_int($pages)){
	$pages='NULL';
}
//cover
$file = $_FILES['cover'];
$fileName = $_FILES['cover']['name'];
$fileTmpName = $_FILES['cover']['tmp_name'];
$fileSize = $_FILES['cover']['size'];

$fileErr = $_FILES['cover']['error'];
$fileName = $_FILES['cover']['type'];
$fileExt = explode('.', $fileName);
$fileActualExt = explode('/', strtolower(end($fileExt)))[1];

$allowed = array('jpg', 'jpeg', 'png');
if (!in_array($fileActualExt, $allowed) || !$fileErr === 0) {
	if(isset($_POST['book_id'])){
		header('location: ./publishbook.php?id=' . $libID . '&bookid='.$_POST['book_id'].'&err=1');
	}else{
		header('location: ./publishbook.php?id=' . $libID .'&err=1');

	}
	exit();
}

if(isset($_POST['book_id'])){
	$bookID=$_POST['book_id'];
	if(!ownBook($conn,$bookID)){
		header('location: ./');
		exit();
	}
	$title=addslashes($title);
	$description=addslashes($description);
	$publisher=addslashes($publisher);
	$language=addslashes($language);
	$author=addslashes($author);
	$sql = 'UPDATE books SET title = \''.$title . '\' ,pages = '.$pages.', _description = \''.$description . '\' , publisher = \''.$publisher . '\', _language = \''.$language .'\', author = \''.$author .'\' WHERE book_id = ' . $bookID . ';';
	echo $sql;
	runQuery($conn,$sql);
	$bookData=getBook($conn,$bookID);
	unlink($bookCover.$bookData['cover']);

	if($_FILES['book']['error']==4 && $bookData['book_location']!=NULL){
		unlink($booksLocation.$bookData['book_location']);
		$sql = 'UPDATE books SET book_location = NULL WHERE book_id = '.$bookID.' ;';
		runQuery($conn,$sql);
	}
	
}else{
	$sql=
	'INSERT INTO books(lib_id,title,pages,_description,publisher,_language,author) VALUES (?,?,?,?,?,?,?);';
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	mysqli_stmt_bind_param($stmt, "isissss", $libID,$title,$pages,$description,$publisher,$language,$author);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	$bookID = mysqli_insert_id($conn);
	notifyUser($conn,$libID,'book','all',NULL,null,$bookID);
}

$fileNameNew=$bookID.'.'.$fileActualExt;
$sql = 'UPDATE books SET cover = \'' . $fileNameNew .'\' WHERE book_id = '.$bookID.' ;';
runQuery($conn,$sql);
$fileDestination=$bookCover.$fileNameNew;
move_uploaded_file($fileTmpName,$fileDestination);
if($_FILES['book']['error']!=4){
	$book = $_FILES['book'];
	$bookName = $_FILES['book']['name'];
	$bookTmpName = $_FILES['book']['tmp_name'];
	$bookSize = $_FILES['book']['size'];
	$bookErr = $_FILES['book']['error'];
	$bookName = $_FILES['book']['type'];
	$bookExt = explode('.', $bookName);
	$bookActualExt = explode('/', strtolower(end($bookExt)))[1];
	if (strcasecmp($bookActualExt,'pdf') ==0 && $bookErr != 4) {
		$bookNameNew=$bookID.'.'.$bookActualExt;
		$sql = 'UPDATE books SET book_location = \'' . $bookNameNew .'\' WHERE book_id = '.$bookID.' ;';
		runQuery($conn,$sql);
		$bookDestination=$booksLocation.$bookNameNew;
		move_uploaded_file($bookTmpName,$bookDestination);
	}
}

$sql='DELETE FROM stores WHERE book_id = ' .$bookID.';';
runQuery($conn,$sql);
for($i=1;;$i++){
	if(isset($_POST['store-'.$i])){
		$sql='INSERT INTO stores (book_id , store,link) VALUES (?,?,?) ;';
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			//if user put code
			exit();
		}
		mysqli_stmt_bind_param($stmt, "iss",$bookID,$_POST['store-'.$i],$_POST['link-'.$i] );
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}else{
		break;
	}
}
header('location: ./library.php?id=' . $libID);
