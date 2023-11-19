<?php
include_once $backTpl . 'header.php';
if(!own($conn,$_POST['lib_id'])){
	exit();
}
$listID =$_POST['list_id'];
$sql='DELETE FROM listed_books WHERE list_id='.$listID.';';
runQuery($conn,$sql);
$sql='DELETE FROM list_categories WHERE list_id='.$listID.';';
runQuery($conn,$sql);
$sql='DELETE FROM book_lists WHERE list_id='.$listID.';';
runQuery($conn,$sql);