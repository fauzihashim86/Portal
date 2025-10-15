
<div class="card-box pd-20 height-100-p mb-30">
	<div class="row align-items-center">
		<div class="col-md-12">
			<h4 class="font-20 weight-500 mb-10 text-capitalize">
				Welcome back <div class="weight-600 font-30 text-blue"><?php echo $_SESSION["login"]["name"];?></div>
			</h4>
			<p>Your last login is on <b><?php echo displayDate(lastLogin(),"d F Y H:i A");?></b></p>
		</div>
    <div class="col-12">
      <div class="row" style="display:none;" id="loadView">
        <div class="col-md-12 text-center"><img src="src/images/spin.gif"></div>
      </div>
      <div class="row" id="updateView">
        <?php
        sendReminder();
        if(isset($_GET['sys'])) {
          changeView($_GET['sys']);
        } else {
          changeView();
        }
        ?>
      </div>
    </div>
	</div>
</div>
