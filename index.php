<?php 
  error_reporting(0);
  session_start();

  if ($_SESSION["USERNAME"] != '' && strlen($_SESSION["PASSWORD"]) == 32) {
    header("Location: dashboard.php");
    exit();
  }

?>

<!DOCTYPE html>
<html>
<head>
	<title>Tarih Ajandası - Yönetici Paneli</title>
	<meta charset="utf-8">
	 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">

</head>
<body>

  <style type="text/css">
  	body{
  		background-color: #1a1b4b;
  	}

   .card-signin {
     border: 0;
     border-radius: 1rem;
     box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
     background-color: #E9EBEE;
   }

   .card-signin .card-title {
     margin-bottom: 2rem;
     font-weight: 300;
     font-size: 1.5rem;
   }

   .card-signin .card-body {
     padding: 2rem;
   }

   .form-signin {
     width: 100%;
   }
</style>

<div class="card-header" id="alert" style="position: absolute; padding: 8px; border-radius: 3px; top: 0; right: 0; margin-top: 15px; margin-right: 20px; font-size: 14px; display: none;">
    <i id="alertIcon" class="" aria-hidden="true"></i>&nbsp;&nbsp;<span id="alertMessage">...</span>&nbsp;&nbsp;
  </div>
 <div class="container">

  

    <div class="row">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto" style="margin-top: 15px;">
        <div class="card card-signin my-5">
          <div class="card-body">
          	
          	<h5 class="card-title text-center"><img src="img/login/user_avatar.png" width="80"> </h5>
            <h2 class="text-center" style="font-size: 30px; color: #1a1b4b; margin-bottom: 15px;">Yönetici Paneli</h2>
               <div class="form-group">
            <label for="inputKullanici">Kullanıcı Adınız</label>
            <input type="text" class="form-control" id="inputUsername" aria-describedby="inputUsername" placeholder="">

              </div>

            <div class="form-group">
            	<label for="inputSifre">Şifreniz</label>
            	<input style="margin-bottom: 10px;" type="password" class="form-control" id="inputPassword" aria-describedby="inputPassword" placeholder="">
              </div>


              <button class="btn btn-lg btn-dark btn-block text-uppercase" onclick="login();" style="background-color: #1a1b4b;">GİRİŞ YAP</button>
              <hr class="my-4">
               <p style="text-align: center; color: white; font-size: 16px; font: bold;"><a href="https://ilkcandogan.com" style="color:#1a1b4b;">www.ilkcandogan.com<a/></p>
          </div>
        </div>
      </div>
    </div>
    
  </div>

<script type="text/javascript">
  function login(){
    var username = document.getElementById('inputUsername').value;
    var password = document.getElementById('inputPassword').value;

    if (username.trim() != '' && password.trim() != '') {
        $.ajax({
          type: "POST",
          xhrFields: {
          withCredentials: true
        },
          crossDomain: true,
          url: "function/login.php",
          data: {
              'USERNAME': username,
              'PASSWORD': password  
        },
        dataType: 'json',
        success: function(returnData) {
          var error = returnData['ERROR_CODE'];

            if (error == "0") {
                alert('success','Giriş başarılı. Yönlendiriliyorsunuz...');
                setTimeout(() => {
                  document.location.href="dashboard.php";
                },1000);
            } 
            else if(error == "1"){
                alert('error','Kullanıcı adı veya şifreniz yanlış');
            }
            else if(error == "2"){
                alert('info','Hesabınız yönetici tarafından devredışı bırakıldı');
            }
            else if (error == "3") {
                alert('warning','Lütfen hesabınızı akifleştirin');
            }
           
        },
        error: function(hata) {
            alert('error','Sunucuya ulaşılamıyor');
        }
      });

    }
    else{
        alert('warning','Lütfen boş bırakmayınız');
    }
  }

  function alert(type, message){
    var alert = document.getElementById("alert");
    var alertMessage = document.getElementById('alertMessage');
    var alertIcon = document.getElementById('alertIcon');

    if (type == 'error') {
      alertIcon.className = 'fa fa-times-circle';

      alertMessage.innerHTML = message;
      alert.style.color = "#fff";

      alert.style.backgroundColor = "#f75766";
      alert.style.display = "block";
      setTimeout(() => {
        alert.style.display = "none";
      },2000);
    }
    else if(type == 'warning'){
      alertIcon.className = 'fa fa-warning';
      alertMessage.innerHTML = message;
      alert.style.color = "#000";

      alert.style.backgroundColor = "#FFCC00";
      alert.style.display = "block";
      setTimeout(() => {
        alert.style.display = "none";
      },2000);
    }
    else if(type == 'info'){
      alertIcon.className = 'fa fa-info-circle';
      alertMessage.innerHTML = message;
      alert.style.color = "#fff";

      alert.style.backgroundColor = "#66b3ff";
      alert.style.display = "block";
      setTimeout(() => {
        alert.style.display = "none";
      },2000);
    }
    else if(type == 'success'){
      alertIcon.className = 'fa fa-check-circle';
      alertMessage.innerHTML = message;
      alert.style.color = "#fff";

      alert.style.backgroundColor = "#4BB543";
      alert.style.display = "block";
      setTimeout(() => {
        alert.style.display = "none";
      },2000);
    }
  }
</script>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>



</body>
</html>