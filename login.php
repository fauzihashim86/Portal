<?php
include "security.php";
if(isset($_POST['user']) && isset($_POST['pass'])) {
  $guest = array("fauzihashim86@gmail.com","aiman@mtdgb.com");
  $username = $_POST['user'];
  $password = $_POST['pass'];
  $ldapconn = ldap_connect("ldap://172.17.0.31");
  if($ldapconn) {
    $ldaprdn = "alloymtd\\".$username;
    $ldapbind = @ldap_bind($ldapconn,$ldaprdn,$password);
    if($ldapbind) {
      $result = executeQuery("SELECT id,employee_name,level,employee_category FROM eleave_v4.user
                              INNER JOIN eleave_v4.employee ON user_id=id
                              WHERE username LIKE '%$username' AND status='Active'");
      if($result->rowCount() > 0) {
        $row = $result->fetch();
        $ip = $_SERVER['REMOTE_ADDR'];
        executeQuery("INSERT INTO trail(t_user,t_from,t_query,t_ip,t_date)VALUES('$username','e-central','login','$ip',NOW())",null,1);
        $_SESSION["login"]["username"] = $username;
        $_SESSION["login"]["password"] = $password;
        $_SESSION["login"]["id"] = $row['id'];
        $_SESSION["login"]["name"] = $row['employee_name'];
        $_SESSION["login"]["level"] = $row['level'];
        $_SESSION["login"]["category"] = $row['employee_category'];
        $id = array();
        $result = executeQuery("SELECT t_id FROM trail WHERE t_query='login' AND t_user LIKE '%$username' ORDER BY t_date DESC LIMIT 3");
        while($row = $result->fetch()){
          array_push($id,$row['t_id']);
        }
        $user = implode(",",$id);
        executeQuery("DELETE FROM trail WHERE t_query='login' AND t_user LIKE '%$username' AND t_id NOT IN($user)");
        header("Location: ./?id=1");
      } else {
        $_SESSION["error"] = "admin";
        header("Location: ./");
      }
    } else if(in_array($username,$guest)) {
      if($password == "MTDgu3st"){
        $_SESSION["login"]["id"] = 0;
        $_SESSION["login"]["username"] = $username;
        $_SESSION["login"]["name"] = $username;
        $_SESSION["login"]["level"] = "GUEST";
        header("Location: ./?id=1");
      } else {
        $_SESSION["error"] = "true";
        header("Location: ./");
      }
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
        header("Location: ./?id=1");
      } else {
        $_SESSION["error"] = "true";
        header("Location: ./");
      }
    }
  } else {
    $_SESSION["error"] = "true";
    header("Location: ./");
  }
} else {
  $_SESSION["error"] = "true";
  header("Location: ./");
}
?>
