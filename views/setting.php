<?php if(ITUser() > 0) { ?>
<div class="card-box mb-30">
  <div class="pd-20">
    <h4 class="text-blue h4">System List</h4>
    <div class="row">
      <div class="col-md-12 text-right">
        <button class="btn btn-primary btn-sm" onclick="showModal()"><i class="fa fa-plus"></i> Add New</button>
      </div>
    </div>
  </div>
  <div class="pb-20">
    <table class="data-table table stripe hover nowrap">
      <thead>
        <tr class="text-center">
          <th class="table-plus datatable-nosort">#</th>
          <th class="datatable-nosort">Action</th>
          <th>System</th>
          <th>Database</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = systemList();
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
                  <a class="dropdown-item" href="<?php echo $row['sys_url'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
                  <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('Detail',<?php echo $row['sys_id'];?>,'<?php echo $row['sys_title'];?>')"><i class="dw dw-edit2"></i> Edit</a>
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
              ?><input type="checkbox" id="check<?php echo $row['sys_id'];?>" <?php echo $class;?> class="switch-btn" data-color="#0059b2" data-size="small" onchange="updateStatus(<?php echo $row['sys_id'];?>)">
            </td>
          </tr>
        <?php }?>
      </tbody>
    </table>
  </div>
</div>
<?php } else {?>
  <div class="card-box mb-30">
    <div class="col-12 text-center">
      <br><br><br><i class="fa fa-warning text-danger fa-2x"></i><br> ERROR 401 : Unauthorized Access (You don't have permission to view this page)<br><br><br>
    </div>
  </div>
<?php }?>
