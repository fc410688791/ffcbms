<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
</div>
<!-- /.content-wrapper -->

<footer class="main-footer">
	<div class="pull-right hidden-xs">
		<b>Version</b> <?php echo $version; ?>
	</div>
	<strong>Copyright &copy; 2019 <a href="#"><?php echo $company_name; ?></a>.</strong>
</footer>

</div>
<!-- ./wrapper -->

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo $assets_dir; ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Sparkline 首页图表需要 -->
<script src="<?php echo $assets_dir; ?>/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>

<!-- jvectormap 首页图表需要 -->
<script src="<?php echo $assets_dir; ?>/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo $assets_dir; ?>/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<!-- jQuery Knob Chart 首页图表需要 -->
<script src="<?php echo $assets_dir; ?>/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>

<!-- daterangepicker 首页图表需要-->
<script src="<?php echo $assets_dir; ?>/bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo $assets_dir; ?>/bower_components/bootstrap-daterangepicker/daterangepicker.js?20180517"></script>
<!-- Bootstrap WYSIHTML5 首页图表需要-编辑器需要 -->
<script src="<?php echo $assets_dir; ?>/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<!-- Slimscroll 首页图表需要 -->
<script src="<?php echo $assets_dir; ?>/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<!-- 可搜索式下拉框(select2)-js -->
<script src="<?php echo $assets_dir; ?>/bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- jquery 通知插件(toastr)-js -->
<script src="<?php echo $assets_dir; ?>/bower_components/toastr/toastr.js"></script>
<!-- 文件上传插件(fileinput)-js -->
<script src="<?php echo $assets_dir; ?>/bower_components/fileinput/js/fileinput.min.js"></script>

<!-- 自定义 js -->
<script src="<?php echo $assets_dir; ?>/js/icon.js"></script>
<script src="<?php echo $assets_dir; ?>/js/content.js?v=sa"></script>

<!-- 省份地区 -->
<!-- 
<script src="<?php echo $assets_dir; ?>/js/distpicker.data.js"></script>
<script src="<?php echo $assets_dir; ?>/js/distpicker.js"></script>
-->
</body>
</html>