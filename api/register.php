<?php 
error_reporting(0);
include("../function/class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);
	$func->addHeader();
	
	$firstName = $func->PostDataGet("FIRST_NAME");
	$lastName = $func->PostDataGet("LAST_NAME");
	$email = $func->PostDataGet("EMAIL");
	//$oneSignalId = $func->PostDataGet("ONESIGNAL_PLAYER_ID");
	$key = $func->PostDataGet("KEY");
	$ip = $func->ipDetect();
	$jsonArray = array();

	if($firstName != '' && $lastName != '' && $email != '' /*&& $oneSignalId != ''*/){

		if($key == 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'){
			$db = new Database();
			$data = $db->Procedure("call sp_MEMBER_REGISTER('$firstName','$lastName','$email','None','$ip');");
			if($data[0]["@ERROR"] == "0"){
				$jsonArray["ERROR_CODE"] = "0"; //Successfully Register!
			}
			else if($data[0]["@ERROR"] == "1"){
				$jsonArray["ERROR_CODE"] = "1"; //Email already exists!
			}
		}
		else{
			$jsonArray["ERROR_CODE"] = "8"; //Invalid Key
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