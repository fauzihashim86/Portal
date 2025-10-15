<div class="pd-20 card-box mb-30">
  <table class="data-table table stripe hover nowrap">
    <thead>
      <tr class="text-center">
        <th class="table-plus datatable-nosort">#</th>
        <th class="datatable-nosort">Action</th>
        <th>Category</th>
        <th>Description</th>
        <th>Path</th>
        <th>File Name</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $count = 1;
      $result = executeQuery("SELECT * FROM policy
                LEFT JOIN ref_category ON p_rc_id=rc_id
                ORDER BY p_rc_id");
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
                <a class="dropdown-item" href="<?php echo "upload/".$row['p_path']."/".$row['p_filename'];?>" target="_blank"><i class="dw dw-eye"></i> View</a>
                <a class="dropdown-item" style="cursor:pointer;" onclick="showModal('Policy',<?php echo $row['p_id'];?>,'<?php echo $row['p_desc'];?>')"><i class="dw dw-edit2"></i> Edit</a>
              </div>
            </div>
          </td>
          <td class="text-left"><?php echo $row['rc_desc'];?></td>
          <td class="text-left"><?php echo $row['p_desc'];?></td>
          <td class="text-left"><?php echo $row['p_path'];?></td>
          <td class="text-left"><?php echo $row['p_filename'];?></td>
        </tr>
        <?php
      }
    ?>
    </tbody>
  </table>
</div>
