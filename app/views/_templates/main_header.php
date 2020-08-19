<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo base_url(); ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><?php echo $company_name_abbr; ?></b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b><?php echo $company_name; ?></b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Notifications: style can be found in dropdown.less -->
        <li class="dropdown notifications-menu hide">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell-o"></i>
            <span class="label label-warning">10</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">您有 10 条未读信息</li>
            <li>
              <!-- inner menu: contains the actual data -->
              <ul class="menu">
                <li>
                  <a href="#">
                    <i class="fa fa-users text-aqua"></i> 不错! 今天订单数量破万了.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-warning text-yellow"></i> 亲, 通知标题不宜太长会自动省略.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-users text-red"></i> 额, 有一条待审核订单.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-shopping-cart text-green"></i>不错!又有人用户体验我们的产品了.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-user text-red"></i> 您更改了自己的姓名.
                  </a>
                </li>
              </ul>
            </li>
            <li class="footer"><a href="#">查看所有消息</a></li>
          </ul>
        </li>
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php if ($owner_data['sex'] == 1){ ?>
              <img src="<?php echo $assets_dir; ?>/dist/img/user7-128x128.jpg" class="user-image" alt="User Image">
            <?php }else{ ?>
              <img src="<?php echo $assets_dir; ?>/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
            <?php } ?>
            
            <span class="hidden-xs"><?php echo $owner_data['real_name'] ?></span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <?php if ($owner_data['sex'] == 1){ ?>
                <img src="<?php echo $assets_dir; ?>/dist/img/user7-128x128.jpg" class="img-circle" alt="User Image">
              <?php }else{ ?>
                <img src="<?php echo $assets_dir; ?>/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
              <?php } ?>

              <p>
                <?php echo $owner_data['real_name'] ?>
                <small><?php echo $owner_data['email'] ?></small>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="<?php echo base_url('user/profile') ?>" class="btn btn-default btn-flat">个人资料</a>
              </div>
              <div class="pull-right">
                <a href="<?php echo base_url('auth/logout') ?>" class="btn btn-default btn-flat">退出</a>
              </div>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
      </ul>
    </div>
  </nav>
</header>