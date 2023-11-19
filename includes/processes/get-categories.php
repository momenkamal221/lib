<?php
include_once $backTpl . 'header.php';
$keyword=$_POST['category'];
$chosenCategories=$_POST['chosenCategories'];
if(empty($keyword))
{
	echo '[]';
	exit();
}
$keyword=cleanInput($keyword);
$categories=getAvailableCategories($conn,$keyword,$chosenCategories);
$data=[];
if($categories)
foreach($categories as $category){
	array_push($data,$category['category']);
}else{
	echo '[]';
	exit();
}
echo json_encode($data);