<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class='box'>
                <div class='box-body'>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Ageing/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 200px;">
                                  <input type="text" name="key" class="form-control pull-left " placeholder="设备ID" value="<?php echo isset($key)?$key:''; ?>">
                                  <div class="input-group-btn">
                                      <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Ageing/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id='aging_status' name="aging_status" class="form-control" style="width:120px;">
                                        <option value=''>老化状态</option>
                                        <option value='1' <?php echo isset($aging_status)? ($aging_status == 1)?'selected':'':''; ?>>老化中</option>
                                        <option value='2' <?php echo isset($aging_status)? ($aging_status == 2)?'selected':'':''; ?>>老化完成</option>
                                    </select>
                                    <select id="status" name="status" class="form-control" style="width:120px;">
                                        <option value=''>设备状态</option>
                                        <option value='1' <?php echo isset($status)? ($status == 1)?'selected':'':''; ?>>正常</option>
                                        <option value='2' <?php echo isset($status)? ($status == 2)?'selected':'':''; ?>>故障</option>
                                    </select>
                                    <select id="code" name="code" class="form-control" style="width:120px;">
                                        <option value=''>设备故障</option>
                                        <option value='10116' <?php echo isset($code)? ($code == 10116)?'selected':'':''; ?>>10116(协议板故障)</option>
                                        <option value='10117' <?php echo isset($code)? ($code == 10117)?'selected':'':''; ?>>10117(桩断电或者天线松了)</option>
                                        <option value='10118' <?php echo isset($code)? ($code == 10118)?'selected':'':''; ?>>10118(协议板故障)</option>
                                        <option value='10119' <?php echo isset($code)? ($code == 10119)?'selected':'':''; ?>>10119(线故障或者线松了)</option>
                                        <option value='other' <?php echo isset($code)? ($code == 'other')?'selected':'':''; ?>>other(协议板故障)</option>
                                    </select>
                                    <input type="text" name="reservation" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/Ageing/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>ID</td>
                                <td>设备ID</td>
                                <td>亚克力板数</td>
                                <td>二维码数</td>
                                <td>设备状态</td>
                                <td>故障次数</td>
                                <td>设备故障</td>
                                <td>老化开始时间</td>
                                <td>老化状态</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><a href="javascript:;" title='查看' class="mac" data-id="<?php echo $info['id']; ?>"><?php echo $info['mac']; ?></a></td>
                                <td><?php echo $info['bind_side_num']; ?></td>
                                <td><?php echo $info['bind_plate_code_num']; ?></td>
                                <td style="color:<?php echo $info['status_color']; ?>"><?php echo $info['status_name']; ?></td>
                                <td title="<?php echo $info['fault_code_count']; ?>"><?php echo $info['fault_count']; ?></td>
                                <td title="<?php echo $info['fault_code_mac']; ?>"><?php echo $info['fault_mac']; ?></td>
                                <td><?php echo $info['aging_start_time']; ?></td>
                                <td style="color:<?php echo $info['aging_status_color']; ?>"><?php echo $info['aging_dec']; ?></td>
                                <td>
                                    <?php if ($info['aging_status']==1||$info['aging_status']==2){?>
                                    <a class="detail pointer" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='<?php echo $info['status'];?>'>编辑</a>
                                    <?php }?>
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
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="resetModal-label">编辑</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>设备ID: </th>
              <th>
                <input id="u_id" name='id' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>老化状态:</th>
              <th colspan="3">
                <select id="u_status">
                    <option value='4'>后台设置通过</option>
                </select>
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

<div class="modal fade bs-example-modal-lg text-center" id="showModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="showModal-label"></h3>
      </div>
      <div class="modal-body" id="showModal-body">
      </div>
    </div>
  </div>
</div>

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
	$("#u_id").val(id);
	$('#detailModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$("#btn-update").on("click",function(){
	  var id = $('#u_id').val();
	  var status  = $('#u_status').val();
	  $.ajax({
	      type: 'POST',
	      url: '<?php echo base_url('Ageing/update').'?id=' ?>' + id,
	      dataType: 'json',
	      data:{
		      'status': status
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

$('button.close,#close').on("click",function(){
    $('#detailModal').modal("hide");
});

$('.mac').on("click",function(){
	var id = $(this).data('id');
	$.ajax({
	      type: 'GET',
	      url: '<?php echo base_url('Index/get_machine_iot_triad'); ?>',
	      dataType: 'json',
	      data:{
		      'id': id
		  },
	      async:false,//同步请求
	      success: function(data){
	    	  if (data.code == 200) {
	    	      $('#showModal-label').html(data.data.mac+'设备详情');
	    	      var html = '';
	    	      for(var i=0;i<data.data.list.length;i++){
	    	    	  html += '<h4 style="color:#A1A1A1;"><span>亚克力'+(i+1)+':</span><span style="margin-left:30px;">'+data.data.list[i]+'</span></h4>';
		    	  }
		    	  console.log(html);
	    	      $('#showModal-body').html(html);
		      }else{
		    	  toastr.error(data.msg);
			  }
		  },
      error: function () {
          toastr.error('请求错误!');
      }
  });
  $('#showModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
  });
});
</script>