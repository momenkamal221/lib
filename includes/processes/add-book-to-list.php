<?php
include_once $backTpl . 'header.php';
$bookID=$_POST['book_id'];
$listID =$_POST['list_id'];
if(!ownBook($conn,$bookID)){
	header('location: ./');
	exit();
}
$sql = "INSERT INTO listed_books (book_id,list_id) VALUES (?,?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	//if user put code
	exit();
}
mysqli_stmt_bind_param($stmt, "ii", $bookID,$listID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
echo $bookID . $listID;
