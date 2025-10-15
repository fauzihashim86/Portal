<?php
if(session_id() == '') {
  session_name("2620368ghwahw90w");
  session_start();
}
if(isset($_POST['func'])) {
  $function = $_POST['func'];
  if($function == "changeView") {
    changeView($_POST['sys']);
  } else if($function == "changeTime") {
    timeline($_POST['year'],$_POST['month']);
  } else if($function == "updateStatus") {
    updateStatus($_POST['task'],$_POST['id'],$_POST['status']);
  } else if($function == "searchDirectory") {
    searchDirectory($_POST['keyword'],$_POST['dept'],$_POST['lvl'],$_POST['admin']);
  } else if($function == "systemDetail") {
    systemDetail($_POST['keyword']);
  } else if($function == "systemExtention") {
    systemExtention($_POST['keyword']);
  } else if($function == "saveDetail") {
    saveDetail($_POST['id'],$_POST['title'],$_POST['desc'],$_POST['db'],$_POST['url']);
  } else if($function == "saveExtention") {
    saveExtention($_POST['id'],$_POST['name'],$_POST['code'],$_POST['mobile'],$_POST['level']);
  } else if($function == "systemForms") {
    systemForms($_POST['keyword']);
  } else if($function == "systemform") {
    systemForm($_POST['keyword']);
  } else if($function == "systemdms") {
    systemDms($_POST['keyword']);
  } else if($function == "notify") {
    notify();
  } else if($function == "notis") {
    echo notify("display");
  } else if($function == "filter") {
    filter($_POST['level'],$_POST['department']);
  } else if($function == "changelog") {
    changeLog($_POST['sys']);
  } else if($function == "checkNotify") {
    checkNotify();
  } else if($function == "admin") {
    admin($_POST['id'],$_POST['page']);
  } else if($function == "uploadDoc") {
    uploadDoc($_FILES['file']);
  } else if($function == "uploadImg") {
    uploadImg($_FILES['file']);
  } else if($function == "deleteDoc") {
    deleteDoc($_POST['id']);
  } else if($function == "pdf") {
    showPDF($_POST['id'],$_POST['code'],$_POST['type'],$_POST['user'],$_POST['asset'],$_POST['title']);
  }
}
function executeQuery($query,$db=null,$trail=0) {
  require "setup.php";
  if($db != null) {
    $dbname = $db;
  }
  try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    if($trail == 0) {
      keepTrack($query);
    }
    $result = $pdo->query($query);
    $pdo = null;
    return $result;

  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}
function escapeString($string) {
  require "setup.php";
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
  // return $pdo->real_escape_string($string);
  // return $pdo->quote($string);
  return $string;
}
function verifyToken($expires, $content) {
    $APP_SECRET_KEY = 'e-central';
    $tokenData = array('expires' => $expires, 'content' => $content, 'ip' => $_SERVER['REMOTE_ADDR']);
    $serialized = json_encode($tokenData);
    return hash_hmac('sha256',$serialized, $APP_SECRET_KEY);
}
function encryptIt($string) {
  return base64_encode($string);//mcrypt_encrypt(MCRYPT_RIJNDAEL_256,"dms",$string, MCRYPT_MODE_ECB);
}
function encrypt($string,$secret_key) {
  $output = false;
  $encrypt_method = "AES-256-CBC";
  $secret_iv = 'e-central';
  $key = hash('sha256', $secret_key);
  $iv = substr(hash('sha256', $secret_iv), 0, 16);
  $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
  $output = base64_encode($output);
  return $output;
}
function displayDate($date,$format=null) {
  if(!$date || $date == "0000:00:00 00:00") {
    return "<b>N/A</b>";
  } else {
    if($format == null) {
      return date("d-m-y H:i:s",strtotime($date));
    } else {
      return date($format,strtotime($date));
    }
  }
}
function sendMail(){
  $to = array();
  $email = executeQuery("SELECT user_email FROM itadmin.user WHERE (user_team LIKE '%SUPPORT%' || user_team LIKE '%ADMIN%') AND user_notify='YES'");
  while($emails = $email->fetch()){
    array_push($to,$emails['user_email']);
  }
  $result = executeQuery("SELECT idrf_id,idrf_email,idrf_name,idrf_loan_date,idrf_loan_info,idrf_loan_extend FROM uid_system.idrequest_form
  WHERE idrf_loan_sn IS NOT NULL AND idrf_loan_return IS NULL AND (idrf_loan_email IS NULL OR idrf_loan_email != DATE(NOW()))
  AND idrf_loan_date < DATE(NOW())");
  while($row = $result->fetch()){
    $id = $row['idrf_id'];
    $ext = explode("|",$row["idrf_loan_info"]);
    $days = str_replace("s","",$row["idrf_loan_info"]);
    $date = explode(" ",$days);
    $d = $date[0]-1;
    $days = $d." ".$date[1];
    if(sizeof($ext) > 1){
      $days = str_replace("s","",$ext[0]);
      $day = date('Y-m-d', strtotime($row["idrf_loan_date"].'+'.$days));
    } else {
      $day = date('Y-m-d', strtotime($row["idrf_loan_date"].'+'.$row["idrf_loan_info"]));
    }

    if($row["idrf_loan_extend"] != NULL){
      $total = 0;
      $extend = explode(",",$row["idrf_loan_extend"]);
      foreach ($extend as $key => $value) {
        $total += $value;
      }
      $total = $total-1;
      $day = date('Y-m-d', strtotime($day.'+'.$total." day"));
    }
    if(date('Y-m-d')>=$day){
      $Headers = "From: IT Portal - Loan IT Equipment \n";
      $Headers .= "Reply-To: IT PORTAL\n";
      $Headers .= "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=UTF-8 \r\n";
      $Headers .= 'BCC: '.implode(",", $to)."\r\n";

      $message_header = '<html>
      <head>
      <title>IT Portal: Loan IT Equipment</title>
      </head>
      <body style="font:12px Arial;">';

      $message_footer = '----------------------------------------------------------------------------------<br>
      This is an auto notification from System. Please do not reply.
      </body>
      </html>';

      $Subject = 'Loan IT Equipment Reminder';

      $msg = '<p>Dear '.$row['idrf_name'].'</p>
  	  <p>This is an email to remind you that your loan IT equipment is overdue.<p>
  	  <p>Please return the equipment to IT Department or login into <a href="http://apps.mtdgroup.com.my/itportal/">IT PORTAL</a> to extend your loan</p>
      <p>Please ignore this email if you have returned the equipment</p>
  	  <p>Thanks & Regards.</p>';
  		$content = $message_header.$msg.$message_footer;
      mail($row['idrf_email'],$Subject,$content,$Headers);
      executeQuery("UPDATE uid_system.idrequest_form SET idrf_loan_email=NOW() WHERE idrf_id=$id");
    } else {
      executeQuery("UPDATE uid_system.idrequest_form SET idrf_loan_email=NOW() WHERE idrf_id=$id");
    }
  }
}
function admin($id,$page=null) {
  if($id == 1) {
    ?>
    <div class="row">
      <div class="form-group col-4">
    		<label>Staff Name</label>
    		<input class="form-control" placeholder="Staff Name" type="search" id="searchTxt">
    	</div>
      <div class="form-group col-4">
    		<label>Department</label>
        <select class="form-control" style="width: 100%;" id="searchSelect">
          <option value="0">-- Please Select --</option>
    			<?php selectDepartDivision();?>
        </select>
    	</div>
      <div class="form-group col-4">
    		<label>Level/Area</label>
        <select class="form-control" style="width: 100%;" id="levelSelect">
          <option value="0">-- Please Select --</option>
    			<?php selectLevel();?>
        </select>
    	</div>
      <div class="col-12">
        <button id="search" class="btn btn-primary pull-right" onclick="searchDirectory('true')"><i class="fa fa-search"></i> Search</button>
      </div>
      <div class="col-12">&nbsp;</div>
      <hr>
      <div class="col-12" id="searchDirectory"></div>
    </div>
    <?php
  } else if($id == 2) {
    $rc = $_POST['page'];
    ?>
    <table class="data-table table stripe hover nowrap">
      <thead>
        <tr class="text-center">
          <th class="table-plus datatable-nosort">#</th>
          <th class="datatable-nosort">Action</th>
          <th>Category</th>
          <th>Name</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $count = 1;
          $result = executeQuery("SELECT * FROM policy
                    JOIN ref_category ON rc_id=p_rc_id
                    ORDER BY p_rc_id ASC");
          while($row = $result->fetch()){
            if(!$row['p_filename']) {
              getSub($row['p_id'],$row['p_desc'],$count++);
            } else {
        ?>
          <tr class="text-center">
            <td><?php echo $count++;?></td>
            <td>
              <div class="dropdown">
                <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                  <i class="dw dw-more"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                  <a class="dropdown-item" href="upload/<?php echo $row['p_path'];?>/<?php echo $row['p_filename'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
                  <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('policy',<?php echo $row['p_id'];?>)"><i class="dw dw-edit2"></i> Edit</a>
                </div>
              </div>
            </td>
            <td class="text-left"><?php echo $row['rc_desc'];?></td>
            <td class="text-left"><?php echo $row['p_desc'];?></td>
            <td>
              <?php
                $class = "";
                if($row['p_active'] == 1) {
                  $class = "checked";
                }
              ?><input type="checkbox" id="check<?php echo $row['p_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#0059b2" data-size="small"></td>
            </td>
          </tr>
        <?php }}?>
      </tbody>
    </table>
    <?php
  } else if($id == 3) {
    $rc = $_POST['page'];
    ?>
    <div class="row">
      <div class="col-12 text-right">
        <button class="btn btn-primary" onclick="showModal('form')"><i class="fa fa-plus"></i> New</button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">&nbsp;</div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="data-table table stripe hover nowrap">
          <thead>
            <tr class="text-center">
              <th class="table-plus datatable-nosort">#</th>
              <th class="datatable-nosort">Action</th>
              <th>Category</th>
              <th>Description</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM forms
                      JOIN ref_category ON rc_id=f_rc_id
                      ORDER BY f_rc_id ASC");
            while($row = $result->fetch()){
           ?>
           <tr class="text-center">
                <td><?php echo $count++;?></td>
                <td>
                  <div class="dropdown">
                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                      <i class="dw dw-more"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                      <a class="dropdown-item" href="upload/<?php echo $row['f_path'];?>/<?php echo $row['f_filename'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
                      <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('form',<?php echo $row['f_id'];?>,'Edit Form')"><i class="dw dw-edit2"></i> Edit</a>
                    </div>
                  </div>
                </td>
                <td class="text-left"><?php echo $row['rc_desc'];?></td>
                <td class="text-left"><?php echo $row['f_desc'];?></td>
                <td>
                  <?php
                    $class = "";
                    if($row['f_active'] == 1) {
                      $class = "checked";
                    }
                  ?><input type="checkbox" id="check<?php echo $row['f_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#0059b2" data-size="small"></td>
                </td>
              </tr>
           <?php }?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
  } else if($id == 4) {
    ?>
    <div class="row">
      <div class="col-12 text-right">
        <button class="btn btn-primary" onclick="showModal('dms')"><i class="fa fa-plus"></i> New</button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">&nbsp;</div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="data-table table stripe hover nowrap">
          <thead>
            <tr class="text-center">
              <th class="table-plus datatable-nosort">#</th>
              <th class="datatable-nosort">&nbsp;</th>
              <th>Division/Department</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $count = 1;
              $result = executeQuery("SELECT * FROM dms WHERE dms_status=1");
              while($row = $result->fetch()){
                ?>
                <tr class="text-center">
                  <td><?php echo $count++;?></td>
                  <td>
                    <div class="dropdown">
                      <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <i class="dw dw-more"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                        <a class="dropdown-item" href="<?php echo $row['dms_url'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
                        <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('dms',<?php echo $row['dms_id'];?>)"><i class="dw dw-edit2"></i> Edit</a>
                      </div>
                    </div>
                  </td>
                  <td class="text-left"><?php echo $row['dms_desc'];?></td>
                  <td>
                    <?php
                      $class = "";
                      if($row['dms_status'] == 1) {
                        $class = "checked";
                      }
                    ?><input type="checkbox" id="check<?php echo $row['dms_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#0059b2" data-size="small"></td>
                  </td>
                </tr>
                <?php }?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
  } else if($id == 5) {
    ?>
    <div class="row">
      <div class="col-4 form-group">
  			<label>Upload Document</label>
  			<div class="custom-file">
  				<input type="file" class="custom-file-input" id="attachment" onchange="uploadDoc()">
  				<label class="custom-file-label">Choose file</label>
          <p><sub>* Allowed filetypes: .pdf  <br>* Maximum file size: 5MB</sub></p>
  			</div>
  		</div>
    </div>
    <div class="row">
      <div class="col-12" id="page-content">
        <?php newsletter();?>
      </div>
    </div>
    <?php
  }
}
function uploadDoc($files){
  if(isset($files)){
    $target_dir = "../upload/newsletter";
    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
    $attach_name = overwrite(basename($files['name']));
    $file_name = basename($files['name']);
    $target_file = $target_dir."/".$attach_name;
    if(move_uploaded_file($files["tmp_name"], $target_file)){
      executeQuery("INSERT INTO newsletter(news_attach)VALUES('$attach_name')");
      newsletter();
    }
  }
}
function uploadImg($files){
  if(isset($files)){
    $target_dir = "../upload/profile";
    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
    $attach_name = overwrite(basename($files['name']));
    $file_name = basename($files['name']);
    $target_file = $target_dir."/".$attach_name;
    if(move_uploaded_file($files["tmp_name"], $target_file)){
      $id = $_SESSION["login"]["id"];
      $result = executeQuery("SELECT * FROM profile WHERE profile_user_id=$id");
      if($result->rowCount() > 0){
        $row = $result->fetch();
        $url = $target_dir."/".$row['profile_attach'];
        unlink($url);
        executeQuery("UPDATE profile SET profile_attach='$attach_name' WHERE profile_user_id=$id");
      } else {
        executeQuery("INSERT INTO profile(profile_user_id,profile_attach)VALUES($id,'$attach_name')");
      }
      echo $attach_name;
    }
  }
}
function profile(){
  $id = $_SESSION["login"]["id"];
  $result = executeQuery("SELECT profile_attach FROM profile WHERE profile_user_id=$id");
  $row = $result->fetch();
  if(!$row['profile_attach']){
    return "default.png";
  } else {
    return $row['profile_attach'];
  }
}
function deleteDoc($id){
  $result = executeQuery("SELECT news_attach FROM newsletter WHERE news_id=$id");
  $row = $result->fetch();
  $target_dir = "../upload/newsletter/".$row['news_attach'];
  if(unlink($target_dir)){
    executeQuery("DELETE FROM newsletter WHERE news_id=$id");
    newsletter();
  }
}
function showPDF($id=null,$code=null,$type=null,$user=null,$asset=null,$title=null){
  $url = "idrf_form_louai.php";
  if($title == "ID Request Form" || $title == "Loan IT Equipment Form"){
    $url = "idrf_form_idrequest.php";
  } else if($title == "System Access Request Form"){
    $url = "idrf_form_apprequest.php";
  }
  $set = "?id=".$id."&code=".$code."&type=".$type."&username=".$user;
  if($asset != null){
    $url = "idrf_form_itasset.php";
    $set .= "&pc_assetcode=".$asset;
  }
  $url = $url.$set;
  if($title == "IT Facilities Request Form"){
    $url = "it_form.php?id=".$id;
  }
  ?><iframe src="http://apps.mtdgroup.com.my/itportal/<?php echo $url;?>" style="padding:0px; width:100%; height:500px; border:none; overflow-x:scroll; overflow-y:scroll;"></iframe><?php
}
function newsletter(){
  ?>
  <table class="data-table table stripe hover nowrap">
    <thead>
      <tr class="text-center">
        <th class="table-plus datatable-nosort">#</th>
        <th class="datatable-nosort">Action</th>
        <th>Name</th>
        <th>Description</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $count = 1;
        $result = executeQuery("SELECT * FROM newsletter");
        while($row = $result->fetch()){
      ?>
        <tr class="text-center">
          <td><?php echo $count++;?></td>
          <td>
            <div class="dropdown">
              <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                <i class="dw dw-more"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                <a class="dropdown-item" href="upload/newsletter/<?php echo $row['news_attach'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
                <a class="dropdown-item" href="#" onclick="deleteDoc(<?php echo $row['news_id'];?>)"><i class="dw dw-remove"></i> Delete</a>
              </div>
            </div>
          </td>
          <td class="text-left">IT</td>
          <td class="text-left">Change Management Form</td>
          <td><input type="checkbox" id="check_<?php echo $row['news_id'];?>" <?php if($row['news_status'] == 1){ echo "checked";}?> class="switch-btn" data-color="#0059b2" data-size="small"></td>
        </tr>
      <?php }?>
    </tbody>
  </table>
  <?php
}
function getExtension($name) {
  $ext = pathinfo($name, PATHINFO_EXTENSION);
  return $ext;
}
function overwrite($name) {
  $ext = getExtension($name);
  date_default_timezone_set("asia/kuala_lumpur");
  $datetime = date("dmYhis");
  return $datetime.".".$ext;
}
function getSub($id,$desc,$count) {
  $result = executeQuery("SELECT * FROM policy
            -- JOIN ref_category ON rc_id=p_rc_id
            WHERE p_rc_id=$id
            ORDER BY p_sort ASC");
  while($row = $result->fetch()){
    if(!$row['p_filename']) {
      getSub($row['p_id'],$row['p_desc'],$count++);
    } else {
?>
  <tr class="text-center">
    <td><?php echo $count++;?></td>
    <td>
      <div class="dropdown">
        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
          <i class="dw dw-more"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
          <a class="dropdown-item" href="upload/<?php echo $row['p_path'];?>/<?php echo $row['p_filename'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
          <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('policy',<?php echo $row['p_id'];?>)"><i class="dw dw-edit2"></i> Edit</a>
        </div>
      </div>
    </td>
    <td class="text-left"><?php echo $desc;?></td>
    <td class="text-left"><?php echo $row['p_desc'];?></td>
    <td>
      <?php
        $class = "";
        if($row['p_active'] == 1) {
          $class = "checked";
        }
      ?><input type="checkbox" id="check<?php echo $row['p_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#0059b2" data-size="small"></td>
    </td>
  </tr>
<?php }}
}
function changeLog($sys) {
  if($sys == "staff") {
    ?>
    <div class="input-group custom">
      <input type="text" id="user" class="form-control form-control-lg" placeholder="User ID">
      <div class="input-group-append custom">
        <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
      </div>
    </div>
    <div class="input-group custom">
      <input type="password" id="pass" class="form-control form-control-lg" placeholder="**********">
      <div class="input-group-append custom">
        <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="input-group mb-0">
          <button id="login" class="btn btn-primary btn-lg btn-block" onclick="auth()">Login</button>
        </div>
      </div>
    </div>
    <?php
  } else {
    ?>
    <div class="input-group custom">
      <input type="email" id="email" class="form-control form-control-lg" placeholder="Email">
      <div class="input-group-append custom">
        <span class="input-group-text"><i class="icon-copy dw dw-mail"></i></span>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="input-group mb-0">
          <button id="login" class="btn btn-primary btn-lg btn-block" onclick="log()">Login</button>
        </div>
      </div>
    </div>
    <?php
  }
}
function filter($lvl,$dept){
  $level = implode(",",$lvl);
  $department = implode(",",$dept);
  if($level != "" && !$department) {
    $que = " WHERE emp_level IN($level)";
  } else if($department != "" && !$level) {
    $result = executeQuery("SELECT div_id,dept_name FROM department WHERE dept_id IN($department)","phone_dir");
    $row = $result->fetch();
    if($row['dept_name'] == "ALL") {
      $que = " WHERE division.div_id IN(".$row['div_id'].")";
    }
    $que = " WHERE emp_dept IN($department)";
  } else {
    $que = " WHERE emp_level IN($level) AND emp_dept IN($department)";
  }
  $query = "SELECT emp_id,emp_name,emp_ext,emp_hp,dept_name,div_name,lvl_name FROM phone
            INNER JOIN level ON lvl_id=emp_level
            INNER JOIN department ON dept_id=emp_dept
            INNER JOIN division ON division.div_id=department.div_id";
  $query .= $que;
  $query .= " ORDER BY emp_name";
  // echo $query;
  $count = 1;
  ?>
  <table class="data-table table stripe hover nowrap">
    <thead>
      <tr class="text-center">
        <th class="table-plus datatable-nosort">#</th>
        <th class="datatable-nosort">Action</th>
        <th>Name</th>
        <th>Extension</th>
        <th>Mobile</th>
        <th>Division - Department</th>
        <th>Level</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = executeQuery($query,"phone_dir");
      while($row = $result->fetch()) {
      ?>
        <tr class="text-center">
          <td class="table-plus text-right"><?php echo $count++;?></td>
          <td>
            <div class="dropdown">
              <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                <i class="dw dw-more"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('Extention',<?php echo $row['emp_id'];?>)"><i class="dw dw-edit2"></i> Edit</a>
              </div>
            </div>
          </td>
          <td class="text-left"><?php echo $row['emp_name'];?></td>
          <td><?php echo $row['emp_ext'];?></td>
          <td><?php echo $row['emp_hp'];?></td>
          <td class="text-left"><?php echo $row['div_name']." - ".$row['dept_name'];?></td>
          <td><?php echo $row['lvl_name'];?></td>
        </tr>
      <?php }?>
    </tbody>
  </table>
  <?php
}
function getTable($table=null){
  ?>
  <div class="pd-20 text-right"><button type="button" class="btn btn-success btn-sm" onclick="adminModal('<?php echo $table;?>')"><i class="fa fa-plus"></i> New</button></div>
  <div class="row" style="overflow-x: scroll;">
    <div class="col-12">
    <?php if($table == "system"){?>
      <table class="data-table table stripe hover nowrap">
  			<thead>
  				<tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>System</th>
            <th>Database</th>
            <th>Status</th>
  				</tr>
  			</thead>
  			<tbody>
          <?php
          $result = executeQuery("SELECT * FROM e_central.system");
          $count = 1;
          while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class="table-plus datatable-nosort"><?php echo $count++;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
  									<a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('<?php echo $table;?>',<?php echo $row['sys_id'];?>)">Edit</a>
  									<a class="dropdown-item" target="_blank" href="<?php echo $row['sys_url'];?>">Open</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><?php echo $row['sys_title'];?></td>
              <td class="text-left"><?php echo $row['sys_db'];?></td>
              <td>
                <?php
                  $class = "";
                  if($row['sys_active'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="<?php echo $table;?>_<?php echo $row['sys_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('<?php echo $table;?>',<?php echo $row['sys_id'];?>)">
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php } else if($table == "dms"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Name</th>
            <th>URL</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM e_central.dms ORDER BY dms_desc");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class="table-plus datatable-nosort"><?php echo $count++;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('<?php echo $table;?>',<?php echo $row['dms_id'];?>)">Edit</a>
  									<a class="dropdown-item" target="_blank" href="<?php echo $row['dms_url'];?>">Open</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><?php echo $row['dms_desc'];?></td>
              <td class="text-left"><?php echo $row['dms_url'];?></td>
              <td>
                <?php
                  $class = "";
                  if($row['dms_status'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="<?php echo $table;?>_<?php echo $row['dms_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('<?php echo $table;?>',<?php echo $row['dms_id'];?>)">
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php } else if($table == "policy"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Category</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM e_central.ref_category WHERE rc_type='policy' ORDER BY rc_desc");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class=""><?php echo $count;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('category')">New Sub</a>
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('policy',<?php echo $row['rc_id'];?>)">New Policy</a>
  									<a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('category',<?php echo $row['rc_id'];?>)">Edit</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><b><?php echo $row['rc_desc'];?></b></td>
              <td>
                <?php
                  $class = "";
                  if($row['rc_active'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="category_<?php echo $row['rc_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('category',<?php echo $row['rc_id'];?>)">
              </td>
            </tr>
          <?php subTR($count++,$row['rc_id']);}?>
        </tbody>
      </table>
    <?php } if($table == "form"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Category</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM e_central.ref_category WHERE rc_type='form' ORDER BY rc_desc");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class=""><?php echo $count;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('category')">New Sub</a>
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('form',<?php echo $row['rc_id'];?>)">New Policy</a>
  									<a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('category',<?php echo $row['rc_id'];?>)">Edit</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><b><?php echo $row['rc_desc'];?></b></td>
              <td>
                <?php
                  $class = "";
                  if($row['rc_active'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="category_<?php echo $row['rc_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('category',<?php echo $row['rc_id'];?>)">
              </td>
            </tr>
          <?php subTR($count++,$row['rc_id']);
        }?>
        </tbody>
      </table>
    <?php } else if($table == "phone"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Name</th>
            <th>Extension</th>
            <th>Mobile</th>
            <th>Division - Department</th>
            <th>Level/Area</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT emp_id,emp_name,emp_ext,emp_hp,emp_status,dept_name,div_name,lvl_name FROM phone_dir.phone INNER JOIN phone_dir.level ON lvl_id=emp_level INNER JOIN phone_dir.department ON dept_id=emp_dept INNER JOIN phone_dir.division ON division.div_id=department.div_id ORDER BY emp_name");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class="table-plus datatable-nosort"><?php echo $count++;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('<?php echo $table;?>',<?php echo $row['dms_id'];?>)">Edit</a>
  									<a class="dropdown-item" target="_blank" href="<?php echo $row['dms_url'];?>">Open</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><?php echo $row['emp_name'];?></td>
              <td class="text-left"><?php echo $row['emp_ext'];?></td>
              <td class="text-left"><?php echo $row['emp_hp'];?></td>
              <td class="text-left"><?php echo $row['div_name']."<br>".$row['dept_name'];?></td>
              <td class="text-left"><?php echo $row['lvl_name'];?></td>
              <td>
                <?php
                  $class = "";
                  if($row['emp_status'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="<?php echo $table;?>_<?php echo $row['emp_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('<?php echo $table;?>',<?php echo $row['emp_id'];?>)">
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php } else if($table == "level"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Level</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM phone_dir.level lvl_name");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class="table-plus datatable-nosort"><?php echo $count++;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('<?php echo $table;?>',<?php echo $row['lvl_id'];?>)">Edit</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><?php echo $row['lvl_name'];?></td>
              <td>
                <?php
                  $class = "";
                  if($row['lvl_status'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="<?php echo $table;?>_<?php echo $row['lvl_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('<?php echo $table;?>',<?php echo $row['lvl_id'];?>)">
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php } else if($table == "division"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Division</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM phone_dir.division ORDER BY div_name");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class="table-plus datatable-nosort"><?php echo $count++;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('<?php echo $table;?>',<?php echo $row['div_id'];?>)">Edit</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><?php echo $row['div_name'];?></td>
              <td>
                <?php
                  $class = "";
                  if($row['div_status'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="<?php echo $table;?>_<?php echo $row['div_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('<?php echo $table;?>',<?php echo $row['div_id'];?>)">
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php } else if($table == "department"){?>
      <table class="data-table table stripe hover nowrap">
        <thead>
          <tr>
            <th class="table-plus datatable-nosort" width="1%">No</th>
            <th class="datatable-nosort" width="10%">&nbsp;</th>
            <th>Division</th>
            <th>Department</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $count = 1;
            $result = executeQuery("SELECT * FROM phone_dir.department INNER JOIN phone_dir.division ON division.div_id=department.div_id ORDER BY div_name");
            while($row = $result->fetch()){
          ?>
            <tr class="text-center">
              <td class="table-plus datatable-nosort"><?php echo $count++;?></td>
              <td>
                <div class="dropdown">
  								<a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
  								<div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" style="cursor:pointer;" onclick="adminModal('<?php echo $table;?>',<?php echo $row['dept_id'];?>)">Edit</a>
  								</div>
  							</div>
              </td>
              <td class="text-left"><?php echo $row['div_name'];?></td>
              <td class="text-left"><?php echo $row['dept_name'];?></td>
              <td>
                <?php
                  $class = "";
                  if($row['dept_status'] == 1) {
                    $class = "checked";
                  }
                ?><input type="checkbox" id="<?php echo $table;?>_<?php echo $row['dept_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('<?php echo $table;?>',<?php echo $row['dept_id'];?>)">
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    <?php }?>
    </div>
  </div>
  <?php
}
function subTR($count,$id){
  $c = 1;
  $result = executeQuery("SELECT * FROM e_central.policy WHERE p_rc_id=$id ORDER BY p_sort");
  if($result->rowCount()> 0){
    while($row = $result->fetch()){
      ?>
      <tr class="text-center">
        <td class=""><p style="display:none"><?php echo $count?></p></td>
        <td>
          <div class="dropdown">
            <a class="btn btn-sm btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">Action</a>
            <div class="dropdown-menu dropdown-menu-right">
              <?php if(!$row['p_filename']){?>
                <a class="dropdown-item" href="#">New Sub</a>
                <a class="dropdown-item" href="#">New Policy</a>
                <a class="dropdown-item" href="#">Edit</a>
              <?php } else {?>
                <a class="dropdown-item" href="#">Edit</a>
              <?php }?>
            </div>
          </div>
        </td>
        <td class="text-left"><?php if(!$row['p_filename']){ echo "<p class='text-green'><b>".$row['p_desc']." <i class='fa fa-arrow-down'></i></b></p>";} else { echo "<a href='".$row['p_path']."/".$row['p_filename']."' target='_blank' class='text-blue'>".$row['p_desc']."</a>";}?></td>
        <td>
          <?php
            $class = "";
            if($row['p_active'] == 1) {
              $class = "checked";
            }
          ?><input type="checkbox" id="policy_<?php echo $row['p_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#28a745" data-secondary-color="red" data-size="small" onchange="updateStatus('policy',<?php echo $row['p_id'];?>)">
        </td>
      </tr>
      <?php
      if(!$row['p_filename']){ subTR($count,$row['p_id']);}
    }
  }
}
function access($site=null){
  $query = "";
  $user = $_SESSION["login"]["username"];
  $id = $_SESSION["login"]["id"];
  if($site == "IT Admin" || $site == "CRF System"){
    $query = "SELECT user_id FROM itadmin.user WHERE user_name='$user'";
  } else if($site == "User ID (Toll System)"){
    $query = "SELECT access_id FROM badge.access WHERE access_user_id=$id";
  } else if($site == "Pavement Management System"){
    $query = "SELECT access_id FROM pavement.access WHERE access_user_id=$id";
  } else if($site == "eCMR"){
    $query = "SELECT authorised_id FROM cmr.authorised WHERE staff_id='$user'";
  } else if($site == "e-Travel"){
    $query = "SELECT staff_id FROM etravel_db.staff WHERE staff_id='$user'";
  } else if($site == "e-Meeting"){
    $query = "SELECT member_id FROM emeeting_v2.user WHERE status='Enable' AND username LIKE BINARY '$user'";
  } else if($site == "e-spa"){
    $query = "SELECT usr.id FROM eleave_v4.user u
              INNER JOIN eleave_v4.employee e ON u.id=e.user_id
              INNER JOIN espa.user usr ON (usr.staff_id=e.employee_id OR usr.staff_no=e.employee_ic_no OR usr.staff_name=e.employee_name)
              WHERE e.status='Active' AND  u.username LIKE BINARY '$user'";
  }
  if(!$query){
    return 1;
  } else {
    $result = executeQuery($query);
    return $result->rowCount();
  }
}
function ITUser() {
  $result = executeQuery("SELECT user_id FROM user WHERE user_name='".$_SESSION["login"]["username"]."'","itadmin");
  return $result->rowCount();
}
function tollUser(){
  $result = executeQuery("SELECT access_id FROM access WHERE access_user_id='".$_SESSION["login"]["id"]."'","badge");
  return $result->rowCount();
}
function countToll(){
  $result = executeQuery("SELECT access_task_id FROM access WHERE access_user_id=".$_SESSION["login"]["id"],"badge");
  $row = $result->fetch();
  $access = explode(",",$row['access_task_id']);
  $sta = array();
  if(in_array(2,$access)){
    array_push($sta,1);
  }
  if(in_array(3,$access)){
    array_push($sta,3);
  }
  if(in_array(4,$access)){
    array_push($sta,5);
  }
  if(in_array(5,$access)){
    array_push($sta,8);
  }
  if(in_array(6,$access)){
    array_push($sta,9);
  }
  if(in_array(7,$access)){
    array_push($sta,10);
  }
  if(in_array(8,$access)){
    array_push($sta,13);
  }
  $status = implode(",",$sta);
  if(!$sta) {
    return 0;
  } else {
    $result = executeQuery("SELECT ref_id FROM reference WHERE ref_status_id IN(".$status.") AND ref_acknowledge_by IS NULL","badge");
    return $result->rowCount();
  }
}
function eCMRUser() {
  $result = executeQuery("SELECT authorised_id FROM authorised WHERE staff_id='".$_SESSION["login"]["username"]."'","cmr");
  return $result->rowCount();
}
function travelUser() {
  $result = executeQuery("SELECT staff_id FROM staff WHERE staff_id='".$_SESSION["login"]["username"]."'","etravel_db");
  return $result->rowCount();
}
function meetUser() {
  $result = executeQuery("SELECT member_id FROM user WHERE status='Enable' AND username LIKE BINARY'".$_SESSION["login"]["username"]."'","emeeting_v2");
  return $result->rowCount();
}
function crfUser() {
  $result = executeQuery("SELECT user_id FROM user WHERE user_name='".$_SESSION["login"]["username"]."'","itadmin");
  return $result->rowCount();
}
function spa() {
  $result = executeQuery("SELECT usr.id FROM eleave_v4.user u
            INNER JOIN eleave_v4.employee e ON u.id=e.user_id
            INNER JOIN espa.user usr ON (usr.staff_id=e.employee_id OR usr.staff_no=e.employee_ic_no OR usr.staff_name=e.employee_name)
            WHERE e.status='Active' AND  u.username LIKE BINARY '".$_SESSION["login"]["username"]."'");
  return $result->rowCount();
}
function showMenu($session_user=null) {
  $count = 1;
  $result = executeQuery("SELECT user_team FROM itadmin.user WHERE user_name='$session_user'");
  $row = $result->fetch();
  $query = "SELECT menu_id,menu_icon,menu_name,menu_url FROM menu WHERE menu_active=1 AND menu_id != 4 ORDER BY menu_sort ASC";
  if($row['user_team'] == "DEVELOPMENT") {
    $query = "SELECT menu_id,menu_icon,menu_name,menu_url FROM menu WHERE menu_active=1 ORDER BY menu_sort ASC";
  } else if($_SESSION["login"]["level"] == "GUEST"){
    $query = "SELECT menu_id,menu_icon,menu_name,menu_url FROM menu WHERE menu_active=1 AND menu_guest=1 ORDER BY menu_sort ASC";
  }
  $result = executeQuery($query);

  while($row = $result->fetch()) {
    $results = executeQuery("SELECT sm_id,sm_sm_id,sm_name,sm_icon FROM submenu WHERE sm_active=1 AND sm_menu_id=".$row['menu_id']." ORDER BY sm_sort ASC");
    if($results->rowCount() == 0) {
      $class = "dropdown-toggle no-arrow";
      if(isset($_GET['id']) && ($_GET['id'] == $row['menu_id'])) {
        $class = "dropdown-toggle no-arrow active";
      }
      if($row['menu_name'] == "Log Out") {
        $url = $row['menu_url'];
      } else {
        $url = "./?id=".$row['menu_id'];
      }
  ?>
  <li>
    <a href="<?php echo $url;?>" class="<?php echo $class;?>">
      <span class="micon <?php echo $row['menu_icon'];?>"></span><span class="mtext"><?php echo $row['menu_name'];?></span>
    </a>
  </li>
  <?php
    } else {
      ?>
      <li class="dropdown">
        <a href="javascript:;" class="dropdown-toggle">
          <span class="micon <?php echo $row['menu_icon'];?>"></span><span class="mtext"><?php echo $row['menu_name'];?></span>
        </a>
        <ul class="submenu">
          <?php
          while($rows = $results->fetch()) {
            $subclass = "";
            if(isset($_GET['sub']) && ($_GET['sub'] == $rows['sm_id'])) {
              $subclass = "class='active'";
            }
            $resultss = executeQuery("SELECT sm_id,sm_sm_id,sm_name FROM submenu WHERE sm_active=1 AND sm_sm_id=".$rows['sm_id']." ORDER BY sm_sort ASC");
            if($resultss->rowCount() > 0) {
          ?>
            <li class="dropdown">
              <a href="javascript:;" class="dropdown-toggle">
                <span class="micon <?php echo $rows['sm_icon'];?>"></span><span class="mtext"><?php echo $rows['sm_name'];?></span>
              </a>
              <ul class="submenu child">
                <?php while($rowss = $resultss->fetch()) {?>
                  <li><a href="./?sub=<?php echo $rowss['sm_id'];?>" <?php echo $subclass;?>><?php echo $rowss['sm_name'];?></a></li>
                <?php }?>
              </ul>
            </li>
            <?php
          } else if($rows['sm_sm_id'] == NULL) {
          ?>
            <li><a href="./?sub=<?php echo $rows['sm_id'];?>" <?php echo $subclass;?>><?php echo $rows['sm_name'];?></a></li>
          <?php }
          }
          ?>
        </ul>
      </li>
      <?php
    }
  }
}
function employee_detail($session_user) {
  $query = "SELECT *,(SELECT supervisor_name FROM eleave_v4.supervisor WHERE supervisor.supervisor_id=employee.supervisor_id) AS supervisorName,
          (SELECT supervisor_name FROM eleave_v4.supervisor WHERE supervisor.supervisor_id=employee.immediate_supid) AS immediateName,
			    (SELECT grp_name FROM eleave_v4.group_comp where id=employee_grpcompany)AS comp,
          (SELECT s.job_grade FROM eleave_v4.status s INNER JOIN eleave_v4.entitlement e ON s.entitle_id=e.id WHERE s.employee_id=employee.employee_id) AS grade FROM user
          INNER JOIN employee ON user.id=employee.user_id
          INNER JOIN company ON employee.employee_company=company.id
          WHERE user.username LIKE '%$session_user'";
  $result = executeQuery($query,'eleave_v4');
  return $result->fetch();
}
function changeView($sys=0) {
  $result = systemList("sys_id=".$sys);
  $row = $result->fetch();
  $expires = time() + ($_SESSION['token_duration']*60*1000);
  $token = verifyToken($expires,$row['sys_title']);
  ?>
  <input type="hidden" id="sysID" value="<?php echo $sys;?>">
  <?php if($row['sys_id'] <> 0) {?>
    <div class="col-12 mb-30">
      Click <a onclick="openSite('<?php echo $row['sys_url'];?>secure_login.php?expired=<?php echo $expires;?>&token=<?php echo $token;?>&u=<?php echo encrypt($_SESSION['login']['username'],$token);?>&p=<?php echo encrypt($_SESSION["login"]["password"],$token);?>')" style="color:blue;text-decoration-line: underline;cursor:pointer;"><b>here</b></a> to access <?php echo $row['sys_title'];?>
    </div>
    <?php
  }
  if($sys == "0") {
    $result = systemList("sys_active=1");
    if($result->rowCount() > 0) {
      $count = 0;
      while($row = $result->fetch()) {
        $tokens = verifyToken($expires,$row['sys_title']);
        $total = 0;
        if($row['sys_id'] == 1) {
          $total += $leave = countLeave('recommend')+countLeave('approve');
        } else if($row['sys_id'] == 5) {
          $total += getCountNotify("external")+getCountNotify("in-house")+getCountNotify("evaluation")+getCountNotify("effectiveness");
        } else if($row['sys_id'] == 4) {
          $total += approveMovement();
        } else if($row['sys_id'] == 2) {
          $total += eadmin("fleet");
          $total += eadmin("maintainace")+eadmin("maintainace","pre")+eadmin("maintainace","late")+eadmin("maintainace","pre-late")+eadmin("maintainace","confirm");
          $total += eadmin("room")+eadmin("room","pre")+eadmin("room","late")+eadmin("room","pre-late");
          $total += eadmin("sim")+eadmin("sim","pre")+eadmin("sim","late")+eadmin("sim","pre-late");
        } else if($row['sys_id'] == 13) {
          $total += ecmr("new")+ecmr("processed")+ecmr("verify");
        } else if($row['sys_id'] == 7 && ITUser() > 0) {
          $total += ITAdmin("crf",1)+ITAdmin("facility",1)+ITAdmin("id",1)+ITAdmin("storage",1);//IDRequest("recommend")+IDRequest("approve")+IDRequest("acknowledge");
        } else if($row['sys_id'] == 3) {
          $total += ITPortal()+ITPortal("UAT")+ITPortal("REQUEST")+ITPortal("NEWRECOMMEND")+ITPortal("NEWAPPROVE")+ITPortal("TERMINATERECOMMEND")+ITPortal("TERMINATEAPPROVE")+ITPortal("TERMINATEACKNOWLEDGE")+ITPortal("TERMINATE")+ITPortal("ASSETACKNOWLEDGE")+ITPortal("RRECOMMEND")+ITPortal("RAPPROVE");
        } else if($row['sys_id'] == 14 && tollUser() > 0) {
          $total += countToll();
        }
        if($total > 0){
          ?>
            <div class="col-md-3 col-sm-3 mb-30">
      				<div class="card text-white bg-<?php echo $row['sys_color'];?> card-box">
      					<div class="card-header"><?php echo $row['sys_title'];?></div>
      					<div class="card-body">
      						<p class="card-text"><?php echo $total;?> Pending Approval</p>
                  <p class="card-text"><small>
                    Click <a href="<?php echo $row['sys_url'];?>secure_login.php?expired=<?php echo $expires;?>&token=<?php echo $tokens;?>&u=<?php echo encrypt($_SESSION['login']['username'],$tokens);?>&p=<?php echo encrypt($_SESSION["login"]["password"],$tokens);?>" target="_blank" style="color:#FFF;text-decoration-line: underline;"><b>here</b></a> to access <?php echo $row['sys_title'];?>
                  </small></p>
      					</div>
      				</div>
      			</div>
          <?php
          $count += $total;
        }
      }
      ?><input type="hidden" id="dash_count" value="<?php echo $count;?>"><?php
    } else {
      echo "No New Approval";
    }
  } else if($sys == "100") {
    ?>
    <div class="faq-wrap col-12" id="accordion">
      <?php
      $style = "collapse show";
      $class = "btn btn-block";
      if($_SESSION["login"]["level"] == "GUEST"){
        $result = executeQuery("SELECT * FROM ref_category WHERE rc_type='policy' AND rc_active=1 AND rc_id IN(3,4) ORDER BY rc_desc");
      } else {
        $result = executeQuery("SELECT * FROM ref_category WHERE rc_type='policy' AND rc_active=1 ORDER BY rc_desc");
      }
      while($row = $result->fetch()) {
      ?>
      <div class="card">
        <div class="card-header">
          <button class="<?php echo $class;?>" data-toggle="collapse" data-target="#form<?php echo $row['rc_id'];?>">
            <?php echo $row['rc_desc'];?>
          </button>
        </div>
        <div id="form<?php echo $row['rc_id'];?>" class="<?php echo $style;?>" data-parent="#accordion">
          <div class="card-body">
            <?php
              $policy = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id=".$row['rc_id']." ORDER BY p_sort");
              $body = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id=".$row['rc_id']);
              if($policy->rowCount() == 1) {
                $pol = $policy->fetch();
                ?><embed src="upload/<?php echo $pol['p_path']."/".$pol['p_filename'];?>" width="100%" height="1150" type="application/pdf"><?php
              } else {
                ?>
                <div class="tab">
                  <div class="row clearfix">
                    <div class="col-md-3 col-sm-12">
                      <ul class="make_tree">
            						<?php
            							while($row = $policy->fetch()) {
            								if(!$row['p_filename']) {
            									linked($row['p_id'],$row['p_desc']);
            								} else {
            									?><li style="cursor:pointer;"><a data-toggle="tab" id="link_<?php echo $row['p_id'];?>" href="#tab<?php echo $row['p_id'];?>" onclick="changeTab(<?php echo $row['p_id'];?>)"><i class="fa fa-arrow-right"></i> <?php echo $row['p_desc'];?></a></li><?php
            								}
            							}
            						?>
                      </ul>
                    </div>
                    <div class="col-md-9 col-sm-12">
                      <div class="tab-content">
                        <?php
                          $aria = "tab-pane fade active show";
                          while($content = $body->fetch()) {
                            if(!$content['p_filename']) {
                              getContent($content['p_id'],"tab-pane fade");
                            } else {
                              ?>
                              <div class="<?php echo $aria;?>" id="tab<?php echo $content['p_id'];?>" role="tabpanel">
        												<div class="pd-20">
                                  <embed src="upload/<?php echo $content['p_path']."/".$content['p_filename'];?>" width="100%" height="1150" type="application/pdf">
                                </div>
        											</div>
                              <?php
                            }
                        ?>
                        <?php
                          $aria = "tab-pane fade";
                          }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
            ?>
          </div>
        </div>
      </div>
      <?php
        $style = "collapse";
        $class = "btn btn-block collapsed";
        }
      ?>
    </div>
    <script>$('.make_tree').treed();</script>
    <?php
  } else if($sys == "110") {
    ?>
    <div class="faq-wrap col-12" id="accordion">
      <?php
        $count = 1;
        $result = executeQuery("SELECT * FROM ref_category WHERE rc_type='form' AND rc_active=1 ORDER BY rc_desc");
        while($row = $result->fetch()) {
          if($count == 1) {
            $style = "collapse show";
            $class = "btn btn-block";
          } else {
            $style = "collapse";
            $class = "btn btn-block collapsed";
          }
          $count++;
      ?>
      <div class="card">
        <div class="card-header">
          <button class="<?php echo $class;?>" data-toggle="collapse" data-target="#form<?php echo $row['rc_id'];?>">
            <?php echo $row['rc_desc'];?>
          </button>
        </div>
        <div id="form<?php echo $row['rc_id'];?>" class="<?php echo $style;?>" data-parent="#accordion">
          <div class="card-body">
    				<ul class="list-group">
              <?php
                $results = executeQuery("SELECT * FROM forms WHERE f_active=1 AND f_rc_id=".$row['rc_id']);
                $count = 1;
                while($rows = $results->fetch()) {
              ?>
      					<li class="list-group-item d-flex justify-content-between align-items-center">
      						<?php echo $count++.") ".$rows['f_desc'];?>
      						<span class="badge badge-pill"><a href="upload/<?php echo $rows['f_path']."/".$rows['f_filename'];?>" target="_blank" title="Download"><i class="fa fa-download fa-2x"></i></a></span>
      					</li>
              <?php }?>
    				</ul>
          </div>
        </div>
      </div>
      <?php }?>
    </div>
    <?php
  } else if($sys == "111") {
    $token = verifyToken($expires,"dms");
    $url = "secure_login.php?expired=$expires&token=$token&u=".encryptIt($_SESSION['login']['username'])."&pass=".encryptIt($_SESSION["login"]["password"]);
    ?>
    <div class="col-12">
      <ul>
        <?php
        $result = executeQuery("SELECT * FROM dms WHERE dms_status=1 ORDER BY dms_desc");
        while($row = $result->fetch()){
          ?><li>- <a target="_blank" href="<?php echo $row['dms_url'].$url;?>"><b><?php echo $row['dms_desc'];?></b></a></li><?php
        }
        ?>
      </ul>
    </div>
    <?php
  } else if($sys == "112") {
    $result = executeQuery("SELECT news_attach FROM newsletter ORDER BY news_id DESC LIMIT 1");
    $row = $result->fetch();
    if($row['news_attach'] != ""){
      $target_dir = "../upload/newsletter/".$row['news_attach'];
      if (file_exists($target_dir)) {
      $url = "http://flowpaper.com/flipbook/http://apps.mtdgroup.com.my/central/upload/newsletter/".$row['news_attach'];
      ?>
      <div class="col-12">
        <iframe src="<?php echo $url;?>" width="100%" height="800" style="border: none;" allowFullScreen></iframe>
      </div>
      <?php
    }} else {
      ?>
      <div class="col-12 weight-600 font-30 text-danger text-center">
        No New Newsletter
      </div>
      <?php
    }
  } else if($row['sys_id'] == "9") {
    ?>
    <div class="form-group col-4">
  		<label>Staff Name</label>
  		<input class="form-control" placeholder="Staff Name" type="search" id="searchTxt">
  	</div>
    <div class="form-group col-4">
  		<label>Department</label>
      <select class="form-control" style="width: 100%;" id="searchSelect">
        <option value="0">-- Please Select --</option>
  			<?php selectDepartDivision();?>
      </select>
  	</div>
    <div class="form-group col-4">
  		<label>Level/Area</label>
      <select class="form-control" style="width: 100%;" id="levelSelect">
        <option value="0">-- Please Select --</option>
  			<?php selectLevel();?>
      </select>
  	</div>
    <div class="col-12">
      <button id="search" class="btn btn-primary pull-right" onclick="searchDirectory()"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="col-12">&nbsp;</div>
    <hr>
    <div class="col-12" id="searchDirectory"></div>
    <?php
  } else if($row['sys_id'] == "1") {
  if(countLeave('approve') > 0 && $_SESSION["login"]["level"] == "supervisor") {?>
  <div class="col-sm-6 col-md-6 col-lg-6 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-danger">
        <h5 class="h5 mb-10 text-white"><?php echo countLeave('approve');?></h5>
        <p class="mb-0">Pending leave application for your approval</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-5 mb-30">&nbsp;</div>
  <?php } if(countLeave('recommend') > 0 && ($_SESSION['login']["level"] == "immediatesupervisor" || checkHandleSup($_SESSION["login"]["username"]) == 1)) { ?>
  <div class="col-sm-6 col-md-6 col-lg-6 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-success">
        <h5 class="h5 mb-10 text-white"><?php echo countLeave('recommend');?></h5>
        <p class="mb-0">Pending leave application for your recommendation</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-5 mb-30">&nbsp;</div>
  <?php }?>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-success">
        <h5 class="h5 mb-10 text-white">
          <?php echo countLeave('carry');?>
        </h5>
        <p class="mb-0">Carry Forward Leave</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-success">
        <h5 class="h5 mb-10 text-white"><?php if($_SESSION["login"]["category"] == "permanent"){ echo countLeave('earn')." / "; } echo countLeave('entitle');?></h5>
        <p class="mb-0"><?php if($_SESSION["login"]["category"] == "permanent"){ echo "Earned Leave / ";}?>Full Entitlement</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-success">
        <h5 class="h5 mb-10 text-white"><?php echo countLeave('taken');?></h5>
        <p class="mb-0">Leave taken prior</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-success">
        <h5 class="h5 mb-10 text-white"><?php echo countLeave('balance');?></h5>
        <p class="mb-0">Balance</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content text-white bg-success">
        <h5 class="h5 mb-10 text-white"><?php echo countLeave('sick');?></h5>
        <p class="mb-0">Sick Leave Balance</p>
      </div>
    </div>
  </div>
  <?php
  } else if($row['sys_id'] == "2") {
    ?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo fleet();?></h5>
          <p class="mb-0">Request In-Progress<br>(FLEET)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo maintainace();?></h5>
          <p class="mb-0">Request In-Progress <br>(MAINTAINACE/REPAIR)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo room();?></h5>
          <p class="mb-0">Request In-Progress <br>(MEETING ROOM)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo simcard();?></h5>
          <p class="mb-0">Request In-Progress <br>(SIMCARD)</p>
        </div>
      </div>
    </div>
    <?php
  } else if($row['sys_id'] == "4") {
    ?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo movement('late');?></h5>
          <p class="mb-0">LATE IN</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo movement('early');?></h5>
          <p class="mb-0">EARLY OUT</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo movement('absent');?></h5>
          <p class="mb-0">ABSENT</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo movement('notIn');?></h5>
          <p class="mb-0">NOT LOG IN</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo movement('notOut');?></h5>
          <p class="mb-0">NOT LOG OUT</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo movement('tempo');?></h5>
          <p class="mb-0">TEMPORARY ACCESS</p>
        </div>
      </div>
    </div>
    <?php
  } else if($row['sys_id'] == "5") {
    ?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content text-white bg-success">
          <h5 class="h5 mb-10 text-white"><?php echo getDashInfo('Training History');?></h5>
          <p class="mb-0">Training History</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content text-white bg-success">
          <h5 class="h5 mb-10 text-white"><?php echo getDashInfo('License Record');?></h5>
          <p class="mb-0">License Record</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content text-white bg-success">
          <h5 class="h5 mb-10 text-white"><?php echo getDashInfo('External Training');?></h5>
          <p class="mb-0">External Training</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content text-white bg-success">
          <h5 class="h5 mb-10 text-white"><?php echo getDashInfo('In-House Training');?></h5>
          <p class="mb-0">In-House Training</p>
        </div>
      </div>
    </div>
    <?php
  } else if($row['sys_id'] == "7" && ITUser() > "0") {
    if(ITAdmin("crf",1) > 0){
      ?>
      <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
        <div class="da-card">
          <div class="da-card-photo da-card-content bg-danger text-white">
            <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("crf",1);?></h5>
            <p class="mb-0">CRF SYSTEM<br>PENDING APPROVAL/TASK</p>
          </div>
        </div>
      </div>
      <?php
    }
    if(ITAdmin("facility",1) > 0){
      ?>
      <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
        <div class="da-card">
          <div class="da-card-photo da-card-content bg-danger text-white">
            <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("facility",1);?></h5>
            <p class="mb-0">IT FACILITIES<br>PENDING APPROVAL/TASK</p>
          </div>
        </div>
      </div>
      <?php
    }
    if(ITAdmin("id",1) > 0){
      ?>
      <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
        <div class="da-card">
          <div class="da-card-photo da-card-content bg-danger text-white">
            <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("id",1);?></h5>
            <p class="mb-0">ID REQUEST SYSTEM<br>PENDING APPROVAL/TASK</p>
          </div>
        </div>
      </div>
      <?php
    }
    if(ITAdmin("storage",1) > 0){
      ?>
      <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
        <div class="da-card">
          <div class="da-card-photo da-card-content bg-danger text-white">
            <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("storage",1);?></h5>
            <p class="mb-0">TAPE STORAGE SYSTEM<br>PENDING APPROVAL/TASK</p>
          </div>
        </div>
      </div>
      <?php
    }
    if(ITAdmin("crf",1)+ITAdmin("facility",1)+ITAdmin("id",1)+ITAdmin("storage",1) > 0) {
      ?><div class="col-12">&nbsp;</div><?php
    }
    ?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("sms");?></h5>
          <p class="mb-0">ITSMS SYSTEM<br>ON-GOING CASE(S)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("crf");?></h5>
          <p class="mb-0">CRF SYSTEM<br>ON-GOING PROJECT(S)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("facility");?></h5>
          <p class="mb-0">IT FACILITIES<br>ON-GOING REQUEST(S)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("id");?></h5>
          <p class="mb-0">ID REQUEST SYSTEM<br>ON-GOING PROJECT(S)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITAdmin("storage");?></h5>
          <p class="mb-0">TAPE STORAGE SYSTEM<br>ON-GOING PROJECT(S)</p>
        </div>
      </div>
    </div>
  <?php } else if($row['sys_id'] == "13") {?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ecmr("new");?></h5>
          <p class="mb-0">New Case</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ecmr("processed");?></h5>
          <p class="mb-0">In Process</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ecmr("verify");?></h5>
          <p class="mb-0">Verification</p>
        </div>
      </div>
    </div>
  <?php } else if($row['sys_id'] == "3") {
    if(ITPortal("REQUEST")+ITPortal()+ITPortal("UAT") > 0) {
  ?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("REQUEST");?></h5>
          <p class="mb-0">Approve Request<br>(Change Request)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal();?></h5>
          <p class="mb-0">Verify UAT Form<br>(Change Request)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("UAT");?></h5>
          <p class="mb-0">Approve UAT Form<br>(Change Request)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">&nbsp;</div>
  <?php } if(ITPortal("NEWRECOMMEND")+ITPortal("NEWAPPROVE") > 0) {?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-primary text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("NEWRECOMMEND");?></h5>
          <p class="mb-0">Recommend Request<br>(New ID Issuance)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-primary text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("NEWAPPROVE");?></h5>
          <p class="mb-0">Approve Request<br>(New ID Issuance)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-6 mb-30">&nbsp;</div>
  <?php } if(ITPortal("TERMINATERECOMMEND")+ITPortal("TERMINATEAPPROVE")+ITPortal("TERMINATEACKNOWLEDGE")+ITPortal("TERMINATE")+ITPortal("ASSETACKNOWLEDGE") > 0) {?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-danger text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("TERMINATERECOMMEND");?></h5>
          <p class="mb-0">Recommend Request<br>(Termination)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-danger text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("TERMINATEAPPROVE");?></h5>
          <p class="mb-0">Approve Request<br>(Termination)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-danger text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("TERMINATEACKNOWLEDGE");?></h5>
          <p class="mb-0">Acknowledge Request<br>(Termination)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-danger text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("TERMINATE");?></h5>
          <p class="mb-0">Cancel Request<br>(Termination)</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-danger text-white">
          <h5 class="h5 mb-10 text-white"><?php echo ITPortal("ASSETACKNOWLEDGE");?></h5>
          <p class="mb-0">Acknowledge Request<br>(Asset Return)</p>
        </div>
      </div>
    </div>
  <?php } if(ITPortal("RRECOMMEND")+ITPortal("RAPPROVE") > 0) {?>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content bg-info text-white">
        <h5 class="h5 mb-10 text-white"><?php echo ITPortal("RRECOMMEND");?></h5>
        <p class="mb-0">Recommend Request<br>(IT Facilities)</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
    <div class="da-card">
      <div class="da-card-photo da-card-content bg-info text-white">
        <h5 class="h5 mb-10 text-white"><?php echo ITPortal("RAPPROVE");?></h5>
        <p class="mb-0">Approve Request<br>(IT Facilities)</p>
      </div>
    </div>
  </div>
<?php }} else if($row['sys_id'] == "14") {
    if(countToll() > 0){
    ?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content text-white bg-danger">
          <h5 class="h5 mb-10 text-white"><?php echo countToll();?></h5>
          <p class="mb-0">Pending Approval</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-9 mb-30">&nbsp;</div>
  <?php }?>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo dashboard("new");?></h5>
          <p class="mb-0">On-Going</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo dashboard("draw");?></h5>
          <p class="mb-0">Withdraw</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo dashboard("active");?></h5>
          <p class="mb-0">Active</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 mb-30">
      <div class="da-card">
        <div class="da-card-photo da-card-content bg-success text-white">
          <h5 class="h5 mb-10 text-white"><?php echo dashboard("inactive");?></h5>
          <p class="mb-0">Inactive</p>
        </div>
      </div>
    </div>
    <?php
  } else {?>
    <div class="col-12 text-center"><img src="src/images/um.gif" height="150" width="700"></div><?php
  }
}
function dashboard($task=null){
	$query = "SELECT ref_id FROM reference WHERE";
	if($task == "new") {
		$query .= " ref_status_id NOT IN(4,14) AND ref_acknowledge_by IS NULL";
	} else if($task == "draw") {
		$query .= " ref_status_id IN(4)";
	} else if($task == "active") {
		$query = "SELECT form_id FROM form WHERE form_status=1 AND (form_type_id LIKE '%1%' OR form_type_id LIKE '%3%')";
	} else if($task == "inactive") {
		$query = "SELECT form_id FROM form WHERE form_status=1 AND form_type_id LIKE '%5%'";
	}
	$result = executeQuery($query,"badge");
	return $result->rowCount();
}
function eadmin($task=null,$sub=null){
  $department = department();
  $user = $_SESSION['login']['username'];
  if($task == "fleet") {
    if($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
  	$dashboard_dept = "AND (flf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR flf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
    } else if ($department == "COO`S OFFICE") {
    	$dashboard_dept = "AND (flf_codept='COO`S OFFICE' OR flf_codept='QUALITY TECH. AUDIT & OVERSEA')";
    } else if ($department == "ACP - DMT") {
    	$dashboard_dept = "AND (flf_codept='ACP - DMT' OR flf_codept='SPECIAL PRODUCT DIVISION')";
    } else if ($department == "GROUP SECURITY DEPARTMENT") {
    	$dashboard_dept = "AND (flf_codept='GROUP SECURITY DEPARTMENT' OR flf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
    } else if ($department == "COO (II)") {
    	$dashboard_dept = "AND (flf_codept='CORPORATE OFFICE' OR flf_codept='COO (II)')";
    } else if ($department == "CORPORATE OFFICE") {
    	$dashboard_dept = "AND (flf_codept='CORPORATE OFFICE' OR flf_codept='COO (II)')";
    } else {
    	$dashboard_dept = "AND flf_codept='$department'";
    }
  } else if($task == "sim"){
    if($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
    	$dashboard_dept = "AND (scf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR scf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
    } else if ($department == "COO`S OFFICE") {
    	$dashboard_dept = "AND (scf_codept='COO`S OFFICE' OR scf_codept='QUALITY TECH. AUDIT & OVERSEA')";
    } else if ($department == "ACP - DMT") {
    	$dashboard_dept = "AND (scf_codept='ACP - DMT' OR scf_codept='SPECIAL PRODUCT DIVISION')";
    } else if ($department == "GROUP SECURITY DEPARTMENT") {
    	$dashboard_dept = "AND (scf_codept='GROUP SECURITY DEPARTMENT' OR scf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
    } else if ($department == "COO (II)") {
    	$dashboard_dept = "AND (scf_codept='CORPORATE OFFICE' OR scf_codept='COO (II)')";
    } else if ($department == "CORPORATE OFFICE") {
    	$dashboard_dept = "AND (scf_codept='CORPORATE OFFICE' OR scf_codept='COO (II)')";
    } else {
    	$dashboard_dept = "AND scf_codept='$department'";
    }
  } else if($task == "maintainace") {
    if($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
    	$dashboard_dept = "AND (mtf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR mtf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
    } else if ($department == "COO`S OFFICE") {
    	$dashboard_dept = "AND (mtf_codept='COO`S OFFICE' OR mtf_codept='QUALITY TECH. AUDIT & OVERSEA')";
    } else if ($department == "ACP - DMT") {
    	$dashboard_dept = "AND (mtf_codept='ACP - DMT' OR mtf_codept='SPECIAL PRODUCT DIVISION')";
    } else if ($department == "GROUP SECURITY DEPARTMENT") {
    	$dashboard_dept = "AND (mtf_codept='GROUP SECURITY DEPARTMENT' OR mtf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
    } else if ($department == "COO (II)") {
    	$dashboard_dept = "AND (mtf_codept='CORPORATE OFFICE' OR mtf_codept='COO (II)')";
    } else if ($department == "CORPORATE OFFICE") {
    	$dashboard_dept = "AND (mtf_codept='CORPORATE OFFICE' OR mtf_codept='COO (II)')";
    } else {
    	$dashboard_dept = "AND mtf_codept='$department'";
    }
  } else if($task == "room"){
    if ($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
    	$dashboard_dept = " AND (mrf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR mrf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
    } else if ($department == "COO`S OFFICE") {
    	$dashboard_dept = " AND (mrf_codept='COO`S OFFICE' OR mrf_codept='QUALITY TECH. AUDIT & OVERSEA')";
    } else if ($department == "ACP - DMT") {
    	$dashboard_dept = " AND (mrf_codept='ACP - DMT' OR mrf_codept='SPECIAL PRODUCT DIVISION')";
    } else if ($department == "GROUP SECURITY DEPARTMENT") {
    	$dashboard_dept = " AND (mrf_codept='GROUP SECURITY DEPARTMENT' OR mrf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
    } else if ($department == "COO (II)") {
    	$dashboard_dept = " AND (mrf_codept='CORPORATE OFFICE' OR mrf_codept='COO (II)')";
    } else if ($department == "CORPORATE OFFICE") {
    	$dashboard_dept = " AND (mrf_codept='CORPORATE OFFICE' OR mrf_codept='COO (II)')";
    } else {
    	$dashboard_dept = " AND mrf_codept='$department'";
    }
  }
  $query = "";
  if($task == "fleet") {
    $query = "SELECT flf_id FROM e_admin.form_fleet
              WHERE (flf_status <> '8' AND flf_status <> '9' AND flf_status <> '13')
              AND flf_apprv_user = '$user' AND flf_status = '1'".$dashboard_dept;
  } else if($task == "maintainace"){
    if($sub == "pre" && hod() == "true") {
      $query = "SELECT mtf_id FROM e_admin.form_maintenance
                WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
                AND mtf_apprv_user = '' AND mtf_status = '1'".$dashboard_dept;
    } else if($sub == "late") {
      $query = "SELECT mtf_id FROM e_admin.form_maintenance
                LEFT JOIN e_admin.activity_maintenance ON activity_maintenance.mtf_code=form_maintenance.mtf_code
                WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
                AND mtf_apprv_user = '$user' AND (mtf_status BETWEEN '4' AND '6') AND hod_approve='0'".$dashboard_dept;
    } else if($sub == "pre-late" && hod() == "true"){
      $query = "SELECT mtf_id FROM e_admin.form_maintenance
                LEFT JOIN e_admin.activity_maintenance ON activity_maintenance.mtf_code=form_maintenance.mtf_code
                WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
                AND mtf_apprv_user = '' AND (mtf_status BETWEEN '4' AND '6') AND hod_approve='0'".$dashboard_dept;
    } else if($sub == "confirm" && hod() == "false"){
      $query = "SELECT mtf_id FROM e_admin.form_maintenance
                WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
                AND mtf_by = '$user' AND mtf_status = '4'".$dashboard_dept;
    } else {
      $query = "SELECT mtf_id FROM e_admin.form_maintenance
                WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
                AND mtf_apprv_user = '$user' AND mtf_status = '1'".$dashboard_dept;
    }
  } else if($task == "room"){
    if($sub == "pre" && hod() == "true") {
      $query = "SELECT mrf_id FROM e_admin.form_meeting_room
                WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
                AND mrf_apprv_user = '' AND mrf_status = '1' ".$dashboard_dept;
    } else if($sub == "late") {
      $query = "SELECT mrf_id FROM e_admin.form_meeting_room
                LEFT JOIN e_admin.activity_room ON activity_room.mrf_code=form_meeting_room.mrf_code
                WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
                AND mrf_apprv_user = '$user' AND (mrf_status BETWEEN '3' AND '4') AND hod_approve = '0'".$dashboard_dept;
    } else if($sub == "pre-late" && hod() == "true") {
      $query = "SELECT mrf_id FROM e_admin.form_meeting_room
                LEFT JOIN e_admin.activity_room ON activity_room.mrf_code=form_meeting_room.mrf_code
                WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
                AND mrf_apprv_user = '' AND (mrf_status BETWEEN '3' AND '4') AND hod_approve = '0'".$dashboard_dept;
    } else {
      $query = "SELECT mrf_id FROM e_admin.form_meeting_room
                WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
                AND mrf_apprv_user = '$user' AND mrf_status = '1'".$dashboard_dept;
    }
  } else if($task == "sim"){
    if($sub == "pre" && hod() == "true") {
      $query = "SELECT scf_id FROM e_admin.form_simcard
                WHERE (scf_status <> '7' AND scf_status <> '8')
                AND scf_apprv_user = '' AND scf_status = '1'".$dashboard_dept;
    } else if($sub == "late") {
      $query = "SELECT scf_id FROM e_admin.form_simcard
                LEFT JOIN e_admin.activity_simcard ON activity_simcard.scf_code=form_simcard.scf_code
                WHERE (scf_status <> '7' AND scf_status <> '8')
                AND scf_apprv_user = '$user' AND (scf_status BETWEEN '3' AND '5') AND hod_approve='0'".$dashboard_dept;
    } else if($sub == "pre-late" && hod() == "true") {
      $query = "SELECT scf_id FROM e_admin.form_simcard
                LEFT JOIN e_admin.activity_simcard ON activity_simcard.scf_code=form_simcard.scf_code
                WHERE (scf_status <> '7' AND scf_status <> '8')
                AND scf_apprv_user = '' AND (scf_status BETWEEN '3' AND '5') AND hod_approve='0'".$dashboard_dept;
    } else {
      $query = "SELECT scf_id FROM e_admin.form_simcard
                WHERE (scf_status <> '7' AND scf_status <> '8')
                AND scf_apprv_user = '$user' AND scf_status = '1'".$dashboard_dept;
    }
  }
  if(!$query) {
    return 0;
  } else {
    $result = executeQuery($query);
    return $result->rowCount();
  }
}
function timeline($startYear=null,$startMonth="Select Month") {
  $username = $_SESSION['login']['username'];
  if($startYear == null) {
    $startYear = date("Y");
  }
  $count = 0;
  if($startMonth == "Select Month") {
    $month = 1;
    while($month <= 12) {
      $query = "SELECT t_query AS des,t_from AS title,t_ip AS ip,t_date AS tarikh FROM trail
              WHERE t_user='$username' AND MONTH(t_date)=$month AND YEAR(t_date)=$startYear
              UNION ALL
              SELECT track_title AS title,track_desc AS des,track_ip AS ip,track_date AS tarikh FROM itadmin.tracking
              WHERE track_user='$username' AND MONTH(track_date)=$month AND YEAR(track_date)=$startYear";
      $result = executeQuery($query);
      if($result->rowCount() > 0) {
        $count++;
        ?>
        <div class="timeline-month">
          <h5><?php echo date("F", mktime(0, 0, 0,$month, 10)).", ".$startYear;?></h5>
        </div>
        <div class="profile-timeline-list">
          <ul>
        <?php
        while($row = $result->fetch()) {
        ?>
          <li>
            <div class="date"><?php echo date("d M",strtotime($row['tarikh']));?></div>
            <div class="task-name"><i class="ion-ios-clock"></i> <?php echo $row['title'];?></div>
            <p><?php echo $row['des'];?></p>
            <div class="task-time"><?php echo date("h:i A",strtotime($row['tarikh']));?></div>
          </li>
          <?php
        }
        ?>
        </ul>
      </div>
        <?php
      }
      $month++;
    }
  } else {
    $month = $startMonth;
    $query = "SELECT t_query AS des,t_from AS title,t_ip AS ip,t_date AS tarikh FROM trail
            WHERE t_user='$username' AND MONTH(t_date)=$month AND YEAR(t_date)=$startYear
            UNION ALL
            SELECT track_title AS title,track_desc AS des,track_ip AS ip,track_date AS tarikh FROM itadmin.tracking
            WHERE track_user='$username' AND MONTH(track_date)=$month AND YEAR(track_date)=$startYear";
            $result = executeQuery($query);
            if($result->rowCount() > 0) {
              $count++;
              ?>
              <div class="timeline-month">
                <h5><?php echo date("F", mktime(0, 0, 0,$month, 10)).", ".$startYear;?></h5>
              </div>
              <div class="profile-timeline-list">
                <ul>
              <?php
              while($row = $result->fetch()) {
              ?>
                <li>
                  <div class="date"><?php echo date("d M",strtotime($row['tarikh']));?></div>
                  <div class="task-name"><i class="ion-ios-clock"></i> <?php echo $row['title'];?></div>
                  <p><?php echo $row['des'];?></p>
                  <div class="task-time"><?php echo date("h:i A",strtotime($row['tarikh']));?></div>
                </li>
                <?php
              }
              ?>
              </ul>
            </div>
              <?php
            }

  }
  if($count == 0) {
    echo "<div class=\"profile-timeline-list\">No trail found!</div>";
  }
}
function updateStatus($task,$id,$status) {
  $val = 1;
  if($status == "false") {
    $val = 0;
  }
  if($task == "system"){
    executeQuery("UPDATE e_central.system SET sys_active='$val' WHERE sys_id='$id'");
  } else if($task == "dms"){
    executeQuery("UPDATE e_central.dms SET dms_status='$val' WHERE dms_id='$id'");
  } else if($task == "category"){
    executeQuery("UPDATE e_central.ref_category SET rc_active='$val' WHERE rc_id='$id'");
  } else if($task == "policy"){
    executeQuery("UPDATE e_central.policy SET p_active='$val' WHERE p_id='$id'");
  } else if($task == "level"){
    executeQuery("UPDATE phone_dir.level SET lvl_status='$val' WHERE lvl_id='$id'");
  } else if($task == "division"){
    executeQuery("UPDATE phone_dir.division SET div_status='$val' WHERE div_id='$id'");
  } else if($task == "department"){
    executeQuery("UPDATE phone_dir.department SET dept_status='$val' WHERE dept_id='$id'");
  } else if($task == "phone"){
    executeQuery("UPDATE phone_dir.phone SET emp_status='$val' WHERE emp_id='$id'");
  }
}
function systemList($all=null) {
  if($all == null) {
    $query = "SELECT * FROM system ORDER BY sys_title ASC";
  } else {
    $query = "SELECT * FROM system WHERE ".$all." ORDER BY sys_title ASC";
  }
  return executeQuery($query);
}
function getEmployeeNo() {
  $staffid = $_SESSION["login"]["id"];
  $result = executeQuery("SELECT employee_ic_no FROM employee WHERE user_id='$staffid'","eleave_v4");
  $row = $result->fetch();
  return $row['employee_ic_no'];
}
//e-movement start
function timeIn($band){
  if($band == ''){
    $masuk = '08:30:00';
  } else if($band == '800'){
    $masuk = '08:00:00';
  } else if($band == '830'){
    $masuk = '08:30:00';
  } else if($band == '900'){
    $masuk = '09:00:00';
  }
  return $masuk;
}
function timeOut($band){
  if($band == ''){
    $keluar = '17:30:00';
  } else if($band == '800'){
    $keluar = '17:00:00';
  } else if($band == '830'){
    $keluar = '17:30:00';
  } else if($band == '900'){
    $keluar = '18:00:00';
  }
  return $keluar;
}
function movement($cat){
  $start_date = date("Y-m-")."01";
  $end_date = date("Y-m-d");
  $begin = new DateTime($start_date);
  $end = new DateTime($end_date);
  $end = $end->modify( '+1 day' );
  $interval = new DateInterval('P1D');
  $daterange = new DatePeriod($begin, $interval ,$end);
  $employee_no = getEmployeeNo();
  $count = 0;
  foreach($daterange as $date){
    $att_date = $date->format("Y-m-d");
    $result = executeQuery("SELECT schedule_type FROM movement_db.attendance
		WHERE (actual_date = '$att_date' AND employee_no = '$employee_no') AND status = 0");
    if($result->rowCount() > 0) {
      $row = $result->fetch();
      $masuk = timeIn($row['schedule_type']);
  		$keluar = timeOut($row['schedule_type']);
      if($cat == "late") {
        $query = "SELECT * FROM movement_db.attendance
    		WHERE (actual_date = '$att_date' AND employee_no = '$employee_no') AND
    		((time_in_1 <> '00:00:00' AND time_in_1 > '$masuk') OR ((time_in_1 = '00:00:00' AND time_out_1 = '00:00:00') AND
    		(time_in_2 <> '00:00:00' AND time_in_2 > '$masuk')))
    		AND status = 0";
      } else if($cat == "early") {
        $results = executeQuery("SELECT id FROM `leave`
                  WHERE employee_id=(SELECT employee_id FROM employee WHERE employee_ic_no='$employee_no')
                  AND status='approved' AND (from_date='$att_date' OR to_date='$att_date')","eleave_v4");
        if($results->rowCount() > 0){
          $query = "";
        } else {
          $query = "SELECT * FROM movement_db.attendance
      		WHERE (actual_date = '$att_date' AND employee_no = '$employee_no') AND
      		((time_out_2 = '00:00:00' AND time_out_1 <> '00:00:00' AND  time_out_1 < '$keluar') OR
      		(time_out_1 = '00:00:00' AND time_out_2 <> '00:00:00' AND time_out_2 < '$keluar') OR
      		((time_out_1 <> '00:00:00' AND time_out_1 < '$keluar') AND (time_out_2 <> '00:00:00' AND time_out_2 < '$keluar')))
      		AND status = 0";
        }
      } else if($cat == "absent") {
        $results = executeQuery("SELECT id FROM `leave`
                  WHERE employee_id=(SELECT employee_id FROM employee WHERE employee_ic_no='$employee_no')
                  AND status='approved' AND (from_date='$att_date' OR to_date='$att_date')","eleave_v4");
        if($results->rowCount() > 0){
          $query = "";
        } else {
          $query = "SELECT * FROM movement_db.attendance
      		WHERE (actual_date = '$att_date' AND employee_no = '$employee_no') AND
      		(time_in_1 = '00:00:00' AND time_in_2 = '00:00:00' AND time_out_1 = '00:00:00' AND time_out_2 = '00:00:00')
      		AND status = 0";
        }
      } else if($cat == "notIn") {
        $query = "SELECT * FROM movement_db.attendance
    		WHERE (actual_date = '$att_date' AND employee_no = '$employee_no') AND
    		((time_in_1 = '00:00:00' AND time_in_2 = '00:00:00') AND (time_out_1 <> '00:00:00' OR time_out_2 <> '00:00:00'))
    		AND status = 0";
      } else if($cat == "notOut") {
        $query = "SELECT * FROM movement_db.attendance
    		WHERE (actual_date = '$att_date' AND employee_no = '$employee_no') AND
    		((time_in_1 <> '00:00:00' OR time_in_2 <>'00:00:00') AND (time_out_1 = '00:00:00' AND time_out_2 = '00:00:00'))
    		AND status = 0";
      } else if($cat == "tempo") {
        $query = "SELECT movement.movement_id FROM movement_db.movement
      	LEFT JOIN movement_db.category ON (movement.category = category.category_id)
      	LEFT JOIN movement_db.subcategory ON (movement.subcategory = subcategory.subcategory_id)
        WHERE date_from <= CAST('$att_date' AS DATE) AND date_to >= CAST('$att_date' AS DATE) AND employee_no = '$employee_no'
    		AND movement.status = 0 AND movement.category = 3
    		ORDER BY movement.movement_id";
      }
      if($query != ""){
        $result = executeQuery($query);
        if($result->rowCount() > 0 && just($att_date,$employee_no) == false) {
          $count++;
        }
      }
    }
  }
  return $count;
}
function just($date,$emp){
  $result = executeQuery("SELECT * FROM movement_db.movement WHERE date_from <= CAST('$date' AS DATE) AND date_to >= CAST('$date' AS DATE) AND employee_no = '$emp' AND approve_status=12");
  if($result->rowCount() > 0){
    return true;
  }
}
function approveMovement() {
  $emp_id = $_SESSION["login"]["id"];
  $result = executeQuery("SELECT movement.apply_status FROM movement_db.movement
  LEFT JOIN movement_db.category ON (movement.category = category.category_id)
  LEFT JOIN movement_db.subcategory ON (movement.subcategory = subcategory.subcategory_id)
  WHERE movement.status = 0 AND (movement.apply_status = 0 AND movement.concurence = '$emp_id')
  OR ((movement.apply_status = 11 OR movement.apply_status = 15) AND movement.approver = '$emp_id')
  ORDER BY apply_status ASC, movement_id DESC","movement_db");
  if(!$result) {
    return 0;
  } else {
    return $result->rowCount();
  }
}
//e-movement end
//e-training start
function getDashInfo($cat){
  $username = $_SESSION['login']['username'];
  $staffid = $_SESSION["login"]["id"];
  $thisyear = date("Y");
  if($cat == "Training History"){
    $result = executeQuery("SELECT COUNT(*) AS total
    FROM etraining.program p
    LEFT JOIN etraining.nomination n ON n.prog_id=p.id
    LEFT JOIN etraining.staff s ON s.id=p.staff_id or s.id=n.staff_id
    LEFT JOIN eleave_v4.user u ON s.staff_id=u.username
    LEFT JOIN eleave_v4.employee e ON u.id=e.user_id
    WHERE s.staff_id='$username' AND (p.status='Approved' OR p.status='Completed') AND year(p.start_date)='$thisyear'");
  } elseif($cat == "External Training"){
    $result = executeQuery("SELECT COUNT(*) AS total
    FROM etraining.program p
    LEFT JOIN etraining.nomination n ON n.prog_id=p.id
    LEFT JOIN etraining.staff s ON s.id=p.staff_id or s.id=n.staff_id
    LEFT JOIN eleave_v4.user u ON s.staff_id=u.username
    LEFT JOIN eleave_v4.employee e ON u.id=e.user_id
    WHERE s.staff_id='$username' AND (p.status='Approved' OR p.status='Completed') AND year(p.start_date)='$thisyear' AND p.training_cat='external'");
  } else if($cat=="In-House Training"){
    $result = executeQuery("SELECT COUNT(*) AS total
    FROM etraining.program p
    LEFT JOIN etraining.nomination n ON n.prog_id=p.id
    LEFT JOIN etraining.staff s ON s.id=p.staff_id or s.id=n.staff_id
    LEFT JOIN eleave_v4.user u ON s.staff_id=u.username
    LEFT JOIN eleave_v4.employee e ON u.id=e.user_id
    WHERE s.staff_id='$username' AND (p.status='Approved' OR p.status='Completed') AND year(p.start_date)='$thisyear' AND p.training_cat='in-house'");
  } else{
    $result = executeQuery("SELECT COUNT(*) AS total
    FROM staff s
    INNER JOIN license l ON s.id=l.staff_id
    WHERE s.staff_id='$username' AND year(l.start)='$thisyear'","etraining");
  }
  $row = $result->fetch();
  return $row["total"];
}
function getUserName($field,$username){
  $result = executeQuery("SELECT hod_id FROM staff WHERE hod_id='$username' GROUP BY hod_id","etraining");
  $userid = array();
  while($row = $result->fetch()){
    $userid[] = $row['hod_id'];
  }
  return $userid;
}
function getCountNotify($cat){
  $username = $_SESSION['login']['username'];
  $emp_id = $_SESSION["login"]["id"];
  $staffid = staffID($username);
  $hcd_admin = array('badarina.baharudin','jamhuri.mohamed','aininorzita.ramli','sarah.adzmey','aniz.majid','salleh.mustafa');
  $thisyear = date("Y");
  $query = "";
  if($cat == "external"){
    if(in_array($username, getUserName('hod_id',$username))){
      $adm_user = "'" . implode("','", $hcd_admin) . "'";
      $query = "SELECT s.id FROM staff s
      INNER JOIN program p ON s.id=p.staff_id WHERE p.training_cat='external'
      AND ((p.status='Pending' AND s.imdsup_id='$username') OR (p.status='Verified'
      AND s.hod_id='$username')) OR ((p.status='Pending' OR p.status='Verified'
      OR p.status='Recommended') AND '$username' IN ($adm_user))";
    } else if(in_array($username, getUserName('imdsup_id',$username)) && !in_array($username, getUserName('hod_id',$username))){
      $adm_user = "'" . implode("','", $hcd_admin) . "'";
      $query = "SELECT s.id FROM staff s
      INNER JOIN program p ON s.id=p.staff_id WHERE p.training_cat='external'
      AND (p.status='Pending' AND s.imdsup_id='$username') OR ((p.status='Pending'
      OR p.status='Verified' OR p.status='Recommended') AND '$username' IN ($adm_user))";
    } else if(in_array($username,$hcd_admin)){
      $query = "SELECT id FROM program WHERE staff_id!='0'
      AND training_cat='external' AND (status='Pending' OR status='Verified' OR status='Recommended')";
    }
  } else if($cat=="in-house") {
    if(in_array($username, getUserName('hod_id',$username))){
      if(in_array($username, $hcd_admin)){
        $query = "SELECT id FROM program WHERE training_cat='in-house' AND (status='Pending' OR status='Recommended')";
       } else {
         $query = "SELECT s.id FROM staff s
         INNER JOIN program p ON s.id=p.imdsup_id
         WHERE p.training_cat='in-house' AND ((p.status='Pending') AND s.hod_id='$username')";
       }
    } else if(in_array($username, $hcd_admin)){
      $query = "SELECT id FROM program WHERE training_cat='in-house' AND (status='Pending' OR status='Recommended')";
    }
  } else if($cat=="evaluation") {
    $query = "SELECT * FROM (SELECT p.id FROM program p
    LEFT JOIN nomination n ON n.prog_id=p.id
    INNER JOIN staff s ON (s.id=n.staff_id)
    LEFT JOIN training_evaluation te ON (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
    LEFT JOIN training_eval_induction tei ON (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
    WHERE ((s.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house') or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='external') or
    (n.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
    and p.status='Approved' and p.tef='' and
    (DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4)
    and (p.type!=5 and p.type!=6)
    UNION ALL
    SELECT p.id
    FROM program p
    LEFT JOIN nomination n on n.prog_id=p.id
    INNER JOIN staff s on s.id=p.staff_id
    LEFT JOIN training_evaluation te on (te.prog_id=p.id and (te.staff_id=n.staff_id or te.staff_id=p.staff_id))
    LEFT JOIN training_eval_induction tei on (tei.prog_id=p.id and (tei.staff_id=n.staff_id or tei.staff_id=p.staff_id))
    WHERE(
    (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='in-house') or (s.imdsup_id='$username' and te.status='Pending' and p.training_cat='external') or
    (p.staff_id='$staffid' and ((te.status is null and p.type!='8') or (tei.status is null and p.type='8')) and now()>p.end_date) )
    AND p.status='Approved' and p.tef='' and
    (DATEDIFF(p.end_date, p.start_date) >=0 and time_to_sec(timediff(concat(p.end_date,' ',p.end_time), concat(p.start_date,' ',p.start_time) )) / 3600 >=4)
    AND (p.type!=5 and p.type!=6)  ) x";
  } else if($cat=="effectiveness") {
    $query = "SELECT * FROM(select te.id from program p
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
    and p.status='Approved' and p.tef='' and DATEDIFF(CURDATE(),p.end_date)>=90 and (p.type!=5 and p.type!=6) and tef.date_created is null and te.status='Verified' ) x";
  }
  if(!$query) {
    return 0;
  } else {
    // echo $query;die();
    $result = executeQuery($query,"etraining");
    return $result->rowCount();
  }
}
//e-training end
//itadmin start
function ITUserData() {
  $result = executeQuery("SELECT user_team,user_type FROM itadmin.user WHERE user_name='".$_SESSION["login"]["username"]."'");
  $row = $result->fetch();
  return array($row['user_team'],$row['user_type']);
}
function ITAdmin($task=null,$notice=0) {
  if($task == "sms") {
    $query = "SELECT report_id FROM it_support.it_report
              LEFT JOIN it_support.it_report_activities ON it_report_activities.ita_code=it_report.rep_code
              WHERE rep_status<>2";
  } else if($task == "crf") {
    $query = "SELECT rf_id FROM crf_system.request_form
            	LEFT JOIN crf_system.activities ON request_form.rf_code=activities.rf_code
            	LEFT JOIN crf_system.request_status_latest ON request_form.rf_code=request_status_latest.rf_code
            	WHERE rf_status<>0 AND rf_status<>10";
    if($notice <> null) {
      $query .= " AND rf_status=3";
    }
  } else if($task == "facility") {
    $query = "SELECT idrf_id FROM uid_system.idrequest_form
              WHERE idrf_itcompleted IS NULL
              AND idrf_type IN('REQUEST','LOAN')";
    if($notice <> null) {
      $user = ITUserData();
      if($user[0] == "SYSTEM") {
        $query .= " AND idrf_progress LIKE '%RITCONFIGPC%'  AND idrf_hardware IN('VIDEO CONFERENCE','DATA RECOVERY')";
      } else if($user[0] == "HOD MANAGER") {
        $query .= " AND idrf_progress LIKE '%RITAPPROVE%'";
      } else if($user[0] == "SUPPORT" || $user[0] == "ADMIN") {
        $query .= " AND idrf_progress LIKE '%RITCONFIGPC%'";
      } else {
        return 0;
      }
    }
  } else if($task == "id"){
    $query = "SELECT idrf_id FROM uid_system.idrequest_form
              WHERE idrf_completed IS NULL AND idrf_type <> 'REQUEST'";
    if($notice <> 0) {
      $user = ITUserData();
      if($user[0] == "SYSTEM") {
        $query .= " AND (idrf_progress LIKE '%TITSID%' OR idrf_progress LIKE '%TITID%')";
      } else if($user[0] == "SUPPORT"){
        $query .= " AND (idrf_progress LIKE '%TITCHECKPC%' OR idrf_progress LIKE '%TITPC%')";
      } else if($user[0] == "APPLICATION" && $user[1] == "STAFF"){
        $query .= " AND idrf_progress LIKE '%TITAPP%'";
      } else if($user[0] == "APPLICATION" && $user[1] == "SUPERVISOR"){
        $query .= " AND idrf_progress LIKE '%TITVERIFYAPP%'";
      } else if($user[0] == "DEVELOPMENT"){
        $query .= " AND idrf_progress LIKE '%TITSYS%'";
      } else if($user[0] == "ADMIN"){
        $query .= " AND idrf_progress LIKE '%TITACKNOWLEDGE%'";
      } else if($user[0] == "HOD MANAGER"){
        $query .= " AND (idrf_progress LIKE '%IITAPPROVE%' OR idrf_progress LIKE '%TITAPPROVE%')";
      } else {
        return 0;
      }
    }
  } else if($task == "storage"){
    $user = ITUserData();
    $query = "";
    if($user[0] == "SYSTEM") {
      $query .= "SELECT s_id FROM itadmin.storage WHERE s_st_id=2";
    }
    if($user[0] == "MANAGER" && $user[1] == "APPROVER") {
      $query .= "SELECT s_id FROM itadmin.storage WHERE s_st_id=1";
    }
  }
  if(!$query){
    return 0;
  } else {
    $result = executeQuery($query);
    return $result->rowCount();
  }
}
// function ITSMS() {
//   $result = executeQuery("SELECT report_id FROM it_report LEFT JOIN it_report_activities ON it_report_activities.ita_code=it_report.rep_code	WHERE rep_status <>'2' ","it_support");
//   return $result->rowCount();
// }
// function CRF() {
//   $result = executeQuery("SELECT * FROM crf_system.request_form
// 	LEFT JOIN crf_system.activities ON request_form.rf_code=activities.rf_code
// 	LEFT JOIN crf_system.request_status_latest ON request_form.rf_code=request_status_latest.rf_code
// 	WHERE rf_status <> '0' AND rf_status <> '10'","itadmin");
//   return $result->rowCount();
// }
function IDRequest($task=null) {
  $user = $_SESSION['login']['username'];
  $query = "SELECT idrf_id FROM idrequest_form WHERE idrf_itcompleted IS NULL";
  if($task == "recommend") {
    $query .= " AND idrf_progress='RRECOMMEND'";
  }
  if($task == "approve") {
    $query .= " AND idrf_progress='RAPPROVE'";
  }
  if($task == "acknowledge") {
    $query .= " AND idrf_progress='TACKNOWLEDGE TITSID'";
  }
  if($task != null) {
    $query .= " AND (idrf_supervisor='$user' OR idrf_hod='$user')";
  }
  $result = executeQuery($query,"uid_system");
  return $result->rowCount();
}
function ITPortal($task=null) {
  $user = $_SESSION['login']['username'];
  if(!$task || $task == "UAT" || $task == "REQUEST") {
    $query = "SELECT rf_id FROM request_form
    LEFT JOIN activities ON request_form.rf_code=activities.rf_code
    LEFT JOIN ( SELECT * FROM (SELECT * FROM request_status WHERE rs_name <> 'PROGRESS UPDATED BY IT DEVELOPER' AND rs_name <> 'PROJECT TIMELINE COMPLETED' AND rs_name <> 'PROJECT TIMELINE SAVED' ORDER BY rs_datetime DESC)
  	AS X GROUP BY rf_code ) latest_status ON request_form.rf_code=latest_status.rf_code";
    if($task == "UAT") {
      $query .= " WHERE rf_approver='$user' && rf_status=9";
    } else if($task == "REQUEST") {
      $query .= " WHERE rf_approver='$user' && rf_status=1";
    } else {
      $query .= " WHERE rf_username='$user' && rf_status=8";
    }
    $result = executeQuery($query,"crf_system");
  } else {
    $query = "SELECT idrf_progress,idrf_part FROM idrequest_form
          	LEFT JOIN idrequest_date ON (idrequest_date.date_code=idrequest_form.idrf_code OR idrequest_date.date_username=idrequest_form.idrf_username)
          	LEFT JOIN request_date ON request_date.date_code=idrequest_form.idrf_code
          	LEFT JOIN issuance ON issuance.iss_code=idrequest_form.idrf_code
          	LEFT JOIN termination ON termination.ter_code=idrequest_form.idrf_code
          	LEFT JOIN request ON request.req_code=idrequest_form.idrf_code
          	LEFT JOIN idrequest_status_latest ON idrequest_form.idrf_code=idrequest_status_latest.rs_code
          	WHERE (idrf_part<>'' AND idrf_progress<>'') AND idrf_part='STAFF'";
    if($task == "NEWRECOMMEND") {
      $query .= " AND idrf_supervisor='$user' AND idrf_progress='IRECOMMEND'";
    } else if($task == "NEWAPPROVE") {
      $query .= " AND idrf_hod='$user' AND idrf_progress='IAPPROVE'";
    } else if($task == "TERMINATERECOMMEND") {
      $query .= " AND idrf_supervisor='$user' AND idrf_progress='TRECOMMEND'";
    } else if($task == "TERMINATEAPPROVE") {
      $query .= " AND idrf_hod='$user' AND idrf_progress='TAPPROVE'";
    } else if($task == "TERMINATEACKNOWLEDGE") {
      $query .= " AND idrf_username='$user' AND idrf_progress='TACKNOWLEDGE'";
    } else if($task == "TERMINATE") {
      $query .= " AND idrf_username='$user' AND idrf_progress='TERMINATION'";
    } else if($task == "ASSETACKNOWLEDGE") {
      $query .= " AND idrf_username='$user' AND idrf_progress='TACKRETURNASSET'";
    } else if($task == "RRECOMMEND") {
      $query .= " AND idrf_supervisor='$user' AND idrf_progress='RRECOMMEND'";
    } else if($task == "RAPPROVE") {
      $query .= " AND idrf_hod='$user' AND idrf_progress='RAPPROVE'";
    }
    $result = executeQuery($query,"uid_system");
  }
  return $result->rowCount();
}
//itadmin end
//e-admin start
function fleet($task=null) {
  $department = department();
  $user = $_SESSION['login']['username'];
  if($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
  	$dashboard_dept = "AND (flf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR flf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
  } else if ($department == "COO`S OFFICE") {
  	$dashboard_dept = "AND (flf_codept='COO`S OFFICE' OR flf_codept='QUALITY TECH. AUDIT & OVERSEA')";
  } else if ($department == "ACP - DMT") {
  	$dashboard_dept = "AND (flf_codept='ACP - DMT' OR flf_codept='SPECIAL PRODUCT DIVISION')";
  } else if ($department == "GROUP SECURITY DEPARTMENT") {
  	$dashboard_dept = "AND (flf_codept='GROUP SECURITY DEPARTMENT' OR flf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
  } else if ($department == "COO (II)") {
  	$dashboard_dept = "AND (flf_codept='CORPORATE OFFICE' OR flf_codept='COO (II)')";
  } else if ($department == "CORPORATE OFFICE") {
  	$dashboard_dept = "AND (flf_codept='CORPORATE OFFICE' OR flf_codept='COO (II)')";
  } else {
  	$dashboard_dept = "AND flf_codept='$department'";
  }
  if($task == null) {
    $result = executeQuery("SELECT COUNT(flf_id) AS jumlah_inprogress FROM form_fleet
              WHERE (flf_status <> '8' AND flf_status <> '9' AND flf_status <> '13') ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_inprogress'];
  } else if($task == "survey") {
    $result = executeQuery("SELECT COUNT(flf_id) AS jumlah_survey FROM e_admin.form_fleet
              WHERE (flf_status <> '8' AND flf_status <> '9' AND flf_status <> '13')
              AND flf_by = '$user' AND flf_status = '6' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_survey'];
  } else if($task == "cancel") {
    $result = executeQuery("SELECT COUNT(flf_id) AS jumlah_cancel FROM e_admin.form_fleet
    WHERE (flf_status <> '8' AND flf_status <> '9' AND flf_status <> '13')
    AND flf_by = '$user' AND (flf_status BETWEEN '1' AND '4') ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_cancel'];
  } else if($task == "hod") {
    $result = executeQuery("SELECT count(flf_id) AS jumlah_hod_approve FROM e_admin.form_fleet
    WHERE (flf_status <> '8' AND flf_status <> '9' AND flf_status <> '13')
    AND flf_apprv_user = '$user'
    AND flf_status = '1' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_hod_approve'];
  }
}
function maintainace($task=null) {
  $department = department();
  $user = $_SESSION['login']['username'];
  if($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
  	$dashboard_dept = "AND (mtf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR mtf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
  } else if ($department == "COO`S OFFICE") {
  	$dashboard_dept = "AND (mtf_codept='COO`S OFFICE' OR mtf_codept='QUALITY TECH. AUDIT & OVERSEA')";
  } else if ($department == "ACP - DMT") {
  	$dashboard_dept = "AND (mtf_codept='ACP - DMT' OR mtf_codept='SPECIAL PRODUCT DIVISION')";
  } else if ($department == "GROUP SECURITY DEPARTMENT") {
  	$dashboard_dept = "AND (mtf_codept='GROUP SECURITY DEPARTMENT' OR mtf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
  } else if ($department == "COO (II)") {
  	$dashboard_dept = "AND (mtf_codept='CORPORATE OFFICE' OR mtf_codept='COO (II)')";
  } else if ($department == "CORPORATE OFFICE") {
  	$dashboard_dept = "AND (mtf_codept='CORPORATE OFFICE' OR mtf_codept='COO (II)')";
  } else {
  	$dashboard_dept = "AND mtf_codept='$department'";
  }
  if($task == null) {
    $result = executeQuery("SELECT COUNT(mtf_id) AS jumlah_inprogress FROM form_maintenance
              WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10') ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_inprogress'];
  } else if($task == "confirm") {
    $result = executeQuery("SELECT COUNT(mtf_id) AS jumlah_comform FROM e_admin.form_maintenance
    WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
    AND mtf_by = '$user' AND mtf_status = '4' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_comform'];
  } else if($task == "survey") {
    $result = executeQuery("SELECT count(mtf_id) AS jumlah_survey FROM e_admin.form_maintenance
    WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
    AND mtf_by = '$user' AND mtf_status = '7' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_survey'];
  } else if($task == "cancel") {
    $result = executeQuery("SELECT count(mtf_id) AS jumlah_cancel FROM e_admin.form_maintenance
    WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
    AND mtf_by = '$user' AND (mtf_status BETWEEN '1' AND '4') ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_cancel'];
  } else if($task == "hod") {
    $result = executeQuery("SELECT count(mtf_id) AS jumlah_approve1 FROM e_admin.form_maintenance
    WHERE (mtf_status <> '8' AND mtf_status <> '9' AND mtf_status <> '10')
    AND mtf_apprv_user = '$user' AND mtf_status = '1' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_approve1'];
  }
}
function room($task=null) {
  $department = department();
  $user = $_SESSION['login']['username'];
  if ($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
  	$dashboard_dept = " AND (mrf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR mrf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
  } else if ($department == "COO`S OFFICE") {
  	$dashboard_dept = " AND (mrf_codept='COO`S OFFICE' OR mrf_codept='QUALITY TECH. AUDIT & OVERSEA')";
  } else if ($department == "ACP - DMT") {
  	$dashboard_dept = " AND (mrf_codept='ACP - DMT' OR mrf_codept='SPECIAL PRODUCT DIVISION')";
  } else if ($department == "GROUP SECURITY DEPARTMENT") {
  	$dashboard_dept = " AND (mrf_codept='GROUP SECURITY DEPARTMENT' OR mrf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
  } else if ($department == "COO (II)") {
  	$dashboard_dept = " AND (mrf_codept='CORPORATE OFFICE' OR mrf_codept='COO (II)')";
  } else if ($department == "CORPORATE OFFICE") {
  	$dashboard_dept = " AND (mrf_codept='CORPORATE OFFICE' OR mrf_codept='COO (II)')";
  } else {
  	$dashboard_dept = " AND mrf_codept='$department'";
  }
  if($task == null) {
    $result = executeQuery("SELECT count(mrf_id) AS jumlah_inprogress FROM form_meeting_room
              WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_inprogress'];
  } else if($task == "cancel") {
    $result = executeQuery("SELECT count(mrf_id) AS jumlah_cancel FROM e_admin.form_meeting_room
    WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
    AND mrf_by = '$user' AND (mrf_status BETWEEN '1' AND '3') ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_cancel'];
  } else if($task == "survey") {
    $result = executeQuery("SELECT count(mrf_id) AS jumlah_survey FROM e_admin.form_meeting_room
    WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
    AND (mrf_by = '$user' OR mrf_username = '$user')
    AND mrf_status = '4' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_survey'];
  } else if($task == "hod") {
    $result = executeQuery("SELECT count(mrf_id) AS jumlah_approve FROM e_admin.form_meeting_room
    WHERE mrf_status <> '0' AND mrf_status <> '6' AND mrf_status <> '7'
    AND mrf_apprv_user = '$user'
    AND mrf_status = '1' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_approve'];
  }
}
function simcard($task=null) {
  $department = department();
  $user = $_SESSION['login']['username'];
  if($department == "BUSINESS DEVELOPMENT DEPARTMENT") {
  	$dashboard_dept = "AND (scf_codept='BUSINESS DEVELOPMENT DEPARTMENT' OR scf_codept='GROUP ENTERPRISE RISK MANAGEMENT DEPARTMENT')";
  } else if ($department == "COO`S OFFICE") {
  	$dashboard_dept = "AND (scf_codept='COO`S OFFICE' OR scf_codept='QUALITY TECH. AUDIT & OVERSEA')";
  } else if ($department == "ACP - DMT") {
  	$dashboard_dept = "AND (scf_codept='ACP - DMT' OR scf_codept='SPECIAL PRODUCT DIVISION')";
  } else if ($department == "GROUP SECURITY DEPARTMENT") {
  	$dashboard_dept = "AND (scf_codept='GROUP SECURITY DEPARTMENT' OR scf_codept='GROUP HEALTH, SAFETY & ENVIRONMENT DEPARTMENT')";
  } else if ($department == "COO (II)") {
  	$dashboard_dept = "AND (scf_codept='CORPORATE OFFICE' OR scf_codept='COO (II)')";
  } else if ($department == "CORPORATE OFFICE") {
  	$dashboard_dept = "AND (scf_codept='CORPORATE OFFICE' OR scf_codept='COO (II)')";
  } else {
  	$dashboard_dept = "AND scf_codept='$department'";
  }
  if($task == null) {
    $result = executeQuery("SELECT count(scf_id) AS jumlah_inprogress FROM form_simcard
              WHERE (scf_status <> '7' AND scf_status <> '8') ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_inprogress'];
  } else if($task == "cancel") {
    $result = executeQuery("SELECT count(scf_id) AS jumlah_cancel FROM e_admin.form_simcard
    LEFT JOIN e_admin.activity_simcard ON activity_simcard.scf_code=form_simcard.scf_code
    WHERE (scf_status <> '7' AND scf_status <> '8')
    AND scf_by = '$user'
    AND scf_status BETWEEN '1' AND '3' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_cancel'];
  } else if($task == "survey") {
    $result = executeQuery("SELECT count(scf_id) AS jumlah_survey FROM e_admin.form_simcard
    LEFT JOIN e_admin.activity_simcard ON activity_simcard.scf_code=form_simcard.scf_code
    WHERE (scf_status <> '7' AND scf_status <> '8')
    AND scf_by = '$user'
    AND scf_status = '6' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_survey'];
  } else if($task == "hod") {
    $result = executeQuery("SELECT count(scf_id) AS jumlah_approve1 FROM e_admin.form_simcard
    WHERE (scf_status <> '7' AND scf_status <> '8')
    AND scf_apprv_user = '$user' AND scf_status = '1' ".$dashboard_dept,"e_admin");
    $row = $result->fetch();
    return $row['jumlah_approve1'];
  }
}
function department() {
  $username = $_SESSION['login']['username'];
  $result = executeQuery("SELECT dept_desc FROM employee_info WHERE username='$username' AND status='Active'","eleave_v4");
  $row = $result->fetch();
  if($row['dept_desc'] == "TERRATECH CONSULTANT") { // FOR TERRATECH ONLY
    return "TERRATECH CONSULTANTS";
  } else if ($row['dept_desc'] == "CORPORATE COMMUNICATION") { // FOR CORPORATE COMM ONLY
    return "CORPORATE COMMUNICATION DEPT";
  } else if ($row['dept_desc'] == "REAL ESTATE & PROPERTY DEVELOP") { // FOR REAL ESTATE ONLY
    return "REAL ESTATE & PROPERTY DEVELOPMENT";
  } else if ($row['dept_desc'] == "ACP - DMT ") { // FOR ACP - DMT
    return "ACP - DMT";
  } else {
   return $row['dept_desc'];
  }
}
//e-admin end
function hod(){
  $user = $_SESSION['login']['username'];
  $result = executeQuery("SELECT staff.hod_id FROM staff WHERE staff.hod_id='$user' LIMIT 1","etraining");
  if($result->rowCount() == 0) {
    return "false";
  } else {
    return "true";
  }
}
function checkNotify() {
  $notify = 0;
  if($_SESSION['login']["level"] == "immediatesupervisor" || $_SESSION['login']["level"] == "supervisor") {
    $notify += countLeave('recommend')+countLeave('approve');
  }
  $notify += getCountNotify("external")+getCountNotify("in-house")+getCountNotify("evaluation")+getCountNotify("effectiveness");
  $notify += approveMovement();
  $notify += eadmin("fleet");
  $notify += eadmin("maintainace")+eadmin("maintainace","pre")+eadmin("maintainace","late")+eadmin("maintainace","pre-late")+eadmin("maintainace","confirm");
  $notify += eadmin("room")+eadmin("room","pre")+eadmin("room","late")+eadmin("room","pre-late");
  $notify += eadmin("sim")+eadmin("sim","pre")+eadmin("sim","late")+eadmin("sim","pre-late");
  $notify += ecmr("new")+ecmr("processed")+ecmr("verify");
  if(ITUser() > 0) {
    $notify += ITAdmin("crf",1)+ITAdmin("facility",1)+ITAdmin("id",1);
  }
  if(tollUser() > 0){
    $notify += countToll();
  }
  echo $notify;
}
function notify($mode=null) {
  $expires = time() + ($_SESSION['token_duration']*60*1000);
  $leave = 0;
  if($_SESSION['login']["level"] == "immediatesupervisor" || $_SESSION['login']["level"] == "supervisor") {
    $leave = countLeave('recommend')+countLeave('approve');
  }

  $train = getCountNotify("external")+getCountNotify("in-house")+getCountNotify("evaluation")+getCountNotify("effectiveness");
  $move = approveMovement();
  $ecmr = ecmr("new")+ecmr("processed")+ecmr("verify");
  if(ITUser() > 0) {
    $itadmin = ITAdmin("crf",1)+ITAdmin("facility",1)+ITAdmin("id",1);
  }
  $itportal = ITPortal()+ITPortal("UAT")+ITPortal("REQUEST")+ITPortal("NEWRECOMMEND")+ITPortal("NEWAPPROVE")+ITPortal("TERMINATERECOMMEND")+ITPortal("TERMINATEAPPROVE")+ITPortal("TERMINATEACKNOWLEDGE")+ITPortal("TERMINATE")+ITPortal("ASSETACKNOWLEDGE")+ITPortal("RRECOMMEND")+ITPortal("RAPPROVE");

  $admin  = eadmin("fleet");
  $admin  += eadmin("maintainace")+eadmin("maintainace","pre")+eadmin("maintainace","late")+eadmin("maintainace","pre-late")+eadmin("maintainace","confirm");
  $admin  += eadmin("room")+eadmin("room","pre")+eadmin("room","late")+eadmin("room","pre-late");
  $admin  += eadmin("sim")+eadmin("sim","pre")+eadmin("sim","late")+eadmin("sim","pre-late");

  if(tollUser() > 0){
    $toll = countToll();
  }

  $notify = $leave+$train+$move+$ecmr+$itadmin+$itportal+$admin+$toll;
  if($mode == null) {
    if($notify > 0) {
      echo "<p class=\"text-center\">Pending Notification</p>";
  ?>
  <ul>
    <?php
    if($itportal > 0) {
      $token = verifyToken($expires,"IT Portal");
      $url = "http://apps.mtdgroup.com.my/itportal/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
      ?>
      <li>
        <a href="<?php echo $url;?>" target="_blank">
          <span><?php echo $itportal;?></span>
          <h3>IT Portal</h3>
          <?php if(ITPortal("REQUEST") > 0) {?>
            <p>Approve Request (<?php echo ITPortal("REQUEST");?>)</p>
          <?php } if(ITPortal("UAT") > 0) {?>
            <p>Approve UAT Form(<?php echo ITPortal("UAT");?>)</p>
          <?php } if(ITPortal() > 0) {?>
            <p>Verify UAT Form(<?php echo ITPortal();?>)</p>
          <?php }if(ITPortal("NEWRECOMMEND") > 0) {?>
            <p>Recommend ID Issuance Request(<?php echo ITPortal("NEWRECOMMEND");?>)</p>
          <?php } if(ITPortal("NEWAPPROVE") > 0) {?>
            <p>Approve ID Issuance Request(<?php echo ITPortal("NEWAPPROVE");?>)</p>
          <?php } if(ITPortal("TERMINATERECOMMEND") > 0) {?>
            <p>Recommend Termination Request(<?php echo ITPortal("TERMINATERECOMMEND");?>)</p>
          <?php }if(ITPortal("TERMINATEAPPROVE") > 0) {?>
            <p>Approve Termination Request(<?php echo ITPortal("TERMINATEAPPROVE");?>)</p>
          <?php } if(ITPortal("TERMINATEACKNOWLEDGE") > 0) {?>
            <p>Acknowledge Termination Request(<?php echo ITPortal("TERMINATEACKNOWLEDGE");?>)</p>
          <?php } if(ITPortal("TERMINATE") > 0) {?>
            <p>Cancel Termination Request(<?php echo ITPortal("TERMINATE");?>)</p>
          <?php } if(ITPortal("ASSETACKNOWLEDGE") > 0) {?>
            <p>Acknowledge Asset Return(<?php echo ITPortal("ASSETACKNOWLEDGE");?>)</p>
          <?php } if(ITPortal("RRECOMMEND") > 0) {?>
            <p>Recommend IT Facilities Request(<?php echo ITPortal("RRECOMMEND");?>)</p>
          <?php } if(ITPortal("RAPPROVE") > 0) {?>
            <p>Approve IT Facilities Request(<?php echo ITPortal("RAPPROVE");?>)</p>
          <?php }?>
        </a>
      </li>
    <?php
    } if($leave > 0) {
      $token = verifyToken($expires,"e-Leave");
      $url = "http://apps.mtdgroup.com.my/e-leave/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
    ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank">
        <span><?php echo $leave;?></span>
        <h3>e-Leave</h3>
        <?php if(countLeave('recommend') > 0) {?>
          <p>Leave application for your recommendation(<?php echo countLeave('recommend');?>)</p>
        <?php } if(countLeave('approve') > 0) {?>
          <p>Leave application for your approval(<?php echo countLeave('approve');?>)</p>
        <?php }?>
      </a>
    </li>
  <?php
  } if($admin > 0) {
    $token = verifyToken($expires,"e-Admin");
    $url = "http://apps.mtdgroup.com.my/e-admin/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
  ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank">
        <span><?php echo $admin;?></span>
        <h3>e-Admin</h3>
        <?php
        if(eadmin("fleet") > 0) {
          echo "<p><b>FLEET</b></p>";
          echo "<p>Pending Approve Request(".eadmin("fleet").")</p>";
        }
        $maintain = eadmin("maintainace")+eadmin("maintainace","pre")+eadmin("maintainace","late")+eadmin("maintainace","pre-late")+eadmin("maintainace","confirm");
        if($maintain > 0) {
          echo "<p><b>MAINTAINACE/REPAIR</b></p>";
          $maintain = eadmin("maintainace")+eadmin("maintainace","pre")+eadmin("maintainace","late")+eadmin("maintainace","pre-late");
          if(eadmin("maintainace")+eadmin("maintainace","pre")+eadmin("maintainace","late")+eadmin("maintainace","pre-late") > 0){
            echo "<p>Pending Approve Request(".$maintain.")</p>";
          }
          if(eadmin("maintainace","confirm") > 0){
            echo "<p>Pending Confirm Job(".eadmin("maintainace","confirm").")</p>";
          }
        }
        $room = eadmin("room")+eadmin("room","pre")+eadmin("room","late")+eadmin("room","pre-late");
        if($room > 0) {
          echo "<p><b>MEETING ROOM</b></p>";
          echo "<p>Pending Approve Request(".$room.")</p>";
        }
        if(eadmin("sim")+eadmin("sim","pre")+eadmin("sim","late")+eadmin("sim","pre-late") > 0) {
          echo "<p><b>SIMCARD</b></p>";
          echo "<p>Pending Approve Request(".eadmin("sim")+eadmin("sim","pre")+eadmin("sim","late")+eadmin("sim","pre-late").")</p>";
        }
        ?>
      </a>
    </li>
  <?php
  } if($move > 0) {
    $token = verifyToken($expires,"e-Movement");
    $url = "http://apps.mtdgroup.com.my/emovement/process/login/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
  ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank">
        <span><?php echo $move;?></span>
        <h3>e-Movement</h3>
        <p>Pending Application</p>
      </a>
    </li>
  <?php
  } if($train > 0) {
    $token = verifyToken($expires,"e-Training");
    $url = "http://apps.mtdgroup.com.my/e-training/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
  ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank">
        <span><?php echo $train;?></span>
        <h3>e-Training</h3>
        <?php
        if(getCountNotify("external") > 0) {
          echo "<p>Training Requisition(".getCountNotify("external").")</p>";
        }
        if(getCountNotify("in-house") > 0) {
          echo "<p>Training Nomination(".getCountNotify("in-house").")</p>";
        }
        if(getCountNotify("evaluation") > 0) {
          echo "<p>Training Evaluation(".getCountNotify("evaluation").")</p>";
        }
        if(getCountNotify("effectiveness") > 0) {
          echo "<p>Training Effectiveness(".getCountNotify("effectiveness").")</p>";
        }
        ?>
      </a>
    </li>
    <?php
    } if($ecmr > 0) {
      $token = verifyToken($expires,"eCMR");
      $url = "http://apps.mtdgroup.com.my/ecmr/process/main/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
    ?>
      <li>
        <a href="<?php echo $url;?>" target="_blank">
          <span><?php echo $ecmr;?></span>
          <h3>eCMR</h3>
          <?php
          if(ecmr("new") > 0) {
            echo "<p>New Case(".ecmr("new").")</p>";
          }
          if(ecmr("processed") > 0) {
            echo "<p>In Process(".ecmr("processed").")</p>";
          }
          if(ecmr("verify") > 0) {
            echo "<p>Verification(".ecmr("verify").")</p>";
          }
          ?>
        </a>
      </li>
    <?php
    } if($itadmin > 0 && ITUser() > 0) {
      $token = verifyToken($expires,"IT Portal");
      $url = "http://apps.mtdgroup.com.my/itportal/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
    ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank">
        <span><?php echo $itadmin;?></span>
        <h3>IT Admin</h3>
        <?php
        if(ITAdmin("crf",1) > 0) {
          echo "<p>CRF<br>Pending Approval/Task(".ITAdmin("crf",1).")</p>";
        }
        if(ITAdmin("facility",1) > 0) {
          echo "<p>IT FACILITIES REQUEST<br>Pending Approval/Task(".ITAdmin("facility",1).")</p>";
        }
        if(ITAdmin("id",1) > 0) {
          echo "<p>ID REQUEST SYSTEM<br>Pending Approval/Task(".ITAdmin("id",1).")</p>";
        }
        ?>
      </a>
    </li>
  <?php }
    if($toll > 0 && tollUser() > 0) {
      $token = verifyToken($expires,"User ID (Toll System)");
      $url = "http://apps.mtdgroup.com.my/badge/secure_login.php?expired=$expires&token=$token&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
    ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank">
        <span><?php echo $toll;?></span>
        <h3>User ID (Toll System)</h3>
        <p>Pending Approval/Task</p>
      </a>
    </li>
  <?php }?>
  </ul>
  <?php
    } else {
      ?><li class="text-center">No Notification</li><?php
    }
  } else {
    return $notify;
  }
}
//phone directory function start
function selectDepartDivision($id=null) {
  $result = executeQuery("SELECT div_id,div_name FROM phone_dir.division ORDER BY div_name");
  if($result->rowCount() != 0) {
    while($row = $result->fetch()) {
      $div_id = $row['div_id'];
      $results = executeQuery("SELECT dept_id,dept_name FROM department WHERE div_id='$div_id' AND dept_name IS NOT NULL AND dept_name <> '' ORDER BY dept_name","phone_dir");
      if($results->rowCount() != 0) {
        ?>
        <optgroup label="<?php echo $row['div_name'];?>">
          <?php while($rows = $results->fetch()) {?>
            <option value="<?php echo $rows['dept_id'];?>"><?php echo $rows['dept_name'];?></option>
          <?php }?>
        </optgroup>
        <?php
      }
    }
  }
}
function selectLevel($id=null) {
  $result = executeQuery("SELECT lvl_id,lvl_name FROM phone_dir.level ORDER BY lvl_name");
  if($result->rowCount() != 0) {
    while($row = $result->fetch()) {
    ?><option value="<?php echo $row['lvl_id'];?>"><?php echo $row['lvl_name'];?></option><?php
    }
  }
}
function searchDirectory($key=null,$dept=0,$lvl=0,$admin=null) {
  if($dept != 0 && $key != null) {
    $result = executeQuery("SELECT div_id,dept_name FROM department WHERE dept_id='$dept'","phone_dir");
    $row = $result->fetch();
    $search = "WHERE emp_name LIKE '%$key%'";
    if($row['dept_name'] == "ALL") {
      $search .= " AND division.div_id='".$row['div_id']."'";
    } else {
      $search .= " AND dept_id='$dept'";
    }
  } else if($dept == 0 && $key != null) {
    $search = "WHERE emp_name LIKE '%$key%'";
  } else if($dept != 0 && $key == null) {
    $result = executeQuery("SELECT div_id,dept_name FROM department WHERE dept_id='$dept'","phone_dir");
    $row = $result->fetch();
    if($row['dept_name'] == "ALL") {
      $search = "WHERE division.div_id='".$row['div_id']."'";
    } else {
      $search = "WHERE dept_id='$dept'";
    }
  }
  if($lvl <> 0 && ($key == null && $dept == 0)) {
    $search .= "WHERE lvl_id=".$lvl;
  } else if($lvl <> 0){
    $search .= " AND lvl_id=".$lvl;
  }
  $result = executeQuery("SELECT emp_id,emp_name,emp_ext,emp_hp,dept_name,div_name,lvl_name FROM phone INNER JOIN level ON lvl_id=emp_level INNER JOIN department ON dept_id=emp_dept INNER JOIN division ON division.div_id=department.div_id $search ORDER BY emp_name","phone_dir");
  if($result->rowCount() != 0) {
?>
<div class="table-responsive">
	<table class="table table-striped">
    <thead>
      <tr class="text-center">
        <?php if($admin == "true") {?>
          <th width="10%">Action</th>
        <?php }?>
        <th width="35%">Name</th>
        <th width="10%">Extension</th>
        <th width="10%">Mobile</th>
        <th width="35%">Division - Department</th>
        <th width="10%">Level/Area</th>
      </tr>
    </thead>
	  <tbody>
      <?php while($row = $result->fetch()) {?>
        <tr class="text-center">
          <?php if($admin == "true") {?>
            <td>
              <div class="dropdown">
								<a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
									<i class="dw dw-more"></i>
								</a>
								<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
									<a class="dropdown-item" style="cursor:pointer;" onclick="showModal('Extention','<?php echo $row['emp_id'];?>','Phone/Extension')"><i class="dw dw-edit2"></i> Edit</a>
								</div>
							</div>
            </td>
          <?php }?>
          <td class="text-left"><?php echo $row['emp_name'];?></td>
          <td><?php echo $row['emp_ext'];?></td>
          <td><?php echo $row['emp_hp'];?></td>
          <td class="text-left"><?php echo $row['div_name']." - ".$row['dept_name'];?></td>
          <td><?php echo $row['lvl_name'];?></td>
        </tr>
      <?php }?>
	  </tbody>
	</table>
</div>
<?php
  } else {
    ?>
    <div class="col-12 text-center"><i class="fa fa-warning fa-2x text-danger"></i> <h4>No result found from your search</h4></div>
    <?php
  }
}
function systemExtention($id=null) {
  $ids = "";
  $title = "";
  $desc = "";
  $db = "";
  $url = "";
  if($id != null) {
    $result = executeQuery("SELECT * FROM phone WHERE emp_id='$id'","phone_dir");
    $row = $result->fetch();
    $ids = $row['emp_id'];
    $title = $row['emp_name'];
    $desc = $row['emp_ext'];
    $db = $row['emp_level'];
    $url = $row['emp_hp'];
  }
  ?>
  <input type="hidden" id="sys_task" value="extension">
  <input type="hidden" id="ext_id" value="<?php echo $ids;?>">
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Name</label>
		<div class="col-sm-12 col-md-5">
			<input id="ext_name" class="form-control" type="text" placeholder="Employee Name" value="<?php echo $title;?>">
		</div>
	</div>
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Extension</label>
		<div class="col-sm-12 col-md-5">
      <input id="ext_code" class="form-control" type="text" placeholder="Extension" value="<?php echo $desc;?>">
		</div>
	</div>
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Mobile</label>
		<div class="col-sm-12 col-md-5">
			<input id="ext_mobile" class="form-control" type="text" placeholder="Mobile No" value="<?php echo $url;?>">
		</div>
	</div>
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Level</label>
		<div class="col-sm-12 col-md-5">
      <select id="ext_level" class="form-control" data-size="5">
        <?php
          $result = executeQuery("SELECT lvl_id,lvl_name FROM level","phone_dir");
          while($row = $result->fetch()) {
        ?>
          <option value="<?php echo $row['lvl_id'];?>" <?php if($row['lvl_id'] == $db) { echo "selected";}?>><?php echo $row['lvl_name'];?></option>
        <?php }?>
      </select>
		</div>
	</div>
<?php }

function systemForm($id=null) {
  $file = "";
  $desc = "";
  $cat = "";
  if($id != null){
    $result = executeQuery("SELECT * FROM forms WHERE f_id=".$id);
    $row = $result->fetch();
    $file = $row['f_filename'];
    $desc = $row['f_desc'];
    $cat = $row['f_rc_id'];
  }
  ?>
  <input type="hidden" id="sys_task" value="form">
  <input type="hidden" id="form_id" value="<?php echo $id;?>">
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Description</label>
		<div class="col-sm-12 col-md-8">
			<input id="ext_name" class="form-control" type="text" placeholder="Description" value="<?php echo $desc;?>">
		</div>
	</div>
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Category</label>
		<div class="col-sm-12 col-md-8">
      <select id="level" class="form-control" data-size="5">
        <?php
          $result = executeQuery("SELECT * FROM ref_category WHERE rc_type='form'");
          while($row = $result->fetch()) {
        ?>
          <option value="<?php echo $row['rc_id'];?>" <?php if($row['rc_id'] == $cat) { echo "selected";}?>><?php echo $row['rc_desc'];?></option>
        <?php }?>
      </select>
		</div>
	</div>
  <div class="form-group row">
    <label class="col-sm-12 col-md-2 col-form-label">Document</label>
    <div class="col-sm-12 col-md-8">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="attachment">
        <label class="custom-file-label">Choose file</label>
        <p><sub>* Allowed filetypes: .pdf  <br>* Maximum file size: 5MB</sub></p>
      </div>
    </div>
  </div>
<?php }
function systemDms($id=null) {
  $desc = "";
  $url = "";
  if($id != null){
    $result = executeQuery("SELECT dms_desc,dms_url FROM dms WHERE dms_id=".$id);
    $row = $result->fetch();
    $desc = $row['dms_desc'];
    $url = $row['dms_url'];
  }
  ?>
  <div class="form-group row">
		<label class="col-2 col-form-label">Description</label>
		<div class="col-8">
      <input type="text" class="form-control" placeholder="Description" value="<?php echo $desc;?>">
			<!-- <input id="ext_name" class="form-control" type="text" placeholder="Employee Name" value="Adrizal Jaafar"> -->
		</div>
	</div>
  <div class="form-group row">
		<label class="col-2 col-form-label">URL</label>
		<div class="col-8">
      <input type="text" class="form-control" placeholder="URL" value="<?php echo $url;?>">
			<!-- <input id="ext_name" class="form-control" type="text" placeholder="Employee Name" value="Adrizal Jaafar"> -->
		</div>
	</div>
<?php }
//phone directory function end
function systemDetail($id=null) {
  $ids = "";
  $title = "";
  $desc = "";
  $db = "";
  $url = "";
  if($id != null) {
    $result = executeQuery("SELECT * FROM system WHERE sys_id='$id'");
    $row = $result->fetch();
    $ids = $row['sys_id'];
    $title = $row['sys_title'];
    $desc = $row['sys_desc'];
    $db = $row['sys_db'];
    $url = $row['sys_url'];
  }
  ?>
  <input type="hidden" id="sys_task" value="system">
  <input type="hidden" id="sys_id" value="<?php echo $ids;?>">
  <div class="row">
    <div class="col-6">
      <div class="form-group">
    		<label class="col-form-label">System</label>
    		<input id="sys_title" class="form-control" type="text" placeholder="System Name" value="<?php echo $title;?>">
    	</div>
    </div>
    <div class="col-6">
      <div class="form-group">
    		<label class="col-form-label">Database</label>
    		<input id="sys_db" class="form-control" type="text" placeholder="Database" value="<?php echo $db;?>">
    	</div>
    </div>
  </div>
  <div class="row">
    <div class="<?php if($id == 1){echo "col-6";} else {echo "col-12";}?>">
      <div class="form-group">
    		<label class="col-form-label">URL</label>
    		<input id="sys_url" class="form-control" type="text" placeholder="URL" value="<?php echo $url;?>">
    	</div>
    </div>
  <?php if($id == 1){?>
    <div class="col-3">
      <div class="form-group">
    		<label class="col-form-label">Date From (Maintainace)</label>
    		<input id="sys_from" class="form-control datetimepicker" type="text" placeholder="Date From">
    	</div>
    </div>
    <div class="col-3">
      <div class="form-group">
    		<label class="col-form-label">Date To (Maintainace)</label>
    		<input id="sys_to" class="form-control datetimepicker" type="text" placeholder="Date To">
    	</div>
    </div>
  <?php }?>
  </div>
  <div class="form-group row" style="display:none;">
		<label class="col-sm-12 col-md-2 col-form-label">Description</label>
		<div class="col-sm-12 col-md-10">
			<textarea id="sys_desc" class="form-control"><?php echo $desc;?></textarea>
		</div>
	</div>
<?php }
function saveDetail($id=null,$title,$desc,$db,$url) {
  if($id != null) {
    executeQuery("UPDATE system SET sys_title='$title',sys_desc='$desc',sys_db='$db',sys_url='$url' WHERE sys_id='$id'");
  } else {
    executeQuery("INSERT INTO system (sys_title,sys_desc,sys_db,sys_url)VALUES('$title','$desc','$db','$url')");
  }
}
function saveExtention($id=null,$name,$code,$mobile,$level) {
  // $query = "UPDATE phone SET emp_name='$name',emp_ext='$code',emp_hp='$mobile',emp_level='$level' WHERE emp_id='$id'";
  // echo $query;
  executeQuery("UPDATE phone SET emp_name='$name',emp_ext='$code',emp_hp='$mobile',emp_level='$level' WHERE emp_id='$id'","phone_dir");
}
function keepTrack($query,$from="e-central") {
  $search = "SELECT";
  if(preg_match("/{$search}/i", $query) == false) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $user = $_SESSION['login']['username'];
    $query = escapeString($query);
    executeQuery("INSERT INTO trail(t_user,t_from,t_query,t_ip,t_date)VALUES('$user','$from',$query,'$ip',NOW())",null,1);
  }
}
function lastLogin($task=null) {
  $user = $_SESSION['login']['username'];
  $result = executeQuery("SELECT t_ip,t_date FROM trail WHERE t_user='$user' AND t_query='login' ORDER BY t_date DESC LIMIT 2");
  if($result->rowCount() > 0) {
    $ip = "";
    $date = "";
    while($row = $result->fetch()) {
      $ip = $row['t_ip'];
      $date = $row['t_date'];
    }
    if($task == "IP") {
      return $ip;
    } else {
      return $date;
    }
  } else {
    return "";
  }
}
function getContent($id,$aria) {
  $result = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id=".$id);
  while($row = $result->fetch()) {
    if(!$row['p_filename']) {
      getContent($row['p_id'],"tab-pane fade");
    } else {
      //echo $row['p_filename'];
      ?>
      <div class="<?php echo $aria;?>" id="tab<?php echo $row['p_id'];?>" role="tabpanel">
        <div class="pd-20">
          <embed src="upload/<?php echo $row['p_path']."/".$row['p_filename'];?>" width="100%" height="1150" type="application/pdf">
        </div>
      </div>
      <?php
    }
  }
}
function getLink($p_desc,$id,$class,$aria) {
  ?>
  <li class="nav-item">
    <?php echo $p_desc;?>
    <ul class="nav flex-column vtabs nav-tabs" role="tablist">
      <?php
      $result = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id='$id' ORDER BY p_id");
      $class = "";
      while($row = $result->fetch()) {
        if(!$row['p_filename']) {
          getLink($row['p_desc'],$row['p_id'],$class,$aria);
        } else {
          ?><li class="nav-item"><a class="<?php echo $class;?>" data-toggle="tab" href="#tab<?php echo $row['p_id'];?>" role="tab" aria-selected="<?php echo $aria;?>"><?php echo $row['p_desc'];?></a></li><?php
        }
      }
    ?>
    </ul>
  </li>
  <?php
}
function showLink($desc,$id,$lvl=0) {
  if($lvl == 0) {
    ?>
    <li class="dropdown">
      <a href="javascript:;" class="dropdown-toggle">
        <span class="micon fa fa-list"></span><span class="mtext"><?php echo $desc;?></span>
      </a>
      <ul class="submenu">
        <?php
        $result = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id='$id' ORDER BY p_id");
        while($row = $result->fetch()) {
          if(!$row['p_filename']) {
            showLink($row['p_desc'],$row['p_id'],1);
          } else {
            ?><li><a data-toggle="tab" href="#tab<?php echo $row['p_id'];?>"><?php echo $row['p_desc'];?></a></li><?php
          }
        }
        ?>
      </ul>
    </li>
    <?php
  } else {
    ?>
    <li class="dropdown">
      <a href="javascript:;" class="dropdown-toggle">
        <span class="micon fa fa-arrow-right"></span><span class="mtext"><?php echo $desc;?></span>
      </a>
      <ul class="submenu child">
        <?php
        $result = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id='$id' ORDER BY p_id");
        while($row = $result->fetch()) {
          if(!$row['p_filename']) {
            showLink($row['p_desc'],$row['p_id'],1);
          } else {
            ?><li><a data-toggle="tab" href="#tab<?php echo $row['p_id'];?>"><?php echo $row['p_desc'];?></a></li><?php
          }
        }
        ?>
      </ul>
    </li>
    <?php
  }
}
function linked($id,$desc) {
  ?>
  <li style="cursor:pointer;"><?php echo $desc;?>
    <ul>
      <?php
        $result = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id='$id' ORDER BY p_sort");
        while($row = $result->fetch()) {
          if(!$row['p_filename']) {
            linked($row['p_id'],$row['p_desc']);
          } else {
            ?><li><a data-toggle="tab" id="link_<?php echo $row['p_id'];?>" href="#tab<?php echo $row['p_id'];?>" onclick="changeTab(<?php echo $row['p_id'];?>)"><?php echo $row['p_desc'];?></a></li><?php
          }
        }
      ?>
    </ul>
  </li>
  <?php
}
//e-leave function start
function checkHandleSup($username){
  $result = executeQuery("SELECT u.id FROM user u INNER JOIN supervisor s ON u.id=s.user_id INNER JOIN handle_supervisor hs ON hs.sup_id=s.supervisor_id WHERE u.username='$username'","eleave_v4");
  if($result->rowCount() != 0) {
    return 1;
  } else {
    return 0;
  }
}
function getValSup($username,$val){
  $result = executeQuery("SELECT sup_id,comp_id FROM user u INNER JOIN supervisor s ON u.id=s.user_id INNER JOIN handle_supervisor hs ON hs.sup_id=s.supervisor_id WHERE u.username='$username'","eleave_v4");
  if($result->rowCount() != 0) {
    $row = $result->fetch();
    if($val=='sup_id'){
      return $row["sup_id"];
    } else {
      return $row["comp_id"];
    }
  }
}
function leave($emp_id) {
  $emp_id = empID($emp_id);
  $result = executeQuery("SELECT brought_fwd,earned_leave,grant_entitle,employee_dateresigned,earned_sickleave,earned_replaceleave,employee_category FROM employee e INNER JOIN status s ON e.employee_id=s.employee_id WHERE e.employee_id='$emp_id'","eleave_v4");
  $leave = array();
  if($result->rowCount() > 0) {
    $row = $result->fetch();
    if(!$row["earned_replaceleave"]) {
      $replace = 0;
    } else {
      $replace = $row["earned_replaceleave"];
    }
    array_push($leave,$row["brought_fwd"],$row["earned_leave"],$row["grant_entitle"],$row["employee_dateresigned"],$row["earned_sickleave"],$replace,$row["employee_category"]);
  } else {
    array_push($leave,0,0,"No","0000-00-00",0,0,0);
  }
  return $leave;
}
function ecmr($task) {
  $staff_id = $_SESSION["login"]["username"];
  $result = executeQuery("SELECT authorised_for FROM authorised WHERE staff_id = '$staff_id' AND status = 0 AND active_status = 1","cmr");
  if($result->rowCount() > 0) {
      $row = $result->fetch();
      $author = $row['authorised_for'];
      $query = "SELECT complaint_id FROM complaint WHERE STATUS = 0";
      if($task == "new") {
        $query .= " AND activity = 1";
      } else if($task == "processed") {
        $query .= " AND activity = 2";
      } else if($task == "verify") {
        $query .= " AND activity = 3";
      }
      $status = array(1,2);
      if(in_array($author,$status)) {
        $query .= "";
      } else {
        $query .= " AND FIND_IN_SET($author,respondent) <> 0";
      }
      $result = executeQuery($query,"cmr");
      return $result->rowCount();
    } else {
      return 0;
    }
  }
function countLeave($task) {
  $year = date("Y");
  $username = $_SESSION["login"]["username"];
  $emp_id = $_SESSION["login"]["id"];
  $staff_id = empID($emp_id);
  $leave = leave($emp_id);
  if($task == "approve") {
    $handleSup = checkHandleSup($username);
    if($handleSup == 1) {
      $sup_id = getValSup($username,'sup_id');
      $comp_id = getValSup($username,'comp_id');
      $result = executeQuery("SELECT `leave`.id FROM user INNER JOIN supervisor ON user.id=supervisor.user_id INNER JOIN employee ON supervisor.supervisor_id=employee.supervisor_id INNER JOIN `leave` ON employee.employee_id=`leave`.employee_id INNER JOIN handle_supervisor hs ON employee.employee_company=hs.comp_id WHERE hs.sup_id='$sup_id' and `leave`.status='pending' AND employee.status='Active'","eleave_v4");
    } else {
      $result = executeQuery("SELECT `leave`.id FROM user INNER JOIN supervisor ON user.id=supervisor.user_id INNER JOIN employee ON supervisor.supervisor_id=employee.supervisor_id INNER JOIN `leave` ON employee.employee_id=`leave`.employee_id WHERE user.username='$username' AND `leave`.status='pending' AND employee.status='Active' AND `leave`.recommend_date<>'0000-00-00 00:00:00'","eleave_v4");
    }
    return $result->rowCount();
  } else if($task == "recommend") {
    $result = executeQuery("SELECT `leave`.id FROM user INNER JOIN supervisor ON user.id=supervisor.user_id INNER JOIN employee ON supervisor.supervisor_id=employee.immediate_supid INNER JOIN `leave` ON employee.employee_id=`leave`.employee_id WHERE user.username='$username' AND `leave`.status='pending' AND employee.status='Active' AND `leave`.recommend_date='0000-00-00 00:00:00'","eleave_v4");
    return $result->rowCount();
  } else if($task == "carry") {
    return $leave[0];
  } else if($task == "earn") {
    if($leave[2] == "No") {
      $brought_fwd = $leave[0];
      $earned_leave = $leave[1];
      $grant_entitle = $leave[2];
      $employee_dateresigned = $leave[3];
      $date = date("Y-m-d");
      $fyStart = "1/1"; // set start financial date
      $fyEnd = "12/31"; // set end financial date
      $fyStartCheck="-01-01"; // set start financial date for checking view form
      $fyEndCheck="-12-31"; // set end financial date for checking view form
      $num_year = "0"; // fix value = 0. no need to change. wheather 0 (current year = calendar year) or 1 (minus 1 year = fiscal year) for start year
      $carry_fwd_interval = "+6 months";
      $startfinancialyeardate = calculateFiscalYearForDate($date,$fyStart,$fyEnd)-$num_year;
      $endfinancialyeardate = calculateFiscalYearForDate($date,$fyStart,$fyEnd);
      $startdate = $startfinancialyeardate.$fyStartCheck;
      $enddate = $endfinancialyeardate.$fyEndCheck;
      $endcarryfwd = date('Y-m-d', strtotime($carry_fwd_interval, strtotime($startdate)));
      $period_date1 = date('Y-m-d', strtotime("+3 months", strtotime($startdate)));
      $period_date2 = date('Y-m-d', strtotime("+3 months", strtotime($period_date1)));
      $period_date3 = date('Y-m-d', strtotime("+3 months", strtotime($period_date2)));
      $period_date4 = date('Y-m-d', strtotime("+3 months", strtotime($period_date3)));
      $quarters = get_quarters($startdate,$enddate);
      $tmpPeriod = $leave[1]/$quarters;
      $valPeriod = round($tmpPeriod, 2);
      $tPeriod = floor($valPeriod);
      $testVal1 = $tPeriod + 0.0;
      $testVal2 = $tPeriod + 0.5;

      $firstQuarter_leave = getquarterleave($emp_id,$startdate,$endcarryfwd);

      $periodEarned_leave = 0;
      if($date < $period_date1 && ($date>=$startdate && $date<=$enddate)){
       if($testVal1 == $valPeriod || $testVal2 == $valPeriod){
         $periodEarned_leave = $valPeriod;
       } else if($valPeriod < $testVal2){
         $periodEarned_leave = $testVal2;
       } else if($valPeriod > $testVal2){
         $periodEarned_leave = round($valPeriod);
       }
      } else if ($date < $period_date2 && ($date >= $startdate && $date <= $enddate)){
       if($carry_fwd_interval == "+6 months"){
      	 if($testVal1 == $valPeriod || $testVal2 == $valPeriod){
      	   $periodEarned_leave = ($valPeriod*2);
      	 } else if($valPeriod < $testVal2){
      	    $periodEarned_leave = ($testVal2*2);
      	 } else if($valPeriod > $testVal2){
      	    $periodEarned_leave = (round($valPeriod)*2);
      	 }
       } else {
      	 if($testVal1 == $valPeriod || $testVal2 == $valPeriod){
      	  if($firstQuarter_leave >= $brought_fwd){
      	     $carryfwdTaken = $brought_fwd;
      	     $periodEarned_leave = ($valPeriod*2)+$carryfwdTaken;
      	  } else{
      	     $periodEarned_leave=($valPeriod*2)+$firstQuarter_leave;
      	  }
      	 } else if($valPeriod < $testVal2){
      	  if($firstQuarter_leave >= $brought_fwd){
      	     $carryfwdTaken=$brought_fwd;
      	     $periodEarned_leave = ($testVal2*2)+$carryfwdTaken;
      	  } else{
      	     $periodEarned_leave = ($testVal2*2)+$firstQuarter_leave;
      	  }
      	 } else if($valPeriod > $testVal2){
      	  if($firstQuarter_leave >= $brought_fwd){
      	     $carryfwdTaken = $brought_fwd;
      	     $periodEarned_leave = (round($valPeriod)*2)+$carryfwdTaken;
      	  } else{
      	     $periodEarned_leave = (round($valPeriod)*2)+$firstQuarter_leave;
      	  }
      	 }
       }
      } else if ($date < $period_date3 && ($date >= $startdate && $date <= $enddate)){
       if($testVal1 == $valPeriod || $testVal2 == $valPeriod){
        if($firstQuarter_leave >= $brought_fwd){
          $carryfwdTaken = $brought_fwd;
          $periodEarned_leave = ($valPeriod*3)+$carryfwdTaken;
        } else {
          $periodEarned_leave = ($valPeriod*3)+$firstQuarter_leave;
        }
       } else if($valPeriod<$testVal2){
        if($firstQuarter_leave >= $brought_fwd){
          $carryfwdTaken = $brought_fwd;
          $periodEarned_leave = ($testVal2*2)+$testVal1+$carryfwdTaken;
        } else {
          $periodEarned_leave = ($testVal2*2)+$testVal1+$firstQuarter_leave;
        }
       } else if($valPeriod>$testVal2){
        if($firstQuarter_leave >= $brought_fwd){
          $carryfwdTaken = $brought_fwd;
          $periodEarned_leave = (round(($valPeriod),0,PHP_ROUND_HALF_UP)*3+$carryfwdTaken);
        } else{
          $periodEarned_leave = (round(($valPeriod),0,PHP_ROUND_HALF_UP)*3+$firstQuarter_leave);
        }
       }
      }elseif ($date < $period_date4 && ($date >= $startdate && $date <= $enddate)){
       if($testVal1==$valPeriod || $testVal2==$valPeriod){
        if($firstQuarter_leave >= $brought_fwd){
          $carryfwdTaken = $brought_fwd;
          $periodEarned_leave = ($valPeriod*4)+$carryfwdTaken;
        } else{
          $periodEarned_leave=($valPeriod*4)+$firstQuarter_leave;
        }
       } else if($valPeriod < $testVal2){
        if($firstQuarter_leave >= $brought_fwd){
          $carryfwdTaken = $brought_fwd;
          $tmpPeriodEarned_leave = (($testVal2*4)+$carryfwdTaken)-1;
          if($earned_leave != $tmpPeriodEarned_leave){
            $periodEarned_leave = $earned_leave+$carryfwdTaken;
          } else{
            $periodEarned_leave=$tmpPeriodEarned_leave;
          }
        } else{
          $tmpPeriodEarned_leave = (($testVal2*4)+$firstQuarter_leave)-1;
          if($earned_leave != $tmpPeriodEarned_leave){
            $periodEarned_leave = $earned_leave+$firstQuarter_leave;
          } else{
            $periodEarned_leave = $tmpPeriodEarned_leave;
          }
        }
       } else if($valPeriod > $testVal2){
        if($firstQuarter_leave >= $brought_fwd){
        $carryfwdTaken = $brought_fwd;
        $tmpPeriodEarned_leave = ((round($valPeriod)*4)+$carryfwdTaken)-1;
         if($earned_leave != $tmpPeriodEarned_leave){
           $periodEarned_leave = $earned_leave+$carryfwdTaken;
         } else{
           $periodEarned_leave = $tmpPeriodEarned_leave;
         }
        } else{
          $tmpPeriodEarned_leave = ((round($valPeriod)*4)+$firstQuarter_leave)-1;
          if($earned_leave != $tmpPeriodEarned_leave){
           $periodEarned_leave = $earned_leave+$firstQuarter_leave;
         } else{
           $periodEarned_leave = $tmpPeriodEarned_leave;
         }
        }
       }
      }
      if($date >= $startdate && $date < $endcarryfwd){
        $periodEarned_leave = $periodEarned_leave;
      } else {
        if($firstQuarter_leave>=$brought_fwd){
          $quarterEarned_leave = $periodEarned_leave-$brought_fwd;
          $periodEarned_leave = $quarterEarned_leave;
        } else {
          $quarterEarned_leave = $periodEarned_leave-$firstQuarter_leave;
          $periodEarned_leave = $quarterEarned_leave;
        }
      }
      return $periodEarned_leave;
    } else {
      return $leave[1];
    }
  } else if($task == "entitle") {
    return $leave[1];
  } else if($task == "taken") {
    $taken = 0;
    $result = executeQuery("SELECT days_leave FROM `leave` WHERE employee_id='$staff_id' AND Year(from_date)='$year' AND status='approved' AND (leave_type='Annual Leave' OR leave_type='Emergency Leave')","eleave_v4");
    while($row = $result->fetch()){
      $taken += $row['days_leave'];
    }
    return $taken;
  } else if($task == "sick-taken") {
    $taken = 0;
    $result = executeQuery("SELECT days_leave FROM `leave` WHERE employee_id='$staff_id' AND Year(from_date)='$year' AND status='approved' AND leave_type='Sick Leave'","eleave_v4");
    while($row = $result->fetch()){
      $taken += $row['days_leave'];
    }
    return $taken;
  } else if($task == "balance") {
    if($leave[6] != "permanent"){
      return $leave[0]+$leave[1]+$leave[5]-countLeave('taken');
    } else {
      return $leave[0]+countLeave('earn')+$leave[5]-countLeave('taken')-countLeave("forfeit");
    }
  } else if($task == "sick") {
    $result = executeQuery("SELECT id FROM `leave` WHERE employee_id='$staff_id' AND Year(from_date)='$year' AND status='approved' AND leave_type='Sick Leave'","eleave_v4");
    $take = $result->rowCount();
    return $leave[4]-$take;
  } else if($task == "replace") {
    return $leave[5];
  } else if($task == "forfeit") {
    $taken = 0;
    $date = carryInterval("+6 month");
    if($date <= date("Y-m-d")){
      $result = executeQuery("SELECT days_leave FROM `leave`
              WHERE employee_id='$staff_id' AND YEAR(from_date)='$year' AND to_date<'$date'
              AND STATUS='approved' AND (leave_type='Annual Leave' OR leave_type='Emergency Leave')","eleave_v4");
      while($row = $result->fetch()){
        $taken += $row['days_leave'];
      }
      if($taken < $leave[0]){
        return $leave[0]-$taken;
      } else {
        return 0;
      }
    } else{
      return 0;
    }
  }
}
function carryInterval($interval){
  $date = date("Y")."-01-01";
  return date('Y-m-d', strtotime($date.$interval));
}
function getquarterleave($empid,$startdate,$endcarryfwd){
  $result = executeQuery("SELECT days_leave FROM `leave` WHERE employee_id='$empid' and from_date>='$startdate' and from_date<'$endcarryfwd' and status='approved' and (leave_type='Annual Leave' or leave_type='Emergency Leave')","eleave_v4");
  $leave_taken = 0;
  if($result->rowCount() != 0) {
    while($row = $result->fetch()) {
      $leave_taken += $row["days_leave"];
    }
  } else {
    $leave_taken = 0;
  }
  return $leave_taken;
}
function calculateFiscalYearForDate($inputDate,$fyStart,$fyEnd){
  $date = strtotime($inputDate);
  $inputyear = strftime('%Y',$date);

  $fystartdate = strtotime($fyStart.'/'.$inputyear);
  $fyenddate = strtotime($fyEnd.'/'.$inputyear);

  if($date <= $fyenddate){
    $fy = intval($inputyear);
  } else {
    $fy = intval(intval($inputyear) + 1);
  }
  return $fy;
}
// Return quarters between tow dates. Array of objects
function get_quarters($start_date, $end_date){
	$quarters = array();
	$start_month = date( 'm', strtotime($start_date) );
	$start_year = date( 'Y', strtotime($start_date) );
	$end_month = date( 'm', strtotime($end_date) );
	$end_year = date( 'Y', strtotime($end_date) );
	$start_quarter = ceil($start_month/3);
	$end_quarter = ceil($end_month/3);
	$quarter = $start_quarter;

	for( $y = $start_year; $y <= $end_year; $y++ ){
		if($y == $end_year)
			$max_qtr = $end_quarter;
		else
			$max_qtr = 4;
		for($q=$quarter; $q<=$max_qtr; $q++){
			$current_quarter = new stdClass();
			$quarters[] = $current_quarter;
			unset($current_quarter);
		}
		$quarter = 1; // reset to 1 for next year
	}
	return count($quarters);
}//get_quarters
//e-Leave function end
function empID($userid) {
  $result = executeQuery("SELECT employee_id FROM employee WHERE user_id='$userid'","eleave_v4");
  $row = $result->fetch();
  return $row['employee_id'];
}
function staffID($username) {
  $result = executeQuery("SELECT id FROM staff WHERE staff_id='$username'","etraining");
  $row = $result->fetch();
  return $row['id'];
}
function sendReminder(){
  $day = (int)date("d");
  $month = (int)date("m");
  $year = date("Y");
  if($day >= 10){
    $result = executeQuery("SELECT email_id FROM badge.email WHERE email_month=$month AND email_year=$year");
    if($result->rowCount() == 0){
      $result = executeQuery("SELECT ref_code FROM badge.reference WHERE ref_status_id=5");
      if($result->rowCount() > 0){
        $str = '<table border="1">
        <tr>
          <td style="padding:5px;text-align:center;">No</td>
          <td style="padding:5px;text-align:center;">Reference No</td>
        </tr>';
        $count = 1;
        $locate = array();
        while($row = $result->fetch()){
          array_push($locate,$row['ref_code']);
          $str .= '<tr>
            <td style="padding:5px;text-align:center;">'.$count++.'</td>
            <td style="padding:5px;">'.$row['ref_code'].'</td>
          </tr>';
        }
        $str .= '</table>';
        $header = "From: USER ID for TOLL SYSTEM \n";
      	$header .= "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=UTF-8 \r\n";
      	$subject = "Request Reminder";
      	$message_header = '<html>
      	<head>
      		<title>USER ID for TOLL SYSTEM: System Reminder</title>
      	</head>
      	<body>';
      	$message_footer = '----------------------------------------------------------------------------------<br>
      			This is an auto notification from System. Please do not reply.
      		</body>
      	</html>';
        $results = executeQuery("SELECT access_user_id,access_loc_id FROM badge.access WHERE access_task_id LIKE '%4%'");
        while($rows = $results->fetch()){
          $user = $rows['access_user_id'];
          $access = explode(",",$rows['access_loc_id']);
          foreach ($locate as $key => $val) {
            $form = array();
            $result = executeQuery("SELECT form_loc_id FROM badge.form WHERE form_ref_no='$val'");
            while($row = $result->fetch()){
              array_push($form,$row['form_loc_id']);
            }
            foreach ($form as $key => $value) {
              if(in_array($value,$access)){
                $result = executeQuery("SELECT employee_name,employee_email FROM eleave_v4.employee WHERE user_id=$user");
                $row = $result->fetch();
                $message = '<p>Dear '.$row['employee_name'].'</p>
            		<p>Task reminder for reference no:</p>
                '.$str.'
            		<p>Please login <a href="http://apps.mtdgroup.com.my/badge/">http://apps.mtdgroup.com.my/badge/</a> and go to USER ID for TOLL SYSTEM to check or approve this request</p>
            		<p>Regards.</p>';
                $body =  $message_header.$message.$message_footer;
                mail($row['employee_email'],$subject,$body,$header);
                $result = executeQuery("SELECT email_id FROM badge.email WHERE email_month=$month AND email_year=$year");
                if($result->rowCount() == 0){
                  executeQuery("INSERT INTO badge.email(email_month,email_year)VALUES($month,$year)");
                }
              }
            };
          }
        }
      }
    }
  }
}
?>
