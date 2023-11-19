<?php
session_start();
include_once 'includes/dbh.inc.php';
if (isloggedin()){
	include_once 'website/general.phtml';
	include_once 'website/content/read.phtml';
	include_once 'website/footer.phtml';
}else{
	include_once 'website/logging.phtml';
}