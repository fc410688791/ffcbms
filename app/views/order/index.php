<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
#infoTable .th-title{
	width:180px;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <?php if (!isset($merchant_id)){?>
                <div class="box-body">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Order/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="订单号/用户ID/设备ID" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" style="margin-left: 10px;" id='search'>搜索</button>
                                    </div>
                                </div>
                            </div>
                        </div>               
                    </form>
                    <form id="selectForm" class="" method="GET" action='<?php echo base_url('Order/index') ?>' style="float:right;">
                        <input type="hidden" name="selectTime" id='selectTime' value="<?php echo isset($selectTime)?$selectTime:''; ?>">
                        <!-- 日期选择开始 -->
                        <div class="pull-left form-group">
                          <div class="control-group">
                              <div class="input-prepend input-group">
                                <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                              </div>
                          </div>
                        </div>
                        <!-- 日期选择结束 -->
                        <!-- 支付类型选择 -->
                        <div class="pull-left form-group" >
                          <div class="control-group">
                            <div class="input-prepend input-group">
                              <select class="form-control " name="pay_type">
                                <option value=''>支付类型</option>
                                <?php foreach( $pay_type_option as $ptk => $ptv){ ?>
                                  <option value="<?php echo $ptk ?>" <?php echo isset($pay_type)? ($pay_type == $ptk)?'selected':'':''; ?> ><?php echo $ptv ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <!-- 支付类型结束 -->
                        <!-- 商品名称选择 -->
                        <div class="pull-left form-group" >
                          <div class="control-group">
                            <div class="input-prepend input-group">
                              <select class="form-control " id='product_name_option' name="product_id">
                                <option value=''>所有商品名称</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <!-- 商品名称选择结束 -->
                        <!-- 设备类型选择 -->
                        <div class="pull-left form-group" >
                          <div class="control-group">
                            <div class="input-prepend input-group">
                              <select class="form-control" name="type">
                                <option value=''>采购商品类型</option>
                                <?php foreach( $agent_product_type_option as $type_k => $type_v){ ?>
                                  <option value="<?php echo $type_k ?>" <?php echo isset($type)? ($type == $type_k)?'selected':'':''; ?> ><?php echo $type_v ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <!-- 设备类型选择结束 -->
                        <div class="pull-left form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id=province name="province_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择省份</option>   
                                        <?php foreach($first_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($province_id)? ($province_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="city" name="city_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择城市</option>   
                                        <?php foreach($second_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($city_id)? ($city_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="street" name="street_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择区</option>   
                                        <?php foreach($third_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($street_id)? ($street_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="village" name="village_id" class="form-control select2" style="width:120px;">
                                        <option value="">选择街道</option>   
                                        <?php foreach($fourth_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($village_id)? ($village_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <!-- 默认切换订单转状态导航 -->
                                    <input type="hidden" name="orderSelect" value="<?php echo $this->input->get('orderSelect')?? 'all'; ?>">
                                    <button type="submit" id="query_submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/Order/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->
                <?php }?>

                <div class="box-body table-responsive">
                    <?php if (!isset($merchant_id)){?>
                    <div class='with-border'>
                        <div class="btn-group menuBar">
                            <span class="btn btn-default chartbtn" data-bm='all'>全部</span>
                            <span class="btn btn-default chartbtn" data-bm='prepay'>待支付</span>
                            <span class="btn btn-default chartbtn" data-bm='payed'>已支付</span>
                            <span class="btn btn-default chartbtn" data-bm='complete'>已完成</span>
                            <span class="btn btn-default chartbtn" data-bm='paycancel'>已取消</span>
                        </div>
                    </div>
                    <?php }?>
                    
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                               <th>用户ID</th>
                               <th>订单编号</th>
                               <th>金额</th>
                               <th>支付类型</th>
                               <th>设备信息</th>
                               <th>下单时间</th>
                               <th>订单状态</th>
                               <th>操作</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <?php foreach ($list as $key => $val) { ?>
                            <tr>
                                <td><a href="<?php echo base_url('Member/index').'?key='.$val['uuid'] ?>"><?php echo $val['uuid'] ?></a></td>
                                <td><a href="javascript:;" class="info" data-order_id="<?= $val['id']?>"><?php echo $val['out_trade_no'] ?></a></td>
                                <td>到账：<?php echo $val['cash_fee'] ?>元
                                    </br>实付：<?php echo $val['real_cash_fee'] ?>元
                                    <?php if ($val['at_receive_id']){?>
                                    </br><a href="<?php echo base_url('MemberActivityCard/index').'?receive_id='.$val['at_receive_id'] ?>">优惠：<?php echo $val ['product_price']-$val ['cash_fee']; ?>元
                                    <?php }?>
                                </td>
                                <td><?php echo $val['pay_type']?></td>
                                <td>
                                    <a href="<?php echo base_url('Machine/index').'?key='.$val['machine_id'] ?>"><?php echo $val['machine_id']." ".$val['agent_product_type_name']; ?></a>
                                    </br>
                                    <?php echo $val['merchant_name']?>
                                </td>
                                <td><?php echo date("Y-m-d H:i:s", $val['create_time']) ?></td>
                                <td>
                                    <span class="label <?php echo  $val['sta_color'];?> "><?php echo $val['status'] ?></span>
                                </td>
                                <th><a href="<?php echo base_url('CustomerService/index').'?key='.$val['out_trade_no'] ?>" >去退款</a></th>
                            </tr>
                            <?php } ?>
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

<!-- 编辑infoModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel"></h3>
      </div>

      <div class="modal-body">
        <table id="infoTable" class="table">
            <tbody>
                <tr>
                    <th class="th-title">ID: </th>
                    <th><span id="id"></span></th>
                </tr>
                <tr>
                    <th class="th-title">订单号: </th>
                    <th><span id="out_trade_no"></span></th>
                </tr>
                <tr>
                    <th class="th-title">创建时间: </th>
                    <th><span id="create_time"></span></th>
                </tr>
                <tr>
                    <th class="th-title">订单支付状态: </th>
                    <th><span id="o_status"></span></th>
                </tr>
                <tr>
                    <th class="th-title">订单完成状态: </th>
                    <th><span id="complete_status"></span></th>
                </tr>
                <tr>
                    <th class="th-title">用户实际支付: </th>
                    <th><span id="cash_fee"></span></th>
                </tr>
                <!-- <tr>
                    <th class="th-title">设备类型: </th>
                    <th><span id="type"></span></th>
                </tr> -->
                <tr>
                    <th class="th-title">设备ID: </th>
                    <th><span id="machine_id"></span></th>
                </tr>
                <tr>
                    <th class="th-title">支付类型: </th>
                    <th><span id="pay_type"></span></th>
                </tr>
                <tr>
                    <th class="th-title">支付时间: </th>
                    <th><span id="pay_time"></span></th>
                </tr>
                <tr>
                    <th class="th-title">启动时间: </th>
                    <th><span id="open_time"></span></th>
                </tr>
                <tr>
                    <th class="th-title">充电时间: </th>
                    <th><span id="charge_time"></span></th>
                </tr>
                <tr>
                    <th class="th-title">退款: </th>
                    <th><span id="r_status"></span></th>
                </tr>
            </tbody>
        </table> 
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
  $('#reservation').daterangepicker({
      locale: {
          format: 'YYYY-MM-DD',
          applyLabel: '确认',
          cancelLabel: '取消',
          daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
          monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
          firstDay: 1
      },
  }).on('apply.daterangepicker', function (ev, picker) {
      $('#selectTime').val('1');
  });

  //获取下拉列表
  get_option(); 
  //订单状态
  activitySelectOrder();
});

function get_option() {
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('Index/get_product_list')?>',
        data: {
            'type':''
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
              var html = "";
              var product_id = '<?php echo isset($product_id)?$product_id:''; ?>';
              console.log(product_id);
              for(var i=0;i<data.data.length;i++){ 
                  if(product_id==data.data[i].id){
                	  html += "<option value="+data.data[i].id+" selected >"+data.data[i].name+"("+data.data[i].price+")</option>";
                  }else{
                	  html += "<option value="+data.data[i].id+">"+data.data[i].name+"("+data.data[i].price+")</option>";
                  }
              }
              $('#product_name_option').append(html);
          }else{
            toastr.error(data.msg);
          }        
        },
        error: function(xhr, type){
           toastr.error(detailLabel+"未知错误");
        }
    });
}

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
$('.chartbtn').on('click', function(){
        $("input[name=orderSelect]").val($(this).data('bm'));
        console.log("点击订单分类"+$("input[name=orderSelect]").val());
        activitySelectOrder();
        $('#selectForm').submit();
})

/**
 * [activitySelectOrder 激活状态的状态菜单栏]
 *
 * @author breite
 * @param  {[type]} $elem [description]
 * @return {[type]}       [description]
 */
function activitySelectOrder()
{
    var actElem = $("input[name=orderSelect]").val();
    $('.chartbtn').each(function(){
        var databm = $(this).data('bm');
        if (databm == actElem) {
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        } else {
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-default');
        }
    })
}

$(".info").click(function(){
	$('#infoModal').modal({
        backdrop: 'static', // 空白处不关闭.
        keyboard: false // ESC 键盘不关闭.
    });
	var url = '<?php echo base_url('Order/get_order_info')?>';
	var order_id = $(this).data('order_id');
    $.ajax({
        type: 'GET',
        url: url,
        data: {
        	  "order_id":order_id
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
            if(data.code==200){
                console.log(data.data);
                var order_info = data.data.order_info;
                $('#id').html(order_info.id);
                $('#out_trade_no').html(order_info.out_trade_no);
                $('#cash_fee').html(order_info.cash_fee);
                $('#complete_status').html(order_info.complete_status);
                $('#machine_id').html(order_info.machine_id);
                $('#create_time').html(order_info.create_time);
                $('#o_status').html(order_info.o_status);
                $('#pay_time').html(order_info.pay_time);
                $('#pay_type').html(order_info.pay_type);
                $('#r_status').html(order_info.r_status);
                /* $('#type').html(order_info.type); */
                $('#open_time').html(order_info.open_time);
                $('#charge_time').html(order_info.charge_time);
            }else{
                toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error("未知错误");
        }
    });
});
</script>