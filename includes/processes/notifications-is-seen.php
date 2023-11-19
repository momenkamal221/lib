<?php
include_once $backTpl . 'header.php';
$sql='DELETE FROM unseen_notifications WHERE lib_id = '.$_SESSION['main_lib_id'].';';
runQuery($conn,$sql);