<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">          
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i>添加充币活动</a>
                    *1元=100充币
                </div>
                
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberActivityCharge/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="商品名称" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="selectForm" class="" method="GET" action='<?php echo base_url('MemberActivityCharge/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id='is_charge_status' name="is_charge_status" class="form-control" style="width:120px;">
                                        <option value='99'>展示状态</option>
                                        <option value="1" <?php echo isset($is_charge_status)? ($is_charge_status == 1)?'selected':'':''; ?> >展示</option>
                                        <option value="0" <?php echo isset($is_charge_status)? ($is_charge_status == 0)?'selected':'':''; ?> >不展示</option>
                                    </select>
                                    <select id='is_gift_status' name="is_gift_status" class="form-control" style="width:150px;">
                                        <option value='99'>是否参与活动</option>
                                        <option value="1" <?php echo isset($is_gift_status)? ($is_gift_status == 1)?'selected':'':''; ?> >参与</option>
                                        <option value="0" <?php echo isset($is_gift_status)? ($is_gift_status == 0)?'selected':'':''; ?> >不参与</option>
                                    </select>
                                    <button type="submit" id="query_submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/MemberActivityCharge/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>商品名称</td>
                                <td>充值金额</td>
                                <td>购买充币(个)</td>
                                <td>赠送充币(个)</td>
                                <td>展示状态</td>
                                <td>是否参与赠送活动</td>
                                <td>起止时间</td>
                                <td>创建日期</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['charge_name']; ?></td>
                                <td><?php echo $info['charge_amount']; ?></td>
                                <td><?php echo $info['charge_amount']*100; ?></td>
                                <td><?php echo $info['gift_currency']; ?></td>
                                <td><?php echo $info['is_charge_status']; ?></td>
                                <td><?php echo $info['is_gift_status']; ?></td>
                                <td><?php echo $info['gift_start_time']; ?></br><?php echo $info['gift_end_time']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td>
                                    <a data-id='<?php echo $info['id']; ?>' onclick="showdetails(this);" href="#" >编辑</a>
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
<?php echo $del_confirm; ?>

<!-- 添加 addModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width:50%;margin:auto;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel"></h3>
      </div>

      <div class="modal-body">
        <table class="table">
        <form id='add_form'>
          <tbody>
            <tr>
              <th>商品名称:</th>
              <th colspan="3">
                <input id='a_charge_name' name="charge_name" type="text" maxlength="50" placeholder="商品名称">
              </th>
            </tr>
            <tr>
              <th>充值金额:</th>
              <th colspan="3">
                <input id="a_charge_amount" name="charge_amount" type="number" min="0.01" step="1" placeholder="1元=100充币">
              </th>
            </tr>
            <tr>
              <th>展示状态:</th>
              <th colspan="3">
                <select id="a_is_charge_status" name="is_charge_status">
                    <option value='1'>展示</option>
                    <option value='0'>不展示</option>
                </select>
              </th>
            </tr>
            <tr>
              <th>是否参与活动:</th>
              <th colspan="3">
                <select id="a_is_gift_status" name="is_gift_status">
                    <option value='0'>不参与</option>
                    <option value='1'>参与</option>
                </select>
              </th>
            </tr>
            <tr class="join">
              <th>赠送充币:</th>
              <th colspan="3">
                <input id="a_gift_currency" name="gift_currency" type="number" min="1" step="1">
              </th>
            </tr>
            <tr class="join">
              <th>生效类型:</th>
              <th colspan="3">
                <select id="a_type" name="type">
                    <option value='1'>永久生效</option>
                    <option value='2'>立即生效</option>
                    <option value='3'>选择时间范围内生效</option>
                </select>
              </th>
            </tr>
            <tr class="start_time">
              <th>开始时间:</th>
              <th colspan="3">
                <input id="a_gift_start_time" name="gift_start_time" type="date">
              </th>
            </tr>
            <tr class="end_time">
              <th>结束时间:</th>
              <th colspan="3">
                <input id="a_gift_end_time" name="gift_end_time" type="date">
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer" style="margin-top:30px;">
        <center>
          <button type="button" class="btn btn-primary" id='submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<!-- 详情detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width:50%;margin:auto;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel"></h3>
      </div>

      <div class="modal-body">
        <table class="table">
        <form id='update_form'>
          <tbody>
            <tr>
              <th>ID:</th>
              <th colspan="3">
                <input id='u_id' type="text" disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>商品名称:</th>
              <th colspan="3">
                <input id='u_charge_name' type="text" maxlength="50" placeholder="商品名称">
              </th>
            </tr>
            <tr>
              <th>充值金额:</th>
              <th colspan="3">
                <input id="u_charge_amount" type="number" min="0.01" step="1" placeholder="1元=100充币">
              </th>
            </tr>
            <tr>
              <th>展示状态:</th>
              <th colspan="3">
                <select id="u_is_charge_status">
                    <option value='1'>展示</option>
                    <option value='0'>不展示</option>
                </select>
              </th>
            </tr>
            <tr>
              <th>是否参与活动:</th>
              <th colspan="3">
                <select id="u_is_gift_status">
                    <option value='0'>不参与</option>
                    <option value='1'>参与</option>
                </select>
              </th>
            </tr>
            <tr class="join">
              <th>赠送充币:</th>
              <th colspan="3">
                <input id="u_gift_currency" type="number" min="1" step="1">
              </th>
            </tr>
            <tr class="join">
              <th>生效类型:</th>
              <th colspan="3">
                <select id="u_type">
                    <option value='1'>永久生效</option>
                    <option value='2'>立即生效</option>
                    <option value='3'>选择时间范围内生效</option>
                </select>
              </th>
            </tr>
            <tr class="start_time">
              <th>开始时间:</th>
              <th colspan="3">
                <input id="u_gift_start_time" type="date">
              </th>
            </tr>
            <tr class="end_time">
              <th>结束时间:</th>
              <th colspan="3">
                <input id="u_gift_end_time" type="date">
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer" style="margin-top:30px;">
        <center>
          <button type="button" class="btn btn-primary" id='btn-update' style='width:400px'>保存</button>
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
    var is_gift_status = $('#a_is_gift_status').val();
    if(is_gift_status==1){
		$(".join").show();
	}else{
		$(".join").hide();
    }
    a_type();
});
$("#a_is_gift_status").change(function () {
	var is_gift_status = $('#a_is_gift_status').val();
	if(is_gift_status==1){
		$(".join").show();
		a_type();
	}else{
		$(".join").hide();
		$(".start_time").hide();
		$(".end_time").hide();
    }
});
$("#a_type").change(function () {
	a_type();
});

$('#submit').click(function(){   
    var url = '<?php echo base_url('MemberActivityCharge/add')?>';
    var charge_name = $('#a_charge_name').val();
    var charge_amount = $('#a_charge_amount').val();
    var is_charge_status = $('#a_is_charge_status').val();
    var is_gift_status = $('#a_is_gift_status').val();
    var gift_currency = $('#a_gift_currency').val();
    var type = $('#a_type').val();
    var gift_start_time = $('#a_gift_start_time').val();
    var gift_end_time = $('#a_gift_end_time').val();
    if(!charge_name||charge_amount<=0){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "charge_name":charge_name,
          "charge_amount":charge_amount,
          "is_charge_status":is_charge_status,
          "is_gift_status":is_gift_status,
          "gift_currency":gift_currency,
          "type":type,
          "gift_start_time":gift_start_time,
          "gift_end_time":gift_end_time
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

function showdetails(source) {
    var id = $(source).data('id');
    $("#u_id").val(id);
    var url = '<?php echo base_url('MemberActivityCharge/info')?>';
    $.ajax({
      type: 'GET',
      url: url,
      data:{
        'id':id
      },
      dataType:'json',
      async:false, // 同步请求
      success:function(data) {
        if(data.code==200){
            $("#u_id").val(data.data.id);
            $("#u_charge_name").val(data.data.charge_name);
            $("#u_charge_amount").val(data.data.charge_amount);
        	$("#u_is_charge_status").val(data.data.is_charge_status);
        	var is_gift_status = data.data.is_gift_status;
        	$("#u_is_gift_status").val(data.data.is_gift_status);
            if(is_gift_status==1){
        		$(".join").show();
        	}else{
        		$(".join").hide();
        		$(".start_time").hide();
        		$(".end_time").hide();
            }
            $("#u_gift_currency").val(data.data.gift_currency);
            var type = data.data.type;
            $('#u_type').val(data.data.type);
            if(type==1){
        		$(".start_time").hide();
        		$(".end_time").hide();
        	}else if(type==2){
        		$(".start_time").hide();
        		$(".end_time").show();
        		$("#u_gift_end_time").val(data.data.gift_end_time);
            }else if(type==3){
        		$(".start_time").show();
        		$("#u_gift_start_time").val(data.data.gift_start_time);
        		$(".end_time").show();
        		$("#u_gift_end_time").val(data.data.gift_end_time);
            }
            $("#detailModal").modal('show');
         }else{
           toastr.error(data.msg);
         }
      }
    });
}
$("#btn-update").on("click",function(){
	var id = $('#u_id').val();
    var charge_name = $('#u_charge_name').val();
    var charge_amount = $('#u_charge_amount').val();
    var is_charge_status = $('#u_is_charge_status').val();
    var is_gift_status = $('#u_is_gift_status').val();
    var gift_currency = $('#u_gift_currency').val();
    var type = $('#u_type').val();
    var gift_start_time = $('#u_gift_start_time').val();
    var gift_end_time = $('#u_gift_end_time').val();
    if(!charge_name||charge_amount<=0){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    var url = '<?php echo base_url('MemberActivityCharge/update')?>'+'?id='+id;
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "charge_name":charge_name,
          "charge_amount":charge_amount,
          "is_charge_status":is_charge_status,
          "is_gift_status":is_gift_status,
          "gift_currency":gift_currency,
          "type":type,
          "gift_start_time":gift_start_time,
          "gift_end_time":gift_end_time
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
$("#u_is_gift_status").change(function () {
	var is_gift_status = $('#u_is_gift_status').val();
	if(is_gift_status==1){
		$(".join").show();
		u_type();
	}else{
		$(".join").hide();
		$(".start_time").hide();
		$(".end_time").hide();
    }
});
$("#u_type").change(function () {
	u_type();
});
function a_type(){
	var type = $('#a_type').val();
    if(type==1){
		$(".start_time").hide();
		$(".end_time").hide();
	}else if(type==2){
		$(".start_time").hide();
		$(".end_time").show();
    }else if(type==3){
		$(".start_time").show();
		$(".end_time").show();
    }
}
function u_type(){
	var type = $('#u_type').val();
    if(type==1){
		$(".start_time").hide();
		$(".end_time").hide();
	}else if(type==2){
		$(".start_time").hide();
		$(".end_time").show();
    }else if(type==3){
		$(".start_time").show();
		$(".end_time").show();
    }
}
</script>