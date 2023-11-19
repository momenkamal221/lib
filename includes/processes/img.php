<?php
include_once $backTpl . 'header.php';

if(!own($conn,$_POST['lib-id'])){
	header('location: ./library.php?id=' . $_POST['lib-id']);
}
if($_POST['img']=='lib-pic'){
	$oldImg=getLibPic($conn,$_POST['lib-id']);
	$file = $_FILES['lib-pic'];
	$fileName=$_FILES['lib-pic']['name'];
	$fileTmpName=$_FILES['lib-pic']['tmp_name'];
	$fileSize=$_FILES['lib-pic']['size'];
	
	$fileErr=$_FILES['lib-pic']['error'];
	$fileName=$_FILES['lib-pic']['type'];
	$fileExt=explode('.',$fileName);
	$fileActualExt = explode('/',strtolower(end($fileExt)))[1];
	$allowed = array('jpg','jpeg','png');
	if(in_array($fileActualExt,$allowed)&&$fileErr ===0){
		$fileNameNew = $_POST['lib-id'].'.'.$fileActualExt;
	}else{
		header('location: ./library.php?id=' . $_POST['lib-id']);
		exit();
	}
	$fileDestination=$libPics.$fileNameNew;
	$sql='UPDATE libraries
	SET lib_pic = \''.$fileNameNew .'\'
	WHERE lib_id = '. $_POST['lib-id'] .';' ;
	if(getLibPic($conn,$_POST['lib-id'])!='default.png'){
		unlink($libPics . $oldImg);
	}
}elseif($_POST['img']=='lib-cover'&&isset($_FILES)){
	$oldImg=getLibCover($conn,$_POST['lib-id']);
	$file = $_FILES['lib-cover'];
	$fileName=$_FILES['lib-cover']['name'];
	$fileTmpName=$_FILES['lib-cover']['tmp_name'];
	$fileSize=$_FILES['lib-cover']['size'];
	$fileErr=$_FILES['lib-cover']['error'];
	$fileName=$_FILES['lib-cover']['type'];
	$fileExt=explode('.',$fileName);
	$fileActualExt = explode('/',strtolower(end($fileExt)))[1];
	$allowed = array('jpg','jpeg','png');
	if(in_array($fileActualExt,$allowed)&&$fileErr ===0){
		$fileNameNew = $_POST['lib-id'].'.'.$fileActualExt;
	}else{
		header('location: ./library.php?id=' . $_POST['lib-id']);
		exit();
	}
	$fileDestination=$libCovers.$fileNameNew;
	$sql='UPDATE libraries
	SET lib_cover = \''.$fileNameNew .'\'
	WHERE lib_id = '. $_POST['lib-id'] .';' ;
	if(getLibCover($conn,$_POST['lib-id'])!='default.png'){
		unlink($libCovers . $oldImg);
	}
}
move_uploaded_file($fileTmpName,$fileDestination);
runQuery($conn,$sql);
header('location: ./library.php?id=' . $_POST['lib-id']);