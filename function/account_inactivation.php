<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$accountUsername = $func->PostDataGet("USERNAME");

	$jsonArray = array();

	if($accountUsername != ''){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_INACTIVATION_ACCOUNT('$username','$password','$accountUsername');");
		if($data[0]["@ERROR"] == "0"){
			$jsonArray["ERROR_CODE"] = "0"; //Successfully Account Delete
		}
		else if($data[0]["@ERROR"] == "1"){
			$jsonArray["ERROR_CODE"] = "1"; //Only Allowed For Admin!
		}
		else if($data[0]["@ERROR"] == "2"){
			$jsonArray["ERROR_CODE"] = "2"; //Invalid Action For Admin!
		}
		else if($data[0]["@ERROR_CODE"] == "3"){
			$jsonArray["ERROR_CODE"] = "3"; //Username Not Found!
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