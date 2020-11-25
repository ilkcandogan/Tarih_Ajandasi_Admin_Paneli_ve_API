<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$categoryId = $func->PostDataGet("CATEGORY_ID");

	$jsonArray = array();

	if($categoryId != ''){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_FILE_SUBCATEGORY_LIST('$username', '$password', '$categoryId');");
		if(count($data) > 0){
			$jsonArray = $data; //Return category list
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
