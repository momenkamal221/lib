<?php
include_once 'includes/dbh.inc.php';
session_start();
if (isloggedin()){
	$rootPage=true;
	include_once 'website/general.phtml';
	include_once 'website/content/home.phtml';
	include_once 'website/footer.phtml';
}else{
	include_once 'website/logging.phtml';
}