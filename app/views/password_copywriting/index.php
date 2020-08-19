<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i> 新增密码文案</a>
                </div>
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号ID</td>
                                <td>文案名称</td>
                                <td>创建时间</td>
                                <td>操作</td> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['id']?></td>
                                <td><?= $info['button_text']?></td>
                                <td><?= $info['create_time']?></td>
                                <td>
                                <a href="javascript:;"><span class='del' data-id="<?php echo $info['button_text'] ?>" data-href="<?php echo base_url('Copywriting/del')."?id={$info['id']}"; ?>">删除</span></a>
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
<!-- 删除弹窗  -->
<?php echo $del_confirm; ?>

<!-- 添加 addModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel"></h3>
      </div>

      <div class="modal-body">
        <table class="table">
        <form id='wifi_form'>
          <tbody>
            <tr class='det_tr'>
              <th>密码文案:</th>
              <th colspan="3">
                <input type="text" name="button_text" id='button_text' maxlength="5"  onkeyup="value=value.replace(/[^\u4E00-\u9FA5]/g,'')">
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer" style="margin-top:30px;">
        <center>
          <button type="button" class="btn btn-primary" id='submit'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<script>
$('#add').click(function(){
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
    document.getElementById("button_text").value='';
});
$('#submit').click(function(){   
    var url = '<?php echo base_url('Copywriting/add')?>';
    var button_text = $('#button_text').val();
    if(!button_text||button_text.length<5){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
      type: 'get',
      url: url,
      data: {
          "button_text":button_text
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