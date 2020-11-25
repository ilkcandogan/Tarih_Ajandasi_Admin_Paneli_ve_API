<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$currentPassword = $func->PostDataGet("CURRENT_PASSWORD");
	$newPassword = $func->PostDataGet("NEW_PASSWORD");

	$jsonArray = array();

	if(strlen($currentPassword) >= 8 && strlen($newPassword) >= 8){
		$currentPassword = md5($currentPassword);
		$newPassword = md5($newPassword);

		session_start();
		if($currentPassword == $_SESSION["PASSWORD"]){
			$username = $_SESSION["USERNAME"];
			$db = new Database();
			$data = $db->Procedure("call sp_ADMIN_RENEW_PASSWORD('$username','$currentPassword','$newPassword');");

			if($data[0]["@ERROR"] == "0"){
				$jsonArray["ERROR_CODE"] = "0"; //Successfully Change Password

				unset($_SESSION["PASSWORD"]);
				$_SESSION["PASSWORD"] = $newPassword;
			}
			else if($data[0]["@ERROR"] == "1"){
				$jsonArray["ERROR_CODE"] = "1"; //Username or Password Incorrect!
			}

		}else {
			$jsonArray["ERROR_CODE"] = "2"; //Password Does Not Match
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