<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      
      <?php 
      foreach($module_menu_tree as $tree)
      {
        $isdis = FALSE;
        foreach ($tree['menu_list'] as $menu)
        {
          if ( in_array($menu['menu_id'], $roles))
          {
            $isdis = TRUE;
            break;
          }
        }
        if (!$isdis)
        {
          continue;
        }

        $m_url_valid = FALSE;
        if ($tree['module_url'] == '#/#')
        {
          $m_url_valid = TRUE;
        }
      ?>
      
      <li class="
      <?php 
        echo ($m_url_valid)?'treeview':'';

        $tree_i_id = isset($menu_url_tree["{$curr_controller}/index"]['module_id'])?$menu_url_tree["{$curr_controller}/index"]['module_id']:'';
        echo ($tree['module_id'] == $tree_i_id)?' active':'';
      ?>">

        <!-- 菜单模块 -->
        <a href="<?php echo ($m_url_valid)? '#': base_url($tree['module_url']); ?>">
          <i class="fa fa-fw-<?php echo $tree['module_icon'] ?>"></i>
          <span><?php echo $tree['module_name'] ?></span>

          <?php if ($m_url_valid) { ?>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          <?php } ?>
        </a>
        
        <!-- 功能列表- 菜单 -->
        <?php if ($m_url_valid){ ?>
          <ul class="treeview-menu">

          <?php
          foreach ($tree['menu_list'] as $menu)
          {
            // if($owner_data['user_group'] != 1 && !in_array($menu['menu_id'],$roles)){continue;}
            if ( ! in_array($menu['menu_id'], $roles))
            {
              continue;
            }
          ?>
            <li class="<?php echo ($menu['menu_url'] == "{$curr_controller}/index")?'active':''; ?>">
              <a href="<?php echo base_url($menu['menu_url']) ?>"><i class="fa fa-circle-o"></i><?php echo $menu['menu_name'] ?></a>
            </li>
          <?php } ?>

          </ul>
        <?php } ?>

      </li>
      <?php } ?>

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
