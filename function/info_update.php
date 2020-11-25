<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$firstName = $func->PostDataGet("FIRST_NAME");
	$lastName = $func->PostDataGet("LAST_NAME");
	$email = $func->PostDataGet("EMAIL");
	$phoneNumber = $func->PostDataGet("PHONE_NUMBER");

	$jsonArray = array();

	if($firstName != '' && $lastName != '' && $email != '' && strlen($phoneNumber) == 11){
		session_start();
		$username = $_SESSION["USERNAME"];
		$password = $_SESSION["PASSWORD"];

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_UPDATE_ACCOUNT('$username','$password','$firstName','$lastName','$email','$phoneNumber');");
		if($data[0]["@ERROR"] == "0"){

			unset($_SESSION["FIRST_NAME"]);
			$_SESSION["FIRST_NAME"] = $firstName;

			unset($_SESSION["LAST_NAME"]);
			$_SESSION["LAST_NAME"] = $lastName;

			unset($_SESSION["EMAIL"]);
			$_SESSION["EMAIL"] = $email;

			unset($_SESSION["PHONE_NUMBER"]);
			$_SESSION["PHONE_NUMBER"] = $phoneNumber;

			$jsonArray["ERROR_CODE"] = "0"; //Successfully Info Change!

		}
		else if($data[0]["@ERROR"] == "1"){
			$jsonArray["ERROR_CODE"] = "1"; //Username or Password Incorrect!
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