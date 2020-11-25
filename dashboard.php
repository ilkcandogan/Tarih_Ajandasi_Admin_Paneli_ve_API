<?php 
   error_reporting(0);
   session_start();
   
   if (empty($_SESSION["USERNAME"]) && empty($_SESSION["PASSWORD"])) {
     header("Location: index.php");
     exit();
   }
   
   $page = htmlspecialchars($_GET['page'],ENT_QUOTES);
   if($page == 'logout'){
   	session_destroy();
   	header("Location: index.php");
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
      <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
      <style type="text/css">
         body{
         background-color: #E9EBEE;
         }
         .bg-dark-blue {
         background-color: #1a1b4b;
         }
         .nav-item a {
            color: #1a1b4b;
         }
         .fa-button {
            cursor: pointer;
            color: #1a1b4b;

         }
         .d-button {
            background-color: #2f306e;
            color: white;
         }
         .d-button:hover{
            color: white;
            background-color: #1a1b4b; 
         }
         td{
            white-space: nowrap;
         }
         #read-button {
         	margin-top: -6px; 
         	padding: 3px;
         }
         #mobile-filter {
     		display: none;
     	}

         @media only screen and (min-width:990px) and (max-width:1230px){
         	
		}

		@media only screen and (min-width: 480px) and (max-width: 992px){
			#filter {
				display: none;
			}
			#mobile-filter {
         		display: inherit;
         	}
		}

		@media only screen and (max-width: 479px) {
			#filter {
				display: none;
			}
			#mobile-filter {
         		display: inherit;
         	}
		}
      </style>
   </head>
   <body>
	    <div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="txtModalTitle">...</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                        <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">
	                    <p id="txtModalMessage">...</p>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn d-button" class="close" data-dismiss="modal" aria-label="Close">Tamam</button>
	                </div>
	            </div>
	        </div>
	    </div>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue">
         <div class="container">
          <a class="navbar-brand" href="dashboard.php">Tarih Ajandası</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
               <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                  <li class="nav-item active">
                     <a class="nav-link" href="dashboard.php">Ana Sayfa<span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="dashboard.php?page=members">Üyeler</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="dashboard.php?page=file-manager">Dosya Yöneticisi</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" target="_blank" href="tarihajandasi_api.pdf">Dökümantasyon</a>
                  </li>
               </ul>
               <form class="form-inline my-2 my-lg-0">
                  <div class="btn-group">
                     <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i id="" class="fa fa-user" aria-hidden="true"></i>&nbsp;
                        <span id="adminFullName"><?php echo $_SESSION["FIRST_NAME"]." ".$_SESSION["LAST_NAME"];?></span>
                     </button>
                     <div class="dropdown-menu " >
                        <a class="dropdown-item mt-1" href="dashboard.php?page=managers" style="padding-left: 15px;">
                        <i id="" class="fa fa-users" aria-hidden="true"></i>&nbsp; Yöneticiler
                        </a>
                        <a class="dropdown-item disabled" href="dashboard.php?page=general-settings" style="padding-left: 15px;">
                        <i id="" class="fa fa-cogs" aria-hidden="true"></i>&nbsp; Genel Ayarlar
                        </a>
                        <a class="dropdown-item" href="dashboard.php?page=account-settings" style="padding-left: 15px;">
                        <i id="" class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;Hesap Ayarları
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="dashboard.php?page=logout" style="padding-left: 15px;">
                        <i id="" class="fa fa-sign-out fa-lg" aria-hidden="true"></i>&nbsp; Çıkış yap
                        </a>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </nav>

      <?php if($page == '') {  ?>
      <div class="container">
         <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mt-4 mb-4">
               <h5 class="text-center">Günlük Üye Kayıt Grafiği</h5>
               <canvas id="register-day-statistics"></canvas>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mt-4 mb-4">
                <h5 class="text-center">Aylık Üye Kayıt Grafiği</h5>
               <canvas id="register-mounth-statistics"></canvas>
            </div>
         </div>
         <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mt-4 mb-4">
               <h5 class="text-center">Cihaz Kullanım Grafiği</h5>
               <canvas id="mobile-devices-statistics"></canvas>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mt-4 mb-4">
               <h5 class="text-center">Cinsiyet Grafiği</h5>
               <canvas id="gender-statistics"></canvas>
            </div>
         </div>
      </div>
      <script type="text/javascript">
        <?php  
          error_reporting(0);
          include('function/class.php');

          $db = new Database();
          $data = $db->Procedure("call sp_ADMIN_WEEKLY_MEMBER_STATISTICS();");
          //MON, TUE, WED, THU, FRI, SAT, SUN
          $days = $data[0]["MON"].','.$data[0]["TUE"].','.$data[0]["WED"].','.$data[0]["THU"].','.$data[0]["FRI"].','.$data[0]["SAT"].','.$data[0]["SUN"];
        ?>
      	  var ctx = document.getElementById('register-day-statistics').getContext('2d');
      		var chart = new Chart(ctx, {
      		    type: 'line',

      		    // The data for our dataset
      		    data: {
      		        labels: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
      		        datasets: [{
      		            label: 'Üye Kayıt Sayısı',
      		            backgroundColor: 'rgb(77, 81, 250)',
      		            borderColor: 'rgb(77, 81, 250)',
      		            fill: false,
                      data: [<?php echo $days; ?>]
      		        }]
      		    },

      		    options: {responsive: true}
      		});

      		var ctx2 = document.getElementById('register-mounth-statistics').getContext('2d');
      		var chart2 = new Chart(ctx2, {
      		    type: 'bar',

      		    // The data for our dataset
      		    data: {
      		        labels: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl','Eki','Kas', 'Ara'],
      		        datasets: [{
      		            label: 'Üye Kayıt Sayısı',
      		            backgroundColor: 'rgb(128, 128, 128)',
      		            borderColor: 'rgb(128, 128, 128)',
      		            fill: false,
      		            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
      		        }]
      		    },

      		    options: {responsive: true}
      		});

      		var ctx3 = document.getElementById('mobile-devices-statistics').getContext('2d');
      		var chart3 = new Chart(ctx3, {
      		    type: 'pie',
      		    data: {
      		        labels: ['Android (%)' , 'IOS (%)', 'Web (%)'],
      		        datasets: [{
      		            backgroundColor: [
      		            	'rgb(128, 128, 128)',
      		            	'rgb(128, 128, 128)',
      		            	'rgb(128, 128, 128)'
      		            ],
      		            borderColor: 'rgb(233,235,238)',
      		            fill: false,
      		            data: [50, 30, 20]
      		        }]
      		    },

      		    options: {responsive: true}
      		});

      		var ctx4 = document.getElementById('gender-statistics').getContext('2d');
      		var chart4 = new Chart(ctx4, {
      		    type: 'doughnut',
      		    data: {
      		        labels: ['Erkek' , 'Kadın', 'Belirtilmemiş'],
      		        datasets: [{
      		            backgroundColor: [
      		            	'rgb(128, 128, 128)',
      		            	'rgb(128, 128, 128)',
      		            	'rgb(128, 128, 128)'
      		            ],
      		            borderColor: 'rgb(233,235,238)',
      		            fill: false,
      		            data: [20, 30, 20]
      		        }]
      		    },

      		    options: {responsive: true}
      		});
      </script>
      <?php } ?>

      <?php if($page == 'managers') {  ?>

      <div class="modal fade" id="ActionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="txtActionModalTitle">...</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="txtActionModalMessage">...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
                    <button id="actionButton" type="button" class="btn d-button">Evet</button>
                </div>
            </div>
        </div>
    </div>

      <div class="container">
         <div class="row">
            <div class="col-12 mt-4">
               <h5>Yöneticiler</h5>
               <ul class="nav nav-tabs" id="myTab" role="tablist" >
                 <li class="nav-item" role="presentation">
                   <a class="nav-link active" id="managers-list-tab" data-toggle="tab" href="#managers-list" role="tab" aria-controls="managers-list" aria-selected="true">   <i id="" class="fa fa-id-card-o fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Yönetici Listesi
                   </a>
                 </li>
                 <li class="nav-item" role="presentation">
                   <a class="nav-link" id="inactive-account-list-tab" data-toggle="tab" href="#inactive-account-list" role="tab" aria-controls="inactive-account-list" aria-selected="false">
                     <i id="" class="fa fa-list fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Pasif Hesaplar
                  </a>
                 </li>
                 <li class="nav-item" role="presentation">
                  <a class="nav-link" id="manager-add-tab" data-toggle="tab" href="#manager-add" role="tab" aria-controls="manager-add" aria-selected="false">
                    <i id="" class="fa fa-user-plus fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Yönetici Ekle
                  </a>
                 </li>
               </ul>
               <div class="tab-content" id="myTabContent">
                  <div class="tab-pane active" id="managers-list" role="tabpanel">
                     <div class="table-responsive">
                        <table class="table">
                           <thead style="white-space: nowrap; background-color: #1a1b4b; color:white;">
                              <tr>
                                 <th>#</th>
                                 <th>Ad</th>
                                 <th>Soyad</th>
                                 <th>Kullanıcı Adı</th>
                                 <th>E-Posta</th>
                                 <th>Telefon Numarası</th>
                                 <th>Kayıt Tarihi</th>
                                 <th>Son Giriş IP</th>
                                 <th>İşlem</th>
                              </tr>
                           </thead>
                           <tbody>
                            <?php 
                              error_reporting(0);
                              include('function/class.php');

                              $db = new Database();
                              $data = $db->Procedure("call sp_ADMIN_ALL_ACCOUNT_LIST();");
                              $i = 1;
                              foreach ($data as $value) { ?>
                              <!-- Block -->
                              <tr>
                                 <td><?php echo $i; ?></td>
                                 <td><?php echo $value["FIRST_NAME"]; ?></td>
                                 <td><?php echo $value["LAST_NAME"]; ?></td>
                                 <td><?php echo $value["USERNAME"]; ?></td>
                                 <td><?php echo $value["EMAIL"]; ?></td>
                                 <td><?php echo $value["PHONE_NUMBER"]; ?></td>
                                 <td><?php echo $value["REG_DATE"]; ?></td>
                                 <td><?php echo $value["LAST_LOGIN_IP_ADDRESS"]; ?></td>
                                 <td>
                                    <i class="fa fa-ban fa-lg fa-button" aria-hidden="true" data-html="true" title="Hesabı pasifleştir" onclick="ActionModal('Hesabı Pasifleştir','Hesabı pasifleştirmek istediğinize emin misiniz?','<?php echo $value["USERNAME"]; ?>','inactivation');"></i>&nbsp;&nbsp;
                                    <i class="fa fa-trash fa-lg fa-button" aria-hidden="true" data-html="true" title="Hesabı sil" onclick="ActionModal('Hesabı Sil','Hesabı silmek istediğinize emin misiniz?','<?php echo $value["USERNAME"]; ?>','delete');"></i>&nbsp;&nbsp;
                                    <i class="fa fa-unlock fa-lg fa-button" aria-hidden="true" data-html="true" title="Şifreyi sıfırla" onclick="ActionModal('Şifreyi Sıfırla','Hesap şifresini sıfırlamak istediğinize emin misiniz?','<?php echo $value["USERNAME"]; ?>','reset');"></i>&nbsp;
                                 </td>
                              </tr>
                              <!-- Block -->
                              <?php $i++; } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <div class="tab-pane" id="inactive-account-list" role="tabpanel" aria-labelledby="inactive-account-list-tab">
                     <div class="table-responsive">
                        <table class="table">
                           <thead style="white-space: nowrap; background-color: #1a1b4b; color:white;">
                              <tr>
                                 <th>#</th>
                                 <th>Ad</th>
                                 <th>Soyad</th>
                                 <th>Kullanıcı Adı</th>
                                 <th>E-Posta</th>
                                 <th>Telefon Numarası</th>
                                 <th>Kayıt Tarihi</th>
                                 <th>Son Giriş IP</th>
                                 <th>İşlem</th>
                              </tr>
                           </thead>
                           <tbody>
                              
                              <?php
                                error_reporting(0);
                                $data = $db->Procedure("call sp_ADMIN_INACTIVE_ACCOUNT_LIST();");
                                $i = 1;
                                foreach ($data as $value) { ?>                                 
                              <!-- Block -->
                              <tr>
                                 <td><?php echo $i; ?></td>
                                 <td><?php echo $value["FIRST_NAME"]; ?></td>
                                 <td><?php echo $value["LAST_NAME"]; ?></td>
                                 <td><?php echo $value["USERNAME"]; ?></td>
                                 <td><?php echo $value["EMAIL"]; ?></td>
                                 <td><?php echo $value["PHONE_NUMBER"]; ?></td>
                                 <td><?php echo $value["REG_DATE"]; ?></td>
                                 <td><?php echo $value["LAST_LOGIN_IP_ADDRESS"]; ?></td>
                                  <td >
                                    <i class="fa fa-check fa-lg fa-button" aria-hidden="true" data-html="true" title="Hesabı aktifleştir" onclick="ActionModal('Hesabı Aktifleştir','Hesabı aktifleştirmek istediğinize emin misiniz?','<?php echo $value["USERNAME"]; ?>','activation');"></i>&nbsp;&nbsp;
                                    <i class="fa fa-trash fa-lg fa-button" aria-hidden="true" data-html="true" title="Hesabı sil" onclick="ActionModal('Hesabı Sil','Hesabı silmek istediğinize emin misiniz?','<?php echo $value["USERNAME"]; ?>', 'delete');"></i>&nbsp;&nbsp;
                                 </td>
                              </tr>
                              <!-- Block -->
                              <?php $i++; } ?>
                              
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="manager-add" role="tabpanel" aria-labelledby="manager-add-tab">
                     <div class="row mt-4 mb-5">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                           <form>
                             <div class="form-group">
                               <label for="txtFirstName">Ad</label>
                               <input type="text" class="form-control" id="txtFirstName" aria-describedby="txtFirstNameHelp" placeholder="">
                             </div>
                              <div class="form-group">
                               <label for="txtLastName">Soyad</label>
                               <input type="text" class="form-control" id="txtLastName" aria-describedby="txtLastNameHelp" placeholder="">
                             </div>
                              <div class="form-group">
                               <label for="txtUsername">Kullanıcı Adı</label>
                               <input type="text" class="form-control" id="txtUsername" aria-describedby="txtUsernameHelp" placeholder="">
                             </div>
                             <div class="form-group">
                               <label for="txtEmail">E-Posta</label>
                               <input type="text" class="form-control" id="txtEmail" aria-describedby="txtEmailHelp" placeholder="">
                             </div>
                           </form>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                           <form>
                             <div class="form-group">
                               <label for="txtPhoneNumber">Telefon Numarası</label>
                               <input type="text" class="form-control" id="txtPhoneNumber" aria-describedby="txtPhoneNumberHelp" placeholder="">
                             </div>
                              <div class="form-group">
                               <label for="txtPassword">Şifre</label>
                               <input type="text" class="form-control" id="txtPassword" aria-describedby="txtPasswordHelp" placeholder="">
                             </div>
                              <div class="form-group">
                               <label for="txtPasswordConfirm">Şifre (Tekrar)</label>
                               <input type="text" class="form-control" id="txtPasswordConfirm" aria-describedby="txtPasswordConfirmHelp" placeholder="">
                             </div>
                           </form>
                           <div class="form-check mb-2">
                            <label class="form-check-label" for="randomPassword"></label>
                          </div>
                           <button class="btn btn-warning mr-3" onclick="inputClear();">
                              <i class="fa fa-refresh" aria-hidden="true"></i> Temizle
                           </button>
                           <button class="btn d-button" onclick="addManager();">
                              <i class="fa fa-user-plus" aria-hidden="true"></i> Yönetici Ekle
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script type="text/javascript">
          function inputClear(){
            clearInput('txtFirstName');
            clearInput('txtLastName');
            clearInput('txtUsername');
            clearInput('txtEmail');
            clearInput('txtPhoneNumber');
            clearInput('txtPassword');
            clearInput('txtPasswordConfirm');
          }

          function addManager() {
            var firstName = getInput('txtFirstName');
            var lastName = getInput('txtLastName');
            var username = getInput('txtUsername');
            var email = getInput('txtEmail');
            var phoneNumber = getInput('txtPhoneNumber');
            var password = getInput('txtPassword');
            var passwordConfirm = getInput('txtPasswordConfirm');


            if(firstName != '' && lastName != '' && username != '' && email != '' && phoneNumber != '' && password != '' && passwordConfirm != ''){
              if(phoneNumber.length == 11){
                if(password.length >= 8) {
                  if(password == passwordConfirm){
                    ajaxRequest('POST', 'function/add_manager.php', {
                      'NEW_FIRST_NAME': firstName,
                      'NEW_LAST_NAME':  lastName,
                      'NEW_USERNAME': username,
                      'NEW_EMAIL': email,
                      'NEW_PHONE_NUMBER': phoneNumber,
                      'NEW_PASSWORD': password
                    }, (code)=> {
                        if(code == "0"){
                          AlertModal('AlertModal','Bilgi','Yönetici eklendi.');
                          inputClear();
                          window.location.reload();
                        }
                        else if(code == "1"){
                          AlertModal('AlertModal','Uyarı','Kullanıcı adı kullanılmaktadır.');
                        }
                        else if(code == "2"){
                          AlertModal('AlertModal','Uyarı','E-Posta adresi kullanılmaktadır.');
                        }
                    });
                  }
                  else{
                    AlertModal('AlertModal','Uyarı','Şifreler uyuşmuyor. Lütfen kontol edin.');
                  }
                }
                else{
                  AlertModal('AlertModal','Uyarı','Şifre en az 8 karakterli olmalıdır.');
                }
              }
              else{
                AlertModal('AlertModal','Uyarı','Telefon numarası 11 haneli olmalıdır.');
              }
            }
            else{
              AlertModal('AlertModal','Uyarı','Lütfen boş bırakmayınız.');
            }
          }

          function ActionModal(title, message, username, action){
            document.getElementById('txtActionModalTitle').textContent = title;
            document.getElementById('txtActionModalMessage').textContent = message;
            document.getElementById("actionButton").onclick = function(){ Action(username,action); }
            $('#ActionModal').modal('show');
          }

          function Action(username, action){
            var url = '';

            if(action == 'delete'){
              url = 'function/account_delete.php';
            }
            else if(action == 'activation'){
              url = 'function/account_activation.php';
            }
            else if(action == 'inactivation'){
              url = 'function/account_inactivation.php';
            }
            else if(action == 'reset'){
              url = 'function/account_password_reset.php';
            }

            ajaxRequest('POST',url,{
              'USERNAME': username
            }, (code)=> {
            	$('#ActionModal').modal('hide');

                if(action == 'delete'){
                  if (code == '0') {
                      AlertModal('AlertModal','Bilgi','Hesap başarıyla silindi.');
                      window.location.reload();
                  }
                  else if(code == '1'){
                      AlertModal('AlertModal','Uyarı','Yöneticileri sadece "admin" silebilir.');
                  }
                  else if(code == '2'){
                  	  AlertModal('AlertModal','Uyarı','"admin" hesabı silinemez.');
                  }
                  else if(code == '3'){
                  	  AlertModal('AlertModal','Uyarı','Kullanıcı adı bulunamadı!');
                  }
                  else {
                  	  AlertModal('AlertModal','Hata','Hata Kodu: 0x600!');
                  }
                  
                }
                else if(action == 'inactivation'){
                	if(code == '0'){
                		AlertModal('AlertModal','Bilgi','Hesap başarıyla pasif edildi.');
                		window.location.reload();
                	}
                	else if(code == '1'){
                		AlertModal('AlertModal','Uyarı','Yöneticileri sadece "admin" pasif edebilir.');
                	}
                	else if(code == '2'){
                		AlertModal('AlertModal','Uyarı','"admin" hesabı pasif edilemez.');
                	}
                	else if(code == '3'){
                		AlertModal('AlertModal','Uyarı','Kullanıcı adı bulunamadı!');
                	}
                	else {
                		AlertModal('AlertModal','Hata','Hata Kodu: 0x600!');
                	}
                }
                else if(action == 'activation'){
                	if(code == '0'){
                		AlertModal('AlertModal','Bilgi','Hesap başarıyla aktif edildi.');
                		window.location.reload();
                	}
                	else if(code == '1'){
                		AlertModal('AlertModal','Uyarı','Yöneticileri sadece "admin" aktif edebilir.');
                	}
                	else if(code == '2'){
                		AlertModal('AlertModal','Uyarı','"admin" hesabı aktif edilemez.');
                	}
                	else if(code == '3'){
                		AlertModal('AlertModal','Uyarı','Kullanıcı adı bulunamadı!');
                	}
                	else {
                		AlertModal('AlertModal','Hata','Hata Kodu: 0x600!');
                	}
                }
                else if(action == 'reset'){
                	if(code == '0'){
                		AlertModal('AlertModal','Bilgi','Hesap şifresiz sıfırlandı. Şifre: 12345678');
                	}
                	else if(code == '1'){
                		AlertModal('AlertModal','Uyarı','Hesap şifresini sadece "admin" sıfırlayabilir.');
                	}
                	else if(code == '2'){
                		AlertModal('AlertModal','Uyarı','"admin" hesabının şifresi sıfırlanamaz.');
                	}
                	else if(code == '3'){
                		AlertModal('AlertModal','Uyarı','Kullanıcı adı bulunamadı!');
                	}
                	else {
                		AlertModal('AlertModal','Hata','Hata Kodu: 0x600!');
                	}
                }
                
            });
          }
      </script>
      <?php } ?>

      <?php if($page == "account-settings"){ ?>
           <div class="container">
              <div class="row">
                  <div class="col-12 mt-4">
                     <ul class="nav nav-tabs" id="myTab" role="tablist" >
                       <li class="nav-item" role="presentation">
                         <a class="nav-link active" id="account-settings-tab" data-toggle="tab" href="#account-settings" role="tab" aria-controls="account-settings" aria-selected="true">   <i id="" class="fa fa-cog fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Hesap Ayarları
                         </a>
                       </li>
                     </ul>
                      <div class="tab-content" id="myTabContent">
                        <div class="tab-pane active" id="account-settings" role="tabpanel">
                           <div class="row">
                             <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mt-4">
                                 <form>
                                   <div class="form-group">
                                     <label for="inputFirstName">Ad</label>
                                     <input type="text" class="form-control" id="inputFirstName" value="<?php echo $_SESSION["FIRST_NAME"]; ?>">
                                   </div>
                                    <div class="form-group">
                                     <label for="inputLastName">Soyad</label>
                                     <input type="text" class="form-control" id="inputLastName" value="<?php echo $_SESSION["LAST_NAME"]; ?>">
                                   </div>
                                   <div class="form-group">
                                     <label for="inputEmail">E-Posta</label>
                                     <input type="text" class="form-control" id="inputEmail" value="<?php echo $_SESSION["EMAIL"]; ?>">
                                   </div>
                                   <div class="form-group">
                                     <label for="inputPhoneNumber">Telefon Numarası</label>
                                     <input type="text" class="form-control" id="inputPhoneNumber" value="<?php echo $_SESSION["PHONE_NUMBER"]; ?>">
                                   </div>
                                 </form>
                                 <button class="btn d-button" onclick="updateInfo();">
                                    <i class="fa fa-repeat" aria-hidden="true"></i>&nbsp; Bilgilerimi Güncelle
                                 </button>
                              </div>
                               <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mt-4">
                                 <form>
                                   <div class="form-group">
                                     <label for="inputCurrentPassword">Geçerli Şifre</label>
                                     <input type="password" class="form-control" id="inputCurrentPassword">
                                   </div>
                                    <div class="form-group">
                                     <label for="inputNewPassword">Yeni Şifre</label>
                                     <input type="password" class="form-control" id="inputNewPassword">
                                   </div>
                                   <div class="form-group">
                                     <label for="inputNewPasswordConfirm">Yeni Şifre (Tekrar)</label>
                                     <input type="password" class="form-control" id="inputNewPasswordConfirm">
                                   </div>
                                 </form>
                                  <button class="btn d-button mb-5" onclick="changePassword();">
                                    <i class="fa fa-lock" aria-hidden="true"></i>&nbsp; Şifremi Değiştir
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
              </div>
           </div>
           <script type="text/javascript">
              function changePassword(){
                  var currentPassword = getInput("inputCurrentPassword");
                  var newPassword = getInput("inputNewPassword");
                  var newPasswordConfirm = getInput("inputNewPasswordConfirm");

                  if(currentPassword.trim() != '' && newPassword.trim() != '' && newPasswordConfirm.trim() != ''){
                     if(newPassword.length >= 8) {
                        if(newPassword == newPasswordConfirm){
                           ajaxRequest('POST', 'function/new_password.php', {
                              'CURRENT_PASSWORD': currentPassword,
                              'NEW_PASSWORD': newPassword
                           }, (code)=> {
                              if(code == "0"){
                                 AlertModal('AlertModal','Bilgi','Şifreniz değiştirildi.');
                              }
                              else if(code == "1"){
                                 AlertModal('AlertModal','Uyarı','Kullanıcı adınız veya şifreniz yanlış. Lütfen sisteme tekrar giriş yapın.');
                              }
                              else if(code == "2"){
                                 AlertModal('AlertModal','Uyarı','Geçerli şifreniz yanlış.');
                              }
                           });
                        }
                        else{
                           AlertModal('AlertModal','Uyarı','Şifreniz uyuşmuyor. Lütfen kontol edin.');
                        }
                     }
                     else{
                        AlertModal('AlertModal','Uyarı','Şifreniz en az 8 karakterli olmalıdır.');
                     }
                  }
                  else{
                     AlertModal('AlertModal','Uyarı','Lütfen boş bırakmayınız.');
                  }
              }

              function updateInfo(){
                  var firstName = getInput("inputFirstName");
                  var lastName = getInput("inputLastName");
                  var email = getInput("inputEmail");
                  var phoneNumber = getInput("inputPhoneNumber");

                  if(firstName.trim() != '' && lastName.trim() != '' && email.trim() != '' && phoneNumber.trim() != ''){
                     if(phoneNumber.length == 11){
                        ajaxRequest('POST','function/info_update.php', {
                           'FIRST_NAME': firstName,
                           'LAST_NAME': lastName,
                           'EMAIL': email,
                           'PHONE_NUMBER': phoneNumber
                        }, (code)=> {
                           if (code == "0") {
                              document.getElementById('adminFullName').innerHTML = firstName + " " + lastName;
                              AlertModal('AlertModal','Bilgi','Bilgileriniz güncellendi.');
                           }
                           else if(code == "1"){
                              AlertModal('AlertModal','Uyarı','Kullanıcı adınız veya şifreniz yanlış. Lütfen sisteme tekrar giriş yapın.');
                           }
                           else if(code == "2"){
                              AlertModal('AlertModal','Uyarı','E-Posta adresi zaten kayıtlı.');
                           }
                        });

                     }  
                     else{
                        AlertModal('AlertModal','Uyarı','Telefon numarası 11 haneli olmalıdır.');
                     }
                  }
                  else{
                     AlertModal('AlertModal','Uyarı','Lütfen boş bırakmayınız.');
                  }
              }
           </script>
      <?php } ?>


      <?php if($page == "members"){ ?>
      	<div class="modal fade" id="ActionModal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="txtActionModalTitle">...</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                        <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">
	                    <p id="txtActionModalMessage">...</p>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
	                    <button id="actionButton" type="button" class="btn d-button">Evet</button>
	                </div>
	            </div>
	        </div>
	    </div>

      	<div class="container">
      		<div class="row">
      			<div class="col-12 mt-4">
      				<h5>Üyeler</h5>
      				<ul class="nav nav-tabs" id="myTab" role="tablist" >
                       <li class="nav-item" role="presentation">
                         <a class="nav-link active" id="member-list-tab" data-toggle="tab" href="" role="tab" aria-controls="member-list" aria-selected="true">
                         	<i id="" class="fa fa-user fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Üye Listesi
                         </a>
                       </li>
                     </ul>
      				<div class="table-responsive">
                        <table class="table">
                           <thead style="white-space: nowrap; background-color: #1a1b4b; color:white;">
                              <tr>
                                 <th>#</th>
                                 <th>Ad</th>
                                 <th>Soyad</th>
                                 <th>E-Posta</th>
                                 <th>OneSignal ID</th>
                                 <th>Kayıt IP Adresi</th>
                                 <th>Kayıt Tarihi</th>
                                 <th>İşlem</th>
                              </tr>
                           </thead>
                           <tbody>
                           		<?php 
                           			error_reporting(0);
                           			include('function/class.php');

                              		$db = new Database();
                                	$data = $db->Procedure("call sp_ADMIN_ALL_MEMBERS();");
                                	$i = 1;
                                	foreach ($data as $value) { ?>
	                           		<!-- Block -->
		                            <tr>
		                            	<td><?php echo $i; ?></td>
		                            	<td><?php echo $value["FIRST_NAME"]; ?></td>
		                            	<td><?php echo $value["LAST_NAME"]; ?></td>
		                            	<td><?php echo $value["EMAIL"]; ?></td>
		                            	<td><?php echo $value["ONESIGNAL_PLAYER_ID"]; ?></td>
		                            	<td><?php echo $value["IP_ADDRESS"]; ?></td>
		                            	<td><?php echo $value["REG_DATE"]; ?></td>
		                            	<td class="content-center">
		                            		<i class="fa fa-trash fa-lg fa-button" aria-hidden="true" data-html="true" title="Hesabı sil" onclick="ActionModal('Üye Sil','Üye hesabını silmek istediğinize emin misiniz?','<?php echo $value["EMAIL"]; ?>');"></i>
		                            	</td>
		                            </tr>
		                            <!-- Block -->
		                        <?php $i++; } ?>
                           </tbody>
                        </table>
                     </div>
      			</div>
      		</div>
      	</div>

      	<script type="text/javascript">
      	  function ActionModal(title, message, email){
            document.getElementById('txtActionModalTitle').textContent = title;
            document.getElementById('txtActionModalMessage').textContent = message;
            document.getElementById("actionButton").onclick = function(){ Action(email); }
            $('#ActionModal').modal('show');
          }

          function Action(email){
            var url = 'function/member_delete.php';

            ajaxRequest('POST',url,{
              'EMAIL': email
            }, (code)=> {
            	  $('#ActionModal').modal('hide');
	              if (code == '0') {
	                  AlertModal('AlertModal','Bilgi','Üye başarıyla silindi.');
	                  window.location.reload();
	              }
	              else if(code == '1'){
	                  AlertModal('AlertModal','Uyarı','Lütfen sisteme tekrar giriş yapınız');
	              }
	              else if(code == '2'){
	              	  AlertModal('AlertModal','Uyarı','Üye bulunamadı.');
	              }
	              else {
	              	  AlertModal('AlertModal','Hata','Hata Kodu: 0x700!');
	              }
            });
          }
      	</script>
      <?php } ?>


      <?php if($page == "file-manager"){ ?>
      	<div class="modal fade" id="FilterModal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title">
	                    	Kategori Filtresi
	                    </h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                        <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">
	                    <div class="form-group container">
	                    	<div class="row">
	                    		<div class="col-10">
	                    			<label for="modalCategorySelect">Kategori</label>
			                    	<select id="modalCategorySelect" class="form-control" onchange="categoryChange('modalCategorySelect','modalSubCategorySelect');">
			                    		<?php 
			                    			error_reporting(0);
	                              			include('function/class.php');
	                              			$db = new Database();
	                              			

	                              			session_start();
	                              			$username = $_SESSION["USERNAME"];
	                              			$password = $_SESSION["PASSWORD"];

	                              			$catList = $db->Procedure("call sp_ADMIN_FILE_CATEGORY_LIST('$username','$password');");
	                              			$_firstCatId = $catList[0]['ID'];
	                              			foreach ($catList as $value) { ?>
	                              				<option value="<?php echo $value['ID']; ?>"><?php echo $value["CATEGORY_NAME"]; ?></option>
	                              			<?php } ?>
			                    	</select>
	                    		</div>
	                    		<div class="col-1">
	                    			<div class="form-group">
	                    				<i class="fa fa-trash fa-lg fa-button" style="margin-top: 42px;" onclick="<?php 
	                    					if($_firstCatId != null){ ?>
	                    						DeleteModal('Kategori Sil','Bu kategoriyi silerseniz alt kategoriler ve dosyalar silinecektir. Bunu yapmak istediğinize emin misiniz?','modalCategorySelect','');
	                    					<?php } ?>"></i>
	                    			</div>
	                    		</div>
	                    	</div>
	                    </div>
	                    <div class="form-group container">
	                    	<div class="row">
	                    		<div class="col-10">
	                    			<label for="modalSubCategorySelect">Alt Kategori</label>
			                    	<select id="modalSubCategorySelect" class="form-control">
			                    		<?php
			                    			if($_firstCatId == null) $_firstCatId = 0; 
	                              			$subcatList = $db->Procedure("call sp_ADMIN_FILE_SUBCATEGORY_LIST('$username','$password',$_firstCatId);");
	                              			foreach ($subcatList as $value) { ?>
	                              				<option value="<?php echo $value['ID']; ?>"><?php echo $value["SUBCATEGORY_NAME"]; ?></option>
	                              			<?php } ?>
			                    	</select>
	                    		</div>
	                    		<div class="col-1">
	                    			<div class="form-group">
	                    				<i class="fa fa-trash fa-lg fa-button" style="margin-top: 42px;" onclick="<?php 
	                    					if($_firstCatId != 0){ ?>
	                    						DeleteModal('Alt Kategori Sil','Bu alt kategoriyi silerseniz tüm dosylar da silinecektir. Bunu yapmak istediğinize emin misiniz?','modalCategorySelect','modalSubCategorySelect');
	                    				<?php } ?>"></i>
	                    			</div>
	                    		</div>
	                    	</div>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
	                    <button id="FilterButton" type="button" class="btn d-button" onclick="catFilter();">Filtrele</button>
	                </div>
	            </div> 
	        </div>
	    </div>

	    <div class="modal fade" id="ActionModal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="txtActionModalTitle">...</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                        <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">
	                    <p id="txtActionModalMessage">...</p>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
	                    <button id="actionButton" type="button" class="btn d-button">Evet</button>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="txtDeleteModalTitle">...</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                        <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">
	                    <p id="txtDeleteModalMessage">...</p>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
	                    <button id="DeleteButton" type="button" class="btn d-button">Evet</button>
	                </div>
	            </div>
	        </div>
	    </div>
       <div class="modal fade" id="categoryAddModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="txtCategoryAddTitle">Yeni Kategori Ekle</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                        <label>Kategori Adı</label>
                        <input type="text" id="txtCategoryAddName" class="form-control" onfocus="setInput('modal-message','');">
                      </div>
                      <label id="modal-message" style="color: red;" class=""></label>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
                      <button id="actionButton" type="button" class="btn d-button" onclick="categoryAdd();">Ekle</button>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="subcategoryAddModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="txtSubcategoryAddTitle">Yeni Alt Kategori Ekle</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                        <label>Alt Kategori Adı</label>
                        <input type="text" id="txtSubcategoryAddName" class="form-control" onfocus="setInput('modal-message2','');">
                      </div>
                      <div class="form-group">
                      	<label id="modal-message2" style="color: red;"></label>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-warning" class="close" data-dismiss="modal" aria-label="Close">İptal</button>
                      <button id="actionButton" type="button" class="btn d-button" onclick="categoryAdd(true);">Ekle</button>
                  </div>
              </div>
          </div>
      </div>

      	<div class="container">
      		<div class="row">
      			<div class="col-12 mt-4">
	               <h5>Dosya Yöneticisi / <?php
	               		if($_GET['c'] != '' && $_GET['s'] != ''){
	                      	$categoryId = $_GET['c'];
	                      	$subCategoryId = $_GET['s'];

	                      	$catName = $db->Procedure("call sp_ADMIN_FILE_CATEGORY_NAME($categoryId);")[0]['CATEGORY_NAME'];

	                      	if($subCategoryId != 0){
	                      		$subCatName = $db->Procedure("call sp_ADMIN_FILE_SUBCATEGORY_NAME($subCategoryId);")[0]['SUBCATEGORY_NAME'];
	                      		if($subCatName != ''){
                              $catName = $catName.' > '.$subCatName;
                            }
	                      		echo '<span style="font-size: 15px; text-decoration: underline;">'.$catName.'</span>';
	                      	}
	                      	else{
	                      		echo '<span style="font-size: 15px; text-decoration: underline;">'.$catName.'</span>';
	                      	}
	                      }
	                      else{
	                      	echo '<span style="font-size: 15px; text-decoration: underline;">Tüm Dosyalar</span>';
	                      }
	                ?>
	               </h5>
                 	<div style="float: right; cursor: pointer;" class="btn" id="filter" onclick="$('#FilterModal').modal('show');">
                      <i class="fa fa-filter fa-lg fa-button"></i> <span style="text-decoration: underline;">Kategoriye Göre Filtrele</span>
                   </div>
	               <ul class="nav nav-tabs" id="myTab" role="tablist" >
	                 <li class="nav-item" role="presentation">
	                   <a class="nav-link <?php if($_GET['tab'] == '1') echo null; else echo 'active'; ?>" id="files-tab" data-toggle="tab" href="#files-list" role="tab" aria-controls="files-list" aria-selected="true" onclick="filterDisplay('filter',true);">
	                   	<i id="" class="fa fa-folder fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Dosyalar
	                   </a>
	                 </li>
	                 <li class="nav-item" role="presentation">
	                   <a class="nav-link <?php if($_GET['tab'] == '1') echo 'active'; else echo null; ?>" id="file-upload-tab" data-toggle="tab" href="#file-upload" role="tab" aria-controls="file-upload" aria-selected="false" onclick="filterDisplay('filter',false);">
	                     <i id="" class="fa fa-cloud-upload fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Dosya Yükle
	                  </a>
	                 </li>

	               </ul>
	               <div class="tab-content" id="myTabContent">

	                  <div class="tab-pane <?php if($_GET['tab'] == '1') echo null; else echo 'active'; ?>" id="files-list" role="tabpanel">
	                     <div class="table-responsive">
	                        <table class="table">
	                           <thead style="white-space: nowrap; background-color: #1a1b4b; color:white;">
	                              <tr>
	                                 <th>#</th>
	                                 <th>Dosya Adı</th>
	                                 <th>Boyut</th>
	                                 <th>Açıklama</th>
	                                 <th>İndirme Linki</th>
	                                 <th>Yükleme Tarihi</th>
	                                 <th>İşlem</th>
	                              </tr>
	                           </thead>
	                           <tbody>
	                            <?php
	                              $downUrl = $_SERVER['SERVER_NAME'].'/uploads/';
	                              if($_GET['c'] != '' && $_GET['s'] != ''){
	                              	$categoryId = $_GET['c'];
	                              	$subCategoryId = $_GET['s'];

	                              	$data = $db->Procedure("call sp_ADMIN_CATEGORY_FILTER($categoryId,$subCategoryId);");
	                              }
	                              else{
	                              	$data = $db->Procedure("call sp_ADMIN_FILE_LIST();");
	                              }
	                              $i = 1;
	                              foreach ($data as $value) { ?>
	                              <!-- Block -->
	                              <tr>
	                                 <td><?php echo $i; ?></td>
	                                 <td><?php echo $value["FILE_NAME"]; ?></td>
	                                 <td><?php echo $value["FILE_SIZE"]; ?></td>
	                                 <td>
	                                 	<button id="read-button" class="btn btn-primary btn-block" onclick="AlertModal('AlertModal','Dosya Açıklaması','<?php echo $value["FILE_DESCRIPTION"]; ?>')">Oku</button>
	                                 </td>
	                                 <td><?php echo $downUrl.$value["FILE_NO"]; ?></td>
	                                 <td><?php echo $value["REG_DATE"]; ?></td>
	                                 <td>
	                                    <i class="fa fa-trash fa-lg fa-button" aria-hidden="true" data-html="true" title="Hesabı sil" onclick="ActionModal('Dosya Sil','Dosyayı silmek istediğinize emin misiniz?','<?php echo $value["FILE_NO"]; ?>');"></i>
	                                 </td>
	                              </tr>
	                              <!-- Block -->
	                              <?php $i++; } ?>
	                           </tbody>
	                        </table>
	                     </div>
	                     <div style="float: right; cursor: pointer;" class="btn" id="mobile-filter" onclick="$('#FilterModal').modal('show');">
	                      	<i class="fa fa-filter fa-lg fa-button"></i> <span style="text-decoration: underline;">Kategoriye Göre Filtrele</span>
	                  	  </div>
	                  </div>
	                  <div class="tab-pane <?php if($_GET['tab'] == '1') echo 'active'; else echo null; ?>" id="file-upload" role="tabpanel" aria-labelledby="file-upload-tab">
	                     <div class="container">
	                     	<div class="row">
	                     		<div class="col-12 col-sm-6 mt-4">
	                     			<form>
	                     				<div class="form-group">
	                     					<label for="txtFileName">Dosya Adı</label>
	                     					<input type="text" id="txtFileName" class="form-control">
	                     				</div>
	                     				<div class="form-group">
	                     					<label for="txtFileDesc">Dosya Açıklaması</label>
	                     					<textarea id="txtFileDesc" class="form-control" style="height: 160px;" oninput="charCount(this);"></textarea>
	                     					<label id="charCount" style="float: right; opacity: 60%; font-size: 14px;">0/500</label>
	                     				</div>
	                     			</form>
	                     		</div>
	                     		<div class="col-12 col-sm-6 mt-4">
	                     			<form onsubmit="return false">
		                              <div class="form-group">
		                                <label for="categorySelect">Kategori </label>
		                                <label style="float: right; text-decoration: underline; cursor: pointer;" onclick="categoryAddModal();">
		                                  <i class="fa fa-plus  fa-button"></i>
		                                  Ekle
		                                </label>
		                                <select id="categorySelect" class="form-control" onchange="categoryChange();">
		                                  <?php
		                                  	session_start();
		                                  	$username = $_SESSION["USERNAME"];
		                                  	$password = $_SESSION["PASSWORD"];

		                                  	$data = $db->Procedure("call sp_ADMIN_FILE_CATEGORY_LIST('$username','$password');");
		                                  	$firstCategoryId = $data[0]["ID"];
                                        if($firstCategoryId == ''){
                                          $firstCategoryId = 0;
                                        }
		                                  	foreach ($data as $value) { ?>
		                                  		<option value="<?php echo $value['ID']; ?>"><?php echo $value["CATEGORY_NAME"]; ?></option>
		                                  <?php } ?>
		                                </select>
		                              </div>
		                              <div class="form-group">
		                                <label for="subcategorySelect" >Alt Kategori </label>
		                                 <label style="float: right; text-decoration: underline; cursor: pointer;" onclick="categoryAddModal(true)">
		                                  <i class="fa fa-plus  fa-button"></i>
		                                  Ekle
		                                </label>
		                                <select id="subcategorySelect" class="form-control">
		                                  <?php 

                                        $data = $db->Procedure("call sp_ADMIN_FILE_SUBCATEGORY_LIST('$username','$password',$firstCategoryId);");
		                                  	foreach ($data as $value) { ?>
		                                  		<option value="<?php echo $value['ID']; ?>"><?php echo $value["SUBCATEGORY_NAME"]; ?></option>
		                                  	<?php } ?>
		                                </select>
		                              </div>
	                     				<div class="form-group mt-4">
	                     					<input type="file" id="selectFile">
	                     				</div>
	                     				<div class="form-group">
	                     					<button id="upload-button" class="btn d-button mt-1" onclick="fileUpload();" style="white-space: nowrap;">
	                     						<i class="fa fa-check fa-lg" aria-hidden="true"></i>
	                     						&nbsp;&nbsp;<span id="btn-text">Dosya Yükle</span>
	                     					</button>
	                     				</div>
	                     			</form>
	                     		</div>
	                     	</div>
	                     </div>
	                  </div>
	               </div>
	            </div>
      		</div>
      	</div>
      	<script type="text/javascript">
      	  function ActionModal(title, message, file_no){
            document.getElementById('txtActionModalTitle').textContent = title;
            document.getElementById('txtActionModalMessage').textContent = message;
            document.getElementById("actionButton").onclick = function(){ Action(file_no); }
            $('#ActionModal').modal('show');
          }
          
          function DeleteModal(title, message, catElement, subCatElement){
          	document.getElementById('txtDeleteModalTitle').textContent = title;
          	document.getElementById('txtDeleteModalMessage').textContent = message;
          	document.getElementById('DeleteButton').onclick = function() { categoryDelete(catElement, subCatElement); }
          	$('#FilterModal').modal('hide');
          	$('#DeleteModal').modal('show');
          }

          function Action(file_no){
            var url = 'function/file_delete.php';

            ajaxRequest('POST',url,{
              'FILE_NO': file_no
            }, (code)=> {
            	  $('#ActionModal').modal('hide');
	              if (code == '0') {
	                  AlertModal('AlertModal','Bilgi','Dosya silindi.');
	                  window.location.reload();
	              }
	              else if(code == '1'){
	                  AlertModal('AlertModal','Uyarı','Lütfen sisteme tekrar giriş yapınız');
	              }
	              else if(code == '2'){
	              	  AlertModal('AlertModal','Uyarı','Dosya bulunamadı.');
	              }
	              else {
	              	  AlertModal('AlertModal','Hata','Hata Kodu: 0x800!');
	              }
            });
          }

          function charCount(event){
          	if(event.value.length > 500){
          		event.value = event.value.slice(0, 500);
          	}
          	setInput('charCount', event.value.length + '/500'); 
          }

          function filterDisplay(element,status){
            if(status){
              document.getElementById(element).style.display = '';
            }
            else{
              document.getElementById(element).style.display = 'none';
            }
          }

          function getCategoryId(elm = 'categorySelect'){
          	 var element = document.getElementById(elm); 
          	 try {
          	 	return element.options[element.selectedIndex].value;
          	 }catch(err){
          	 	return 0;
          	 }
          }

          function categoryAdd(sub = false){  
          	if(sub){
          		var subcategoryName = getInput('txtSubcategoryAddName');
          		var catId = getCategoryId();

          		if(subcategoryName != ''){
          			ajaxRequest('POST', 'function/add_subcategory.php', {
          				'CATEGORY_ID': catId,
          				'SUBCATEGORY_NAME': subcategoryName
          			}, (code) => {
          				if(code == '0'){
          					document.location.href="dashboard.php?page=file-manager&tab=1";
          				}
          				else if(code == '2'){
          					AlertModal('AlertModal','Uyarı','Bu kategori zaten mevcut!');
          				}
          			});
          		}
          		else{
          			setInput('modal-message2','* Lütfen boş bırakmayınız.');
          		}
          	}
          	else{
          		var categoryName = getInput('txtCategoryAddName');
          		
          		if(categoryName != ''){
          			ajaxRequest('POST', 'function/add_category.php',{
	          			'CATEGORY_NAME': categoryName
	          		}, (code) => {
	          			if(code == '0'){
	          				document.location.href="dashboard.php?page=file-manager&tab=1";
	          			}
	          			else if(code == '2'){
	          				AlertModal('AlertModal','Uyarı','Bu kategori zaten mevcut!');
	          			}
	          		})
          		}
          		else{
          			setInput('modal-message','* Lütfen boş bırakmayınız.');
          		}
          	}
          }

          function fileUpload(){
          	var file = document.getElementById("selectFile").files;
          	var fileName = getInput('txtFileName');
          	var fileDesc = getInput('txtFileDesc');
          	
          	var catId = getCategoryId();
          	var subCatId = getCategoryId('subcategorySelect');

          	if(fileName != '' /*&& fileDesc != ''*/){
          		if(catId != 0){
          			if(subCatId == ''){
          				subCatId = 0;
          			}
          			var formData = new FormData();
          		
	          		if(file.length == 1) {
	          			formData.append('FILE',file[0]);
	          			formData.append('FILE_NAME',fileName);
	          			formData.append('FILE_DESC', fileDesc);
	          			formData.append('CATEGORY_ID', catId);
	          			formData.append('SUBCATEGORY_ID', subCatId);

	          			setInput('upload-button','Lütfen Bekleyin...','on');

	          			ajaxRequest('POST', 'function/file_upload.php', formData, (code) => {
		          			  if (code == '0') {
		          			  	  setInput('upload-button','Dosya Yükle','off');
				                  AlertModal('AlertModal','Bilgi','Dosya yüklendi.');
				                  document.location.href="dashboard.php?page=file-manager";
				              }
				              else if(code == '1'){
				                  AlertModal('AlertModal','Uyarı','Lütfen sisteme tekrar giriş yapınız');
				              }
				              else {
				              	  AlertModal('AlertModal','Hata','Hata Kodu: 0x900!');
				              }
	          			}, true);
	          		}
	          		else{
	          			AlertModal('AlertModal','Uyarı','Lütfen bir dosya seçiniz.');
	          		}
          		}
          		else{
          			AlertModal('AlertModal','Uyarı','Lütfen kategori seçiniz.');
          		}
          	}
          	else{
          		AlertModal('AlertModal','Uyarı','Lütfen boş bırakmayınız.');
          	}
          }

          function categoryAddModal(subcategory = false) {
            if(subcategory){
              var element = document.getElementById('categorySelect'); 
              $('#subcategoryAddModal').modal('show');
              setInput('txtSubcategoryAddTitle', element.options[element.selectedIndex].text + ' için alt kategori ekle');
            }
            else{
              $('#categoryAddModal').modal('show');
            }
          }

          function removeOptions(e){
          	var element = document.getElementById(e);
          	var i, L = element.options.length - 1;
          	for(i = L; i >= 0; i--){
          		element.remove(i);
          	}
          }

          function categoryChange(element = 'categorySelect', subelemet = 'subcategorySelect') {
          	 var catId = getCategoryId(element);

          	 ajaxRequest('POST','function/get_category.php',{
          	 	'CATEGORY_ID': catId
          	 },(res_json) => {
          	 	removeOptions(subelemet);

          	 	for(let i = 0; i < res_json.length; i++){
  					$(new Option(res_json[i].SUBCATEGORY_NAME, res_json[i].ID)).appendTo('#' + subelemet);
          	 	}

          	 },false,true);
          }

          function categoryDelete(element, subelemet = ''){
          	if(subelemet != ''){
          		var catId = getCategoryId(element);
          		var subCatId = getCategoryId(subelemet);

          		ajaxRequest('POST','function/delete_category.php', {
          			'CATEGORY_ID': catId,
          			'SUBCATEGORY_ID': subCatId
          		}, (code) => {
          			if(code == '0'){
          				window.location.reload();
          			}
          			else{
          				AlertModal('AlertModal','Hata','Hata Kodu: 0x901!');
          			}
          		});
          	}
          	else{
          		var catId = getCategoryId(element);

          		ajaxRequest('POST','function/delete_category.php', {
          			'CATEGORY_ID': catId,
          			'SUBCATEGORY_ID': 0
          		}, (code) => {
          			if(code == '0'){
          				window.location.reload();
          			}
          			else{
          				AlertModal('AlertModal','Hata','Hata Kodu: 0x902!');
          			}
          		});	
          	}
          }

          function catFilter(){
          	var categoryId = getCategoryId('modalCategorySelect');
          	var subCatId = getCategoryId('modalSubCategorySelect');

          	document.location.href="dashboard.php?page=file-manager&c=" + categoryId + "&s=" + subCatId;
          }
      	</script>
      <?php } ?>

      <script type="text/javascript">
         $().tooltip(options);

         function AlertModal(modalName, title, message){
         	$('#ActionModal').modal('hide');
         	$('#subcategoryAddModal').modal('hide');
         	$('#categoryAddModal').modal('hide');
         	$('#DeleteModal').modal('hide');

            document.getElementById('txtModalTitle').textContent = title;
            document.getElementById('txtModalMessage').textContent = message;
            $('#' + modalName).modal('show');
         }

         function getInput(input){
            return document.getElementById(input).value;
         }

         function clearInput(input){
            document.getElementById(input).value = '';
         }

         function setInput(input, text, disabled = 'null'){
         	document.getElementById(input).innerHTML = text;

         	if(disabled == 'on'){
         		document.getElementById(input).disabled = true;
         	}
         	else if(disabled == 'off'){
         		document.getElementById(input).disabled = false;
         	}
         }

         function ajaxRequest(RequestType, Url, Data, callback, formData = false, res_json = false){
            if(!formData){
            	$.ajax({
	                   type: RequestType,
	                   xhrFields: {
	                   withCredentials: true
	               },
	                  crossDomain: true,
	                  url: Url,
	                  data: Data,
	                  dataType: 'json',        
	               success: function(returnData) {
	                 if(res_json){
	                 	 callback(returnData);
	                 }
	                 else{
	                 	 callback(returnData['ERROR_CODE']);
	                 }  
	               },
	               error: function(error) {
	                  console.log(error);
	                  AlertModal('AlertModal','Hata','Sunucuya ulaşılamıyor!');
	               }
	            });
            }
            else{
            	$.ajax({
	                   type: RequestType,
	                   xhrFields: {
	                   withCredentials: true
	               },
	                  crossDomain: true,
	                  url: Url,
	                  data: Data,
	                  processData: false,
    				  contentType: false,        
	               success: function(returnData) {
	                  callback(returnData['ERROR_CODE']);    
	               },
	               error: function(error) {
	               	  setInput('upload-button','Dosya Yükle','off');
	                  console.log(error);
	                  AlertModal('AlertModal','Hata','Sunucuya ulaşılamıyor!');
	               }
	            });
            }
         }

      </script>

      <script src="js/jquery-3.5.1.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.min.js"></script>
      
   </body>
</html>