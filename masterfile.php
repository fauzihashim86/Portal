</head>
	<body>
		<?php
		//include "loader.php";
		include "header.php";
		?>
			<div class="right-sidebar">
				<?php require "sidebar-right.php";?>
			</div>
			<div class="left-side-bar">
				<?php require "sidebar-left.php";?>
			</div>
			<div class="mobile-menu-overlay"></div>
			<div class="main-container">
				<!-- <div class="xs-pd-20-10 pd-ltr-20"> -->
					<?php //if($id <> 1) {?>
						<div class="page-header">
							<?php require "breadcrumb.php";?>
						</div>
					<?php //}?>
					<?php require "views/".$filename;?>
					<?php //if($id <> 1) {?>
						<div class="footer-wrap pd-20 mb-20 card-box">
							Copyright <?php echo date("Y");?> Group IT, MTD GROUP
						</div>
					<?php //}?>
				<div class="modal fade bs-example-modal-lg" id="bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
							<div class="modal-body"><center><img src='src/images/spin.gif'></center></div>
							<div class="modal-footer">
								<button type="button" id="saved" class="btn btn-primary" onclick="saved()"><i class="fa fa-envelope"></i> Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade bs-example-modal-lg" id="modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Large modal</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
							<div class="modal-body"><center><img src='src/images/spin.gif'></center></div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade bs-example-modal-lg" id="modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Large modal</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
							<div class="modal-body"><center><img src='src/images/spin.gif'></center></div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="login-box bg-white box-shadow border-radius-10">
								<div class="login-title">
									<h2 class="text-center text-primary"><i class="icon-copy dw dw-lock"></i> Screen Lock</h2>
								</div>
								<div class="input-group custom">
									<input type="text" id="username" class="form-control form-control-lg" placeholder="Username" value="<?php echo $_SESSION['login']['username'];?>" readonly>
									<div class="input-group-append custom">
										<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
									</div>
								</div>
								<div class="input-group custom">
									<input type="password" id="getpassword" class="form-control form-control-lg" placeholder="**********">
									<div class="input-group-append custom">
										<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
									</div>
								</div>
								<div class="row pb-30">
									<div class="col-sm-12">
										<div class="input-group mb-0">
											<button id="log" class="btn btn-primary btn-lg btn-block" onclick="unlock()">Login</button>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-12 text-center">
										<a href="./?id=5">Or login as a different user</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
		<!-- js -->
	<script src="src/scripts/core.js"></script>
	<script src="src/scripts/script.min.js"></script>
	<script src="src/scripts/process.js"></script>
	<script src="src/scripts/layout-settings.js"></script>
	<script src="src/scripts/datatable-setting.js"></script>
	<script src="src/scripts/dateclock.js"></script>
	<script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
	<script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
	<!-- buttons for Export datatable -->
	<script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.print.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
	<script src="src/plugins/datatables/js/pdfmake.min.js"></script>
	<script src="src/plugins/datatables/js/vfs_fonts.js"></script>
	<script src="src/plugins/switchery/switchery.min.js"></script>
	<script src="src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
	<script src="src/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.js"></script>
	<script src="src/scripts/advanced-components.js"></script>
	<script src="src/plugins/printDiv/printDiv.js"></script>
	<script src="src/plugins/printDiv/jQuery.print.js"></script>
	<script src="src/plugins/sweetalert2/sweetalert2.all.js"></script>
	<script>token = <?php echo ($_SESSION['token_duration']-1)*60*1000;?>;</script>
	<script src="central.js"></script>
	<?php
		if($filename == "policies.php") {
			echo "<script>$('.make_tree').treed();</script>";
		}
	 ?>
