<div class="faq-wrap" id="accordion">
  <?php
  $style = "collapse show";
  $class = "btn btn-block";
  $result = executeQuery("SELECT * FROM ref_category WHERE rc_type='policy' AND rc_active=1 ORDER BY rc_desc");
  while($row = $result->fetch()) {
  ?>
  <div class="card">
    <div class="card-header">
      <button class="<?php echo $class;?>" data-toggle="collapse" data-target="#form<?php echo $row['rc_id'];?>">
        <?php echo $row['rc_desc'];?>
      </button>
    </div>
    <div id="form<?php echo $row['rc_id'];?>" class="<?php echo $style;?>" data-parent="#accordion">
      <div class="card-body">
        <?php
          $policy = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id=".$row['rc_id']);
          $body = executeQuery("SELECT * FROM policy WHERE p_active=1 AND p_rc_id=".$row['rc_id']);
          if($policy->rowCount() == 1) {
            $pol = $policy->fetch();
            ?><embed src="upload/<?php echo $pol['p_path']."/".$pol['p_filename'];?>" width="100%" height="1150" type="application/pdf"><?php
          } else {
            ?>
            <div class="tab">
              <div class="row clearfix">
                <div class="col-md-3 col-sm-12">
                  <ul class="make_tree">
        						<?php
        							while($row = $policy->fetch()) {
        								if(!$row['p_filename']) {
        									linked($row['p_id'],$row['p_desc']);
        								} else {
        									?><li style="cursor:pointer;"><a data-toggle="tab" href="#tab<?php echo $row['p_id'];?>"><i class="fa fa-arrow-right"></i> <?php echo $row['p_desc'];?></a></li><?php
        								}
        							}
        						?>
                  </ul>
                </div>
                <div class="col-md-9 col-sm-12">
                  <div class="tab-content">
                    <?php
                      $aria = "tab-pane fade active show";
                      while($content = $body->fetch()) {
                        if(!$content['p_filename']) {
                          getContent($content['p_id'],"tab-pane fade");
                        } else {
                          ?>
                          <div class="<?php echo $aria;?>" id="tab<?php echo $content['p_id'];?>" role="tabpanel">
    												<div class="pd-20">
                              <embed src="upload/<?php echo $content['p_path']."/".$content['p_filename'];?>" width="100%" height="1150" type="application/pdf">
                            </div>
    											</div>
                          <?php
                        }
                    ?>
                    <?php
                      $aria = "tab-pane fade";
                      }
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        ?>
      </div>
    </div>
  </div>
  <?php
    $style = "collapse";
    $class = "btn btn-block collapsed";
    }
  ?>
</div>
