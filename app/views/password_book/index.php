<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i> 新增密码本</a>
                </div>
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>编号</td>
                                <td>名称</td>
                                <td>密码组</td>
                                <td>每组密码数</td>
                                <td>创建时间</td>
                                <td>操作</td>   
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['id']?></td>
                                <td><?= $info['name']?></td>
                                <td><?= $info['group_num']?></td>
                                <td><?= $info['password_num']?></td>
                                <td><?= date("Y-m-d H:i:s", $info['create_time'])?></td>
                                <td>
                                    <a href="javascript:;" title='查看' onclick='clip_modal_ft(<?= $info['id']?>)'>查看</a>
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
              <th>密码本名称:</th>
              <th colspan="3">
                <input type="text" name="name" id='name'>
              </th>
            </tr>
            <tr class='det_tr'>
              <th>密码本分组个数:</th>
              <th colspan="3">
                <input type="text" name='group_num' id='group_num' maxlength="3" oninput = "value=value.replace(/[^\d]/g,'')">
              </th>
            </tr>
            <tr class='det_tr'>
              <th>组成员数:</th>
              <th colspan="3">
                <input type="text" name='password_num' id='password_num' maxlength="3" oninput = "value=value.replace(/[^\d]/g,'')">
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

<!-- 复制文本模态框 -->
<div class="modal fade manageGoodsType" id="copyModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="ture">
  <div class="modal-dialog">
    <div class="modal-content">

      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">复制文本模态框</h4>
      </div>

      <div class="modal-body" id='clip_content' style="overflow-x:auto"></div>

      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary copy_btn" data-clipboard-action="copy" data-clipboard-target="#clip_content">复制</button>
        </center>
      </div>
      
    </div><!-- /.modal-content -->
  </div>
</div>
<!-- 复制文本模态框 - end -->

<!-- 一键复制值到剪切板 -->
<script src="<?php echo $assets_dir; ?>/bower_components/clipboard/dist/clipboard.min.js"></script>
<script>
$('#add').click(function(){
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
    document.getElementById("name").value='';
    document.getElementById("group_num").value='';
    document.getElementById("password_num").value='';
    $('#staffModal').modal('show');
});
$('#submit').click(function(){   
    var url = '<?php echo base_url('PwdBook/add')?>';
    var name = $('#name').val();
    var group_num = $('#group_num').val();
    var password_num = $('#password_num').val();
    if(!name||group_num<1||password_num<1){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
      type: 'get',
      url: url,
      data: {
          "name":name,
          'group_num': group_num,
          'password_num': password_num
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
//可复制信息的模态框
function clip_modal_ft(thisDom) {
	$('#copyModal').modal({
	    backdrop: 'static', // 空白处不关闭.
	    keyboard: false // ESC 键盘不关闭.
	});
	var url = '<?php echo base_url('PwdBook/index')?>';   
    $.ajax({
      type: 'get',
      url: url,
      data: {
          "id":thisDom
      },
      dataType: 'json',
      async:false,//同步请求
      success: function(data){
        if(data.code==200){
        	$('#clip_content').html(data.msg);
        	$('#copyModal').modal('show');
        }else{
          toastr.error(data.msg);
        }        
      },
      error: function(xhr, type){
         toastr.error(detailLabel+"未知错误");
      }
    });

  // 复制 modal 中文本
  $('.copy_btn').click(function(){
    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function(e) {
      $('#copyModal').modal('hide');
    });

    clipboard.on('error', function(e) {
      console.log(e);
    });
  });
}
</script>