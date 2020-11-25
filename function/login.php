<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$username = $func->PostDataGet("USERNAME");
	$password = md5($func->PostDataGet("PASSWORD"));
	$ip = $func->ipDetect();
	$jsonArray = array();

	if($username != ''){
		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_LOGIN('$username','$password','$ip');");

		
		if($data[0]["ADMIN_ID"]){
			$jsonArray["ERROR_CODE"] = "0"; //Successfully Login!
			//data = {ADMIN_ID, FIRST_NAME, LAST_NAME, USERNAME, EMAIL, PHONE_NUMBER, REG_DATE}
			session_start();
			$_SESSION["ADMIN_ID"] = $data[0]["ADMIN_ID"];
			$_SESSION["FIRST_NAME"] = $data[0]["FIRST_NAME"];
			$_SESSION["LAST_NAME"] = $data[0]["LAST_NAME"];
			$_SESSION["USERNAME"] = $data[0]["USERNAME"];
			$_SESSION["PASSWORD"] = $password;
			$_SESSION["EMAIL"] = $data[0]["EMAIL"];
			$_SESSION["PHONE_NUMBER"] = $data[0]["PHONE_NUMBER"];
			$_SESSION["REG_DATE"] = $data[0]["REG_DATE"];
		}
		else if($data[0]["@ERROR"] == "1"){
			$jsonArray["ERROR_CODE"] = "1"; //Username or Password Incorrect!
		}
		else if($data[0]["@ERROR"] == "2"){
			$jsonArray["ERROR_CODE"] = "2"; //Inactive Account!
		}
		else if($data[0]["@ERROR"] == "3"){
			$jsonArray["ERROR_CODE"] = "3"; //Unverified!
		}


	}
	else{
		$jsonArray["ERROR_CODE"] = "9"; //Empty Username! 
	}	

	echo $func->json($jsonArray);
}   
else{
	$func->setHeader(400);
}

 ?>