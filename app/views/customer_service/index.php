<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.file_image{
	widtht:50px;
	height:50px;
	cursor:pointer;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">  
            <div class="box">
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('CustomerService/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="订单号" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="selectForm" class="" method="GET" action='<?php echo base_url('CustomerService/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id=status name="status" class="form-control" style="width:120px;">
                                        <option value="">所有状态</option>   
                                        <?php foreach($status_list as $k=>$v){ ?>
                                        <option value="<?php echo $k ?>" <?php echo isset($status)? ($status == $k)? 'selected':'':''; ?>><?php echo $v ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id=province name="province_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($first_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($province_id)? ($province_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="city" name="city_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($second_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($city_id)? ($city_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="street" name="street_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($third_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($street_id)? ($street_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="village" name="village_id" class="form-control select2" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($fourth_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($village_id)? ($village_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/CustomerService/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body table-responsive">
                    <?php if ($order_list){ ?>
                    <table id="tb_search" class="table table-bordered table-hover" role='1' style="margin-bottom: 30px;">
                        <thead>
                            <tr>
                               <th>订单编号</th>
                               <th>下单时间</th>
                               <th>金额（元）</th>
                               <th>支付类型</th>
                               <th>设备投放点</th>
                               <th>订单状态</th>
                               <th>退款状态</th>
                               <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_list as $key1=>$order_info) { ?>
                            <tr>
                                <th><?php echo $order_info['out_trade_no'];?></th>
                                <th><?php echo $order_info['create_time'];?></th>
                                <th><?php echo $order_info['cash_fee'];?></th>
                                <th><?php echo $order_info['pay_type'];?></th>
                                <th><?php echo $order_info['merchant_name'];?></th>
                                <th><?php echo $order_info['status'];?></th>
                                <th><?php echo $order_info['r_status'];?></th>
                                <th>
                                    <a href="#" title='查看' onclick="viewRfModal('<?PHP echo $order_info['id'] ?>')">查看</a>
                                </th>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <?php }?>
                </div>
                <div class="box-body table-responsive">   
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                               <th>订单编号</th>
                               <th>创建时间</th>
                               <th>金额（元）</th>
                               <th>支付类型</th>
                               <th>手机号</th>
                               <th>退款原因</th>
                               <th>凭证</th>
                               <th>设备位置</th>
                               <th>处理反馈</th>
                               <th>退款状态</th>
                               <th>备注</th>
                               <th>操作</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <?php foreach ($list as $key2=>$info) { ?>
                            <tr>
                                <th>
                                    <a href="<?php echo base_url('CustomerService/index').'?key='.$info['out_trade_no'] ?>"><?php echo $info['out_trade_no'];?></a>
                                </th>
                                <th><?php echo $info['create_time'];?></th>
                                <th><?php echo $info['cash_fee'];?></th>
                                <th><?php echo $info['pay_type'];?></th>
                                <th><?php echo $info['mobile'];?></th>
                                <th><?php echo $info['text'].'</br>'.$info['reason'];?></th>
                                <th>
                                    <?php if (isset($info['file_list'])){foreach ($info['file_list'] as $v) { ?>
                                    <img class="file_image" alt="" src="<?php echo $v['url']; ?>">
                                    <?php }}?>
                                </th>
                                <th><?php echo $info['position_name'].$info['position_position'];?></th>
                                <th><?php echo ($info['agent_process_status']?'已处理':'待处理').'</br>'.$info['agent_process_text']; ?></th>
                                <th style="color:<?php echo $info['status_color'];?>;"><?php echo $info['status'];?></th>
                                <th><?php echo $info['remark']?$info['remark']:'-';?></th>
                                <th>
                                <?php if ($info['status']==$status_list[2]) {?>
                                    <a class="refund" data-id="<?php echo $info['id'] ?>" data-out_trade_no="<?php echo $info['out_trade_no'] ?>">退款</a>
                                    <a class="refuse" data-id="<?php echo $info['id'] ?>" data-out_trade_no="<?php echo $info['out_trade_no'] ?>">拒绝</a>
                                <?php } ?>
                                    <a class="remark" data-id="<?php echo $info['id'] ?>" data-out_trade_no="<?php echo $info['out_trade_no'] ?>">备注</a>
                                </th>
                            </tr>
                            <?php }?>
                        </tbody>
                        <tfoot>
                            <tr>
                              <th colspan="10"><?php echo $pagination; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>

<!--图片预览-->
<div class="modal fade bs-example-modal-lg text-center" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:1150px;">
      <div style="background-color:rgba(180,180,180,0);box-shadow:none;border:0;" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="break-label">图片预览</h3>
      </div>
      <div class="modal-body">
         <!-- <h2>Slideshow #3</h2> -->
         <div style="padding-top: 0px;" class="ss3_wrapper">
            <a href="#" class="slideshow_prev hidden"><span>Previous</span></a>
            <a href="#" class="slideshow_next hidden"><span>Next</span></a>
            <div class="slideshow_box">
                <div class="data"></div>
            </div>
            <div id="image_view" class="slideshow">
                     
            </div>
        </div><!-- .ss3_wrapper -->
      </div>
      <div class="modal-footer" style="display: none">
        <button class="big-img">+</button>
        <button class="small-img">-</button>
      </div>
    </div>
  </div>
</div>
<!-- 退款弹窗  -->
<!-- 
// 列表查看操作:已人工退款退款查看:rf_view;客户端退款退款查看:rf_client_view;未存在退款条件的查看:n_rf_view;rf_noaccept_view:不受理查看
// 列表退款操作:客服人工退款:rf_manual;客户端退款:rf_client
// 列表不受理操作:rf_noaccept_opt
 -->
<div class="modal fade bs-example-modal-lg" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop='static' data-keyboard='false'>
    <div class="modal-dialog modal-lg" style="width:520px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title  text-center" id="rf_detailLabel">详情</h3>
            </div>
            <div class="modal-body">
                <div id="rf_rfsdo">
                    <table class="table">
                        <tbody>
                            <tr class='det_tr'><th>订单号:</th><td id="rf_oid"></td></tr>
                            <tr class='det_tr'><th>支付单号:</th><td id="rf_wxoid"></td></tr>
                            <tr class='det_tr'><th>商品价格:</th><td id="rf_productprice"></td></tr>
                            <tr class='det_tr'><th>创建时间:</th><td id="rf_createtime"></td></tr>
                            <tr class='det_tr'><th>支付时间:</th><td id="rf_paytime"></td></tr>
                            <tr class='det_tr'><th>启动时间:</th><td id="rf_opentime"></td></tr>
                            <tr class='det_tr'><th>充电时间:</th><td id="rf_chargetime"></td></tr>
                            <tr class='det_tr'><th>订单状态:</th><td id="rf_o_status" class="text-red"></td></tr>
                            <tr class='det_tr'><th>退款状态:</th><td id="rf_r_status" class="text-red"></td></tr>
                            <!--操作时显示-->
                            <tr class='det_tr'>
                            	<th><span class="text-red rfMk">*</span>手机号:</th>
                            	<td>
                            		<input type="text" class="form-control" id="rf_mobile"/>
                            	</td>
                            </tr>
                            <tr class='det_tr rfOprate'>
                            	<th><span class="text-red rfMk">*</span>退款原因类型:</th>
                            	<td>
                            		<select class="form-control" id="rf_text_id">
                            		    <option value="4">其他</option>
                            			<option value="1">不通电</option>
                            			<option value="2">充电速度慢</option>
                            			<option value="3">设备损坏</option>
                            		</select>
                            	</td>
                            </tr>
                            <tr class='det_tr'>
                            	<th><span class="text-red rfMk"></span>申请退款原因:</th>
                            	<td>
                            		<input type="text" class="form-control" id="rf_reason"/>
                            	</td>
                            </tr>
                            <tr class='det_tr'>
                            	<th><span class="text-red rfMk"></span>备注:</th>
                            	<td>
                            		<input type="text" class="form-control" id="rf_remark"/>
                            	</td>
                            </tr>
                            <tr class='det_tr rfOprate'>
                            	<th><span class="text-red rfMk">*</span>退款来源:</th>
                            	<td>
                            		<select class="form-control" id="rf_source">
                            		    <option value="2">后台</option>
                            			<option value="1">用户</option>
                            		</select>
                            	</td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <button id="rf_btn" class="btn btn-default confirm_rf" onclick="doAccept();"><i class='fa fa-spinner fa-spin hide'></i>退款</button>
                                </td>
                            </tr>
                        
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- 退款 - detailModal -end -->

<div class="modal fade bs-example-modal-lg text-center" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">退款</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>ID：</th>
              <th colspan="3">
                  <input id="r-id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>订单编号：</th>
              <th colspan="3">
                  <input id="r-out_trade_no" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>备注：</th>
              <th colspan="3">
                  <input id="r-remark" value=''>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
        <center>
          <h3 style="color:red;">确认退款将会把支付货币原路返回</h3>
        </center>
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='refund-submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg text-center" id="refuseModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">拒绝</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>ID：</th>
              <th colspan="3">
                  <input id="s-id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>订单编号：</th>
              <th colspan="3">
                  <input id="s-out_trade_no" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>备注：</th>
              <th colspan="3">
                  <input id="s-content" type="text"/>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='refuse-submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg text-center" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">备注</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>ID：</th>
              <th colspan="3">
                  <input id="remark-id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>订单编号：</th>
              <th colspan="3">
                  <input id="remark-out_trade_no" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>备注：</th>
              <th colspan="3">
                  <input id="remark-content" type="text"/>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='remark-submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<script>
var option = "<option value=''>选择地址</option>";
$(".position").on('change',function(){
	var name = $(this).attr('id');
	if(name=='province'){
		$("#city").html(option);
		$("#street").html(option);
		$("#village").html(option);
    }else if(name=='city'){
    	$("#street").html(option);
    	$("#village").html(option);
    }else if(name=='street'){
    	$("#village").html(option);
    }	
	var pid = $("#"+ name +" option:selected").val();
	if(pid){
		$.ajax({
	      type: 'get',
	      url: '<?php echo base_url('Location/get_list')?>',
	      data: {
	          "pid":pid
	      },
	      dataType: 'json',
	      async:false,
	      success: function(data){
            console.log(data)
	        if(data.code==200){
                var list = data.msg;
                if(name=='province'){
                    var id = 'city';
                }else if(name=='city'){
                	var id = 'street';
                }else if(name=='street'){
                	var id = 'village';
                }
                for(var i=0;i<list.length;i++)
                {
                	$("#" + id).append("<option value='"+list[i].id+"'"+">"+list[i].name+"</option>");                         
                }
	        }        
	      },
	      error: function(xhr, type){
	         toastr.error(detailLabel+"未知错误");
	      }
		});
	}
		
});

//图片预览
$(document).on("click",".file_image",function(){
    var $image_view = $("#image_view");
    var html = "";
    var img_url = $(this).attr("src");
      html+='<div class="slideshow_item">'
             +'<div class="image"><a href="#"><img style="width:600px;height:auto;" src="'+img_url+'" alt="photo 1"/></a></div>'
             +'<div class="data">'
                   +'<h4><a href="#"></a></h4>'
               +'</div>'
           +'</div>';
    $image_view.html(html);
    $(".slideshow_next").addClass("hidden");
    $(".slideshow_prev").addClass("hidden");           
    $("#imgModal").modal("show");
});

//操作退款的数据弹窗
function viewRfModal(order_id){
	if(!order_id)
		return;
	$.ajax({
	    url: "<?php echo base_url("Order/get_order_info") ?>",
	    type: 'GET',
	    dataType: 'json',
	    data: {'order_id': order_id},
	    success: function (result) {
	        if (result['code'] != 200) {
	        	toastr.error('查询详细信息失败:' + result['msg']);
	        	return false;
	        }
	        var order_info = result['data']['order_info'];
	        var refund_info = result['data']['refund_info'];
	        console.log(refund_info);
	        
	        /*数据填充*/
	        $("#rf_oid").html(order_info['out_trade_no']);//订单号
	        $("#rf_wxoid").html(order_info['transaction_id']);//微信或支付宝订单号 
	        $("#rf_productprice").html(order_info['cash_fee']);//金额
	        $("#rf_createtime").html(order_info['create_time']);//创建时间
	        $("#rf_paytime").html(order_info['pay_time']);//支付时间
	        $("#rf_opentime").html(order_info['open_time']);//启动时间
	        $("#rf_chargetime").html(order_info['charge_time']);//充电时间
	        $("#rf_o_status").html(order_info['o_status']);//订单状态
	        $("#rf_r_status").html(order_info['r_status']);//退款状态
	        $("#rf_mobile").val(order_info['mobile']);//用户手机号
	        if(refund_info){
	        	$("#rf_mobile").val(refund_info['r_mobile']);//申请退款手机号
	        	$("#rf_text_id").val(refund_info['refund_text_id']);//退款原因类型
	        	$("#rf_reason").val(refund_info['reason']);//申请退款原因
	        	$("#rf_remark").val(refund_info['remark']);//退款备注
	        	$("#rf_source").val(refund_info['source']);//退款申请来源
		    }
		    if(order_info['o_status']=='支付成功'&&(order_info['r_status']=='待退款'||order_info['r_status']=='无')){
		    	$("#rf_btn").removeClass("hide");
			}else{
				$("#rf_btn").addClass("hide");
		    }
	        $("#detailModal").modal('show');
	    },
	    error: function () {
	        toastr.error('查询信息出错');
	    }
	});
}

function doAccept()
{
	var out_trade_no = $("#rf_oid").html();// 订单编号
	var mobile = $("#rf_mobile").val();//申请退款手机号;
	var refund_text_id = $("#rf_text_id").val();//退款原因类型
	var reason = $("#rf_reason").val();//退款原因
	var remark = $("#rf_remark").val();//备注
	var source = $("#rf_source").val();//退款申请来源
 	var array_data=new Object();
	array_data.out_trade_no=out_trade_no;
	array_data.mobile=mobile;
	array_data.refund_text_id=refund_text_id;
	array_data.reason=reason;
	array_data.remark=remark;
	array_data.source=source;
	if(!out_trade_no||!mobile||!refund_text_id||!source){
		toastr.error('请将表单填充完整');
		return;
    }
	$.ajax({
	  url: "<?php echo base_url("CustomerService/refund") ?>",
	  type: 'POST',
	  dataType: 'json',
	  data: array_data,
	  success: function (result) {
	    if (result.code == 200) {
	    	toastr.success(result.msg);
	    	setTimeout(function(){
	    		location.reload();    	  
            }, 1500);
	    } else {
	    	toastr.error(result.msg);
	    }
	  },
	  error: function () {
	    toastr.error('失败!');
	  }
	});
}
$(".refund").on("click",function(){
	var id = $(this).data('id');
	var out_trade_no = $(this).data('out_trade_no');
	$("#r-id").val(id);
	$("#r-out_trade_no").val(out_trade_no);
	$("#refundModal").modal('show');
});
$("#refund-submit").on("click",function(){
	var id = $('#r-id').val();
	var remark = $('#r-remark').val();
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('CustomerService/refund').'?id=' ?>' + id,
          dataType: 'json',
          data:{
                'operation': 'refund',
        	    'remark': remark
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
$(".refuse").on("click",function(){
	var id = $(this).data('id');
	var out_trade_no = $(this).data('out_trade_no');
	$("#s-id").val(id);
	$("#s-out_trade_no").val(out_trade_no);
	$("#refuseModal").modal('show');
});
$("#refuse-submit").on("click",function(){
	var id = $('#s-id').val();
	var content = $('#s-content').val();
	if(!content){
		toastr.warning('请输入原因!');
		return;
	}
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('CustomerService/refund').'?id=' ?>' + id,
          dataType: 'json',
          data:{
        	  'operation': 'refuse',
              'remark': content
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
$(".remark").on("click",function(){
	var id = $(this).data('id');
	var out_trade_no = $(this).data('out_trade_no');
	$("#remark-id").val(id);
	$("#remark-out_trade_no").val(out_trade_no);
	$("#remarkModal").modal('show');
});
$("#remark-submit").on("click",function(){
	var id = $('#remark-id').val();
	var content = $('#remark-content').val();
	if(!content){
		toastr.warning('请输入备注!');
		return;
	}
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('CustomerService/refund').'?id=' ?>' + id,
          dataType: 'json',
          data:{
        	  'operation': 'remark',
              'remark': content
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
</script>