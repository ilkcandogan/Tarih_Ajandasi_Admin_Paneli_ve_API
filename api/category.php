<?php 
error_reporting(0);
include("../function/class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);
	$func->addHeader();
	
	$key = $func->PostDataGet("KEY");
	$jsonArray = array();

	if($key == 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'){
		$db = new Database();
		$data = $db->Procedure("call sp_USER_CATEGORY_LIST();");

		$jsonArray['category'] = $data;
	}
	else{
		$jsonArray["ERROR_CODE"] = "8"; //Invalid Key
	}	

	echo $func->json($jsonArray);
}   
else{
	$func->setHeader(400);
}

?>