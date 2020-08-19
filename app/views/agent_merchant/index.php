<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.sort-field,.sort{
	cursor:pointer;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='turn'>经营权转移</a>
                </div>
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('AgentMerchant/index') ?>'>
                      <div class="pull-left form-group">
                        <div class="control-group">
                          <div class="controls">
                            <div class="input-prepend input-group">
                              <input type="text" name="key" class="form-control pull-left " style="width:230px;" placeholder="投放点名称/代理商姓名/代理商ID" value="<?php echo isset($key)?$key:''; ?>">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-2">
                        <div class="control-group">
                          <div class="controls">
                            <div class="input-prepend input-group">
                              <select class="form-control " id='sort' name="sort">
                                  <option value="-1" <?php echo isset($sort)? ($sort == 0)?'selected':'':''; ?>>所有状态</option>
                                  <option value="1" <?php echo isset($sort)? ($sort == 1)?'selected':'':''; ?>>已投设备</option>
                                  <option value="2" <?php echo isset($sort)? ($sort == 2)?'selected':'':''; ?>>未投设备</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="control-group">
                          <div class="controls">
                            <div class="input-prepend input-group">
                              <span class="add-on input-group-addon">时间</span>
                              <input type="text" name="reservation" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-2">
                        <div class="control-group">
                          <div class="controls">
                            <div class="input-prepend input-group">
                                <button type="submit" id="query_submit" class="btn btn-success">查询</button>&nbsp;<a href="/AgentMerchant/index" class="btn btn-default">重置</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td><input type="checkbox" id="check0"></td>
                                <td style="line-height:32px;">编号</td>
                                <td style="line-height:32px;">投放点名称</td>
                                <td>
                                    <div class="sort-field" style="float:left;height:32px;line-height:32px;<?php if ($o=='m_u'||$o=='m_d'){echo 'color:red;';} ?>">设备数量</div>
                                    <div style="float:left;width:15px;">
                                        <span id="m_u" class="sort" style="width:15px;<?php if ($o=='m_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="m_d" class="sort" style="width:15px;<?php if ($o=='m_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field" style="float:left;height:32px;line-height:32px;<?php if ($o=='o_u'||$o=='o_d'){echo 'color:red;';} ?>">交易量</div>
                                    <div style="float:left;width:15px;">
                                        <span id="o_u" class="sort" style="width:15px;<?php if ($o=='o_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="o_d" class="sort" style="width:15px;<?php if ($o=='o_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field" style="float:left;height:32px;line-height:32px;<?php if ($o=='c_u'||$o=='c_d'){echo 'color:red;';} ?>">完成率</div>
                                    <div style="float:left;width:15px;">
                                        <span id="c_u" class="sort" style="width:15px;<?php if ($o=='c_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="c_d" class="sort" style="width:15px;<?php if ($o=='c_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field" style="float:left;height:32px;line-height:32px;<?php if ($o=='f_u'||$o=='f_d'){echo 'color:red;';} ?>">流水</div>
                                    <div style="float:left;width:15px;">
                                        <span id="f_u" class="sort" style="width:15px;<?php if ($o=='f_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="f_d" class="sort" style="width:15px;<?php if ($o=='f_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td style="line-height:32px;">代理商姓名</td>
                                <td style="line-height:32px;">创建时间</td>
                                <td style="line-height:32px;">创建人</td>
                                <td style="line-height:32px;">状态</td>
                                <td style="line-height:32px;">操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $k=>$info): ?>
                            <tr>
                                <td><input type="checkbox" name="merchant" type="checkbox" value="<?php echo $info['id'];?>" data-name="<?php echo $info['name'];?>"></td>
                                <td><?php echo $info['id'];?></td>
                                <td><?php echo $info['name'];?></td>
                                <td><a target="_blank" href="<?php echo base_url('BindTriadMark/index').'?merchant_id='.$info['id']; ?>"><?php echo $info['merchant_count'];?></a></td>
                                <td><a target="_blank" href="<?php echo base_url('Order/index').'?merchant_id='.$info['id'].'&selectTime=1&reservation='.$reservation ?>"><?php echo $info['pay_count'];?></a></td>
                                <td><?php if ($info['merchant_count']){echo round(($info['pay_count']/$info['merchant_count']/$day),2)*100 . '%';}else{echo '0%';}?></td>
                                <td><?php echo $info['cash_fee_statistics']?'￥'.$info['cash_fee_statistics']:"-";?></td>
                                <td><?php echo $info['card_name'].'</br>'.$info['agent_id'];?></td>
                                <td><?php echo date("Y-m-d H:i:s", $info ['create_time']);?></td>
                                <td><?php echo $info['a_u_name']?$info['a_u_name']:$info['card_name'];?></td>
                                <td style="line-height:32px;"><?php echo $info['status_name'];?></td>
                                <td>
                                    <a class="pointer" href="javascript:;" <?php if ($info['merchant_count']>0){ echo "onclick='showdetails(this);'"; }?> data-id='<?php echo $info['id'];?>'>编辑</a>
                                    <a target="_blank" href="<?php echo base_url('AgentMerchant/monthlyBill').'?merchant_id='.$info['id']; ?>">月账单</a>
                                    <?php if (!$info['merchant_count']&&$info['status']){?>
                                    <a href="javascript:;" class='del' data-id="<?php echo $info['name']; ?>" data-href="<?php echo base_url('AgentMerchant/del')."?id={$info['id']}"; ?>">删除</a>
                                    <?php }?>
                                    <a class="turn_record" href="javascript:;" data-id='<?php echo $info['id'];?>'>迁移记录</a>
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

<!-- 修改 - updateModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">修改</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>投放点ID: </th>
              <th>
                <input id="u_id" value='' disabled="disabled">
              </th>
            </tr>
            <tr class='det_tr'>
              <th>商品类型:</th>
              <th colspan="3">
                <select id="product_type" class="form-control">
                    <option value='1'>密码器</option>
                    <option value='2' selected>物联网</option>
                </select>
              </th>
            </tr>
            <tr>
              <th>当前商品:</th>
              <th id='merchant_product_list' colspan="3"></th>
            </tr> 
            <tr>
              <th>所有商品:</th>
              <th id='product_list' colspan="3"></th>
            </tr>  
          </tbody>
        </table>
          <div class="modal-footer" style="text-align: left;">
            <button id="btn-update" class="btn btn-success">修改</button>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- end -->

<!-- 转移 - turnModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="turnModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">投放点转移</h3>
      </div>

      <div class="modal-body" >
        <table class="table">
          <tbody>
              <tr>
                  <th>已选择投放点: <span id="merchant_count">0</span>个</th>
                  <th colspan="3"><input id="merchant_list" type="text" disabled="disabled"></th>
              </tr>
              <tr>
                  <th>投放点迁移类型：</th>
                  <th colspan="3">
                      <select id="is_redirect_trun_agent" class="form-control" style="width:200px;">
                          <option value='1'>迁移投放点</option>
                          <option value='0'>重新绑定投放点</option>
                      </select>
                  </th>
              </tr>
              <tr>
                  <th>待绑定代理商：</th>
                  <th colspan="3">
                      <select id="agent_id" class="form-control" style="width:200px;">
                          <option value=''>请选择代理商</option>
                          <?php foreach($agent_list as $av){ ?>
                          <option value='<?php echo $av['id'] ?>'><?php echo $av['id'].$av['user_name']; ?></option>
                          <?php } ?>
                      </select>
                  </th>
              </tr>
              <tr id='merchant'>
                  <th>待绑定投放点：</th>
                  <th colspan="3">
                      <select id="merchant_id" class="form-control" style="width:200px;">   
                      </select>
                  </th>
              </tr>
          </tbody>
        </table>
        <div class="modal-footer">
          <span style="float:left;color:red;">*投放点迁移到新的代理商后子账号将移除权限</span>
          <button id="btn-trun" class="btn btn-success" style="float:right;">确认</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end -->

<!-- 查看迁移记录  -TurnRecordModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="TurnRecordModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">迁移记录</h3>
      </div>

      <div class="modal-body" >
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" style="text-align: center;">
                <thead>
                    <tr style="text-align: center;">
                        <td>编号</td>
                        <td>变更前代理商姓名</td>
                        <td>变更后代理商姓名</td>
                        <td>变更时间</td>
                    </tr>
                </thead>
                <tbody id="turn_record-list">
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看迁移记录  -TurnRecordModal -end -->

<!-- datepicker 单个日期选择 -->
<script src='<?php echo $assets_dir ?>/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js'></script>
<script>
var sn = 1;
product_id = [];

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
    });
});

$(".sort-field,.sort").on("click",function(e){
	var id = e.target.id;
	var url = '<?php echo base_url('AgentMerchant/index').'?key='.($key??"").'&reservation='.$reservation ?>'+'&o='+id;
    location.href = url;
});


function showdetails(source) {
	sn = 1;
	product_id = [];
	var id = $(source).data('id');
	$('#u_id').val(id);
	$("#u_product_id").val(0);
	get_merchant_product_list(id);
	get_product_list();
    $("#updateModal").modal('show');
}

$("#product_type").change(function(){
	product_id = [];
	sn = 1;
    get_product_list();
});

$("#btn-update").on("click",function(){
	var id = $('#u_id').val();
	var product_type = $("#product_type option:selected").val();   //获取选中的商品类型
	product_id = product_id;
    var default_product_id = $('input:radio[name=default_product_id]:checked').val();
    if(product_id.length == 0){
    	toastr.warning("请选择商品！"); 
    	return;
    }
    if(!default_product_id){
    	default_product_id = product_id[0];
    }else{
    	if($.inArray( default_product_id, product_id )==-1){
        	toastr.warning("默认商品未选中！"); 
        	return;
        }
    }
    //console.log(id,machine_type,product_id,default_product_id);
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentMerchant/setProduct').'?merchant_id=' ?>' + id,
          dataType: 'json',
          data:{
        	  product_id:product_id,
        	  product_type:product_type,
        	  default_product_id:default_product_id
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

function fun(){
    obj = document.getElementsByName("product");
    check_val = [];
    for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
    return check_val;
}

function get_merchant_product_list(id){
	var id = id;
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('Index/get_merchant_product_list')?>',
        data: {
      	  "merchant_id":id
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
              var html = "";
              for(var i=0;i<data.data.length;i++){
            	  html += data.data[i].name+" ("+data.data[i].price+")</br>";
              }
              $('#merchant_product_list').html(html);
          }else{
            toastr.error(data.msg);
          }        
        },
        error: function(xhr, type){
           toastr.error(detailLabel+"未知错误");
        }
    });
}

function get_product_list (){
	var type = $("#product_type").val();
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('Index/get_product_list')?>',
        data: {
      	  "type":type
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
              var html = "<table>";
              html = "<tr><td>商品</td><td style='width:50px;text-align:center'>价格</td><td style='width:50px;text-align:center'>优惠</td><td style='width:50px;text-align:center'>默认</td><td style='width:50px;text-align:center'>排序</td></tr>";
              for(var i=0;i<data.data.length;i++){
            	  html += "<tr><td><input name='product' type='checkbox' value='"+data.data[i].id+"' onclick='sort(this)'/> "+data.data[i].name+"</td><td style='width:50px;text-align:center'>"+data.data[i].price+"</td><td style='width:50px;text-align:center'>"+data.data[i].incentive_price+"</td><td style='width:50px;text-align:center'><input id='product_default_"+data.data[i].id+"' name='default_product_id' type='radio' value='"+data.data[i].id+"'/></td><td><input id='product_sn_"+data.data[i].id+"' style='width:50px;text-align:center' disabled='disabled'></td></tr>";
              }
              html += "</table>";
              $('#product_list').html(html);
          }else{
            toastr.error(data.msg);
          }        
        },
        error: function(xhr, type){
           toastr.error(detailLabel+"未知错误");
        }
    });
}

function sort(obj){
	var checked = $(obj).prop("checked");
	var val = $(obj).val();
	if(checked==true){
		product_id.push(val);
		$("#product_sn_"+val).val(sn);
		sn = sn+1;
    }else{
    	product_id.forEach(function(item, index, arr) {
            if(item == val) {
                arr.splice(index, 1);
            }
        });
    	$("#product_default_"+val).prop("checked",false);
    	$("#product_sn_"+val).val('');
        sn = 1;
        for(var i=0;i<product_id.length;i++){
        	val = product_id[i];
        	$("#product_sn_"+val).val(sn);
        	sn = sn+1;
        }
    }
	console.log(product_id);
}

$("#check0").on("click",function(){
	var check0 = $("#check0").prop('checked');
	if(check0){
		$("input[name=merchant]").prop("checked",true);
	}else{
		$("input[name=merchant]").prop("checked",false);
	}
});

$("#turn").on("click",function(){
	var merchant_list = [];
	var merchant_name = '';
    $("input[name=merchant]:checked").each(function (index, item) {
        var merchant_id = $(this).val();
        merchant_name += $(this).data('name')+';';
    	merchant_list.push(merchant_id);
    });
    var merchant_count = merchant_list.length;
    if(merchant_count){
    	$("#merchant_count").html(merchant_list.length);
    	$("#merchant_list").val(merchant_name);
    	var is_redirect_trun_agent = $('#is_redirect_trun_agent').val();
    	if(is_redirect_trun_agent == 1){
    		$('#merchant').hide();
        }else{
        	$('#merchant').show();
        }
    	var agent_id = $("#agent_id").val();
    	if(agent_id){
        	get_merchant_list(agent_id);
        }
    	$("#turnModal").modal('show');
    }else{
    	toastr.warning("请选择投放点！"); 
    	return;
    }
});
$("#is_redirect_trun_agent").on("change",function(){
	var is_redirect_trun_agent = $(this).val();
	if(is_redirect_trun_agent == 1){
		$('#merchant').hide();
    }else{
    	$('#merchant').show();
    }
});
$("#agent_id").on("change",function(){
	var agent_id = $(this).val();
	if(agent_id){
		get_merchant_list(agent_id)
	}else{
		$('#merchant_id').html('');
	}
});
function get_merchant_list(agent_id){
	$('#merchant_id').html('');
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('AgentMerchant/get_merchant_list')?>',
        data: {
        	agent_id:agent_id
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
              console.log(data);
              var html = '';
              for(var i=0;i<data.data.length;i++){
            	  html += "<option value='"+data.data[i].id+"'>"+data.data[i].name+"</option>";
              }
              $('#merchant_id').append(html);
          }else{
            toastr.error(data.msg);
          }        
        },
        error: function(xhr, type){
           toastr.error(detailLabel+"未知错误");
        }
    });
}
$("#btn-trun").on("click",function(){
	var merchant_list = [];
    $("input[name=merchant]:checked").each(function (index, item) {
        var merchant_id = $(this).val();
    	merchant_list.push(merchant_id);
    });
    var is_redirect_trun_agent = $('#is_redirect_trun_agent').val();
	var agent_id = $("#agent_id").val();
	var merchant_id = $("#merchant_id").val();
    if(!merchant_list.length){
    	toastr.warning("请选择投放点！"); 
    	return;
    }
    if(!agent_id){
    	toastr.warning("请选择代理商！"); 
    	return;
    }
    if(is_redirect_trun_agent==0&&!merchant_id){
    	toastr.warning("请选择投放点！"); 
    	return;
    }
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentMerchant/turn')?>',
        data: {
        	merchant_list:merchant_list,
        	is_redirect_trun_agent:is_redirect_trun_agent,
        	agent_id:agent_id,
        	merchant_id:merchant_id
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
$(".turn_record").on("click",function(){
	$('#turn_record-list').html('');
	var id = $(this).data('id');
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('AgentMerchant/turn_record'); ?>',
          dataType: 'json',
          data:{
              'id':id
          },
          async:false,//同步请求
          success: function(data){
              var list = data.list;
              console.log(list);
              var html = '';
              for(var i=0;i<list.length;i++){
            	  html += '<tr>';
            	  html += '<td>'+(i+1)+'</td>' +
                	  '<td>'+list[i].current_agent_name+'</td>'+
                	  '<td>'+list[i].after_agent_name+'</td>'+
                	  '<td>'+list[i].create_time+'</td>';
                  html += '</tr>';
              }
              $('#turn_record-list').append(html);
          },
        error: function () {
          toastr.error('请求错误!');
        }
    });
	$("#TurnRecordModal").modal('show');
});
</script>