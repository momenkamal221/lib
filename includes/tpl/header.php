<?php
if (strcasecmp($_SERVER["REQUEST_METHOD"],"pOst")!==0) {
	header('location: /');
	exit();
}