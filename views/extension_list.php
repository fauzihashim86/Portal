<div class="card-box mb-30">
  <div class="pd-20">
    <h4 class="text-blue h4">Extension List</h4>
    <div class="row">
      <div class="col-md-4 col-sm-12">
  			<div class="form-group">
  				<label>Division - Department</label>
  				<select id="department" class="selectpicker form-control" data-size="5" multiple data-max-options="3" onchange="filter()">
            <?php
              $result = executeQuery("SELECT div_id,div_name FROM division ORDER BY div_name","phone_dir");
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
            ?>
  				</select>
  			</div>
  		</div>
      <div class="col-md-4 col-sm-12">
  			<div class="form-group">
  				<label>Level</label>
  				<select id="level" class="selectpicker form-control" data-size="5" multiple data-max-options="3" onchange="filter()">
            <?php
              $result = executeQuery("SELECT lvl_id,lvl_name FROM level","phone_dir");
              while($row = $result->fetch()) {
            ?>
  						<option value="<?php echo $row['lvl_id'];?>"><?php echo $row['lvl_name'];?></option>
            <?php }?>
  				</select>
  			</div>
  		</div>
    </div>
  </div>
  <div class="pb-20" id="tblFilter">
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
        $result = executeQuery("SELECT emp_id,emp_name,emp_ext,emp_hp,dept_name,div_name,lvl_name FROM phone INNER JOIN level ON lvl_id=emp_level INNER JOIN department ON dept_id=emp_dept INNER JOIN division ON division.div_id=department.div_id ORDER BY emp_name","phone_dir");
        $count = 1;
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
  </div>
</div>
