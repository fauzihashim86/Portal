<?php $row = employee_detail($_SESSION['login']['username']);?>
<div class="row">
  <div class="col-xl-4 col-md-6 mb-30">
    <div class="row">
      <div class="col-12">
        <div class="pd-20 card-box height-100-p">
          <div class="profile-photo">
    				<a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
    				<img src="upload/profile/<?php echo profile();?>" alt="" class="avatar-photo" width="150" height="150">
    				<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    					<div class="modal-dialog modal-dialog-centered" role="document">
    						<div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Upload Picture</h4>
    								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                  </div>
    							<div class="modal-body pd-5">
              			<div class="custom-file">
              				<input type="file" class="custom-file-input" id="attachment" onchange="uploadImg()">
              				<label class="custom-file-label">Choose file</label>
                      <p><sub>* Allowed filetypes: .png | .jpg  <br>* Maximum file size: 5MB</sub></p>
              			</div>
    							</div>
    							<div class="modal-footer">
    								<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
    							</div>
    						</div>
    					</div>
    				</div>
    			</div>
      		<h5 class="text-center h5 mb-0"><?php echo $row['employee_name'];?></h5>
      		<p class="text-center text-muted font-14"><?php echo $row['employee_position'];?></p>
      		<div class="profile-info">
      			<!-- <h5 class="mb-20 h5 text-blue">Contact Information</h5> -->
      			<ul>
      				<li><span>Employee`s No:</span><?php echo $row['employee_ic_no'];?></li>
              <li><span>Employee`s Email:</span><?php echo $row['employee_email'];?></li>
      				<li><span>Position:</span><?php echo $row['employee_position'];?></li>
      				<li><span>Job Grade:</span><?php echo $row['grade'];?></li>
      				<li><span>Date Joined:</span><?php echo displayDate($row['employee_datejoined'],"d M Y");?></li>
              <li><span>Department/Project:</span><?php echo $row['description'];?></li>
      				<li><span>Company:</span><?php echo $row['comp'];?></li>
      				<li><span>Immediate Superior:</span><?php echo $row['immediateName'];?></li>
      				<li><span>Superior:</span><?php echo $row['supervisorName'];?></li>
              <?php
                $result = executeQuery("SELECT c.band_type,c.working_hour FROM attendance a
                INNER JOIN batch b on a.batch=b.batch
                INNER JOIN band c ON (a.schedule_type=c.band_time AND b.event_type=c.event_type)
                WHERE a.employee_no='".$row['employee_ic_no']."' ORDER BY a.attendance_id DESC LIMIT 1","movement_db");
                if($result->rowCount() > 0){
                  $rows = $result->fetch();
              ?>
              <li><span>Working Band:</span><?php echo $rows['band_type'];?></li>
      				<li><span>Working Hours:</span><?php echo $rows['working_hour'];?></li>
              <?php }?>
      			</ul>
      		</div>
		    </div>
      </div>
    </div>
	</div>
  <div class="col-xl-8 col-md-6 mb-30">
    <div class="row">
      <div class="col-12">
        <div class="card card-box" style="overflow-y: auto;overflow-xs: hidden;">
          <div class="card-header">Leave Balance</div>
          <!-- <div class="card-body" style="overflow-y: auto;overflow-xs: hidden;"> -->
            <table class="table table-striped" role="grid">
              <thead>
                <tr role="row" class="text-center">
                  <th colspan="5">Annual</th>
                  <th colspan="3">Medical</th>
                </tr>
                <tr role="row" class="text-center">
                  <th>Full Entitlement</th>
                  <th>Earned Leave</th>
                  <th>Brought Forward</th>
                  <th>Taken</th>
                  <th>Balance</th>
                  <th>Entitlement</th>
                  <th>Taken</th>
                  <th>Balance</th>
                </tr>
      				</thead>
      				<tbody>
                <?php $leave = leave($_SESSION["login"]["id"]); ?>
                <tr class="text-center">
                  <td><?php echo countLeave('entitle');?></td>
                  <td><?php echo countLeave('earn');?></td>
                  <td><?php echo countLeave('carry');?></td>
                  <td><?php echo countLeave('taken');?></td>
                  <td><?php echo countLeave('balance');?></td>
                  <td><?php echo $leave[4];?></td>
                  <td><?php echo countLeave('sick-taken');?></td>
                  <td><?php echo countLeave('sick');?></td>
                </tr>
              </tbody>
            </table>
          <!-- </div> -->
    		</div>
      </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-12">
        <div class="card card-box" style="overflow-y: auto;overflow-xs: hidden;">
          <div class="card-header">IT Request</div>
          <table class="table table-striped" role="grid">
            <?php
            $result = executeQuery("SELECT * FROM uid_system.idrequest_form
                  		LEFT JOIN uid_system.idrequest_date ON idrequest_date.date_username=idrequest_form.idrf_username
                  		WHERE idrf_username='".$_SESSION["login"]["username"]."'
                  		AND idrf_completed='YES'");
            $count = $result->rowCount();
            if($count > 0){
            ?>
    				<tbody>
              <?php
              while($row = $result->fetch()){;
                if($row['idrf_type'] == "ISSUANCE"){
              ?>
                <tr>
                  <td colspan="2" class="text-center"><b>ID Issuance</b></td>
                </tr>
                <tr>
                  <td>ID Request Form</td>
                  <td width="5%" class="text-center"><a onclick="showPDF('ID Request Form','<?php echo $row["idrf_id"];?>','<?php echo $row["idrf_code"];?>','<?php echo $row["idrf_type"];?>','<?php echo $row["idrf_username"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a></td>
                </tr>
                <?php if($row["idrf_pc_assetcode"] != ""){?>
                <tr>
                  <td>IT Asset Issuance Form (Asset Code: <?php echo $row['idrf_pc_assetcode'];?>)</td>
                  <td class="text-center"><a onclick="showPDF('IT Asset Issuance Form (Asset Code: <?php echo $row['idrf_pc_assetcode'];?>)','<?php echo $row["idrf_id"];?>','<?php echo $row["idrf_code"];?>','<?php echo $row["idrf_type"];?>','<?php echo $row["idrf_username"];?>','<?php echo $row["idrf_pc_assetcode"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a></td>
                </tr>
                <?php } if($row["idrf_other_assetcode"] != ""){?>
                  <tr>
                    <td>IT Asset Issuance Form - Others (Asset Code: <?php echo $row["idrf_other_assetcode"];?>)</td>
                    <td class="text-center"><a onclick="showPDF('IT Asset Issuance Form - Others (Asset Code: <?php echo $row["idrf_other_assetcode"];?>','<?php echo $row["idrf_id"];?>','<?php echo $row["idrf_code"];?>','<?php echo $row["idrf_type"];?>','<?php echo $row["idrf_username"];?>','<?php echo $row["idrf_other_assetcode"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a></td>
                  </tr>
                <?php } if($row["idrf_apps_software"] != "") {?>
                  <tr>
                    <td>System Access Request Form</td>
                    <td class="text-center"><a onclick="showPDF('System Access Request Form','<?php echo $row["idrf_id"];?>','<?php echo $row["idrf_code"];?>','<?php echo $row["idrf_type"];?>','<?php echo $row["idrf_username"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a></td>
                  </tr>
                <?php }?>
                  <tr>
                    <td>Letter Of Undertaking and Indemnity</td>
                    <td class="text-center"><a onclick="showPDF('Letter Of Undertaking and Indemnity','<?php echo $row["idrf_id"];?>','<?php echo $row["idrf_code"];?>','<?php echo $row["idrf_type"];?>','<?php echo $row["idrf_username"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a></td>
                  </tr>
                <?php if($count != 1){?>
                <tr>
                  <td colspan="2" class="text-center"><b>IT Facilities Request</b></td>
                </tr>
              <?php }} else {
                if($row["idrf_hardware"] == "VIDEO CONFERENCE" || $row["idrf_hardware"] == "DATA RECOVERY"){
                ?>
                <tr>
                  <td><?php echo $row["idrf_code"];?></td>
                  <td class="text-center">
                    <?php if($row_req["idrf_conference_status"] == "NO") {?>
                      <i class="fa fa-eye-slash" title="Request Rejected"></i>
                    <?php } else {?>
                      <a onclick="showPDF('IT Facilities Request Form','<?php echo $row["idrf_id"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a>
                    <?php }?>
                  </td>
                </tr>
                <?php
              } else {
                ?>
                <tr>
                  <td><?php echo $row["idrf_code"];?></td>
                  <td width="5%" class="text-center"><a onclick="showPDF('ID Request Form','<?php echo $row["idrf_id"];?>','<?php echo $row["idrf_code"];?>','<?php echo $row["idrf_type"];?>','<?php echo $row["idrf_username"];?>')" title="View" href="#"><i class="fa fa-eye"></i></a></td>
                </tr>
                <?php
              }}}?>
            </tbody>
            <?php }?>
          </table>
    		</div>
      </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-12">
        <div class="card card-box" style="overflow-y: auto;overflow-xs: hidden;">
          <?php
          $query = "SELECT *,p.id as progid, p.staff_id as extStaffID, n.staff_id as inhseStaffID
          FROM etraining.program p
          LEFT JOIN etraining.nomination n ON n.prog_id=p.id
          LEFT JOIN etraining.type_program t ON t.id=p.type
          INNER JOIN etraining.staff s ON s.id=p.staff_id OR s.id=n.staff_id
          INNER JOIN eleave_v4.user u ON s.staff_id=u.username
          INNER JOIN eleave_v4.employee e ON u.id=e.user_id
          WHERE s.staff_id='".$_SESSION["login"]["username"]."' AND (p.date_complete IS NOT NULL OR p.status='Approved')
          ORDER BY p.start_date DESC";
          $result = executeQuery($query);
          ?>
          <div class="card-header">Personal Training Record (<?php echo $result->rowCount();?>)</div>
          <table class="table table-striped" role="grid">
            <thead>
              <tr>
                <th>Title</th>
                <th>Venue</th>
                <th>Type of Program</th>
                <th>Training Category</th>
                <th>Name of Training provider</th>
                <th>Fee</th>
            </thead>
            <tbody>
            <?php while($row = $result->fetch()){?>
              <tr>
                <td><?php echo $row["title"];?></td>
                <td><?php echo $row["venue"];?></td>
                <td><?php echo $row["name"];?></td>
                <td><?php echo $row["training_cat"];?></td>
                <td><?php echo $row["training_provider"];?></td>
                <td>RM<?php echo number_format($row["fee"],2);?></td>
              </tr>
            <?php }?>
          </tbody>
        </table>
      </div>
    </div>
	</div>
</div>
