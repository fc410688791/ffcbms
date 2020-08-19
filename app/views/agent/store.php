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
                                <td>投放点权限</td>
                                <td>创建时间</td>
                                <td>角色</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $k=>$info): ?>
                            <tr>
                                <td><?php echo $k+1;?></td>  
                                <td><?php echo $info['user_name'];?></td>
                                <td><?php echo $info['name'];?></td>
                                <td><?php echo $info['mobile'];?></td>
                                <td><?php echo $info['role_merchant_id'];?></td>
                                <td><?php echo $info['create_time'];?></td>
                                <td><?php echo $info['group_name'];?></td>
                                <td>
                                <a href="javascript:;" title='查看' data-card="<?php echo $info['card']; ?>" data-postion="<?php echo $info['p_name'].$info['position']; ?>" data-describe="<?php echo $info['describe']; ?>" class="edit"><i class='fa fa-fw fa-file-text-o'></i></a>
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
</script>