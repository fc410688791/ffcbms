<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <span>老化通过率：</span><input id="percent_of_pass" name='percent_of_pass' type='number' min='0' max='100' value='<?php echo $percent_of_pass;?>'><sapn>%</sapn>
                    <button id="pop_update">修改</button>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;background-color:#FFF;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>老化类型</td>
                                <td>老化时间</td>
                                <td>创建时间</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['text']; ?></td>
                                <td><?php echo $info['text_ext'][0].'小时'.$info['text_ext'][1].'分钟'; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td>
                                    <a class="detail pointer" href="javascript:;" data-id='<?php echo $info['id'];?>' data-h='<?php echo $info['text_ext'][0];?>' data-i='<?php echo $info['text_ext'][1];?>'>编辑</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>

<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">详情</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>ID: </th>
              <th>
                <input id="u_id" name='id' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>老化时间:</th>
              <th colspan="3">
                 <input id="u_h" value=''>小时
                 <input id="u_i" value=''>分钟
              </th>
            </tr>
            
          </tbody>
        </table>
          <div class="modal-footer" style="text-align: left;">
            <button id="btn-update" class="btn btn-success">修改</button>
            <button id="close" class="btn btn-default">取消</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<script>
$(function(){
    $('#reservation').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        }
    });
});
$(".detail").click(function () {
	var id = $(this).data('id');
	var h = $(this).data('h');
	var i = $(this).data('i');
	$("#u_id").val(id);
	$("#u_h").val(h);
	$("#u_i").val(i);
	$('#detailModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$("#btn-update").on("click",function(){
	  var id = $('#u_id').val();
	  var h = $('#u_h').val();
	  var i = $('#u_i').val();
	  $.ajax({
	      type: 'POST',
	      url: '<?php echo base_url('AgingSetting/update').'?id=' ?>' + id,
	      dataType: 'json',
	      data:{
		      'h': h,
		      'i': i
		  },
	      async:false,//同步请求
	      success: function(data){
	    	  if (data.code == 200) {
	    		  toastr.success(data.msg);
	    		  setTimeout(function(){
	                  location.reload();
	              }, 2000);
		      }else{
		    	  toastr.error(data.msg);
			  }
		  },
          error: function () {
              toastr.error('请求错误!');
          },
          complete: function () {   
          }
      });
});

$("#pop_update").on("click",function(){
	  var percent_of_pass = $('#percent_of_pass').val();
	  $.ajax({
	      type: 'POST',
	      url: '<?php echo base_url('AgingSetting/pop_update') ?>',
	      dataType: 'json',
	      data:{
		      'percent_of_pass': percent_of_pass,
		  },
	      async:false,//同步请求
	      success: function(data){
	    	  if (data.code == 200) {
	    		  toastr.success(data.msg);
	    		  setTimeout(function(){
	                  location.reload();
	              }, 2000);
		      }else{
		    	  toastr.error(data.msg);
			  }
		  },
      error: function () {
          toastr.error('请求错误!');
      },
      complete: function () {   
      }
  });
});
</script>