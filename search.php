<?php

session_start();
include_once 'includes/dbh.inc.php';
if (isloggedin()){
	if(!isset($_GET['q'])){
		header('location: ./');
	}
	include_once 'website/general.phtml';
	include_once 'website/content/search.phtml';
	include_once 'website/footer.phtml';
}else{
	include_once 'website/logging.phtml';
}