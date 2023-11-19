<?php
include_once $backTpl . 'header.php';
$chattedLib=$_POST['lib_id'];

if($_POST['task']==1){
	$lastMsg=$_POST['last_msg'];
	$msgs=fetch($conn,'SELECT * FROM chat WHERE (lib_id= '.getMainLib().' AND to_lib_id = ' .$chattedLib.' OR lib_id= '.$chattedLib.' AND to_lib_id = ' .getMainLib().') AND msg_id > '.$lastMsg.' ORDER BY  msg_id DESC LIMIT 20');
	
	if($msgs)echo json_encode(array_reverse($msgs));
	else echo 'false';
}else if($_POST['task']==2){
	$preMsg=$_POST['pre_msg'];

	$msgs=fetch($conn,'SELECT * FROM chat WHERE (lib_id= '.getMainLib().' AND to_lib_id = ' .$chattedLib.' OR lib_id= '.$chattedLib.' AND to_lib_id = ' .getMainLib().') AND msg_id < '.$preMsg.' ORDER BY  msg_id DESC LIMIT 20');
	
	if($msgs)echo json_encode($msgs);
	else echo 'false';
}
