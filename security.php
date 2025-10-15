<?php
if(session_id() == '') {
	session_name("2620368ghwahw90w");
	session_start();
}
$sitename = "MTD Apps Portal";
$_SESSION['token_duration'] = 15;
require "config/function.php";
$filename = basename($_SERVER["SCRIPT_NAME"]);
if(isset($_SESSION['login']) && (!$_SESSION['login'] && ($filename != "index.php" && $filename != "login-auth.php"))) {
	header("location: logout.php");
}
?>
