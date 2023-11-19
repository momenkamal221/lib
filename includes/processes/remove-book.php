<?php
include_once $backTpl . 'header.php';

$bookID=$_POST['book_id'];
$listID =$_POST['list_id'];
if(!ownBook($conn,$bookID)){
	header('location: ./');
	exit();
}
$sql='DELETE FROM listed_books WHERE list_id='.$listID.' AND book_id= '.$bookID.';';
runQuery($conn,$sql);
