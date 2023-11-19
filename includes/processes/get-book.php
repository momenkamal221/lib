<?php
include_once $backTpl . 'header.php';
$book=getBook($conn,$_POST['book_id']);
echo json_encode(array('cover'=> $book['cover'],'book_location'=> $book['book_location']));
