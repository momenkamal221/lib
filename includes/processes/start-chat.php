<?php
include_once $backTpl . 'header.php';
$libID=$_POST['lib_id'];
$libPic='data/uploads/lib-pics/'.getLibPic($conn,$libID);
$libTitle=getLibTitle($conn,$libID);
echo json_encode(array('libPic'=>$libPic,'libTitle'=>$libTitle));

