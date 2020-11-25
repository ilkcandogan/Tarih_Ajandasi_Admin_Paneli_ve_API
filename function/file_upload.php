<?php 
error_reporting(0);
include("class.php");

$func = new Functions();
$_method = $_SERVER['REQUEST_METHOD'];

if ($_method == "POST"){
	$func->setHeader(200);

	$fileName = $func->PostDataGet("FILE_NAME");
	$fileDesc = $func->PostDataGet("FILE_DESC");
	$catId    = $func->PostDataGet("CATEGORY_ID");
	$subCatId = $func->PostDataGet("SUBCATEGORY_ID");
	
	session_start();
	$username = $_SESSION["USERNAME"];
	$password = $_SESSION["PASSWORD"];

	if($username != '' && $password != ''){
		$fileInfo = fileUpload();
		$fileSize = $fileInfo['FILE_SIZE'];
		$fileNo   = $fileInfo['FILE_NO'];
	}

	$jsonArray = array();

	if($fileName != '' /*&& strlen($fileDesc) <= 500*/ && $catId != '' && $subCatId != ''){
		

		$db = new Database();
		$data = $db->Procedure("call sp_ADMIN_FILE_UPLOAD('$username','$password',$catId,$subCatId,'$fileName','$fileSize','$fileDesc','$fileNo');");
		if($data[0]["@ERROR"] == "0"){
			$jsonArray["ERROR_CODE"] = "0"; //Successfully File Delete
			
		}
		else if($data[0]["@ERROR"] == "1"){
			$jsonArray["ERROR_CODE"] = "1"; //Access Denied!
		}
		else if($data[0]["@ERROR"] == "2"){
			$jsonArray["ERROR_CODE"] = "2"; //File Not Found!
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

function fileUpload($path = '../uploads/'){
	$fileInfo = array();

	$randNo = rand(1000,9999);
	$fileName = basename($_FILES['FILE']['name']);
	$fileExt = substr($fileName, strrpos($fileName, '.') + 1);
	$readableSize = convertToReadableSize($_FILES['FILE']['size']);

	while (true) {
		if(file_exists($path.$randNo.'.'.$fileExt)){
			$randNo = rand(1000,9999);
		}
		else{
			break;
		}
	}

	if(move_uploaded_file($_FILES['FILE']['tmp_name'], $path.$randNo.'.'.$fileExt)){
		$fileInfo = array(
			'FILE_SIZE' => $readableSize,
			'FILE_NO' => $randNo.'.'.$fileExt
		);
	}
	
	return $fileInfo;
}

function convertToReadableSize($size){
  $base = log($size) / log(1024);
  $suffix = array("", "KB", "MB", "GB", "TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}


 ?>