<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'>新增类型</a>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>ID</td>
                                <td>设备类型</td>
                                <td>模块数</td>
                                <td>亚克力板数</td>
                                <td>二维码数</td>
                                <td>状态</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['id'];?></td>
                                <td><?= $info['type_name'];?></td>
                                <td><?= $info['module_num'];?></td>
                                <td><?= $info['module_plate_num'];?></td>
                                <td><?= $info['module_plate_code_num'];?></td>
                                <td><?= $info['status']==1?'展示':'不展示';?></td>
                                <td>
                                    <a href="javascript:;" class="info" data-id="<?= $info['id']?>">编辑</a>
                                    <a href="javascript:;" class="del" data-id="<?= $info['id']?>">删除</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <tfoot>
                        <tr>
                            <th colspan="11"><?= $pagination; ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>

<!-- infoModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="detailLabel"></h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr id="tr-id">
              <th>ID:</th>
              <th>
                  <input id="id" type="number" disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>设备类型:</th>
              <th>
                  <input id="type_name" type="text" maxlength="50">
              </th>
            </tr>
            <tr>
              <th>模块数:</th>
              <th>
                  <input id="module_num" type="number">
              </th>
            </tr>
            <tr>
              <th>亚克力板数:</th>
              <th>
                  <input id="module_plate_num" type="number">
              </th>
            </tr>
            <tr>
              <th>二维码数:</th>
              <th>
                  <input id="module_plate_code_num" type="number">
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th>
                <input id="r1" type="radio" name="status" checked="checked" value="1"><sapn style="margin-right:20px;">展示</sapn>
                <input id="r2" type="radio" name="status" value="2"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>
          </tbody>
        </table>
        <div class="modal-footer">
            <right>
              <button type="button" class="btn btn-primary" id='submit'>确定</button>
            </right>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$('#add').click(function(){
	$('#detailLabel').html('新增设备类型');
	$('#tr-id').hide();
	$('#id').val('');
	$('#type_name').val('');
    $('#module_num').val('');
    $('#module_plate_num').val('');
    $('#module_plate_code_num').val('');
    $('#r1').prop('checked', true);
	$('#r2').prop('checked', false);
    $('#infoModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});
$("#submit").click(function(){
	var id = $("#id").val();
	if(id){
		var url = '<?php echo base_url('MachineType/update')?>';
    }else{
    	var url = '<?php echo base_url('MachineType/add')?>';
    }
	var type_name = $("#type_name").val();
	var module_num = $("#module_num").val();
	var module_plate_num = $("#module_plate_num").val();
	var module_plate_code_num = $("#module_plate_code_num").val();
	var status  = $('input:radio[name=status]:checked').val();
    $.ajax({
        type: 'POST',
        url: url,
        data: {
        	  "id":id,
        	  "type_name":type_name,
        	  "module_num":module_num,
        	  "module_plate_num":module_plate_num,
        	  "module_plate_code_num":module_plate_code_num,
        	  "status":status
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
            if(data.code==200){
                toastr.success(data.msg);
                setTimeout(function(){
                    location.reload();
                }, 2000);
            }else{
                toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
        }
    });
});
$(".info").click(function(){
	var id = $(this).data('id');
	var url = '<?php echo base_url('MachineType/info')?>';
    $.ajax({
        type: 'GET',
        url: url,
        data: {
        	  "id":id
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
            if(data.code==200){
                console.log(data.data);
                $('#detailLabel').html('编辑设备类型');
                $('#tr-id').show();
            	$('#id').val(data.data.id);
                $('#type_name').val(data.data.type_name);
                $('#module_num').val(data.data.module_num);
                $('#module_plate_num').val(data.data.module_plate_num);
                $('#module_plate_code_num').val(data.data.module_plate_code_num);
                if(data.data.status==1){
            		$('#r1').prop('checked', true);
            		$('#r2').prop('checked', false);
            	}else if (data.data.status==2){
            		$('#r1').prop('checked', false);
            		$('#r2').prop('checked', true);
                }
                $("#submit").attr('data-id',id);
            	$('#infoModal').modal({
                    backdrop: 'static', // 空白处不关闭.
                    keyboard: false // ESC 键盘不关闭.
                });
            }else{
                toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
        }
    });
});


$(".del").click(function(){
	var truthBeTold = window.confirm("确认删除该设备类型吗？");
   
    if (truthBeTold) {
    	var id = $(this).data('id');
    	var url = '<?php echo base_url('MachineType/del')?>';
        $.ajax({
            type: 'GET',
            url: url,
            data: {id:id},
            dataType: 'json',
            async:false,//同步请求
            success: function(data){
                if(data.code==200){
                    toastr.success(data.msg);
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                }else{
                    toastr.error(data.msg);
                }        
            },
            error: function(xhr, type){
                toastr.error(detailLabel+"未知错误");
            }
        });
    } else {
       return; 
    }
});
</script>