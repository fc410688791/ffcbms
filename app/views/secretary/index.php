<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.color_black{
    color:black;
}
.color_red{
    color:red;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class='box'>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>编号</td>
                                <td>昵称</td>
                                <td>备注名</td>
                                <td>角色</td>
                                <td>状态</td>
                                <td>绑定时间</td>
                                <td>代理商</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['user_name']; ?></td>
                                <td><?php echo $info['name']; ?></td>
                                <td><?php echo '秘书'; ?></td>
                                <td><?php echo $info['bind_status_name']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['agent_name']; ?></td>
                                <td>
                                    <?php if ($info['status']==1){?>
                                    <a class="unt pointer color_red" href="javascript:;" data-id='<?php echo $info['id'];?>'>解绑</a>
                                    <?php }elseif ($info['status']==2){?>
                                    <a class="del pointer color_black" href="javascript:;" data-id='<?php echo $info['id'];?>'>删除</a>
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

<script type='text/javascript'>
$('.del').click(function(){
    var id = $(this).attr('data-id');
    var title = '{'+id+'}--你确定删除该内容吗？';
    
    $('#confirm-danger').attr('data-id', id);
    $('#title-danger').html(title);
    $('#modal-danger').modal('show');
});
$('#confirm-danger').click(function(){
	var id = $(this).attr('data-id');
    var url = '<?php echo base_url('Secretary/update')?>';
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

$('.unt').click(function(){
    var id = $(this).attr('data-id');
    var title = '{'+id+'}--解绑之后该秘书将不会看到代理商的权限内容';

    $('#confirm-unt').attr('data-id', id);
    $('#title-unt').html(title);
    $('#modal-unt').modal('show');
});
$('#confirm-unt').click(function(){
	var id = $(this).attr('data-id');
    var url = '<?php echo base_url('Secretary/update')?>';
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "id":id,
          "status":2,
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