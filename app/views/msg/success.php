<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="error-page">
    <div class="error-content"><br>
		<h3><i class="fa  fa-check-square text-green"></i> <?php echo $msg; ?></h3>
		
		<?php if ( empty($return_jump_url)){ ?>
		<a href="javascript:;" onClick="javascript :history.back(-1);"><?php echo isset($return_jump_content)?$return_jump_content:'返回上一页'; ?></a>
		<?php }else{ ?>
		<a href="<?php echo $return_jump_url; ?>"><?php echo isset($return_jump_content)?$return_jump_content:'返回上一页'; ?></a>
		<?php } ?>
    </div>
    <!-- /.error-content -->
  </div>
  <!-- /.error-page -->
</section>
<!-- /.content -->
