<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$uploadPath = '../uploads/';

	$categoryId = $func->PostDataGet("CATEGORY_ID");
	$subCategoryId = $func->PostDataGet("SUBCATEGORY_ID");

	$jsonArray = array();

	if($categoryId != '' && $subCategoryId != ''){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$db = new Database();

		if($subCategoryId == 0){
			$data = $db->Procedure("call sp_ADMIN_FILE_CATEGORY_DELETE('$username', '$password', $categoryId);");
			foreach ($data as $value) {
				unlink($uploadPath.$value['FILE_NO']);
			}
			$jsonArray["ERROR_CODE"] = "0"; //Successfully 
		}
		else{
			$data = $db->Procedure("call sp_ADMIN_FILE_SUBCATEGORY_DELETE('$username', '$password', $categoryId, $subCategoryId);");
			foreach ($data as $value) {
				unlink($uploadPath.$value['FILE_NO']);
			}
			$jsonArray["ERROR_CODE"] = "0"; //Successfully 
		}
	}
	else{
		$jsonArray["ERROR_CODE"] = "9"; //Empty Field! 
	}	

	echo $func->json($jsonArray);
}   
else{
	$func->setHeader(400);
}

 ?>
