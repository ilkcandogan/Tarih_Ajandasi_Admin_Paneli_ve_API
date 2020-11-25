<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$categoryId = $func->PostDataGet("CATEGORY_ID");
	$subcategoryName = $func->PostDataGet("SUBCATEGORY_NAME");

	$jsonArray = array();

	if($categoryId != '' && $subcategoryName != ''){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_FILE_SUBCATEGORY_ADD('$username', '$password', '$categoryId','$subcategoryName');");
		if($data[0]["@ERROR"] == "0"){
			$jsonArray["ERROR_CODE"] = "0"; //Successfully 

		}
		else if($data[0]["@ERROR"] == "2"){
			$jsonArray["ERROR_CODE"] = "2"; //Category name already exists!
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
