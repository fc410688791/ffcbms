<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo $assets_dir; ?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $assets_dir; ?>/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo $assets_dir; ?>/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $assets_dir; ?>/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo $assets_dir; ?>/plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="/login/index"><b><?php echo $company_name; ?></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><b>登录</b></p>

    <form action=<?php echo base_url('/auth/login'); ?> method='post' id='form_login'>

        <!-- 表单验证器返回的错误信息 -->
        <?php echo $form_error_contents; ?>


        <div class="form-group has-feedback">
          <input type="text" name='user_name' class="form-control" placeholder="登录名 / 手机号" value="<?php echo set_value('user_name'); ?>" >
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
          <input type="password" name='password' class="form-control" placeholder="密码" value="<?php echo set_value('password'); ?>" >
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        
        <?php if ( isset($img)){ ?>
          <div class="row">
            <div class="col-xs-5">
                <div class="form-group">
                  <input type="text" name="captcha_code" class="form-control" placeholder="请输入验证码" value="" />
                </div>
            </div>

            <div class="col-xs-2">
              <?php echo $img ?>
            </div>
          </div>
        <?php } ?>

        <div class="row">

          <!-- 记住密码 => 暂时不需要 -->
          <!-- <div class="col-xs-8">
            <div class="checkbox icheck">
              <label>
                <input type="checkbox" name='remember'><span style='color:#333'>记住我</span> <button class='btn btn-info btn-xs'>一个月有效</button>
              </label>
            </div>
          </div> -->

          <div class="col-xs-4">
            <button type='submit' class="form-control btn btn-primary btn-block btn-flat form_button"><i class='fa fa-spinner fa-spin hide'></i>登录</button>
          </div>
        </div>

    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?php echo $assets_dir; ?>/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo $assets_dir; ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo $assets_dir; ?>/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $assets_dir; ?>/js/icon.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });
    });
</script>
</body>
</html>
