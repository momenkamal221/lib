<?php
include_once $backTpl . 'header.php';
if($_POST['book_id']==0){
	echo 'no book';
	exit();
}
$bookID=$_POST['book_id'];
echo json_encode(getStores($conn,$bookID));