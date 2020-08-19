<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i>发送消息</a>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>ID</td>
                                <td>发送类型</td>
                                <td>发送账号</td>
                                <td>标题</td>
                                <td>发送时间</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['id']?></td>
                                <td><?= $info['user_type']==2?'指定用户':'所有用户'?></td>
                                <td><?= $info['user']??'-'?></td>
                                <td>
                                    <a class="edit" href="javascript:;" data-id="<?= $info['id']?>" data-user_type="<?= $info['user_type']?>" data-user="<?= $info['user']??'-'?>" data-title="<?= $info['title']?>" data-content="<?= $info['content']?>" data-create_time="<?= $info['create_time']?>">
                                    <?= $info['title']?>
                                    </a>
                                </td>
                                <td><?= $info['create_time']?></td>
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
        <table class="table" style="width:60%;">
        <form id='add_form'>
          <tbody>
            <tr>
              <th>发送类型:</th>
              <th colspan="3">
                <select name="user_type" id="user_type">
                    <option value="1">所有用户</option>
                    <option value="2">指定用户</option>
                </select>
              </th>
            </tr>
            <tr id="a-user">
              <th>指定用户:</th>
              <th colspan="3">
                <input id='user' name="user" type="text" placeholder="代理商ID，多个请用;隔开">
              </th>
            </tr>
            <tr>
              <th>标题:</th>
              <th colspan="3">
                <input id="title" name="title" type="text">
              </th>
            </tr>
            <tr>
              <th>内容:</th>
              <th colspan="3">
                <textarea id="content" style="padding:0px;min-height:60px;" name="content" rows="3" required></textarea>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer" style="">
        <center>
          <button type="button" class="btn btn-primary" id='submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 id="e-title" style="text-align:left;"></h4>
      </div>

      <div class="modal-body" >
        <div class="info">
            <p id="e-user" style="text-align:left;"></p>
            <p id="e-content" style="text-align:left;margin-top:20px;"></p>
            <p id="e-create_time" style="text-align:left;margin-top:20px;"></p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<script>
var submit = true;
$('#add').click(function(){
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
    document.getElementById("user_type").value=1;
    $('#a-user').hide();
    document.getElementById("user").value='';
    document.getElementById("title").value='';
    document.getElementById("content").value='';
});

$("#user_type").change(function () {
	var user_type = $('#user_type').val();
	if(user_type==1){
		$('#a-user').hide();
	}else if(user_type==2){
		$('#a-user').show();
    }else{
        alert(user_type);
    }
});

$('#submit').click(function(){   
	if(!submit){
		return;
    }
    var url = '<?php echo base_url('Msg/send')?>';
    var user_type = $('#user_type').val();
    var user = $('#user').val();
    var title = $('#title').val();
    var content = $('#content').val();
    if(user_type==2&&!user){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    if(!title||!content){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
        type: 'POST',
        url: url,
        data: {
          "user_type":user_type,
          "user":user,
          "title":title,
          "content":content
        },
        dataType: 'json',
        async:false,//同步请求
        beforeSend: function(){
        	submit = false;
        },
        success: function(data){
            if(data.code==200){
              toastr.success(data.msg);
              setTimeout(function(){
                location.reload();
              }, 2000);
            }else{
              toastr.error(data.msg);
              submit = true;
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
            submit = true;
        }
	});
});

//查看设备详情
$(".edit").on("click",function(){
  var id = $(this).attr("data-id");;
  var user_type = $(this).attr("data-user_type");
  var user = $(this).attr("data-user");
  var title = $(this).attr("data-title");
  var content = $(this).attr("data-content");
  var create_time = $(this).attr("data-create_time");
  if(user_type==1){
	  user = '所有用户';
  }
  $('#e-title').html(title);
  $('#e-user').html('发送客户：'+user);
  $('#e-content').html('发送内容：'+content);
  $('#e-create_time').html('发送时间：'+create_time);
  $('#detailModal').modal('show');
});
</script>