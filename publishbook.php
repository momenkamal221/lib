<?php
session_start();
include_once 'includes/dbh.inc.php';
if (isloggedin()){
	if(!isset($_GET['id'])||!own($conn,$_GET['id'])){
		header('location: ./');
	}
	//page content
	include_once 'website/general.phtml';
	include_once 'website/content/publish-book.phtml';
	include_once 'website/footer.phtml';
}else{
	include_once 'website/logging.phtml';
}