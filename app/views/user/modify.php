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
        <h3 class="box-title">修改账号</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <form role="form" action="<?php echo base_url('user/modify') ?>" method='POST'>
        <div class="box-body">

          <!-- 自定义表单验证失败的错误提示 -->
          <?php echo $form_error_contents;?>

          <div class="form-group">
            <label for="user_name">账号 <span class='text-yellow'> (不可修改)</span></label>
            <input type="text" class="form-control" id='user_name' value="<?php echo $user['user_name']; ?>" disabled>
            <input type="hidden" name='user_id' value="<?php echo $user['user_id']; ?>">
          </div>
          <div class="form-group">
            <label for="password">密码 <span class='text-yellow'> (如不修改请留空)</span></label>
            <input type="password" name="password" class="form-control" id="password" value="<?php echo set_value('password')? : ''; ?>" >
          </div>
          <div class="form-group">
            <label for="real_name">姓名</label>
            <input type="text" name="real_name" class="form-control" id="real_name" value="<?php echo set_value('real_name')? : $user['real_name']; ?>" required>
          </div>
          <div class="form-group">
            <label for="sex">性别</label><br>
              男<input type="radio" name="sex" class="form-control flat-red" value=0 checked>&nbsp;&nbsp;&nbsp;&nbsp;
              女<input type="radio" name="sex" class="form-control flat-red" value=1 <?php echo ((set_value('sex')? : $user['sex']) == 1)?'checked':''; ?>>
          </div>
          <div class="form-group">
            <label for="user_group">角色</label>
            <select class="form-control" name="user_group" required <?php echo ($user['user_id'] ==  1)? 'disabled':''; ?>>
              <option value="">请选择角色</option>
              <?php foreach($user_group_option as $k => $v){ ?>
              <option value="<?php echo $k ?>" <?php echo (set_value('user_group')? : $user['user_group'] == $k)? 'selected': ''; ?> ><?php echo $v ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="mobile">手机号</label>
            <input type="text" name="mobile" class="form-control" id="mobile" value="<?php echo set_value('mobile')? : $user['mobile']; ?>" required>
          </div>
          <div class="form-group">
            <label for="card_id">身份证</label>
            <input type="text" name="card_id" class="form-control" id="card_id" value="<?php echo set_value('card_id')? : $user['card_id']; ?>">
          </div>
          <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" name="email" class="form-control" id="email" value="<?php echo set_value('email')? : $user['email']; ?>" >
          </div>
          <div class="form-group">
            <label for="address">所在地区</label>
            <div data-toggle="distpicker" >
              <div class="form-group">
                <select class="form-control" id="province" data-province="<?php echo set_value('province')? : $user['province']; ?>" name="province"></select>
              </div>
              <div class="form-group">
                <select class="form-control" id="city" data-city="<?php echo set_value('city')? : $user['city']; ?>" name="city"></select>
              </div>
            </div>
            <div style="clear:both;"></div>
          </div>
          <div class="form-group">
            <label for="addr_detail">详细地址</label>
            <input type="text" name="addr_detail" class="form-control" id="addr_detail" value="<?php echo set_value('addr_detail')? : $user['addr_detail']; ?>" placeholder='例宝安大道123号'>
          </div>
          <div class="form-group">
            <label for="user_desc">描述</label>
            <textarea class="form-control" rows="3" name='user_desc' id='user_desc'><?php echo set_value('user_desc')? : $user['user_desc']; ?></textarea>
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
<script type="text/javascript">
  
  $(function(){
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
  });
  
</script>
