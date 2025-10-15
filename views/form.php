<div class="faq-wrap" id="accordion">
  <?php
    $count = 1;
    $result = executeQuery("SELECT * FROM ref_category WHERE rc_type='form' ORDER BY rc_desc");
    while($row = $result->fetch()) {
      if($count == 1) {
        $style = "collapse show";
        $class = "btn btn-block";
      } else {
        $style = "collapse";
        $class = "btn btn-block collapsed";
      }
      $count++;
  ?>
  <div class="card">
    <div class="card-header">
      <button class="<?php echo $class;?>" data-toggle="collapse" data-target="#form<?php echo $row['rc_id'];?>">
        <?php echo $row['rc_desc'];?>
      </button>
    </div>
    <div id="form<?php echo $row['rc_id'];?>" class="<?php echo $style;?>" data-parent="#accordion">
      <div class="card-body">
				<ul class="list-group">
          <?php
            $results = executeQuery("SELECT * FROM forms WHERE f_active=1 AND f_rc_id=".$row['rc_id']);
            $count = 1;
            while($rows = $results->fetch()) {
          ?>
  					<li class="list-group-item d-flex justify-content-between align-items-center">
  						<?php echo $count++.") ".$rows['f_desc'];?>
  						<span class="badge badge-pill"><a href="upload/<?php echo $rows['f_path']."/".$rows['f_filename'];?>" target="_blank" title="Download"><i class="fa fa-download fa-2x"></i></a></span>
  					</li>
          <?php }?>
				</ul>
      </div>
    </div>
  </div>
  <?php }?>
</div>
