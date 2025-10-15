<?php
include "security.php";
if(isset($_POST['func'])) {
  $function = $_POST['func'];
  if($function == "auth") {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    $ldapconn = ldap_connect("ldap://172.17.0.31");
    if(!$username || !$_POST['pass']) {
      echo "false";
    } else if($ldapconn) {
      $ldaprdn = "alloymtd\\".$username;
      $ldapbind = @ldap_bind($ldapconn,$ldaprdn,$password);
      if($ldapbind) {
        $result = executeQuery("SELECT id,employee_name,level,employee_category FROM user INNER JOIN employee ON user_id=id WHERE username LIKE '%$username' AND status='Active'",'eleave_v4');
        $row = $result->fetch();
        $ip = $_SERVER['REMOTE_ADDR'];
        executeQuery("INSERT INTO trail(t_user,t_from,t_query,t_ip,t_date)VALUES('$username','e-central','login','$ip',NOW())",null,1);
        $_SESSION["login"]["username"] = $username;
        $_SESSION["login"]["password"] = $password;
        $_SESSION["login"]["id"] = $row['id'];
        $_SESSION["login"]["name"] = $row['employee_name'];
        $_SESSION["login"]["level"] = $row['level'];
        $_SESSION["login"]["category"] = $row['employee_category'];
        echo "true";
      } else {
        $enc = md5($password);
        $result = executeQuery("SELECT id,employee_name,level,employee_category FROM user INNER JOIN employee ON user_id=id WHERE username='$username' AND password='$enc' AND status='Active'",'eleave_v4');
        if($result->rowCount() > 0) {
          $row = $result->fetch();
          $_SESSION["login"]["username"] = $username;
          $_SESSION["login"]["password"] = $password;
          $_SESSION["login"]["id"] = $row['id'];
          $_SESSION["login"]["name"] = $row['employee_name'];
          $_SESSION["login"]["level"] = $row['level'];
          $_SESSION["login"]["category"] = $row['employee_category'];
          echo "true";
        } else {
          echo "wrong";
        }
      }
    } else {
      echo "db";
    }
  } else if($function == "lock") {
    $_SESSION['login']['lock'] = "lock";
  } else if($function == "unlock") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ldapconn = ldap_connect("ldap://172.17.0.31");
    if(!$password) {
      echo "false";
    } else if($ldapconn) {
      $ldaprdn = "alloymtd\\".$username;
      $ldapbind = @ldap_bind($ldapconn,$ldaprdn,$pass);
      if($ldapbind) {
        unset($_SESSION['login']['lock']);
        echo "true";
      } else {
        echo "false";
      }
    } else {
      echo "false";
    }
  }
}
?>
