<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="error-page">
    <!-- <div class="error-content"><br> -->


		<div class="box box-solid">
	        <div class="box-header with-border">
	          <i class="fa fa-exclamation-triangle text-yellow"></i>

	          <h3 class="box-title">页面发生错误 ~</h3>
	        </div>
	        <!-- /.box-header -->
	        <div class="box-body">
	          <blockquote>
	            <p><?php echo $msg; ?></p>
	          </blockquote>
	          <center>
                <?php if(!$auto_jump) { ?>
	          	<?php if ( empty($return_jump_url)){ ?>
	          	<a class='btn btn-default' href="<?php echo base_url("$curr_controller/index") ?>"><?php echo isset($return_jump_content)?$return_jump_content:'返回列表页'; ?></a>
	          	<?php }else{ ?>
	          	<a class='btn btn-default' href="<?php echo $return_jump_url ?>"><?php echo isset($return_jump_content)?$return_jump_content:'返回列表页'; ?></a>
	          	<?php } ?>
                <?php }else{ ?>
                    正在自动跳转....
                <?php } ?>
	          </center>
	        </div>
	        <!-- /.box-body -->
	      </div>


    <!-- </div> -->
    <!-- /.error-content -->
  </div>
  <!-- /.error-page -->
</section>
<!-- /.content -->
<script>
    <?php if($auto_jump) { ?>
        setTimeout(function(){
           window.history.back();      
        },2000)
       
    <?php } ?>
</script>
