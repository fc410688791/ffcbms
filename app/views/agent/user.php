<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">   
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>账号</td>
                                <td>姓名</td>
                                <td>手机号码</td>
                                <td>邮箱</td>
                                <td>创建时间</td>
                                <td>角色</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $k=>$info): ?>
                            <tr>
                                <td><?php echo $info['id'];?></td>  
                                <td><?php echo $info['user_name'];?></td>
                                <td><?php echo $info['name'];?></td>
                                <td><?php echo $info['mobile'];?></td>
                                <td><?php echo $info['email']?$info['email']:"-";?></td>
                                <td><?php echo $info['create_time'];?></td>
                                <td><?php echo $info['group_name'];?></td>
                                <td>
                                    <a href="javascript:;" title='查看' data-card="<?php echo $info['card']; ?>" data-postion="<?php echo $info['p_name'].$info['position']; ?>" data-describe="<?php echo $info['describe']; ?>" class="edit">详情</a>
                                    <?php if ($info['status']==1){?>
                                        <a class="upd" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='2' data-name="<?php echo $info['name'];?>">封停</a>
                                    <?php }elseif ($info['status']==2){?>
                                        <a class="upd" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='1' data-name="<?php echo $info['name'];?>">解封</a>
                                    <?php }?>
                                    <a class="upd" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='0' data-name="<?php echo $info['name'];?>">删除</a>
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
<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
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
              <th>身份证: </th>
              <th>
                <input id="card" name='card' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>详细地址: </th>
              <th>
                <input id="postion" name='postion' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>备注: </th>
              <th>
                <input id="describe" name='describe' value='' disabled="disabled">
              </th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<div class='modal modal-danger fade' id='modal-danger'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span></button>
        <h4 class='modal-title' id='title-danger'></h4>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-outline pull-left' data-dismiss='modal'>取消</button>
        <a type='button' class='btn btn-outline' id='confirm-danger' href='javascript:;'>确定</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class='modal modal-unt fade' id='modal-unt'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header' style="background-color: orange;color:#FFFFFF;border-bottom: 1px solid orange;">
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span></button>
        <h4 class='modal-title' id='title-unt'></h4>
      </div>
      <div class='modal-footer' style="background-color: orange;color:#FFFFFF;border-top: 1px solid red;">
        <button type='button' class='btn btn-outline pull-left' data-dismiss='modal'>取消</button>
        <a type='button' class='btn btn-outline' id='confirm-unt' href='javascript:;'>确定</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
//查看设备详情
$(".edit").on("click",function(){
  var card = $(this).attr("data-card");
  var postion = $(this).attr("data-postion");
  var describe = $(this).attr("data-describe");
  $('#card').val(card);
  $('#postion').val(postion);
  $('#describe').val(describe);
  $('#detailModal').modal('show');
});
$('button.close,#close').on("click",function(){
    $('#detailModal').modal("hide");
});

$(".upd").on("click",function(){
	var id = $(this).data('id');
	var status = $(this).data('status');
	var name = $(this).data('name');
	if(status==1){
	    var title = '{'+name+'}--你确定要解封该账号吗 ?';
	    $('#confirm-unt').attr('data-id', id);
	    $('#confirm-unt').attr('data-status', status);
	    $('#title-unt').html(title);
	    $('#modal-unt').modal('show');
	}else if(status==2){
		var title = '{'+name+'}--你确定要封停该账号吗 ?';
	    $('#confirm-unt').attr('data-id', id);
	    $('#confirm-unt').attr('data-status', status);
	    $('#title-unt').html(title);
	    $('#modal-unt').modal('show');
	}else{
		var title = '{'+name+'}--你确定删除该内容吗？';
	    $('#confirm-danger').attr('data-id', id);
	    $('#title-danger').html(title);
	    $('#modal-danger').modal('show');
	}
});
$('#confirm-unt').click(function(){
	var id = $(this).attr('data-id');
	var status = $(this).attr('data-status');
    var url = '<?php echo base_url('AgentUser/update')?>';
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "id":id,
          "status":status,
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
$('#confirm-danger').click(function(){
	var id = $(this).attr('data-id');
    var url = '<?php echo base_url('AgentUser/update')?>';
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "id":id,
          "status":0,
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
</script>