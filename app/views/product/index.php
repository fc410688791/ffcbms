<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">          
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i> 新增商品</a>
                </div>
                
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Product/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="名称/ID" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>编号</td>
                                <td>商品名称</td>
                                <td>商品类型</td>
                                <td>商品价格(元)</td>
                                <td>优惠价格(元)</td>
                                <td>服务时间</td>
                                <td>商品说明</td>
                                <td>创建时间</td>
                                <td>状态</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['name']; ?></td>
                                <td><?php echo $info['type_name']; ?></td>
                                <td><?php echo $info['price']; ?></td>
                                <td><?php echo $info['incentive_price']; ?></td>
                                <td><?php echo $info['open_time']; ?></td>
                                <td><?php echo $info['describe']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['status']==1?'展示':'不展示'; ?></td>
                                <td>
                                <a data-id='<?php echo $info['id']; ?>' onclick="showdetails(this);" title='详情'  href="#" >编辑</a>
                                <!-- <a href="javascript:;" title= "删除商品"><i class='fa fa-fw fa-trash-o' data-id="<?php echo $info['name']; ?>" data-href="<?php echo base_url('Product/del')."?id={$info['id']}"; ?>"></i></a> -->
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
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">添加</h3>
      </div>

      <div class="modal-body">
        <table class="table">
        <form id='add_form'>
          <tbody>
            <tr>
              <th>商品名称:</th>
              <th colspan="3">
                <input id='name' name="name" type="text" maxlength="50">
                <!-- onkeyup="value=value.replace(/[^\u4E00-\u9FA5]/g,'')" -限制只能使用中文-->
              </th>
            </tr>
            <tr>
              <th>商品类型:</th>
              <th colspan="3">
                <select name="type" id="type">
                    <option value='1'>密码器</option>
                    <option value='2'>物联网</option>
                </select>
              </th>
            </tr>
            <tr>
              <th>商品价格（单位：元）:</th>
              <th colspan="3">
                <input id="price" name="price" type="number" min="0.01" step="0.01">
              </th>
            </tr>
            <tr>
              <th>优惠价格（单位：元）:</th>
              <th colspan="3">
                <input id="incentive_price" name="incentive_price" type="number" min="0.01" step="0.01">
              </th>
            </tr>
            <tr id="a-open_time">
              <th>解锁时间:</th>
              <th colspan="3">
                <input id="open_time_h" name="open_time_h" type="text" maxlength="2" style="float:left;width:50px;"><span style="float:left;">小时-</span><input id="open_time_m" name="open_time_m" type="text" maxlength="2" style="float:left;width:50px;">分钟
              </th>
            </tr>
            <tr>
              <th>商品说明:</th>
              <th colspan="3">
                <textarea id="describe" name="describe" rows="3" required></textarea>
              </th>
            </tr>
            <tr>
              <th>默认状态：</th>
              <th>
                <input id="a_r3" type="radio" name="is_default" value="1"><sapn style="margin-right:20px;">默认</sapn>
                <input id="a_r4" type="radio" name="is_default" value="0" checked="checked"><sapn style="margin-right:20px;">普通</sapn>
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th>
                <input id="a_r1" type="radio" name="status" checked="checked" value="1"><sapn style="margin-right:20px;">展示</sapn>
                <input id="a_r2" type="radio" name="status" value="0"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <right>
          <button type="button" class="btn btn-primary" id='submit'>确定</button>
        </right>
      </div>
    </div>
  </div>
</div>

<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">编辑</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>商品 ID: </th>
              <th>
                <input id="u_id" name='id' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>创建者: </th>
              <th>
                <input id="u_admin_name" name='user_name' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>商品名称:</th>
              <th>
                <input id="u_name" name='name' value=''>
              </th>
            </tr>
            <tr>
              <th>商品类型:</th>
              <th colspan="3">
                <select name="type" id="u_type">
                    <option value='1'>密码器</option>
                    <option value='2'>物联网</option>
                </select>
              </th>
            </tr>
            <tr>
              <th>商品价格（单位：元）:</th>
              <th>
                <input id="u_price" name='price' value='' type="number" min="0.01" step="0.01">
              </th>
            </tr>
            <tr>
              <th>优惠价格（单位：元）:</th>
              <th>
                <input id="u_incentive_price" name='incentive_price' value='' type="number" min="0.01" step="0.01">
              </th>
            </tr>
            <tr id="u_open_time">
              <th>解锁时间:</th>
              <th colspan="3">
                <input id="u_open_time_h" name="open_time_h" type="text" maxlength="2" style="float:left;width:50px;"><span style="float:left;">小时-</span><input id="u_open_time_m" name="open_time_m" type="text" maxlength="2" style="float:left;width:50px;">分钟
              </th>
            </tr>
            <tr>
              <th>商品说明:</th>
              <th colspan="3">
                <textarea id="u_describe" name="describe" rows="3" required></textarea>
              </th>
            </tr>
            <tr>
              <th>默认状态：</th>
              <th>
                <input id="u_r3" type="radio" name="u_is_default" value="1"><sapn style="margin-right:20px;">默认</sapn>
                <input id="u_r4" type="radio" name="u_is_default" value="0" checked="checked"><sapn style="margin-right:20px;">普通</sapn>
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th>
                <input id="u_r1" type="radio" name="u_status" checked="checked" value="1"><sapn style="margin-right:20px;">展示</sapn>
                <input id="u_r2" type="radio" name="u_status" value="0"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>
          </tbody>
        </table>
        <div class="modal-footer">
            <right>
                <button id="btn-update" class="btn btn-success">修改</button>
                <button id="close" class="btn btn-default">取消</button>
            </right>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<script>
$('#add').click(function(){
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
    document.getElementById("type").value=2;
    $('#a-open_time').show();
    document.getElementById("name").value='';
    document.getElementById("price").value='';
    document.getElementById("open_time_h").value='';
    document.getElementById("open_time_m").value='';
    document.getElementById("describe").value='';
});

$("#type").change(function () {
	var type = $('#type').val();
	if(type==1){
		$('#a-open_time').hide();
	}else{
		$('#a-open_time').show();
    }
});
$("#u_type").change(function () {
	var u_type = $('#u_type').val();
	if(u_type==1){
		$('#u_open_time').hide();
	}else{
		$('#u_open_time').show();
    }
});
$('#submit').click(function(){   
    var url = '<?php echo base_url('Product/add')?>';
    var name = $('#name').val();
    var type = $('#type').val();
    var price = $('#price').val();
    var incentive_price = $('#incentive_price').val();
    var open_time_h = $('#open_time_h').val();
    var open_time_m = $('#open_time_m').val();
    var describe = $('#describe').val();
    var status  = $('input:radio[name=status]:checked').val();
    var is_default  = $('input:radio[name=is_default]:checked').val();
    if(!name||price<=0){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    if(Number(open_time_m)>=60){
    	toastr.warning("分钟不能大于59"); 
    	$('#open_time_m').val('');
    	return;
    }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "name":name,
          "type":type,
          "price":price,
          "incentive_price":incentive_price,
          "open_time_h":open_time_h,
          "open_time_m":open_time_m,
          "describe":describe,
          "status":status,
          "is_default":is_default
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

/*点击弹出 modal 查看详情方法*/
function showdetails(source) {
    var id = $(source).data('id');
    $("#u_id").val(id);
    $.ajax({
      type: 'GET',
      url: '<?php echo base_url('Product/get_info') ?>',
      data:{
        'id':id
      },
      dataType:'json',
      async:false, // 同步请求
      success:function(data) {
        if(data.code==200){
        	$("#u_name").val(data.data.name);
        	$("#u_type").val(data.data.type);
        	if(data.data.type==1){
        		$('#u_open_time').hide();
            }else {
                $('#u_open_time').show();
            }
        	$("#u_price").val(data.data.price);
        	$("#u_incentive_price").val(data.data.incentive_price);
        	$("#u_open_time_h").val(data.data.open_time_h);
        	$("#u_open_time_m").val(data.data.open_time_m);
        	$("#u_describe").html(data.data.describe);
        	$("#u_admin_name").val(data.data.user_name);
        	if(data.data.status==0){
        		$('#u_r1').prop('checked', false);
        		$('#u_r2').prop('checked', true);
        	}else if (data.data.status==1){
        		$('#u_r1').prop('checked', true);
        		$('#u_r2').prop('checked', false);
            }
        	if(data.data.is_default==0){
        		$('#u_r3').prop('checked', false);
        		$('#u_r4').prop('checked', true);
        	}else if (data.data.is_default==1){
        		$('#u_r3').prop('checked', true);
        		$('#u_r4').prop('checked', false);
            }
            $("#detailModal").modal('show');
         }else{
           toastr.error(data.msg);
         }
      }
    });
}
/*点击弹出 modal 查看详情方法 -end*/

$("#btn-update").on("click",function(){
	  var id = $('#u_id').val();
	  var name = $('#u_name').val();
	  var type = $('#u_type').val();
	  var price = $('#u_price').val();
	  var incentive_price = $('#u_incentive_price').val();
	  var open_time_h = $('#u_open_time_h').val();
	  var open_time_m = $('#u_open_time_m').val();
	  var describe = $('#u_describe').val();
	  var status  = $('input:radio[name=u_status]:checked').val();
	  var is_default  = $('input:radio[name=u_is_default]:checked').val();
	  if(!name||price<=0){ 
	    	toastr.warning("请添按要求将数据填充完整！"); 
	    	return;
	    }
	  if(Number(open_time_m)>=60){
        	toastr.warning("分钟不能大于59");
        	return;
        }
	  $.ajax({
	      type: 'POST',
	      url: '<?php echo base_url('Product/update').'?id=' ?>' + id,
	      dataType: 'json',
	      data:{
		      'name': name,
		      'type': type,
		      'price': price,
		      'incentive_price': incentive_price,
		      'open_time_h': open_time_h,
		      'open_time_m': open_time_m,
		      'describe': describe,
		      'status': status,
		      'is_default': is_default
		      
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
</script>