<div class="header">
  <div class="header-left">
    <div class="menu-icon dw dw-menu"></div>
  </div>
  <div class="header-right">
    <div class="dashboard-setting user-notification">
      <div class="dropdown">
        <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
          <i class="dw dw-settings2"></i>
        </a>
      </div>
    </div>
    <div class="user-notification">
      <div class="dropdown">
        <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
          <i class="icon-copy dw dw-notification"></i>
          <?php
            if(notify("display") > 0) {
              $style = "";
            } else {
              $style = "display:none;";
            }
          ?>
          <span class="badge notification-active" id="showNotify" style="<?php echo $style;?>"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="notification-list mx-h-350 customscroll" id="showNotification"><?php notify();?></div>
        </div>
      </div>
    </div>
    <div class="user-info-dropdown">
      <div class="dropdown">
        <?php if($_SESSION["login"]["level"] == "GUEST"){?>
          <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
            <span class="user-name"><?php echo $_SESSION["login"]["name"];?></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
            <a class="dropdown-item" href="javascript:;" data-toggle="right-sidebar"><i class="dw dw-settings2"></i> Setting</a>
            <a class="dropdown-item" href="./?id=5"><i class="dw dw-logout"></i> Log Out</a>
          </div>
        <?php } else {?>
          <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
            <?php if(profile() != "default.png"){?>
              <span class="user-icon"><img src="upload/profile/<?php echo profile();?>" width="50" height="50"></span>
            <?php }?>
            <span class="user-name"><?php echo $_SESSION["login"]["name"];?></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
            <a class="dropdown-item" href="./?id=2"><i class="dw dw-user1"></i> Profile</a>
            <a class="dropdown-item" href="javascript:;" data-toggle="right-sidebar"><i class="dw dw-settings2"></i> Setting</a>
            <a class="dropdown-item" style="cursor:pointer;" onclick="lockdown()"><i class="icon-copy dw dw-lock"></i> Lockscreen</a>
            <a class="dropdown-item" href="./?id=5"><i class="dw dw-logout"></i> Log Out</a>
          </div>
        <?php }?>
      </div>
    </div>
    <!-- <div class="github-link">
      <a href="http://www.mtdgroup.com.my/" target="_blank"><img src="src/images/github.svg" alt=""></a>
    </div> -->
  </div>
</div>
