<?php
function getNoLDAP($username){
include("dbconnect.php");

ldap_set_option( $ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3 );
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

$ldap_success = false;
$ldap_bd = ldap_bind($ldapconn, $root_dn, $root_pw) or die("Could not bind to server. Error is " .ldap_error($ldapconn));
$dn = "DC=alloymtd,DC=internal";
$result=ldap_search($ldapconn, $dn,"(|(samaccountname=$username))")or die ("Error in query");
$data = ldap_get_entries($ldapconn, $result);
$ldap_success = ($data && $data['count'] === 1);

return $ldap_success;

}//getNoLDAP

function getNonUser(){
include("dbconnect.php");

//$ret1 = mysql_query("select * from user where username like 'noncomputer.%'",$conn2);
$ret1 = $db2->query( "select * from user where username like 'noncomputer.%'", "listing" );

//while($row=mysql_fetch_array($ret1)){
while( $row = $db2->fetch_array($ret1) ){
$username[]=$row["username"];

}//while
return $username;
//mysql_free_result($ret1);
$db2->free_result( $ret1 );
}//getNonUser

function getExtUser($email_domain){
include("dbconnect.php");

$ret1 = $db2->query( "select * from user u inner join employee e on u.id=e.user_id WHERE e.employee_email NOT REGEXP '$email_domain'", "checking user" );

while($row = $db2->fetch_array($ret1)){
$username[]=$row["username"];

}//while
return $username;

$db->free_result( $ret1 );
}//getExtUser

function getUserName($field,$username){
include("dbconnect.php");

//$ret = mysql_query("select * from approval_line where $field='$username' group by $field",$conn1);
$ret = $db->query( "select * from staff where $field='$username' group by $field", "listing" );

//$num=mysql_num_rows($ret);
//if($num != "0") {
//while($row=mysql_fetch_array($ret)){
while( $row = $db->fetch_array($ret) ){
$userid[]=$row[$field];

}//while
//}
return $userid;

$db->free_result( $ret );
//mysql_free_result($ret);
}//getUserName

function getHODName($username){
include("dbconnect.php");

$ret = $db2->query( "select e.employee_name as fullname from $dbname2.user u left join $dbname2.employee e on u.id=e.user_id left join $dbname1.staff s on s.hod_id=u.username where s.staff_id='$username'", "listing" );

$row = $db2->fetch_array($ret);
$fullname=$row["fullname"];

return $fullname;

$db2->free_result( $ret );

}//getHODName

function getempinfo($u){
include("dbconnect.php");

//$ret = mysql_query("select * from user inner join employee on user.id=employee.user_id inner join company on employee.employee_company=company.id where user.username='$u'",$conn2);
$ret = $db2->query( "select * from user left join employee on user.id=employee.user_id left join company on employee.employee_company=company.id where user.username='$u'", "listing" );

//$row=mysql_fetch_array($ret);
$row = $db2->fetch_array($ret);

  $employee_name=$row["employee_name"];
  $employee_company=$row["description"];
  $employee_ic_no=$row["employee_ic_no"];
  $employee_position=$row["employee_position"];
  $employee_grpcompany=$row["employee_grpcompany"];
  $supervisor=$row["supervisor_id"];
  $immediate_supervisor=$row["immediate_supid"];
  $employee_datejoined=$row["employee_datejoined"];

if($employee_datejoined!="0000-00-00"){
$member="<small>Member since ".date("F Y", strtotime($employee_datejoined))."</small>";
}

if($u=="supadmin"){
return "Web Developer<br><small>Member since August 2008</small>";
}else{
return $employee_name."<br>".$employee_ic_no."<br>".$employee_position."<br>".$member;
}
//mysql_free_result($ret);
$db2->free_result( $ret );
}//getempname function

function getGrade($empID){
include("dbconnect.php");

//$ret1 = mysql_query("select s.job_grade as grade, e.leave_code as code from status s inner join entitlement e on s.entitle_id=e.id where s.employee_id='$empID'",$conn2);
$ret1 = $db2->query( "select s.job_grade as grade, e.leave_code as code from status s inner join entitlement e on s.entitle_id=e.id where s.employee_id='$empID'", "listing" );
$row = $db2->fetch_array($ret1);
//$row=mysql_fetch_array($ret1);

$job_grade=$row["grade"];
$leave_code=$row["code"];

//return $leave_code." (".$job_grade.")";
return $job_grade;

$db2->free_result( $ret1 );
//mysql_free_result($ret1);
}//getGrade

function getGrpComp($grpcomp){
include("dbconnect.php");

$ret1 = $db2->query( "select * from group_comp where id='$grpcomp'", "listing" );
//$ret1 = mysql_query("select * from group_comp where id='$grpcomp'",$conn2);
//$row=mysql_fetch_array($ret1);
$row = $db2->fetch_array($ret1);

$grp_desc=$row["grp_desc"];

return $grp_desc;

$db2->free_result( $ret1 );
//mysql_free_result($ret1);
}//getGrpComp

function getDept($dept){
include("dbconnect.php");

$ret1 = $db2->query( "select * from company where id='$dept'", "listing" );

$row = $db2->fetch_array($ret1);

$name=$row["name"];

return $name;

$db2->free_result( $ret1 );
}//getDept

function getUserDept($username){
include("dbconnect.php");

//$ret1 = $db2->query( "select * from company where id='$dept'", "listing" );
$ret1 = $db2->query( "select c.id as deptid,c.name as name from $dbname2.company c left join $dbname2.employee e on c.id=e.employee_company left join $dbname2.user u on u.id=e.user_id left join $dbname1.staff s on s.staff_id=u.username where s.staff_id='$username'", "listing" );

$row = $db2->fetch_array($ret1);

$deptid=$row["deptid"];

return $deptid;

$db2->free_result( $ret1 );
}//getDept

function getHODDept($progid){
include("dbconnect.php");

//$ret1 = $db2->query( "select c.name as deptname from user u inner join employee e on u.id=e.user_id inner join company c on e.employee_company=c.id where u.username='$id'", "listing" );
  $ret1 = $db2->query( "select c.name as deptname from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id
  left join $dbname2.company c on e.employee_company=c.id
  left join $dbname1.staff s on s.staff_id=u.username
  left join $dbname1.nomination n on n.staff_id=s.id
  left join $dbname1.program p on p.id=n.prog_id where n.prog_id='$progid' group by c.name", "listing" );

$row = $db2->fetch_array($ret1);

$deptname=$row["deptname"];

return $deptname;

$db2->free_result( $ret1 );
}//getHODDept

function getImmNSup($id,$field){
include("dbconnect.php");

//$ret1 = mysql_query("select * from approval_line where employee_id='$id'",$conn1);
$ret1 = $db->query( "select * from staff where staff_id='$id'", "listing" );
//$row=mysql_fetch_array($ret1);
$row = $db->fetch_array($ret1);

$sup_id=$row[$field];

//$ret2 = mysql_query("select * from user u inner join supervisor s on u.id=s.user_id where u.username='$sup_id'",$conn2);
$ret2 = $db2->query( "select * from user u inner join supervisor s on u.id=s.user_id where u.username='$sup_id'", "listing" );
//$row=mysql_fetch_array($ret2);
$row = $db2->fetch_array($ret2);

$supervisor_name=$row["supervisor_name"];

return $supervisor_name;

//mysql_free_result($ret1);
}//getImmNSup

function getEmpName($id,$which){
include("dbconnect.php");

//$ret1 = mysql_query("select * from user u inner join employee e on u.id=e.user_id where u.username='$id'",$conn2);
if($which=="approval"){
$ret1 = $db2->query( "select * from user u inner join employee e on u.id=e.user_id where u.username='$id'", "listing" );
}else{
$ret1 = $db2->query( "select e.employee_name from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id left join $dbname1.staff s on s.staff_id=u.username left join $dbname1.license l on l.staff_id=s.id where s.id='$id'", "listing" );
}
//$row=mysql_fetch_array($ret1);
$row = $db2->fetch_array($ret1);

$employee_name=$row["employee_name"];

return $employee_name;

$db2->free_result( $ret1 );
//mysql_free_result($ret1);
}//getEmpName

function select_progType($program_type){
include("dbconnect.php");
$ret = $db->query( "select * from type_program order by name", "filling listing" );

  $num=mysql_num_rows($ret);
  echo "<select name='program_type' id='program_type' class=\"form-control\">";
  if($num != "0") {
  echo "<option value=''>--select--</option>";
  while( $row = $db->fetch_array($ret) ){
    $id=$row["id"];
    $name=$row["name"];
  echo "<option value='$id'";
  if($program_type==$id){echo "selected";}
  echo ">$name</option>";
  }//while
  }else{
  echo "<option value=''>--empty--</option>";
  }//if num
  echo "</select>";
}//select_progType

function select_progTypeExt(){
include("dbconnect.php");
$ret = $db->query( "select * from type_program where id!='8' or name!='Induction' order by name", "filling listing" );

  $num=mysql_num_rows($ret);
  echo "<select name='program_type' id='program_type' class=\"form-control\">";
  if($num != "0") {
  echo "<option value=''>--select--</option>";
  while( $row = $db->fetch_array($ret) ){
    $id=$row["id"];
    $name=$row["name"];
  echo "<option value='$id'";
  if($program_type==$id){echo "selected";}
  echo ">$name</option>";
  }//while
  }else{
  echo "<option value=''>--empty--</option>";
  }//if num
  echo "</select>";
}//select_progTypeExt

function select_staff(){
include("dbconnect.php");
$ret = $db2->query( "select e.employee_name as empname,s.staff_id as staffid from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id left join $dbname1.staff s on s.staff_id=u.username order by e.employee_name", "listing" );

  $num=mysql_num_rows($ret);
  echo "<select name='staff_name' id='staff_name' class=\"form-control\">";
  if($num != "0") {
  echo "<option value=''>--select--</option>";
  while( $row = $db->fetch_array($ret) ){
    $staffid=$row["staffid"];
    $empname=$row["empname"];
  echo "<option value='$staffid'>$empname</option>";
  }//while
  }else{
  echo "<option value=''>--empty--</option>";
  }//if num
  echo "</select>";
}//select_staff

function select_staff2($username,$staff_name){
include("dbconnect.php");

if(in_array($username, $hcd_admin)){
$ret = $db2->query( "select e.employee_name as empname,s.staff_id as staffid from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id left join $dbname1.staff s on s.staff_id=u.username where e.status='Active' order by e.employee_name", "listing" );
}else{
$ret = $db2->query( "select e.employee_name as empname,s.staff_id as staffid from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id left join $dbname1.staff s on s.staff_id=u.username where e.status='Active' and (s.staff_id='$username' or s.hod_id='$username') order by e.employee_name", "listing" );
}

  $num=mysql_num_rows($ret);
  echo "<select name='staff_name' id='staff_name' class=\"form-control\">";
  if($num != "0") {
  //if($staff_name!=""){
  //$sel= "selected";
  //}
  echo "<option value='ALL' >--ALL--</option>";
  while( $row = $db->fetch_array($ret) ){
    $staffid=$row["staffid"];
    $empname=$row["empname"];
  echo "<option value='$staffid' ";
  if($staff_name!="" && $staff_name==$staffid){
  echo "selected";
  }
  echo ">$empname</option>";
  }//while
  }else{
  echo "<option value=''>--empty--</option>";
  }//if num
  echo "</select>";
}//select_staff2


function getStaffID($id){
include("dbconnect.php");

$ret = $db->query( "select id from staff where staff_id='$id'", "listing" );

if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}

$row = $db->fetch_array($ret);
$staffid=$row["id"];

return $staffid;
$db->free_result( $ret );
}//getStaffID function

function getStaffName($id){
include("dbconnect.php");

$ret = $db->query( "select staff_id from staff where id='$id'", "listing" );

if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}

$row = $db->fetch_array($ret);
$staffname=$row["staff_id"];

return $staffname;
$db->free_result( $ret );
}//getStaffID function

function getProgType($type){
include("dbconnect.php");

$ret = $db->query( "select name from type_program where id='$type'", "listing" );

if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}

$row = $db->fetch_array($ret);
$type_program=$row["name"];

return $type_program;
$db->free_result( $ret );
}//getProgType function

function getDashInfo($cat){
include("dbconnect.php");
global $username;
$thisyear=date("Y");
$staffid=getStaffID($username);

if($cat=="Training History"){
/*
$ret = $db->query( "select count(*) as total from staff s inner join program p on (s.id=p.staff_id or s.id=p.imdsup_id) left join nomination n on n.prog_id=p.id where (p.staff_id='$staffid' or n.staff_id='$staffid') and p.date_complete is not null and year(p.date_created)='$thisyear' ", "listing" );
*/
$ret = $db2->query( "select count(*) as total
from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username left join $dbname2.employee e on u.id=e.user_id where s.staff_id='$username' and (p.status='Approved' or p.status='Completed') and year(p.start_date)='$thisyear'", "listing" );

}elseif($cat=="External Training"){
/*
$ret = $db->query( "select count(*) as total from staff s inner join program p on s.id=p.staff_id where s.staff_id='$username' and p.date_complete is not null and year(p.date_created)='$thisyear' and p.training_cat='external'", "listing" );
*/
$ret = $db2->query( "select count(*) as total
from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username left join $dbname2.employee e on u.id=e.user_id where s.staff_id='$username' and (p.status='Approved' or p.status='Completed') and year(p.start_date)='$thisyear' and p.training_cat='external'", "listing" );

}elseif($cat=="In-House Training"){
/*
$ret = $db->query( "select count(*) as total from staff s inner join program p on (s.id=p.staff_id or s.id=p.imdsup_id) left join nomination n on n.prog_id=p.id where n.staff_id='$staffid' and p.date_complete is not null and year(p.date_created)='$thisyear' and p.training_cat='in-house'", "listing" );
*/
$ret = $db2->query( "select count(*) as total
from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username left join $dbname2.employee e on u.id=e.user_id where s.staff_id='$username' and (p.status='Approved' or p.status='Completed') and year(p.start_date)='$thisyear' and p.training_cat='in-house'", "listing" );

}else{
$ret = $db->query( "select count(*) as total from staff s inner join license l on s.id=l.staff_id where s.staff_id='$username' and year(l.start)='$thisyear'", "listing" );
}

if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}

//$num=mysql_num_rows($ret);
$row = $db->fetch_array($ret);
$total=$row["total"];

return $total;
$db->free_result( $ret );
}//getDashInfo function

function getTrainCalDetail($mth){
include("dbconnect.php");
$thisyear=date("Y");
global $username;

$ret = $db->query( "select * from training_calendar where month(start)='$mth' and year(start)='$thisyear'", "listing" );

if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}

while( $row = $db->fetch_array($ret) ){
$title=$row["title"];
$start=$row["start"];
$end=$row["end"];
$status=$row["status"];
$remark=$row["remark"];

$title2=addslashes($title);
  $start_date = str_replace('-', '/', $start);
  $StartDate = date('d/m/Y', strtotime($start_date));
  $end_date   = str_replace('-', '/', $end);
  $EndDate = date('d/m/Y', strtotime($end_date));
//encodeURIComponent(.replace("'","\\'"))
echo $StartDate." - ".$EndDate."<br><i>".$title."</i><br>";

//echo getDuplicateTrain($title2,$start,$end);
if(in_array($username, getUserName('imdsup_id',$username))){
if(getDuplicateTrain($title2,$start,$end) == true){
echo "<a class=\"btn btn-success btn-flat btn-xs\" href=\"#\" disabled>Applied</a><br><br>";
}else{
if($status=="Open"){
echo "<a class=\"btn btn-warning btn-flat btn-xs\" href=\"#\" onclick=\"passdata('$title2','$StartDate','$EndDate');\">Apply</a><br><br>";
}else{
echo "<a class=\"btn btn-danger btn-flat btn-xs\" href=\"#\" disabled>$status</a><br><small>$remark</small><br><br>";
}
}//if duplicate
}else{
echo "<br><br>";
}//if supervisor

}//while
$db->free_result( $ret );
}//getTrainCalDetail function

function getTrainSlideCalDetail($mth,$year,$mode){
session_start();
include("dbconnect.php");
//$thisyear=date("Y");
//global $username;
$username = $_SESSION["loginuser"];
$tmpDept=getUserDept($username);
$ret = $db->query( "select * from training_calendar where month(start)='$mth' and year(start)='$year' and (dept='ALL' or dept='$tmpDept') group by title,start,end,start_time,end_time order by start", "listing" );

if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}

while( $row = $db->fetch_array($ret) ){
$train_cal_id=$row["id"];
$title=$row["title"];
$start=$row["start"];
$end=$row["end"];
$start_time=$row["start_time"];
$end_time=$row["end_time"];
$budgeted=$row["budgeted"];
$duration=$row["duration"];
$venue=$row["venue"];
$training_provider=$row["training_provider"];
$type=$row["type"];
$objective=$row["objective"];

$status=$row["status"];
$remark=$row["remark"];

  $title2=htmlentities(addslashes($title));
  //$objective2=htmlentities(addslashes($objective));
  $objective2 = addslashes(str_replace(chr(194)," ",$objective));
  $training_provider2=htmlentities(addslashes($training_provider));
  $start_date = str_replace('-', '/', $start);
  $StartDate = date('d/m/Y', strtotime($start_date));
  $end_date   = str_replace('-', '/', $end);
  $EndDate = date('d/m/Y', strtotime($end_date));
  $thisdate = date('Y-m-d');
  $start_time = date('h:i A', strtotime($start_time));
  $end_time = date('h:i A', strtotime($end_time));

  //$stop_date = date('Y-m-d', strtotime($thisdate . ' +3 day'));
  $stop_date = date('Y-m-d', strtotime($thisdate.' + 3 weekdays'));
  //$startmonth = date("m",strtotime($start_date));
  //$startyear = date("Y",strtotime($start_date));
//encodeURIComponent(.replace("'","\\'"))
echo $StartDate." - ".$EndDate."<br><i>".$title."</i><br>";

//echo getDuplicateTrain($title2,$start,$end);
if(in_array($username, getUserName('imdsup_id',$username)) || (in_array($username, $hcd_admin) && $mode==2)){
if(getDuplicateTrain($title2,$start,$end) == true){
echo "<a class=\"btn btn-success btn-flat btn-xs\" href=\"#\" disabled>Applied</a><br><br>";
}else{
if($status=="Open"){
 //if($thisdate>=$start){
 if(($stop_date>=$start) || ($thisdate>=$start)){
// echo "<a class=\"btn btn-danger btn-flat btn-xs\" href=\"#\" disabled>Closed</a><br><br>";
 echo "<br>";
 }else{
 //echo "<a class=\"btn btn-warning btn-flat btn-xs\" href=\"#\" onclick=\"passdata('$title2','$StartDate','$EndDate');\">Apply</a><br><br>";
 echo "<a class=\"btn btn-warning btn-flat btn-xs\" href=\"#\" onclick=\"passdata('$train_cal_id','$title2','$StartDate','$EndDate','$start_time','$end_time','$budgeted','$duration','$venue','$training_provider2','$type','$objective2');\">Apply</a><br><br>";
 }
}else{
 if((in_array($username, $hcd_admin) && $mode==2)){
 //if($thisdate>=$start){
 //echo "<br>";
 //}else{
 echo "<a class=\"btn btn-warning btn-flat btn-xs\" href=\"#\" onclick=\"passdata('$train_cal_id','$title2','$StartDate','$EndDate','$start_time','$end_time','$budgeted','$duration','$venue','$training_provider2','$type','$objective2');\">Apply</a><br><br>";
 //}
 }else{
 echo "<a class=\"btn btn-danger btn-flat btn-xs\" href=\"#\" disabled>$status</a><br><small>$remark</small><br><br>";
 }
}
}//if duplicate
}else{
echo "<br><br>";
}//if supervisor

}//while
$db->free_result( $ret );
}//getTrainSlideCalDetail function

function getDuplicateTrain($tname,$tstart,$tend){
session_start();
include("dbconnect.php");
//global $username;
$username = $_SESSION["loginuser"];
$staffid = getStaffID($username);
//return $tstart."dasd";
//$tname2=stripslashes($tname);
$ret = $db->query( "select * from program where (imdsup_id='$staffid' or hcd_id='$username') and title='$tname' and start_date='$tstart' and end_date='$tend' and status!='Cancelled'", "listing" );

/*if (!$ret) {
echo("<p>Error performing query: " . mysql_error() . "</p>");
exit();
}*/
/*
while( $row = $db->fetch_array($ret) ){
$tname2=$row["title"];

return $tname2;
}*/

$num=mysql_num_rows($ret);
if($num != "0") {
return true;
}else{
return false;
}

$db->free_result( $ret );
}//getDuplicateTrain function

function getCountNotify($username,$cat){
include("dbconnect.php");
$thisyear=date("Y");
$staffid = getStaffID($username);

if($cat=="external"){
if(in_array($username, getUserName('hod_id',$username))){
$adm_user = "'" . implode("','", $hcd_admin) . "'";
$ret = $db->query( "select count(s.id) as totalNotify from staff s inner join program p on s.id=p.staff_id where p.training_cat='external'
and ((p.status='Pending' and s.imdsup_id='$username') or (p.status='Verified' and s.hod_id='$username')) or ((p.status='Pending' or p.status='Verified' or p.status='Recommended') and '$username' in ($adm_user)) ", "listing" );
}
elseif(in_array($username, getUserName('imdsup_id',$username)) && !in_array($username, getUserName('hod_id',$username))){
$adm_user = "'" . implode("','", $hcd_admin) . "'";
$ret = $db->query( "select count(s.id) as totalNotify from staff s inner join program p on s.id=p.staff_id where p.training_cat='external' and (p.status='Pending' and s.imdsup_id='$username') or ((p.status='Pending' or p.status='Verified' or p.status='Recommended') and '$username' in ($adm_user))", "listing" );
}
elseif(in_array($username, $hcd_admin)){
///$ret = $db->query( "select count(*) as totalNotify from staff s inner join program p on s.id=p.staff_id where p.training_cat='external' and /(p.status='Recommended') ", "listing" );
$ret = $db->query( "select count(id) as totalNotify from program where staff_id!='0' and training_cat='external' and (status='Pending' or status='Verified' or status='Recommended') ", "listing" );
}
}//if external

if($cat=="in-house"){
if(in_array($username, getUserName('hod_id',$username))){
 //$adm_user = "'" . implode("','", $hcd_admin) . "'";
 //$ret = $db->query( "select count(*) as totalNotify from staff s inner join program p on s.id=p.imdsup_id where p.training_cat='in-house' and //((p.status='Pending') and s.hod_id='$username') or ((p.status='Pending' or p.status='Recommended') and '$username' in ($adm_user)) ", "listing" );
 if(in_array($username, $hcd_admin)){
 $ret = $db->query( "select count(id) as totalNotify from program where training_cat='in-house' and (status='Pending' or status='Recommended') ", "listing" );
 }else{
 $ret = $db->query( "select count(s.id) as totalNotify from staff s inner join program p on s.id=p.imdsup_id where p.training_cat='in-house' and ((p.status='Pending') and s.hod_id='$username') ", "listing" );
 }
}
elseif(in_array($username, $hcd_admin)){
 //$ret = $db->query( "select count(*) as totalNotify from staff s inner join program p on s.id=p.imdsup_id where p.training_cat='in-house' and //(p.status='Recommended') ", "listing" );

 $ret = $db->query( "select count(id) as totalNotify from program where training_cat='in-house' and (status='Pending' or status='Recommended') ", "listing" );
}
}//if in-house

if($cat=="evaluation"){
//////modified on 13/6/16/////////////
/*$ret = $db->query( "select count(p.id) as totalNotify
from program p
left join nomination n on (n.prog_id=p.id)
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
inner join staff s on (s.id=n.staff_id or s.id=p.staff_id)
where ( (p.staff_id='$staffid' or n.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date)
or (p.imdsup_id='$staffid' or s.imdsup_id='$username' and s.id is null and te.status='Pending' and p.training_cat='in-house')
or (s.imdsup_id='$username' and s.id is null and te.status='Pending' and p.training_cat='external') )
and p.status='Approved' and p.tef='' and (DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4) and (p.type!=5 and p.type!=6) ", "listing" );
*/
/*$ret = $db->query( "select count(p.id) as totalNotify
from program p
left join nomination n on (n.prog_id=p.id)
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
inner join staff s on (s.id=n.staff_id or s.id=p.staff_id)
where ( (p.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) or (p.imdsup_id='$staffid' and te.status='Pending' and p.training_cat='in-house')
or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house') or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='external') or (n.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
and p.status='Approved' and p.tef='' and (DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4) and (p.type!=5 and p.type!=6)", "listing" ); */

/*$ret = $db->query( "select count(p.id) as totalNotify
from (select * from staff where staff_id='$username' or hod_id='$username' or imdsup_id='$username') as t,program p
inner join nomination n on (n.prog_id=p.id)
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
where ( (p.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date)
or (p.imdsup_id='$staffid' and te.status='Pending' and p.training_cat='in-house')
or (t.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house')
or (t.imdsup_id='$username' and te.status='Pending' and p.training_cat='external')
or (n.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
and p.status='Approved' and p.tef='' and (DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time),
concat(p.start_date,' ',p.start_time) )) / 3600 >=4) and (p.type!=5 and p.type!=6) and (t.id=n.staff_id or t.id=p.staff_id)", "listing" ); */
/*
$ret = $db->query( "select count(p.id) as totalNotify
from (select * from program where status='Approved' and tef='') as p
left join nomination n on n.prog_id=p.id
left join (select * from staff where staff_id='$username' or hod_id='$username' or imdsup_id='$username') t on t.id=p.staff_id or t.id=n.staff_id
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
where ( (p.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date)
or (p.imdsup_id='$staffid' and te.status='Pending' and p.training_cat='in-house')
or (t.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house')
or (t.imdsup_id='$username' and te.status='Pending' and p.training_cat='external')
or (n.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
and (DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time),
concat(p.start_date,' ',p.start_time) )) / 3600 >=4) and (p.type!=5 and p.type!=6)", "listing" );
*/
/*$ret = $db->query( "select count(p.id) as totalNotify
from program p
left join nomination n on (n.prog_id=p.id)
inner join (select * from staff where staff_id='$username' or imdsup_id='$username') as s on
(n.prog_id=p.id and p.staff_id=s.id) or (n.prog_id=p.id and n.staff_id=s.id)
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
where ( (((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) or
(s.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house') or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='external') or
(((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
and p.status='Approved' and p.tef='' and
(DATEDIFF(p.end_date, start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4) and (p.type!=5 and p.type!=6) ", "listing" );
*/
$ret = $db->query( "select count(*) as totalNotify from (
select p.id
from program p
left join nomination n on n.prog_id=p.id
inner join staff s on (s.id=n.staff_id)
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
where (
(s.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house') or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='external') or
(n.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
and p.status='Approved' and p.tef='' and
(DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4)
and (p.type!=5 and p.type!=6)
union all
select p.id
from program p
left join nomination n on n.prog_id=p.id
inner join staff s on s.id=p.staff_id
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
where (
(s.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house') or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='external') or
(p.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
and p.status='Approved' and p.tef='' and
(DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4)
and (p.type!=5 and p.type!=6)  ) x", "listing" );

}

if($cat=="effectiveness"){
//////modified on 13/6/16/////////////
//////modified on 12/1/17/////////////
//////modified on 16/1/17/////////////
$ret = $db->query( "select count(*) as totalNotify from (
select te.id
from program p
left join nomination n on (n.prog_id=p.id)
inner join (select * from staff where imdsup_id='$username' and disable_tee='No') as s on p.staff_id=s.id
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_effectiveness tef on (tef.prog_id=p.id and (tef.staff_id=n.staff_id or tef.staff_id=p.staff_id))
where (te.id!='1315' and te.id!='670' and te.id!='72') and ( (te.status is null and CURDATE()>p.end_date) or (te.status='Verified' and p.training_cat='in-house')
or (te.status='Verified' and p.training_cat='external') or (te.status is null and CURDATE()>p.end_date) )
and p.status='Approved' and p.tef='' and DATEDIFF(CURDATE(),p.end_date)>=90 and (p.type!=5 and p.type!=6) and tef.date_created is null and te.status='Verified'
union all
select te.id
from program p
left join nomination n on (n.prog_id=p.id)
inner join (select * from staff where imdsup_id='$username' and disable_tee='No') as s on n.staff_id=s.id
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_effectiveness tef on (tef.prog_id=p.id and (tef.staff_id=n.staff_id or tef.staff_id=p.staff_id))
where (te.id!='1315' and te.id!='670' and te.id!='72') and ( (te.status is null and CURDATE()>p.end_date) or (te.status='Verified' and p.training_cat='in-house')
or (te.status='Verified' and p.training_cat='external') or (te.status is null and CURDATE()>p.end_date) )
and p.status='Approved' and p.tef='' and DATEDIFF(CURDATE(),p.end_date)>=90 and (p.type!=5 and p.type!=6) and tef.date_created is null and te.status='Verified' ) x
", "listing" );

/*$ret = $db->query( "select count(distinct(te.id)) as totalNotify
from program p
left join nomination n on (n.prog_id=p.id)
inner join (select * from staff where imdsup_id='$username') as s on
(n.prog_id=p.id and p.staff_id=s.id) or (n.prog_id=p.id and n.staff_id=s.id)
left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join training_effectiveness tef on (tef.prog_id=p.id and (tef.staff_id=n.staff_id or tef.staff_id=p.staff_id))
where ( (te.status is null and CURDATE()>p.end_date) or (te.status='Verified' and p.training_cat='in-house')
or (te.status='Verified' and p.training_cat='external') or (te.status is null and CURDATE()>p.end_date) )
and p.status='Approved' and p.tef='' and DATEDIFF(CURDATE(),p.end_date)>=90 and (p.type!=5 and p.type!=6) and tef.date_created is null and te.status='Verified'
", "listing" );
*/
/*$ret = $db->query( "select count(distinct(te.id)) as totalNotify
from program p
left join nomination n on (n.prog_id=p.id)
left join training_evaluation te on (te.prog_id=p.id)
left join training_effectiveness tef on (tef.prog_id=p.id)
inner join staff s on (s.id=n.staff_id or s.id=p.staff_id)
where ( (te.staff_id='$staffid' and te.status is null and CURDATE()>p.end_date) or (s.imdsup_id='$username' and te.status='Verified' and
p.training_cat='in-house')
or (s.imdsup_id='$username' and te.status='Verified' and p.training_cat='external') )
and p.status='Approved' and p.tef='' and DATEDIFF(CURDATE(),p.end_date)>=90 and (p.type!=5 and p.type!=6) and tef.date_created is null", "listing" );
*/
//$ret = $db->query( "select count(*) as totalNotify
//from staff s inner join program p on (s.id=p.staff_id or s.id=p.imdsup_id )
//left join nomination n on (n.prog_id=p.id) left join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
//left join training_effectiveness tef on (tef.prog_id=p.id and (tef.staff_id=n.staff_id or tef.staff_id=p.staff_id))
//where ( (p.staff_id='$staffid' and te.status is null and CURDATE()>p.end_date) or (p.imdsup_id='$staffid' and te.status='Verified' and p.training_cat='in-house')
//or (s.imdsup_id='$username' and te.status='Verified' and p.training_cat='external') )
//and p.status='Approved' and DATEDIFF(CURDATE(),p.end_date)>90 and (p.type!=5 and p.type!=6) and tef.date_created is null", "listing" );

}

$row = $db->fetch_array($ret);
$totalNotify=$row["totalNotify"];

$num=mysql_num_rows($ret);
if($num != "0") {
return $totalNotify;
}else{
return 0;
}

}//getCountNotify

function getCountPostTrainHCD($cat){
include("dbconnect.php");
$thisyear=date("Y");

if($cat=="external"){
 /*$ret = $db->query( "select count(*) as totalPost  from staff s inner join program p on s.id=p.staff_id where p.training_cat='external'
 and (p.status='Recommended' or p.status='Approved') ", "listing" );*/
 $ret = $db->query( "select count(*) as totalPost  from program where training_cat='external'
 and (status='Recommended' or status='Approved') ", "listing" );
}else{
/*$ret = $db->query( "select count(*) as totalPost from staff s inner join program p on s.id=p.imdsup_id where p.training_cat='in-house'
and (p.status='Recommended' or p.status='Approved') ", "listing" ); */
$ret = $db->query( "select count(*) as totalPost from program where training_cat='in-house'
and (status='Recommended' or status='Approved') ", "listing" );
}

$row = $db->fetch_array($ret);
$totalPost=$row["totalPost"];

$num=mysql_num_rows($ret);
if($num != "0") {
return $totalPost;
}else{
return 0;
}

}//getCountPostTrainHCD

function getEmailUser($username,$field){
include("dbconnect.php");

//if($which=="staff_id"){
$ret1 = $db2->query( "select e.employee_email as email_user from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id left join $dbname1.staff s on s.$field=u.username where s.staff_id='$username'", "listing" );
//}elseif($which=="imdsup_id"){
//$ret1 = $db2->query( "select e.employee_email from  $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname1.staff s on s.staff_id=u.username where u.username='$username'", "listing" );
//}

$row = $db2->fetch_array($ret1);

$email_user=$row["email_user"];

return $email_user;

$db2->free_result( $ret1 );
}//getEmailUser

function getFullName($username,$field){
include("dbconnect.php");

//if($which=="staff_id"){
$ret1 = $db2->query( "select e.employee_name as fullname from  $dbname2.employee e left join $dbname2.user u on e.user_id=u.id left join $dbname1.staff s on s.$field=u.username where s.staff_id='$username'", "listing" );
//}elseif($which=="imdsup_id"){
//$ret1 = $db2->query( "select e.employee_email from  $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname1.staff s on s.staff_id=u.username where u.username='$username'", "listing" );
//}

$row = $db2->fetch_array($ret1);

$fullname=$row["fullname"];

return $fullname;

$db2->free_result( $ret1 );
}//getFullName

function dateDifference($date_1 , $date_2 , $differenceFormat )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);

}

function checkEvalStatus($progid,$staffid,$mode){
include("dbconnect.php");

if($mode=="Evaluation"){
$ret = $db->query( "select id from  training_evaluation where prog_id='$progid' and staff_id='$staffid' and (status='Pending' or status='Verified')", "listing" );
//$ret = $db->query( "select id from  training_evaluation where prog_id='$progid' and staff_id='$staffid' and status='Verified'", "listing" );
}elseif($mode=="Effectiveness"){
$ret = $db->query( "select id from  training_effectiveness where prog_id='$progid' and staff_id='$staffid'", "listing" );
}elseif($mode=="Induction"){
$ret = $db->query( "select id from  training_eval_induction where prog_id='$progid' and staff_id='$staffid'", "listing" );
}else{
$ret = $db->query( "select id from  training_eval_lms where prog_id='$progid' and staff_id='$staffid'", "listing" );
}

$num=mysql_num_rows($ret);
if($num != "0") {
return 1;
}else{
return 0;
}

$db->free_result( $ret );
}//checkEvalStatus

function getEvalStatus($mode,$progid){
include("dbconnect.php");

if($mode=="TEF"){
$ret = $db->query( "select count(*) as totalEval  from  training_evaluation where prog_id='$progid' and (status='Pending' or status='Verified')", "listing" );
}elseif($mode=="TEE"){
$ret = $db->query( "select count(*) as totalEval  from  training_effectiveness where prog_id='$progid'", "listing" );
}else{
$ret = $db->query( "select count(*) as totalEval  from  training_eval_induction where prog_id='$progid'", "listing" );
}

$num=mysql_num_rows($ret);
if($num != "0") {
$row = $db->fetch_array($ret);
$totalEval=$row["totalEval"];
return $totalEval;
}else{
return 0;
}

$db->free_result( $ret );
}//getEvalStatus

function getTrainType($type){
include("dbconnect.php");

$ret = $db->query( "select * from  type_program where id='$type'", "listing" );

$row = $db->fetch_array($ret);

$name=$row["name"];

return $name;

$db->free_result( $ret );
}//getTrainType

function getSelectMth($m){
  //$thismonth=date("m");

  for ($i=1; $i<=12; $i=$i+1){
     echo "<option value=\"$i\" ";
     if($i==$m){echo "selected";}
     echo ">".getMonthFullname($i)."</option>";
  }//for

}//end func getSelectMth

function getMonthFullname($month){
        if(empty($month))
            $month = date("m");

        $month_names = array("January","February","March","April","May","June",
                    "July","August","September","October","November","December");

        return $month_names[($month - 1)];
} // end func getMonthFullname

function smart_wordwrap($string, $width = 75, $break = "\n") {
    // split on problem words over the line length
    $pattern = sprintf('/([^ ]{%d,})/', $width);
    $output = '';
    $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    foreach ($words as $word) {
        if (false !== strpos($word, ' ')) {
            // normal behaviour, rebuild the string
            $output .= $word;
        } else {
            // work out how many characters would be on the current line
            $wrapped = explode($break, wordwrap($output, $width, $break));
            $count = $width - (strlen(end($wrapped)) % $width);

            // fill the current line and add a break
            $output .= substr($word, 0, $count) . $break;

            // wrap any remaining characters from the problem word
            $output .= wordwrap(substr($word, $count), $width, $break, true);
        }
    }

    // wrap the final output
    return wordwrap($output, $width, $break);
}

function showParticipant($mth,$year,$cat){
include("dbconnect.php");

/*$qry = "select *
from (select * from $dbname1.program) p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username
left join $dbname2.employee e on u.id=e.user_id
left join (select employee_id,entitle_id from $dbname2.status) st on e.employee_id=st.employee_id
where p.date_complete is not null and month(p.date_created)='$mth'
and year(p.date_created)='$year'";*/

$qry = "select *
from (select * from $dbname1.program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and month(date_created)='$mth'
and year(date_created)='$year') as p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) as s on s.id=p.staff_id or s.id=n.staff_id
left join
(select u.username as uname,job_grade
from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.status s on e.employee_id=s.employee_id";


if($cat=="Management" ){
//$qry .= " and st.entitle_id IN ('2','3','4','5')";
$qry .= " where job_grade IN ('TM1','TM2','SM1','SM2','SM3','SM4','MM1','MM2','MM3','MM4','MM5','M2','M3','M4','M5','M6','M7')) as b on b.uname=s.staff_id";
}

if($cat=="Executives" ){
//$qry .= " and st.entitle_id IN ('6')";
$qry .= " where job_grade IN ('EX1', 'EX2', 'EX3', 'EX4', 'EX5','E1','E2','E3','E4')) as b on b.uname=s.staff_id";
}

if($cat=="Non-Executives" ){
//$qry .= " and st.entitle_id IN ('7','8','9')";
$qry .= " where job_grade IN ('NT1', 'NT2', 'NT3', 'NT4', 'TL1', 'TL2', 'TL3', 'TL4', 'NT5', 'TL5', 'NT6', 'TL6','N1','N2','N3','N4','T1','T2')) as b on b.uname=s.staff_id";
}

$ret = $db2->query( $qry, "listing" );
$num=mysql_num_rows($ret);
$total=0;
while( $row = $db2->fetch_array($ret) ){
$uname=$row["uname"];
if(!is_null($uname)){
$total = $total + 1;
}
}//while

return $total;

$db2->free_result( $ret );
}//showParticipant

function showParticipantQTR($qtr,$year,$cat){
include("dbconnect.php");

$qry = "select *
from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username left join $dbname2.employee e on u.id=e.user_id left join $dbname2.status st on e.employee_id=st.employee_id where p.date_complete is not null";

if($qtr=="Q1" ){
$qry .= " and (p.date_created>='$year-01-01' and p.date_created<='$year-06-30')";
}

if($qtr=="Q2" ){
$qry .= " and (p.date_created>='$year-07-01' and p.date_created<='$year-12-31')";
}

if($cat=="Management" ){
$qry .= " and st.job_grade IN ('TM1','TM2','SM1','SM2','SM3','SM4','MM1','MM2','MM3','MM4','MM5','M2','M3','M4','M5','M6','M7')";
}

if($cat=="Executives" ){
$qry .= " and st.job_grade IN ('EX1', 'EX2', 'EX3', 'EX4', 'EX5','E1','E2','E3','E4')";
}

if($cat=="Non-Executives" ){
$qry .= " and st.job_grade IN ('NT1', 'NT2', 'NT3', 'NT4', 'TL1', 'TL2', 'TL3', 'TL4', 'NT5', 'TL5', 'NT6', 'TL6','N1','N2','N3','N4','T1','T2')";
}

$ret = $db2->query( $qry, "listing" );
$total=mysql_num_rows($ret);
//$row = $db2->fetch_array($ret);

//$total=$row["total"];

return $total;

$db2->free_result( $ret );
}//showParticipantQTR

function showParticipantYr($year,$cat){
include("dbconnect.php");

/*$qry = "select *
from $dbname1.program p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username
left join $dbname2.employee e on u.id=e.user_id
left join (select employee_id,entitle_id from $dbname2.status) st on e.employee_id=st.employee_id
where p.date_complete is not null and year(p.date_created)='$year'";

if($cat=="Management" ){
$qry .= " and st.entitle_id IN ('2','3','4','5')";
}

if($cat=="Executives" ){
$qry .= " and st.entitle_id IN ('6')";
}

if($cat=="Non-Executives" ){
$qry .= " and st.entitle_id IN ('7','8','9')";
}*/
$qry = "select *
from (select * from $dbname1.program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and year(date_created)='$year') as p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) as s on s.id=p.staff_id or s.id=n.staff_id
left join
(select u.username as uname,job_grade
from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.status s on e.employee_id=s.employee_id";


if($cat=="Management" ){
$qry .= " where job_grade IN ('TM1','TM2','SM1','SM2','SM3','SM4','MM1','MM2','MM3','MM4','MM5','M2','M3','M4','M5','M6','M7')) as b on b.uname=s.staff_id";
}

if($cat=="Executives" ){
$qry .= " where job_grade IN ('EX1', 'EX2', 'EX3', 'EX4', 'EX5','E1','E2','E3','E4')) as b on b.uname=s.staff_id";
}

if($cat=="Non-Executives" ){
$qry .= " where job_grade IN ('NT1', 'NT2', 'NT3', 'NT4', 'TL1', 'TL2', 'TL3', 'TL4', 'NT5', 'TL5', 'NT6', 'TL6','N1','N2','N3','N4','T1','T2')) as b on b.uname=s.staff_id";
}

$ret = $db2->query( $qry, "listing" );
$num=mysql_num_rows($ret);
$total=0;
while( $row = $db2->fetch_array($ret) ){
$uname=$row["uname"];
if(!is_null($uname)){
$total = $total + 1;
}
}//while

return $total;

$db2->free_result( $ret );
}//showParticipantYr

function showAttendedMth($comp,$mth,$year,$cat,$prog_type){
include("dbconnect.php");

/*$qry = "select *
from (select * from $dbname1.program where training_cat='$prog_type') p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username
left join $dbname2.employee e on u.id=e.user_id
left join (select employee_id,entitle_id from $dbname2.status) st on e.employee_id=st.employee_id
where (p.status='Approved' or p.status='Completed') and (p.start_date>='$year-$mth-01'
and p.end_date<='$year-$mth-31')";

if($comp=="ALL" ){
}else{
$qry .= " and e.employee_grpcompany like '$comp'";
}

if($cat=="Management" ){
$qry .= " and st.entitle_id IN ('2','3','4','5')";
}

if($cat=="Executives" ){
$qry .= " and st.entitle_id IN ('6')";
}

if($cat=="Non-Executives" ){
$qry .= " and st.entitle_id IN ('7','8','9')";
}

$ret = $db2->query( $qry, "listing" );
$total=mysql_num_rows($ret);
*/
//(start_date>='$year-$mth-01' and end_date<='$year-$mth-31') and training_cat='$prog_type') as p
$qry = "select *
from (select * from $dbname1.program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and
(MONTH(start_date)='$mth' and YEAR(start_date)='$year') and training_cat='$prog_type') as p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) as s on s.id=p.staff_id or s.id=n.staff_id
left join
(select u.username as uname,job_grade
from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.status s on e.employee_id=s.employee_id where";

	/*$qry = "select *
    from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
    left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
	left join
(select u.username as uname, c.description,e.employee_grpcompany,e.employee_company
from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.company c on e.employee_company=c.id inner join $dbname2.status s on e.employee_id=s.employee_id ) as b on b.uname=s.staff_id";*/

if($comp=="ALL" ){
}else{
$qry .= " e.employee_grpcompany like '$comp' and";
}

if($cat=="Management" ){
$qry .= " s.job_grade IN ('TM1','TM2','SM1','SM2','SM3','SM4','MM1','MM2','MM3','MM4','MM5','M2','M3','M4','M5','M6','M7')) as b on b.uname=s.staff_id";
}

if($cat=="Executives" ){
$qry .= " s.job_grade IN ('EX1', 'EX2', 'EX3', 'EX4', 'EX5','E1','E2','E3','E4')) as b on b.uname=s.staff_id";
}

if($cat=="Non-Executives" ){
$qry .= " s.job_grade IN ('NT1', 'NT2', 'NT3', 'NT4', 'TL1', 'TL2', 'TL3', 'TL4', 'NT5', 'TL5', 'NT6', 'TL6','N1','N2','N3','N4','T1','T2')) as b on b.uname=s.staff_id";
}

$qry .= " where (n.staff_id is not null or p.staff_id is not null)";

if($prog_type=="in-house"){
$qry .= " group by n.staff_id,n.train_cal_id";
}

	/*$qry .= " left join $dbname1.training_evaluation te on te.prog_id=p.id and (te.staff_id=p.staff_id or te.staff_id=n.staff_id)
	left join $dbname1.training_eval_induction tei on tei.prog_id=p.id and (tei.staff_id=p.staff_id or tei.staff_id=n.staff_id)
    left join $dbname1.training_effectiveness tee on tee.prog_id=p.id and (tee.staff_id=p.staff_id or tee.staff_id=n.staff_id)
    where (p.status='Pending' or p.status='Verified' or p.status='Recommended' or  p.status='Approved' or p.status='Completed') and (p.start_date>='$year-$mth-01' and p.end_date<='$year-$mth-31') and training_cat='$prog_type' and (n.staff_id is not null or p.staff_id is not null)";*/



$ret = $db2->query( $qry, "listing" );
$num=mysql_num_rows($ret);
$total=0;
while( $row = $db2->fetch_array($ret) ){
$uname=$row["uname"];
if(!is_null($uname)){
$total = $total + 1;
}
}//while

return $total;

$db2->free_result( $ret );
}//showAttendedMth

function showAttendedMth_bk($comp,$mth,$year,$cat,$prog_type){
include("dbconnect.php");

$qry = "select *
from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username
left join $dbname2.employee e on u.id=e.user_id
where p.date_complete is not null and (p.date_created>='$year-$mth-01'
and p.date_created<='$year-$mth-31')";
//month(p.date_created)='$mth' and year(p.date_created)='$year'";
//left join $dbname2.status st on e.employee_id=st.employee_id

if($comp=="ALL" ){
}else{
$qry .= " and e.employee_grpcompany like '$comp'";
}

if($prog_type=="in-house" ){
$qry .= " and p.training_cat='in-house'";
}

if($prog_type=="external" ){
$qry .= " and p.training_cat='external'";
}

if($cat=="Management" ){
//$qry .= " and st.entitle_id IN ('2','3','4','5')";
$qry .= " and exists (select 1 from $dbname2.status st where e.employee_id=st.employee_id and st.entitle_id IN ('2','3','4','5'))";
}

if($cat=="Executives" ){
//$qry .= " and st.entitle_id IN ('6')";
$qry .= " and exists (select 1 from $dbname2.status st where e.employee_id=st.employee_id and st.entitle_id IN ('6'))";
}

if($cat=="Non-Executives" ){
//$qry .= " and st.entitle_id IN ('7','8','9')";
$qry .= " and exists (select 1 from $dbname2.status st where e.employee_id=st.employee_id and st.entitle_id IN ('7','8','9'))";
}

$ret = $db2->query( $qry, "listing" );
$total=mysql_num_rows($ret);
//$row = $db2->fetch_array($ret);

//$total=$row["total"];

return $total;

$db2->free_result( $ret );
}//showAttendedMth

function showAttendedQTR($qtr,$comp,$year,$cat,$prog_type){
include("dbconnect.php");

$qry = "select *
from $dbname1.program p left join $dbname1.nomination n on n.prog_id=p.id
left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username left join $dbname2.employee e on u.id=e.user_id left join $dbname2.status st on e.employee_id=st.employee_id where p.status='Approved'";

if($comp=="ALL" ){
}else{
$qry .= " and e.employee_grpcompany like '$comp'";
}

if($prog_type=="in-house" ){
$qry .= " and p.training_cat='in-house'";
}

if($prog_type=="external" ){
$qry .= " and p.training_cat='external'";
}

if($qtr=="Q1" ){
$qry .= " and (p.start_date>='$year-01-01' and p.end_date<='$year-06-30')";
}

if($qtr=="Q2" ){
$qry .= " and (p.start_date>='$year-07-01' and p.end_date<='$year-12-31')";
}

if($cat=="Management" ){
$qry .= " and st.job_grade IN ('TM1','TM2','SM1','SM2','SM3','SM4','MM1','MM2','MM3','MM4','MM5','M2','M3','M4','M5','M6','M7')";
}

if($cat=="Executives" ){
$qry .= " and st.job_grade IN ('EX1', 'EX2', 'EX3', 'EX4', 'EX5','E1','E2','E3','E4')";
}

if($cat=="Non-Executives" ){
$qry .= " and st.job_grade IN ('NT1', 'NT2', 'NT3', 'NT4', 'TL1', 'TL2', 'TL3', 'TL4', 'NT5', 'TL5', 'NT6', 'TL6','N1','N2','N3','N4','T1','T2')";
}

$ret = $db2->query( $qry, "listing" );
$total=mysql_num_rows($ret);
//$row = $db2->fetch_array($ret);

//$total=$row["total"];

return $total;

$db2->free_result( $ret );
}//showAttendedQTR

function showAttendedYr($comp,$year,$cat,$prog_type){
include("dbconnect.php");

/*$qry = "select *
from (select * from $dbname1.program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and year(start_date)='$year' and training_cat='$prog_type') as p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) as s on s.id=p.staff_id or s.id=n.staff_id
left join
(select u.username as uname,entitle_id
from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.status s on e.employee_id=s.employee_id where";
*/
$qry = "select *
from (select * from $dbname1.program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and year(start_date)='$year' and training_cat='$prog_type') as p
left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) as s on s.id=p.staff_id or s.id=n.staff_id
left join
(select u.username as uname,job_grade
from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.status s on e.employee_id=s.employee_id where";

if($comp=="ALL" ){
}else{
$qry .= " e.employee_grpcompany like '$comp' and";
}

if($cat=="Management" ){
$qry .= " s.job_grade IN ('TM1','TM2','SM1','SM2','SM3','SM4','MM1','MM2','MM3','MM4','MM5','M2','M3','M4','M5','M6','M7')) as b on b.uname=s.staff_id";
}

if($cat=="Executives" ){
$qry .= " s.job_grade IN ('EX1', 'EX2', 'EX3', 'EX4', 'EX5','E1','E2','E3','E4')) as b on b.uname=s.staff_id";
}

if($cat=="Non-Executives" ){
//$qry .= " entitle_id IN ('7','8','9')) as b on b.uname=s.staff_id";
$qry .= " s.job_grade IN ('NT1', 'NT2', 'NT3', 'NT4', 'TL1', 'TL2', 'TL3', 'TL4', 'NT5', 'TL5', 'NT6', 'TL6','N1','N2','N3','N4','T1','T2')) as b on b.uname=s.staff_id";
}

$qry .= " where (n.staff_id is not null or p.staff_id is not null)";

if($prog_type=="in-house"){
$qry .= " group by n.staff_id,n.train_cal_id";
}

$ret = $db2->query( $qry, "listing" );
$num=mysql_num_rows($ret);
$total=0;
while( $row = $db2->fetch_array($ret) ){
$uname=$row["uname"];
if(!is_null($uname)){
$total = $total + 1;
}
}//while

return $total;

$db2->free_result( $ret );
}//showAttendedYr

function showAttendedYr_bk($comp,$year,$cat,$prog_type){
include("dbconnect.php");

$qry = "select *
from (select * from $dbname1.program where training_cat='$prog_type') p";


$qry .= " left join $dbname1.nomination n on n.prog_id=p.id
left join (select * from $dbname1.staff) s on s.id=p.staff_id or s.id=n.staff_id
left join $dbname2.user u on s.staff_id=u.username
left join $dbname2.employee e on u.id=e.user_id";
//left join (select * from eleave_v4.status
//where entitle_id IN ('2','3','4','5')) st on e.employee_id=st.employee_id";
//if($cat=="Management" ){
$qry .= " left join (select employee_id,entitle_id from $dbname2.status
) st on e.employee_id=st.employee_id";
//}
/*
if($cat=="Executives" ){
$qry .= " left join (select employee_id,entitle_id from $dbname2.status
) st on e.employee_id=st.employee_id";
}

if($cat=="Non-Executives" ){
$qry .= " left join (select employee_id,entitle_id from $dbname2.status
) st on e.employee_id=st.employee_id";
}
*/


$qry .= " where (p.status='Approved' or p.status='Completed') and year(p.start_date)='$year'";

//left join $dbname1.staff s on s.id=p.staff_id or s.id=n.staff_id
//left join $dbname2.status st on e.employee_id=st.employee_id

if($comp=="ALL" ){
}else{
$qry .= " and e.employee_grpcompany like '$comp'";
}
/*
if($prog_type=="in-house" ){
$qry .= " and p.training_cat='in-house'";
}

if($prog_type=="external" ){
$qry .= " and p.training_cat='external'";
}
*/
if($cat=="Management" ){
$qry .= " and st.entitle_id IN ('2','3','4','5')";
//$qry .= " and exists (select 1 from $dbname2.status st where e.employee_id=st.employee_id and st.entitle_id IN ('2','3','4','5'))";
}

if($cat=="Executives" ){
$qry .= " and st.entitle_id IN ('6')";
//$qry .= " and exists (select 1 from $dbname2.status st where e.employee_id=st.employee_id and st.entitle_id IN ('6'))";
}

if($cat=="Non-Executives" ){
$qry .= " and st.entitle_id IN ('7','8','9')";
//$qry .= " and exists (select 1 from $dbname2.status st where e.employee_id=st.employee_id and st.entitle_id IN ('7','8','9'))";
}

$ret = $db2->query( $qry, "listing" );
$total=mysql_num_rows($ret);
//$row = $db2->fetch_array($ret);

//$total=$row["total"];

return $total;

$db2->free_result( $ret );
}//showAttendedYr

function showProgManHrsMth($mth,$year,$cat,$prog_type){
include("dbconnect.php");

$qry = "select * from program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and  month(start_date)='$mth' and year(start_date)='$year' and training_cat='$prog_type' group by title,start_date,end_date";
//$qry = "select * from program where date_complete is not null and (date_created>='$year-$mth-01' and date_created<='$year-$mth-31')";
/*
if($prog_type=="in-house" ){
$qry .= " and training_cat='in-house'";
}

if($prog_type=="external" ){
$qry .= " and training_cat='external'";
}
*/

$ret = $db->query( $qry, "listing" );
$total=mysql_num_rows($ret);

while( $row = $db->fetch_array($ret) ){

$startDate=$row["start_date"];
$endDate=$row["end_date"];
$startTime=$row["start_time"];
$endTime=$row["end_time"];

$start=date('Y-m-d H:i:s', strtotime("$startDate $startTime"));
$end=date('Y-m-d H:i:s', strtotime("$endDate $endTime"));
$subTime = strtotime($end) - strtotime($start);
$hours+= ($subTime/(60*60))%24;

}


if($cat=="Program" ){
return $total;
}

if($cat=="Training Man Hours" ){
 if($hours==""){
 return 0;
 }else{
 return $hours;
 }
}

$db->free_result( $ret );
}//showProgManHrsMth

function showProgManHrsQTR($qtr,$year,$cat,$prog_type){
include("dbconnect.php");

$qry = "select * from program where date_complete is not null";

if($prog_type=="in-house" ){
$qry .= " and training_cat='in-house'";
}

if($prog_type=="external" ){
$qry .= " and training_cat='external'";
}

if($qtr=="Q1" ){
$qry .= " and (date_created>='$year-01-01' and date_created<='$year-06-30')";
}

if($qtr=="Q2" ){
$qry .= " and (date_created>='$year-07-01' and date_created<='$year-12-31')";
}


$ret = $db->query( $qry, "listing" );
$total=mysql_num_rows($ret);
while( $row = $db->fetch_array($ret) ){

$startDate=$row["start_date"];
$endDate=$row["end_date"];
$startTime=$row["start_time"];
$endTime=$row["end_time"];

$start=date('Y-m-d H:i:s', strtotime("$startDate $startTime"));
$end=date('Y-m-d H:i:s', strtotime("$endDate $endTime"));
$subTime = strtotime($end) - strtotime($start);
$hours+= ($subTime/(60*60))%24;

}//while


if($cat=="Program" ){
return $total;
}

if($cat=="Training Man Hours" ){
 if($hours==""){
 return 0;
 }else{
 return $hours;
 }
}

$db->free_result( $ret );
}//showProgManHrsQTR

function showProgManHrsYR($year,$cat,$prog_type){
include("dbconnect.php");

$qry = "select * from program where (status='Pending' or status='Verified' or status='Recommended' or  status='Approved' or status='Completed') and year(start_date)='$year' and training_cat='$prog_type' group by title,start_date,end_date";
//$qry = "select * from program where date_complete is not null and year(date_created)='$year'";
/*
if($prog_type=="in-house" ){
$qry .= " and training_cat='in-house'";
}

if($prog_type=="external" ){
$qry .= " and training_cat='external'";
}*/


$ret = $db->query( $qry, "listing" );
$total=mysql_num_rows($ret);
while( $row = $db->fetch_array($ret) ){

$startDate=$row["start_date"];
$endDate=$row["end_date"];
$startTime=$row["start_time"];
$endTime=$row["end_time"];

$start=date('Y-m-d H:i:s', strtotime("$startDate $startTime"));
$end=date('Y-m-d H:i:s', strtotime("$endDate $endTime"));
$subTime = strtotime($end) - strtotime($start);
$hours+= ($subTime/(60*60))%24;

}//while


if($cat=="Program" ){
return $total;
}

if($cat=="Training Man Hours" ){
 if($hours==""){
 return 0;
 }else{
 return $hours;
 }
}

$db->free_result( $ret );
}//showProgManHrsYR

function checkChat($progid){
include("dbconnect.php");
session_start();

$session_user = $_SESSION["loginuser"];

  $ret1 = $db->query( "select max(id) as total from chatbox where prog_id='$progid'", "listing" );
  //$numTot=mysql_num_rows($ret1);
  $row = $db->fetch_array($ret1);
  $total=$row["total"];
  //
  $ret3 = $db->query( "select max(id) as maxid from chatbox where prog_id='$progid' and username!='$session_user'", "listing" );
  //$num=mysql_num_rows($ret3);
  //if ($numTot != "0") {
  $row = $db->fetch_array($ret3);
  $maxid=$row["maxid"];

   if(($total==$maxid) && $total!='' && $maxid!=''){
    return 1;
   }else{
    return 0;
   }
  //}else{
   //return '0';
  //}
  //return $total."/".$maxid;
}//checkChat

function countParticipant($progid,$progtitle,$start_date,$end_date,$train_cal_id){
include("dbconnect.php");
/*
$ret = $db->query( "select count(*) as totalParticipate from program p inner join nomination n on p.train_cal_id=n.train_cal_id where p.train_cal_id='$train_cal_id' and (p.status='Recommended' or p.status='Approved' or p.status='Completed')", "listing" );
*/
if($train_cal_id==""){
  $ret = $db->query( "select count(*) as totalParticipate from program p left join nomination n on p.id=n.prog_id where (p.title='$progtitle' and p.start_date='$start_date' and p.end_date='$end_date') and (p.status='Pending' or p.status='Recommended' or p.status='Approved' or p.status='Completed')", "listing" );
}else{
  //////////modified on 16/1/2017/////////////////////
  $ret = $db->query( "select count(distinct n.staff_id) as totalParticipate from program p left join nomination n on p.id=n.prog_id where (p.status='Pending' or p.status='Recommended' or p.status='Approved' or p.status='Completed') and (p.train_cal_id='$train_cal_id' or (p.title='".addslashes($progtitle)."' and p.start_date='$start_date' and p.end_date='$end_date'))", "listing" );
  /*
  $ret = $db->query( "select count(distinct n.staff_id) as totalParticipate from program p left join nomination n on p.id=n.prog_id where (p.status='Pending' or p.status='Recommended' or p.status='Approved' or p.status='Completed') and p.train_cal_id='$train_cal_id'", "listing" );
  */
}
  /*$ret = $db2->query( "select count(*) as totalParticipate from $dbname2.employee e left join $dbname2.user u on e.user_id=u.id
  left join $dbname2.company c on e.employee_company=c.id
  left join $dbname1.staff s on s.staff_id=u.username
  left join $dbname1.nomination n on n.staff_id=s.id
  left join $dbname1.program p on p.id=n.prog_id where n.prog_id='$progid'", "listing" );*/

$row = $db->fetch_array($ret);
$totalParticipate=$row["totalParticipate"];

$num=mysql_num_rows($ret);
if($num != "0") {
return $totalParticipate;
}else{
return 0;
}

}//countParticipant

function countNominateList($progid){
include("dbconnect.php");

$ret = $db->query( "select count(*) as totalNominate from $dbname1.nomination n inner join $dbname1.program p on p.id=n.prog_id where n.prog_id='$progid'", "listing" );

$row = $db->fetch_array($ret);
$totalNominate=$row["totalNominate"];

$num=mysql_num_rows($ret);
if($num != "0") {
return $totalNominate;
}else{
return 0;
}
}//countNominateList

function checkNominateEval($totNominate,$progid,$mode){
include("dbconnect.php");

if($mode=="Evaluation"){
$ret = $db->query( "SELECT id FROM training_evaluation WHERE prog_id='$progid' AND status='Verified' GROUP BY prog_id HAVING count(*) = '$totNominate'", "listing" );
//$ret = $db->query( "select * from  training_evaluation where prog_id='$progid' and staff_id='$staffid' and status='Verified'", "listing" );
}elseif($mode=="Effectiveness"){
//$ret = $db->query( "select * from  training_effectiveness where prog_id='$progid' and staff_id='$staffid'", "listing" );
$ret = $db->query( "SELECT id FROM training_effectiveness WHERE prog_id='$progid' GROUP BY prog_id HAVING count(*) = '$totNominate'", "listing" );
}else{
$ret = $db->query( "SELECT id FROM training_eval_induction WHERE prog_id='$progid' GROUP BY prog_id HAVING count(*) = '$totNominate'", "listing" );
}

$num=mysql_num_rows($ret);
if($num != "0") {
return 1;
}else{
return 0;
}

$db->free_result( $ret );
}//checkNominateEval

function checkEval($staff_id,$train_cal_id,$progtitle,$startdate,$enddate){
include("dbconnect.php");

$_progtitle = mysql_real_escape_string($progtitle);

$qry = "select *,p.staff_id as ext_staffid, n.staff_id as inhse_staffid
from program p
left join nomination n on (n.prog_id=p.id)
inner join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
left join staff s on s.id=p.staff_id or s.id=n.staff_id where
(p.status='Pending' or p.status='Recommended' or p.status='Approved' or p.status='Completed')
and ((p.train_cal_id='$train_cal_id' or p.title='$_progtitle') and p.start_date='$startdate' and p.end_date='$enddate')";
if($staff_id!=""){
$qry .= " and s.id='$staff_id'";
}
$qry .= " order by s.employee_name";

$ret = $db->query( $qry, "listing" );

$num=mysql_num_rows($ret);
if($num != "0") {
return 1;
}else{
return 0;
}

$db->free_result( $ret );
}//checkEval

function checkEffectiveEval($staff_id,$train_cal_id,$progtitle,$startdate,$enddate){
include("dbconnect.php");

$_progtitle = mysql_real_escape_string($progtitle);

$qry = "select *,p.staff_id as ext_staffid, n.staff_id as inhse_staffid
from program p
left join nomination n on (n.prog_id=p.id)
inner join training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
inner join training_effectiveness tef on (tef.prog_id=p.id and (tef.staff_id=n.staff_id or tef.staff_id=p.staff_id))
left join staff s on s.id=p.staff_id or s.id=n.staff_id where
(p.status='Pending' or p.status='Recommended' or p.status='Approved' or p.status='Completed')
and ((p.train_cal_id='$train_cal_id' or p.title='$_progtitle') and p.start_date='$startdate' and p.end_date='$enddate')";
if($staff_id!=""){
$qry .= " and s.id='$staff_id'";
}
$qry .= " order by s.employee_name";

$ret = $db->query( $qry, "listing" );

$num=mysql_num_rows($ret);
if($num != "0") {
return 1;
}else{
return 0;
}

$db->free_result( $ret );
}//checkEffectiveEval

function getTotalCancel(){
  include("dbconnect.php");
  session_start();

  $username = $_SESSION["loginuser"];

  //$today = date("Y-m-d");

  //$ret = $db->query( "select *, date_add(`leave`.from_date, interval 4 day) as int_date from user inner join employee on user.id=employee.user_id inner join `leave` on employee.employee_id=`leave`.employee_id where (`leave`.status='pending') and date_add(`leave`.from_date, interval 4 day)!='$today' and date_add(`leave`.from_date, interval 4 day)>='$today' and user.username='$session_user' order by `leave`.cancel_date desc", "listing" );
  $ret = $db->query( "select *,p.id as progid from staff s inner join program p on (s.id=p.staff_id or s.id=p.imdsup_id) where s.staff_id='$username' and (p.status='Pending' or p.status='Verified')", "listing" );

  $num=mysql_num_rows($ret);
  if ($num != "0") {
  return $num;
  }else{
  return 0;
  }
  $db->free_result( $ret );
}//getTotalCancel


function getCountStr($progid){
  include("dbconnect.php");

  $ret = $db->query( "select objective from program where id='$progid'", "listing" );
  $row = $db->fetch_array($ret);
  $objective=$row["objective"];

  return strlen($objective);

  $db->free_result( $ret );
}//getCountStr

function myUrlEncode($string) {
    $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
	//$entities = array('1', 'A', '7', '8', '9', 'B', 'A', '0', '6', 'D', 'B', '4', 'C', 'F', '%3F', '%25', '%23', '%5B', '%5D');
    $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    return str_replace($entities, $replacements, urldecode($string));
}

function checkExistTrain($progid){
include("dbconnect.php");

$ret = $db->query( "SELECT * FROM training_calendar tc inner join program p on tc.id=p.train_cal_id WHERE tc.id='$progid'", "listing" );

$num=mysql_num_rows($ret);
if($num != "0") {
return 1;
}else{
return 0;
}

$db->free_result( $ret );
}//checkExistTrain

function getListStaff($progtitle,$startdate,$enddate){
include("dbconnect.php");

$qry = "select * from (select u.username as uname,c.name as cname, e.employee_name,c.description,e.employee_ic_no,e.employee_position from $dbname2.employee e inner join $dbname2.user u on e.user_id=u.id inner join $dbname2.company c on e.employee_company=c.id) as b
right join $dbname1.staff s on b.uname=s.staff_id";

//if($staffname==""){
 if($train_cal_id==""){
  $qry .= " left join $dbname1.nomination n on n.staff_id=s.id,
(select id from $dbname1.program where (status='Pending' or status='Recommended' or status='Approved' or status='Completed')
and (title='$progtitle' and start_date='$startdate' and end_date='$enddate')) as p
where p.id=n.prog_id group by b.employee_name order by  b.description,b.employee_name";

 }else{
  $qry .= " left join $dbname1.nomination n on n.staff_id=s.id,
(select id from $dbname1.program where (status='Pending' or status='Recommended' or status='Approved' or status='Completed')
and (train_cal_id='$train_cal_id' or title='$progtitle' and start_date='$startdate' and end_date='$enddate')) as p
where p.id=n.prog_id group by b.employee_name order by  b.description,b.employee_name";

 }
//}else{
//$qry .= " where s.staff_id='$staffname'";
//}


$ret1 = $db2->query( $qry, "listing" );
  while( $row = $db2->fetch_array($ret1) ){

  $employee_name=$row["employee_name"];
  return $employee_name;
}
$db2->free_result( $ret );
}//getListStaff

function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function RemoveBS($Str) {
  $StrArr = str_split($Str); $NewStr = '';
  foreach ($StrArr as $Char) {
    $CharNo = ord($Char);
    if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £
    if ($CharNo > 31 && $CharNo < 127) {
      $NewStr .= $Char;
    }
  }
  return $NewStr;
}

function fixWrongUTF8Encoding($inputString) {

        // code source:  https://github.com/devgeniem/wp-sanitize-accented-uploads/blob/master/plugin.php#L152
        // table source: http://www.i18nqa.com/debug/utf8-debug.html
        
        $fix_list = array(
            // 3 char errors first
            'â€š' => '‚', 'â€ž' => '„', 'â€¦' => '…', 'â€¡' => '‡',
            'â€°' => '‰', 'â€¹' => '‹', 'â€˜' => '‘', 'â€™' => '’',
            'â€œ' => '“', 'â€¢' => '•', 'â€“' => '–', 'â€”' => '—',
            'â„¢' => '™', 'â€º' => '›', 'â‚¬' => '€',
            // 2 char errors
            'Ã‚'  => 'Â', 'Æ’'  => 'ƒ', 'Ãƒ'  => 'Ã', 'Ã„'  => 'Ä',
            'Ã…'  => 'Å', 'â€'  => '†', 'Ã†'  => 'Æ', 'Ã‡'  => 'Ç',
            'Ë†'  => 'ˆ', 'Ãˆ'  => 'È', 'Ã‰'  => 'É', 'ÃŠ'  => 'Ê',
            'Ã‹'  => 'Ë', 'Å’'  => 'Œ', 'ÃŒ'  => 'Ì', 'Å½'  => 'Ž',
            'ÃŽ'  => 'Î', 'Ã‘'  => 'Ñ', 'Ã’'  => 'Ò', 'Ã“'  => 'Ó',
            'â€'  => '”', 'Ã”'  => 'Ô', 'Ã•'  => 'Õ', 'Ã–'  => 'Ö',
            'Ã—'  => '×', 'Ëœ'  => '˜', 'Ã˜'  => 'Ø', 'Ã™'  => 'Ù',
            'Å¡'  => 'š', 'Ãš'  => 'Ú', 'Ã›'  => 'Û', 'Å“'  => 'œ',
            'Ãœ'  => 'Ü', 'Å¾'  => 'ž', 'Ãž'  => 'Þ', 'Å¸'  => 'Ÿ',
            'ÃŸ'  => 'ß', 'Â¡'  => '¡', 'Ã¡'  => 'á', 'Â¢'  => '¢',
            'Ã¢'  => 'â', 'Â£'  => '£', 'Ã£'  => 'ã', 'Â¤'  => '¤',
            'Ã¤'  => 'ä', 'Â¥'  => '¥', 'Ã¥'  => 'å', 'Â¦'  => '¦',
            'Ã¦'  => 'æ', 'Â§'  => '§', 'Ã§'  => 'ç', 'Â¨'  => '¨',
            'Ã¨'  => 'è', 'Â©'  => '©', 'Ã©'  => 'é', 'Âª'  => 'ª',
            'Ãª'  => 'ê', 'Â«'  => '«', 'Ã«'  => 'ë', 'Â¬'  => '¬',
            'Ã¬'  => 'ì', 'Â®'  => '®', 'Ã®'  => 'î', 'Â¯'  => '¯',
            'Ã¯'  => 'ï', 'Â°'  => '°', 'Ã°'  => 'ð', 'Â±'  => '±',
            'Ã±'  => 'ñ', 'Â²'  => '²', 'Ã²'  => 'ò', 'Â³'  => '³',
            'Ã³'  => 'ó', 'Â´'  => '´', 'Ã´'  => 'ô', 'Âµ'  => 'µ',
            'Ãµ'  => 'õ', 'Â¶'  => '¶', 'Ã¶'  => 'ö', 'Â·'  => '·',
            'Ã·'  => '÷', 'Â¸'  => '¸', 'Ã¸'  => 'ø', 'Â¹'  => '¹',
            'Ã¹'  => 'ù', 'Âº'  => 'º', 'Ãº'  => 'ú', 'Â»'  => '»',
            'Ã»'  => 'û', 'Â¼'  => '¼', 'Ã¼'  => 'ü', 'Â½'  => '½',
            'Ã½'  => 'ý', 'Â¾'  => '¾', 'Ã¾'  => 'þ', 'Â¿'  => '¿',
            'Ã¿'  => 'ÿ', 'Ã€'  => 'À',
            // 1 char errors last
            'Ã' => 'Á', 'Å' => 'Š', 'Ã' => 'Í', 'Ã' => 'Ï',
            'Ã' => 'Ð', 'Ã' => 'Ý', 'Ã' => 'à', 'Ã­' => 'í', ' ' => ' '
        );
    
        $error_chars = array_keys($fix_list);
        $real_chars  = array_values($fix_list);     

        return str_replace($error_chars, $real_chars, $inputString);
}

/*function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}
*/

/*function ddY(){
        for($i=2014;$i<=date('Y', strtotime('+1 year'));$i++)
        $arr[] = $i;
        $arr = array_reverse($arr);
        foreach($arr as $year){
         if($year == date('Y')) {
         echo '<option value="'.$year.'" selected="selected">'.$year.'</option>';

         } else {
            echo '<option value="'.$year.'">'.$year.'</option>';
        }



        //echo'<option value="'.$year.'">'.$year.'</option>';
    }
}//ddY
*/
?>
