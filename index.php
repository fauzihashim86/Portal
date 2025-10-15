<?php
include "security.php";
sendMail();
if(isset($_SESSION['login']) && $filename == "index.php"){
	if(isset($_GET['id'])) {
		$id = $_GET['id'];
		if($id == 5) {
			header("location: logout.php");
		} else {
			$result = executeQuery("SELECT menu_url FROM menu WHERE menu_id='$id' AND menu_active=1");
			require "head.php";
			if($result->rowCount() == 0) {
				require "views/error.php";
			} else {
				$row = $result->fetch();
				$filename = $row['menu_url'];
				require "masterfile.php";
			}
			require "foot.php";
		}
	} else if(isset($_GET['sub'])) {
		$id = $_GET['sub'];
		$result = executeQuery("SELECT sm_url FROM submenu WHERE sm_id='$id'");
		$row = $result->fetch();
		$filename = $row['sm_url'];
		require "head.php";
		if(!$filename) {
			require "views/error.php";
		} else {
			require "masterfile.php";
		}
		require "foot.php";
	} else {
		header("location: ./?id=1");
	}
} else if(isset($_SESSION["visitor"]) && $filename == "index.php"){
	require "visitor.php";
} else {
?>
<!DOCTYPE html>
<html lang="en" class="full">
  <head>
    <title><?php echo $sitename;?></title>
    <link rel="icon" href="src/images/mtd.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="../e-training/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../e-training/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="../e-training/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="../e-training/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
    <link href="../e-training/plugins/vegas/vegas.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="src/plugins/sweetalert2/sweetalert2.css">
    <style type="text/css">
      html,
      body {
        overflow-x: hidden!important;
        font-family: 'Source Sans Pro', sans-serif;
        -webkit-font-smoothing: antialiased;
        min-height: 100%;
        background: #f9f9f9;
      }

      .full {
        color: #f9f9f91 !important;
      }

      .full {
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;

      }
      .full > body {
        background: transparent;
      }

      .full-gradient {
        background: #222222 !important;
        background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #222222), color-stop(1, #3c3c3c)) !important;
        background: -ms-linear-gradient(bottom, #222222, #3c3c3c) !important;
        background: -moz-linear-gradient(center bottom, #222222 0%, #3c3c3c 100%) !important;
        background: -o-linear-gradient(#3c3c3c, #222222) !important;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#3c3c3c', endColorstr='#222222', GradientType=0) !important;
        color: #fff;
      }

      @media (max-width: 767px) {
        .login-box {
          width: 90%;
        }
      }

      /* Background Overlay */
      .body-bg {
      	background: rgba(192,192,192,0.3) url(dist/img/overlay.png) repeat;
      	height: 100%;
      	left: 0;
      	position: fixed;
      	top: 0;
      	width: 100%;
      	z-index: -1;
      }
    </style>
  </head>
  <body id="example" class="login-page">
    <div class="login-box">
		  <div class="margin text-center"><img src="src/images/mtd.png" width="100px" height="100px"></div>
      <div class="login-box-body" style="opacity: 0.8;filter: alpha(opacity=30);">
	      <div class="login-logo"><b>Apps Portal</b></div>
        <p class="login-box-msg">Sign in to start your session</p>
          <form action="login.php" method="POST" onsubmit="spinSubmit()">
            <div class="form-group has-feedback">
              <input type="text" id="user" name="user" class="form-control" placeholder="User ID" required/>
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
              <input type="password" id="pass" name="pass" class="form-control" placeholder="Password" required/>
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
              <div class="col-xs-4">
                <button type="submit" id="submit" name="submit" value="Login!" class="btn btn-primary btn-block btn-flat">Sign In</button>
              </div>
            </div>
          </form>
        </div>
	    <div class="margin text-center">
        <span style="color: #fff;font-family: 'Roboto Slab', serif;font-weight: 400;line-height: 1.16667;margin: 0 0 28px;text-align: center;">Copyright © <?php echo date("Y");?> Group IT, MTD GROUP</span>
      </div>
    </div>
   <script src="../e-training/plugins/jQuery/jQuery-2.1.3.min.js"></script>
   <script src="../e-training/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
   <script src="../e-training/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
   <script src="../e-training/plugins/vegas/vegas.min.js" type="text/javascript"></script>
   <script src="src/plugins/sweetalert2/sweetalert2.all.js"></script>
   <script src="central.js"></script>
   <?php
   if(isset($_SESSION["error"])) {
		 if($_SESSION["error"] == "true"){
			 echo "<script>fail('Invalid User ID or Password');</script>";
		 } else {
			 echo "<script>fail('Access denied, Please contact system administrator');</script>";
		 }
     unset($_SESSION["error"]);
   }
   ?>
   <script>
    	function spinSubmit(){
        $("#submit").html("<i class='fa fa-refresh fa-spin'></i> Sign In");
        $('button').prop('disabled', true);
      }

      $("#example, body").vegas({
      delay: 7000,
          timer: false,
          shuffle: true,
          firstTransition: 'fade',
          firstTransitionDuration: 5000,
          transition: [ 'fade', 'zoomOut', 'blur', 'fade2', 'zoomOut2' ],
      	animation: [ 'kenburnsUp', 'kenburnsDown', 'kenburnsLeft', 'kenburnsRight' ],
          transitionDuration: 5000,
          slides: [
              { src: "../e-training/dist/img/slides/1.jpg" },
              { src: "../e-training/dist/img/slides/2.jpg" },
              { src: "../e-training/dist/img/slides/3.jpg" },
              { src: "../e-training/dist/img/slides/4.jpg" },
              { src: "../e-training/dist/img/slides/5.jpg" },
              { src: "../e-training/dist/img/slides/6.jpg" },
              { src: "../e-training/dist/img/slides/7.jpg" },
              { src: "../e-training/dist/img/slides/8.jpg" },
              { src: "../e-training/dist/img/slides/9.jpg" },
              { src: "../e-training/dist/img/slides/10.jpg" },
              { src: "../e-training/dist/img/slides/11.jpg" }
          ]
      });
    </script>
  </body>
</html>
<?php }?>
