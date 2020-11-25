<?php 
error_reporting(0);
include("../function/class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);
	$func->addHeader();
	
	$key = $func->PostDataGet("KEY");
	$categoryId = $func->PostDataGet("CATEGORY_ID");
	$jsonArray = array();

	if($key == 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'){
		if($categoryId != '') {
			$db = new Database();
			$data = $db->Procedure("call sp_USER_SUBCATEGORY_LIST($categoryId)");

			$jsonArray['subcategory'] = $data;
		}
		else{
			$jsonArray["ERROR_CODE"] = "7"; //Empty category id
		}
		
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