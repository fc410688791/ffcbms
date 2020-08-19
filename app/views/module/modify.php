<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <!-- Main row -->
  <div class="row">

    <!-- general form elements -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">修改模块</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <form role="form" action="<?php echo base_url('module/modify') ?>" method='POST'>
        <div class="box-body">

          <!-- 自定义表单验证失败的错误提示 -->
          <?php echo $form_error_contents;?>

          <div class="form-group">
            <label for="module_name">模块名称</label>
            <input type="text" name="module_name" class="form-control" id="module_name" value="<?php echo set_value('module_name')? :$module['module_name']; ?>" required>
            <input type="hidden" name="module_id" value=<?php echo $module['module_id'] ?>>
          </div>
          <div class="form-group">
            <label for="controller">模块链接 - 控制器<span class="text-red"> (如模块拥有下拉菜单请留井号:#)</span></label>
            <input type="text" name="controller" class="form-control" value="<?php echo set_value('controller')? :$controller; ?>">
          </div>
          <div class="form-group">
            <label for="action">模块链接 - 方法<span class="text-red"> (如模块拥有下拉菜单请留井号:#)</span></label>
            <input type="text" name="action" class="form-control" value="<?php echo set_value('action')? :$action; ?>">
          </div>

          <div class="form-group">
            <label for="action">模块是否在线</label>
            <select name='online' class='form-control'>
              <option value='1'>在线</option>
              <option value='0' <?php echo (set_value('online')? :$module['online'] == 0)?'selected':''; ?>>下线</option>
            </select>
          </div>

          <div class="form-group">
            <label for="module_icon">模块图标</label>&nbsp;&nbsp;<i id="icon_preview" class="fa fa-fw <?php echo set_value('module_icon')? :$module['module_icon']; ?>"></i>
            <a id="icon_select"  data-toggle="modal" data-target="#modal-default" class="btn btn-info btn-xs"> 更改图标</a>
            <input type="text" readonly name="module_icon" class="form-control" id="module_icon" value="<?php echo set_value('module_icon')? :$module['module_icon']; ?>" required>
          </div>
          <div class="form-group">
            <label for="module_sort">模块排序数字<span class="text-info"> (数字越小越靠顶部)</span></label>
            <input type="number" name="module_sort" class="form-control" id="module_sort" value="<?php echo set_value('module_sort')?:$module['module_sort']; ?>" required>
          </div>
          <div class="form-group">
            <label for="module_desc">备注</label>
            <textarea class="form-control" rows="3" name='module_desc' id='module_desc'><?php echo set_value('module_desc')? :$module['module_desc']; ?></textarea>
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

<?php include(VIEWPATH.'module/icon_modal.php'); ?>