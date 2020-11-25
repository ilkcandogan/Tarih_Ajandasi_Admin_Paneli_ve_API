<?php
error_reporting(0);
include('cipher/Crypt/RSA.php');

class Database{
	public static $db;
	function __construct($host="localhost",$db_name="db_name",$db_username="db_username",$db_password="db_password"){
		try {
			self::$db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8",$db_username,$db_password);
		} catch (PDOException $hata) {
			echo "Database Connection Failed";
		}
	}

	function Procedure($query){
		$data_array = array();

		$s = self::$db->query($query);
		$s->setFetchMode(PDO::FETCH_ASSOC);

		while ($data = $s->fetch()) {
			array_push($data_array, $data);
		}

		return $data_array;

	}
}

class Functions{
	function HttpStatusCode($code){
		$status = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',  
        	307 => 'Temporary Redirect',  
       		400 => 'Bad Request',  
        	401 => 'Unauthorized',  
        	402 => 'Payment Required',  
        	403 => 'Forbidden',  
        	404 => 'Not Found',  
        	405 => 'Method Not Allowed',  
        	406 => 'Not Acceptable',  
        	407 => 'Proxy Authentication Required',  
        	408 => 'Request Timeout',  
        	409 => 'Conflict',  
        	410 => 'Gone',  
        	411 => 'Length Required',  
        	412 => 'Precondition Failed',  
        	413 => 'Request Entity Too Large',  
        	414 => 'Request-URI Too Long',  
        	415 => 'Unsupported Media Type',  
        	416 => 'Requested Range Not Satisfiable',  
        	417 => 'Expectation Failed',  
        	500 => 'Internal Server Error',  
        	501 => 'Not Implemented',  
        	502 => 'Bad Gateway',  
        	503 => 'Service Unavailable',  
        	504 => 'Gateway Timeout',  
        	505 => 'HTTP Version Not Supported');
		return $status[$code] ? $status[$code] : $status[500];
	}

	function addHeader(){
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Custom-Header');
        $_POST = json_decode(file_get_contents('php://input'), true);
    }

  	function setHeader($code){
		header("HTTP/1.1 ".$code." ".$this->HttpStatusCode($code));
		header("Content-Type: application/json; charset=utf-8");
	}

	function ipDetect(){
       if (!empty($_SERVER['HTTP_CLIENT_IP']))  
        {  
            $ip=$_SERVER['HTTP_CLIENT_IP'];  
        }  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
        {  
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];  
        }  
        else  
        {  
            $ip=$_SERVER['REMOTE_ADDR'];  
        }  
        
        return $ip;  
    }



    function json($array){
		return json_encode($array, JSON_UNESCAPED_UNICODE);
	}

    function PostDataGet($item){
        return htmlspecialchars($_POST[$item],ENT_QUOTES);
    }

    function VerifyCode(){
        $code = rand(100000, 999999);
        return $code;
    }

    function CreateToken($username,$verifyCode){
        $data = $username.'[i]'.$verifyCode;
        $rsa = new Crypt_RSA();
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----
                           xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                    -----END RSA PRIVATE KEY-----";
        $rsa->LoadKey($privateKey);
        $rsa->LoadKey($rsa->getPublicKey());
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $encryptData = null;
        $encryptionType = 'rc4';
        $key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $token = openssl_encrypt(base64_encode($rsa->encrypt($data)), $encryptionType, $key);
        $token =  rtrim(strtr(base64_encode($token),'+/','-_'),'=');
        return $token;  
    }
}

?>
