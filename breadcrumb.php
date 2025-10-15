<?php
$site = ucfirst(substr($filename, 0, strrpos($filename, '.')));
?>
<div class="row">
  <div class="col-5">
    <div class="title">
      <h4><?php echo $site;?></h4>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./?id=1">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $site;?></li>
        <?php if($site == "Dashboard") {?>
          <li class="breadcrumb-item" id="currentSystem"></li>
        <?php }?>
      </ol>
    </nav>
  </div>
  <div class="col-7">
    <?php if($site == "Dashboard") {?>
      <div class="dropdown pull-right">
        <a class="btn btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">
          Select System
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" style="cursor:pointer;" onclick="changeView(0)">Home</a>
          <a class="dropdown-item" style="cursor:pointer;" onclick="changeView(9,'Phone/Extension Directory')">Phone/Extension Directory</a>
          <a class="dropdown-item" style="cursor:pointer;" onclick="changeView(100,'Policies & Procedures (Policies)')">Policies & Procedures (Policies)</a>
          <a class="dropdown-item" style="cursor:pointer;" onclick="changeView(110,'Policies & Procedures (Forms)')">Policies & Procedures (Forms)</a>
          <?php
          $result = systemList("sys_active=1");
          while($row = $result->fetch()) {
            if(access($row['sys_title']) == 1){
              if($row['sys_board'] == 1) {
                ?><a class="dropdown-item" style="cursor:pointer;" onclick="changeView('<?php echo $row['sys_id'];?>','<?php echo $row['sys_title'];?>')"><?php echo $row['sys_title'];?></a><?php
              } else {
                $expires = time() + ($_SESSION['token_duration']*60*1000);
                $token = verifyToken($expires,$row['sys_title']);
                $url = $row['sys_url']."secure_login.php?expired=".$expires."&token=".$token."&u=".encrypt($_SESSION['login']['username'],$token)."&p=".encrypt($_SESSION["login"]["password"],$token);
                ?><a class="dropdown-item" href="<?php echo $url;?>" target="_blank"><?php echo $row['sys_title'];?></a><?php
              }
            }
          }
          $results = executeQuery("SELECT news_attach FROM newsletter ORDER BY news_id DESC LIMIT 1");
          if($results->rowCount() > 0){
          ?>
            <a class="dropdown-item" style="cursor:pointer;" onclick="changeView(112,'Newsletter')">Newsletter</a>
          <?php }?>
          <a class="dropdown-item" style="cursor:pointer;" onclick="changeView(111,'Document Management System')">Document Management System</a>
        </div>
      </div>
      <input type="hidden" id="clock">
    <?php } else if($site == "Report") {?>
      <select class="custom-select col-3 pull-right mr10" id="report_month" onchange="changeTime()">
				<option selected="">Select Month</option>
        <?php
          $start = 1;
          while($start <= 12) {
        ?>
				<option value="<?php echo $start ;?>"><?php echo date('F', mktime(0, 0, 0, $start, 10));?></option>
        <?php $start++;}?>
			</select>
      <select class="custom-select col-3 pull-right" id="report_year" onchange="changeTime()">
				<option selected="">Select Year</option>
        <?php
          $start = 2020;
          while($start <= date("Y")) {
        ?>
				<option value="<?php echo $start ;?>"><?php echo $start ;?></option>
        <?php $start++;}?>
			</select>
      <input type="hidden" id="clock">
    <?php } else {?>
      <label id="clock" class="pull-right"></label>
    <?php }?>
  </div>
</div>
