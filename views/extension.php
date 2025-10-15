<div class="pd-20 card-box mb-30">
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Staff Name</label>
		<div class="col-sm-12 col-md-10">
			<input class="form-control" placeholder="Staff Name" type="search" id="searchTxt">
		</div>
	</div>
  <div class="form-group row">
		<label class="col-sm-12 col-md-2 col-form-label">Department</label>
		<div class="col-sm-12 col-md-10">
      <select class="selectpicker form-control" style="width: 100%;" id="searchSelect">
        <option value="0">-- Please Select --</option>
  			<?php selectDepartDivision();?>
      </select>
		</div>
	</div>
  <div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
      <button id="search" class="btn btn-primary pull-right" onclick="searchDirectory()"><i class="fa fa-search"></i> Search</button>
    </div>
  </div><hr>
  <div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12" id="searchDirectory">
    </div>
  </div>
</div>
