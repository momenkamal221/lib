<?php
session_start();
include_once 'includes/dbh.inc.php';
if (isloggedin()){
	$lib=true;
	include_once 'website/general.phtml';
	//page content
	if(!isset($_GET['page'])){
		$_GET['page'] = 'lib';
	}
	if($_GET['page']=='lib'){
		include_once 'website/content/library.phtml';
	}elseif($_GET['page']=='community'){
		include_once 'website/content/community.phtml';
	}

	include_once 'website/footer.phtml';
}else{
	include_once 'website/logging.phtml';
}