<?php if($_SESSION["login"]["username"] == "fauzi.hashim"){?>
<div class="card-box pd-20 height-100-p mb-30">
  <div class="row align-items-center">
    <div class="form-group col-4">
  		<label>System</label>
      <select class="form-control" style="width: 100%;" id="systemID" onchange="updatePage()">
        <option value="0">-- Please Select --</option>
        <option value="1">Phone Directory</option>
        <option value="2">Policies</option>
        <option value="3">Forms</option>
        <option value="4">Document Management System</option>
        <option value="5">Newsletter</option>
      </select>
  	</div>
    <div class="form-group col-4" id="divForms" style="display:none;">
  		<label>Forms</label>
      <select class="form-control" style="width: 100%;" id="formsID" onchange="updatePage()">
        <option value="0">-- Please Select --</option>
        <?php
        $result = executeQuery("SELECT rc_id,rc_desc FROM ref_category WHERE rc_type='form' AND rc_active=1");
        while($row = $result->fetch()) {
        ?>
          <option value="<?php echo $row['rc_id'];?>"><?php echo $row['rc_desc'];?></option>
        <?php }?>
      </select>
  	</div>
    <div class="form-group col-4" id="divPolicies" style="display:none;">
  		<label>Policies</label>
      <select class="form-control" style="width: 100%;" id="policiesID" onchange="updatePage()">
        <option value="0">-- Please Select --</option>
        <?php
        $result = executeQuery("SELECT rc_id,rc_desc FROM ref_category WHERE rc_type='policy' AND rc_active=1");
        while($row = $result->fetch()) {
        ?>
          <option value="<?php echo $row['rc_id'];?>"><?php echo $row['rc_desc'];?></option>
        <?php }?>
      </select>
  	</div>
    <div class="col-12" id="page">
    </div>
  </div>
</div>
<?php } else if(access("phone") == 1){
if($_SESSION["login"]["username"] == "fauzi.hashim"){
  ?>
  <div class="form-group col-4">
    <label>System</label>
    <select class="form-control" style="width: 100%;" id="systemID" onchange="updatePage()">
      <option value="0">-- Please Select --</option>
      <option value="1">Phone Directory</option>
      <option value="2">Policies</option>
      <option value="3">Forms</option>
      <option value="4">Document Management System</option>
      <option value="5">Newsletter</option>
    </select>
  </div>
  <?php
}
?>
  <div class="card-box pd-20 height-100-p mb-30">
    <div class="tab">
      <ul class="nav nav-tabs customtab" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#phone" role="tab" aria-selected="true">Phone/Extension Directory</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#division" role="tab" aria-selected="false">Division</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#department" role="tab" aria-selected="false">Department</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#level" role="tab" aria-selected="false">Level/Area</a></li>
			</ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="phone" role="tabpanel">
          <div class="row pd-20">
            <div class="col-12 text-right">
              <button class="btn btn-primary btn-sm" onclick="showModal('Extention',null,'Phone/Extension')"><i class="fa fa-plus"></i> New</button>
            </div>
          </div>
          <div class="row pd-20">
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
          </div>
				</div>
        <div class="tab-pane fade" id="division" role="tabpanel">
          <div class="row pd-20">
            <div class="col-12 text-right">
              <button class="btn btn-primary btn-sm" onclick="showModal('division',null,'Division')"><i class="fa fa-plus"></i> New</button>
            </div>
          </div>
          <div class="pd-20 table-responsive">
          	<table class="data-table table table-striped">
          	  <thead>
          	    <tr>
          	      <th scope="col" width="10%">Action</th>
                  <th scope="col">Division</th>
          	    </tr>
          	  </thead>
          	  <tbody>
                <?php
                $result = executeQuery("SELECT * FROM phone_dir.division");
                while($row = $result->fetch()){
                ?>
                  <tr>
            	      <td>
                      <div class="dropdown">
        								<a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
        									<i class="dw dw-more"></i>
        								</a>
        								<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
        									<a class="dropdown-item" style="cursor:pointer;" onclick="showModal('division','<?php echo $row['div_id'];?>','Division')"><i class="dw dw-edit2"></i> Edit</a>
        								</div>
        							</div>
                    </td>
                    <td><?php echo $row['div_name'];?></td>
            	    </tr>
                <?php }?>
          	  </tbody>
          	</table>
          </div>
				</div>
        <div class="tab-pane fade" id="department" role="tabpanel">
          <div class="row pd-20">
            <div class="form-group col-4">
            		<label>Division</label>
                <select class="form-control" style="width: 100%;" id="getDiv" onchange="getDepartment()">
                  <option value="0">-- Please Select --</option>
                  <?php
                  $result = executeQuery("SELECT * FROM phone_dir.division");
                  while($row = $result->fetch()){
                  ?>
                    <option value="<?php echo $row['div_id'];?>"><?php echo $row['div_name'];?></option>
                  <?php }?>
                </select>
            </div>
            <div class="col-8 text-right">
              <button class="btn btn-primary btn-sm" onclick="showModal('department',null,'Department')"><i class="fa fa-plus"></i> New</button>
            </div>
          </div>
          <div class="pd-20 table-responsive" id="div"></div>
				</div>
        <div class="tab-pane fade" id="level" role="tabpanel">
          <div class="row pd-20">
            <div class="col-12 text-right">
              <button class="btn btn-primary btn-sm" onclick="showModal('level',null,'Level/Area')"><i class="fa fa-plus"></i> New</button>
            </div>
          </div>
          <div class="pd-20 table-responsive">
            <table class="data-table table table-striped">
          	  <thead>
          	    <tr>
          	      <th scope="col" width="10%">Action</th>
                  <th scope="col">Level/Area</th>
          	    </tr>
          	  </thead>
          	  <tbody>
                <?php
                $result = executeQuery("SELECT * FROM phone_dir.level");
                while($row = $result->fetch()){
                ?>
                  <tr>
            	      <td>
                      <div class="dropdown">
        								<a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
        									<i class="dw dw-more"></i>
        								</a>
        								<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
        									<a class="dropdown-item" style="cursor:pointer;" onclick="showModal('level','<?php echo $row['lvl_id'];?>','Level/Area')"><i class="dw dw-edit2"></i> Edit</a>
        								</div>
        							</div>
                    </td>
                    <td><?php echo $row['lvl_name'];?></td>
            	    </tr>
                <?php }?>
          	  </tbody>
          	</table>
          </div>
				</div>
      </div>
    </div>
  </div>
<?php } else {
  include "error.php";
}?>
