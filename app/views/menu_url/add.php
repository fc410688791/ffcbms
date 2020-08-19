<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 功能列表的类型
global $set_func_type_option;
$set_func_type_option = $func_type_option;

// 检查该菜单或者[按钮/链接/数据模块/数据] 是否存在子模块
function check_have_menu_children($data_array, $sub = "&nbsp;&nbsp;&nbsp;&nbsp;")
{
  global $set_func_type_option;
  if ( isset($data_array['children']))
  {
    $children_array = $data_array['children'];
    foreach ($children_array as $ca_k => $ca_v)
    {
?>

    <option value="<?php echo $ca_v['menu_id']; ?>" <?php echo (set_value('father_menu') == $ca_v['menu_id'])? 'selected': ''; ?> ><?php echo $sub.$ca_v['menu_name'] ?> [<?php echo isset($set_func_type_option[$ca_v['is_show']])? $set_func_type_option[$ca_v['is_show']]: ''; ?>]</option>

<?php 
      check_have_menu_children($ca_v, $sub . "&nbsp;&nbsp;&nbsp;&nbsp;");
    }
  }
} 
?>

<!-- Main content -->
<section class="content">
  <!-- Main row -->
  <div class="row">

    <!-- general form elements -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">添加功能</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <form role="form" action="<?php echo base_url('menu_url/add') ?>" method='POST'>
        <div class="box-body">

          <!-- 自定义表单验证失败的错误提示 -->
          <?php echo $form_error_contents;?>

          <div class="form-group">
            <label for="menu_name">功能名称</label>
            <input type="text" name="menu_name" class="form-control" id='menu_name' value="<?php echo set_value('menu_name')? : ''; ?>" required>
          </div>

          <div class="form-group">
            <label for="is_show">功能类型</label><br>
            <!-- <input type="radio" name=""> -->
            <?php foreach ($func_type_option as $fyo_k => $fyo_v){ ?>
                <?php echo $fyo_v; ?>
                <input type="radio" name="is_show" class='flat-red' value="<?php echo $fyo_k; ?>" <?php echo (set_value('is_show') ==  $fyo_k)?'checked': ($fyo_k == 1)? 'checked':''; ?> required> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php } ?>
          </div>

          <div class="form-group">
            <label for="controller">功能链接 - 控制器</label>
            <input type="text" name="controller" class="form-control" id="controller" value="<?php echo set_value('controller')? : ''; ?>" required>
          </div>

          <div class="form-group">
            <label for="action">功能链接 - 方法<span class="text-yellow"> (如是左侧菜单栏显示功能 {功能链接 - 方法} 请填写:index)</span></label>
            <input type="text" name="action" class="form-control" id="action" value="<?php echo set_value('action')? : ''; ?>" required>
          </div>

          <div class="form-group">
            <label for="module_id">所属菜单模块</label>
            <select class="form-control" name="module_id" required>
              <option value="">请选择模块</option>
              <?php foreach($module_name_option as $k => $v){ ?>
              <option value="<?php echo $k ?>" <?php echo (set_value('module_id') == $k)? 'selected': ''; ?> ><?php echo $v ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label for="father_menu">所属菜单/ 按钮/ 链接/ 数据模块/ 数据</label>
            <div>
              <select class="form-control select2" name="father_menu">
                <option value="">无</option>

                <?php foreach($menu_unlimit_data as $mud_k => $mud_v){ ?>
                <optgroup label="<?php echo $mud_k.' =>[菜单模块]'; ?>">

                  <?php foreach($mud_v as $mud_v_v){ ?>
                  <option value="<?php echo $mud_v_v['menu_id']; ?>" <?php echo (set_value('father_menu') == $mud_v_v['menu_id'])? 'selected': ''; ?> ><?php echo $mud_v_v['menu_name'].' =>[菜单]' ?></option>

                  <?php 
                    check_have_menu_children($mud_v_v);
                  }; 
                  ?>

                </optgroup>
                <?php } ?>

              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="menu_desc">描述</label>
            <textarea class="form-control" rows="3" name='menu_desc' id='menu_desc'><?php echo set_value('menu_desc')? : ''; ?></textarea>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <button type="submit" class="btn btn-primary form_button"><i class='fa fa-spinner fa-spin hide'></i>提交</button>
          <a class="btn btn-default" href="<?php echo base_url("$curr_controller/index") ?>">取消</a>
        </div>

      </form>
    </div>
    <!-- /.box -->

  </div>
  <!-- /.row (main row) -->

</section>
<!-- /.content -->

<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="<?php echo $assets_dir; ?>/plugins/iCheck/all.css">
<!-- iCheck 1.0.1 -->
<script src="<?php echo $assets_dir; ?>/plugins/iCheck/icheck.min.js"></script>

<!-- 下拉搜索框 -->
<script>
  $(function () {
    //可搜索式下拉列表,初始化
    $('.select2').select2();

    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
  })
</script>
