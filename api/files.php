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
	$subcategoryId = $func->PostDataGet("SUBCATEGORY_ID");
	$jsonArray = array();

	if($key == 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'){
		if($categoryId != '') {
			if($subcategoryId != ''){
				$db = new Database();
				$data = $db->Procedure("call sp_USER_FILE_LIST($categoryId, $subcategoryId)");

				$jsonArray['files'] = $data;
			}
			else{
				$jsonArray["ERROR_CODE"] = "6"; //Empty category id
			}
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