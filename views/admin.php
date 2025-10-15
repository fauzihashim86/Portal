<?php
  $session_user= $_SESSION["login"]["username"];
  $result = executeQuery("SELECT user_team FROM itadmin.user WHERE user_name='$session_user'");
  $row = $result->fetch();
  if($row['user_team'] == "DEVELOPMENT") {
?>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
      <div class="pd-20 card-box">
        <div class="tab">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active text-blue" data-toggle="tab" href="#system" role="tab" aria-selected="true">System</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#dms" role="tab" aria-selected="false">DMS</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#form" role="tab" aria-selected="false">Forms</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#policy" role="tab" aria-selected="false">Policies</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#phone" role="tab" aria-selected="false">Phone/Extension</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#level" role="tab" aria-selected="false">Level</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#division" role="tab" aria-selected="false">Division</a></li>
      			<li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#department" role="tab" aria-selected="false">Department</a></li>
      		</ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="system" role="tabpanel"><?php getTable("system");?></div>
            <div class="tab-pane fade" id="phone" role="tabpanel"><?php getTable("phone");?></div>
            <div class="tab-pane fade" id="level" role="tabpanel"><?php getTable("level");?></div>
            <div class="tab-pane fade" id="division" role="tabpanel"><?php getTable("division");?></div>
            <div class="tab-pane fade" id="department" role="tabpanel"><?php getTable("department");?></div>
            <div class="tab-pane fade" id="form" role="tabpanel"><?php getTable("form");?></div>
            <div class="tab-pane fade" id="policy" role="tabpanel"><?php getTable("policy");?></div>
            <div class="tab-pane fade" id="dms" role="tabpanel"><?php getTable("dms");?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } else if(phoneUser() > 0){?>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
      <div class="pd-20 card-box">
        <div class="tab">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link text-blue active text-blue" data-toggle="tab" href="#phone" role="tab" aria-selected="false">Phone/Extention</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#level" role="tab" aria-selected="false">Level</a></li>
            <li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#division" role="tab" aria-selected="false">Division</a></li>
      			<li class="nav-item"><a class="nav-link text-blue" data-toggle="tab" href="#department" role="tab" aria-selected="false">Department</a></li>
      		</ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="phone" role="tabpanel"><?php getTable("phone");?></div>
            <div class="tab-pane fade" id="level" role="tabpanel"><?php getTable("level");?></div>
            <div class="tab-pane fade" id="division" role="tabpanel"><?php getTable("division");?></div>
            <div class="tab-pane fade" id="department" role="tabpanel"><?php getTable("department");?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } else { require "error.php";}?>
