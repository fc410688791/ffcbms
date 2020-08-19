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
        <h3 class="box-title">添加系统默认值</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <form role="form" action="<?php echo base_url('sys/add') ?>" method='POST'>
        <div class="box-body">

          <!-- 自定义表单验证失败的错误提示 -->
          <?php echo $form_error_contents;?>

          <div class="form-group">
            <label for="key_name">key_name</label>
            <input type="text" name="key_name" class="form-control" id='key_name' value="<?php echo set_value('key_name')? : ''; ?>" required>
          </div>
          <div class="form-group">
            <label for="key_value">key_value</label>
            <input type="text" name="key_value" class="form-control" id="key_value" value="<?php echo set_value('key_value')? : ''; ?>" required>
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
