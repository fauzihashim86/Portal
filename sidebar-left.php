<div class="brand-logo">
  <a href="/">
    <img src="src/images/mtd-logo.svg" alt="" class="dark-logo">
    <img src="src/images/mtd-logo-white.svg" alt="" class="light-logo">
  </a>
  <div class="close-sidebar" data-toggle="left-sidebar-close">
    <i class="ion-close-round"></i>
  </div>
</div>
<div class="menu-block customscroll">
  <div class="sidebar-menu">
    <ul id="accordion-menu">
      <?php showMenu($_SESSION['login']['username']);?>
    </ul>
  </div>
</div>
