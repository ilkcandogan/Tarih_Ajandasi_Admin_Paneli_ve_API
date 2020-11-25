<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$newFirstName = $func->PostDataGet("NEW_FIRST_NAME");
	$newLastName = $func->PostDataGet("NEW_LAST_NAME");
	$newUsername = $func->PostDataGet("NEW_USERNAME");
	$newEmail = $func->PostDataGet("NEW_EMAIL");
	$newPhoneNumber = $func->PostDataGet("NEW_PHONE_NUMBER");
	$newPassword = $func->PostDataGet("NEW_PASSWORD");

	$jsonArray = array();

	if($newFirstName != '' && $newLastName != '' && $newUsername != '' && $newEmail != '' && strlen($newPhoneNumber) == 11 && strlen($newPassword) >= 8){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$newPassword = md5($newPassword);
		$verifyCode = $func->VerifyCode();

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_ADD_ACCOUNT('$username','$password','$newFirstName','$newLastName','$newUsername','$newEmail','$newPhoneNumber','$verifyCode','$newPassword');");
		if($data[0]["@ERROR"] == "0"){
			$verifyToken = $func->CreateToken($newUsername,$verifyCode);
			$jsonArray["ERROR_CODE"] = "0"; //Successfully 

		}
		else if($data[0]["@ERROR"] == "1"){
			$jsonArray["ERROR_CODE"] = "1"; //Username already exists!
		}
		else if($data[0]["@ERROR"] == "2"){
			$jsonArray["ERROR_CODE"] = "2"; //Email already exists!
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
