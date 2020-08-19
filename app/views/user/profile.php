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
        <h3 class="box-title">修改个人资料</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <form role="form" action="<?php echo base_url('user/profile') ?>" method='POST'>
        <div class="box-body">

          <!-- 自定义表单验证失败的错误提示 -->
          <?php echo $form_error_contents;?>

          <div class="form-group">
            <label for="user_name">账号</label>
            <input type="text" name="user_name" class="form-control" id='user_name' value="<?php echo $owner_data['user_name']; ?>" disabled>
          </div>
          <div class="form-group">
            <label for="password">密码</label>
            <input type="text" name="password" class="form-control" id="password" value="<?php echo set_value('password')? set_value('password') : ''; ?>" placeholder='不更新密码请留空'>
          </div>
          <div class="form-group">
            <label for="real_name">姓名</label>
            <input type="text" name="real_name" class="form-control" id="real_name" value="<?php echo set_value('real_name')? : $owner_data['real_name']; ?>" required>
          </div>
          <div class="form-group">
            <label for="sex">性别</label><br>
              男<input type="radio" name="sex" class="form-control flat-red" value=0 checked>&nbsp;&nbsp;&nbsp;&nbsp;
              女<input type="radio" name="sex" class="form-control flat-red" value=1 <?php echo ((set_value('sex')? : $owner_data['sex']) == 1)?'checked':''; ?>>
          </div>
          <div class="form-group">
            <label for="mobile">手机号</label>
            <input type="tel" name="mobile" class="form-control" id="mobile" value="<?php echo set_value('mobile')? : $owner_data['mobile']; ?>" required>
          </div>
          <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" name="email" class="form-control" id="email" value="<?php echo set_value('email')? : $owner_data['email']; ?>" required>
          </div>
          <div class="form-group">
            <label for="user_desc">描述</label>
            <textarea class="form-control" rows="3" name='user_desc' id='user_desc'><?php echo set_value('user_desc')? : $owner_data['user_desc']; ?></textarea>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <button type="submit" class="btn btn-primary form_button"><i class='fa fa-spinner fa-spin hide'></i>提交</button>
          <a class="btn btn-default" onclick="history.go(-1)">取消</a>
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
<script type="text/javascript">
  
  $(function(){
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
  });
  
</script>