<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$memberEmail = $func->PostDataGet("EMAIL");

	$jsonArray = array();

	if($memberEmail != ''){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_MEMBER_DELETE('$username','$password','$memberEmail');");
		if($data[0]["@ERROR"] == "0"){
			$jsonArray["ERROR_CODE"] = "0"; //Successfully Member Delete
		}
		else if($data[0]["@ERROR"] == "1"){
			$jsonArray["ERROR_CODE"] = "1"; //Access Denied!
		}
		else if($data[0]["@ERROR"] == "2"){
			$jsonArray["ERROR_CODE"] = "2"; //Member Not Found!
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