<div class="pd-20 height-100-p mb-30">
	<div class="row align-items-center">
		<div class="col-md-12">
			<h4 class="font-20 weight-500 mb-10 text-capitalize">
				Welcome back <div class="weight-600 font-30 text-blue"><?php echo $_SESSION["login"]["name"];?></div>
			</h4>
			<p>Your last login is on <b><?php echo displayDate(lastLogin(),"d F Y H:i A");?></b></p>
		</div>
    <div class="col-12">
      <?php
      $result = executeQuery("SELECT * FROM apps WHERE apps_active=1");
      foreach ($result as $row):
      ?>
      <div class="btn-group">
        <a class="btn btn-app btn-danger" data-toggle="dropdown" aria-expanded="false" title="<?php echo $row['apps_name'];?>"><span class="badge bg-danger">0</span><i class="micon fa <?php echo $row['apps_icon'];?>"></i> <sub><?php echo $row['apps_code'];?></sub></a>
        <div class="dropdown-menu" role="menu" style="">
          <a class="dropdown-item" href="#">Action</a>
        </div>
      </div>
      <?php endforeach;?>
    </div>
	</div>
</div>
