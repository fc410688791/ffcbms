<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='turn'>经营权转移</a>
                </div>
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Machine/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 200px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="设备ID/投放点名称" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Machine/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id='machine_type' name="machine_type" class="form-control select2" style="width:120px;">
                                        <option value=''>设备类型</option>
                                        <?php foreach($machine_type_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($machine_type)? ($machine_type == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id='agent_product_type' name="agent_product_type" class="form-control select2" style="width:120px;">
                                        <option value=''>采购类型</option>
                                        <?php foreach($agent_product_type_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($agent_product_type)? ($agent_product_type == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id='status' name="status" class="form-control select2" style="width:120px;">
                                        <option value=''>选择状态</option>
                                        <?php foreach($status_list as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($status)? ($status == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id=first name="first_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($first_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($first_id)? ($first_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="second" name="second_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($second_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($second_id)? ($second_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="third" name="third_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($third_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($third_id)? ($third_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="fourth" name="fourth_id" class="form-control select2" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($fourth_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($fourth_id)? ($fourth_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/Machine/index" class="btn btn-danger">重置</a>
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
                                <td>设备ID</td>
                                <td>设备类型</td>
                                <td>采购商品类型</td>
                                <td>设备状态</td>
                                <td>模块数</td>
                                <td>亚克力板数</td>
                                <td>二维码数</td>
                                <td>文案</td>
                                <td>投放点名称</td>
                                <td>今日交易量</td>
                                <td>位置</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><input type="checkbox" name="machine" type="checkbox" value="<?php echo $info['id'];?>" data-machine_id="<?php echo $info['machine_id'];?>"></td>
                                <td><?= $info['machine_id']?></td>
                                <td><?= $info['type_name']?></td>
                                <td><?= $info['product_type_name']?></td>
                                <td>
                                    <!-- 设备状态 -->
                                    <?php if($info['status'] == 1){ ?>
                                    <span class="label label-success">正常</span>
                                    <?php } ?>
                                    <?php if($info['status'] == 2){ ?>
                                    <span class="label label-info">待绑定</span>
                                    <?php } ?>
                                    <?php if($info['status'] == 3){ ?>
                                    <span class="label label-info">待激活</span>
                                    <?php } ?>
                                    <?php if($info['status'] == 4){ ?>
                                    <span class="label label-info">故障</span>
                                    <?php } ?>
                                </td>
                                <td><?= $info['module_num']?></td>
                                <td><?= $info['module_plate_num']?></td>
                                <td><?= $info['module_plate_code_num']?></td>
                                <td><?= $info['button_text']?></td>
                                <td><?= $info['a_m_name']?></td>
                                <td><?= $info['pay_count']?></td>
                                <td><?= $info['p_name'].$info['position']?></td>
                                <td>
                                <a href="javascript:;" data-id="<?php echo $info['machine_id']; ?>" data-type="<?php echo $info['type']; ?>" class="info">编辑</a>
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

<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="resetModal-label">设备详情</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>设备 ID: </th>
              <th>
                <input id="machine_id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>设备状态:</th>
              <th>
                <select id='p_s'>
                    <?php foreach($status_list as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr>
              <th>设备位置:</th>
              <th>
                <select id='p_1' name="first_id" class="position">
                    <option value=''>选择地址</option>
                    <?php foreach($first_list as $value){ ?>
                    <option value='<?php echo $value['id'] ?>'><?php echo $value['name'] ?></option>
                    <?php } ?>
                </select>
                <select id='p_2' name="second_id" class="position">
                </select>
                <select id='p_3' name="third_id">
                </select>
                <select id='p_4' name="fourth_id">
                </select>
                </br>
                <input id="p_5" type='text' value=''>
                <span style="color:red;">*管理后台目前无法修改设备位置</span>
              </th>
            </tr>
            <tr class="tr_type1">
              <th>mac:</th>
              <th>
                <input id="p_m" type='text' value=''>
              </th>
            </tr>
            <tr class="tr_type1">
              <th>密码本:</th>
              <th>
                <select id='p_b'>
                    <?php foreach($pb_list as $info){ ?>
                    <option value='<?php echo $info['id'] ?>'><?php echo $info['name'] ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr class="tr_type1">
              <th>文案:</th>
              <th>
                <select id='p_c'>
                    <?php foreach($pc_list as $info){ ?>
                    <option value='<?php echo $info['id'] ?>'><?php echo $info['button_text'] ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr class="tr_type2">
              <th>亚克力板数:</th>
              <th>
                  <input id='module_plate_num' type='number' value=''>
              </th>
            </tr>
            <tr class="tr_type2">
              <th>二维码数:</th>
              <th>
                  <input id='module_plate_code_num' type='number' value=''>
              </th>
            </tr>
            <tr>
              <th>商品:</th>
              <th id='product_list'>
              </th>
            </tr>
          </tbody>
        </table>
        <div class="modal-footer" style="text-align: left;">
            <button id="btn-update" class="btn btn-success">修改</button>
            <button id="close" class="btn btn-default">取消</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

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
                  <th>已选择设备: <span id="merchant_count">0</span>个</th>
                  <th colspan="3"><input id="merchant_list" type="text" disabled="disabled"></th>
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
                          <option value=''>请选择投放点</option>
                      </select>
                  </th>
              </tr>
          </tbody>
        </table>
          <div class="modal-footer" style="text-align: left;">
            <button id="btn-trun" class="btn btn-success">确认</button>
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
                        <td>变更前投放点名称</td>
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

<script>
var option_defalut = "<option value=''>选择地址</option>";
var sn = 1;
product_id = [];

$(".position").on('change',function(){
	var name = $(this).attr('id');
	if(name=='first'){
		$("#second").html(option_defalut);
		$("#third").html(option_defalut);
		$("#fourth").html(option_defalut);
    }else if(name=='second'){
    	$("#third").html(option_defalut);
    	$("#fourth").html(option_defalut);
    }else if(name=='third'){
    	$("#fourth").html(option_defalut);
    }else if(name=='p_1'){
    	$("#p_2").html(option_defalut);
    	$("#p_3").html(option_defalut);
    	$("#p_4").html(option_defalut);
    }else if(name=='p_2'){
    	$("#p_3").html(option_defalut);
    	$("#p_4").html(option_defalut);
    }else if(name=='p_3'){
    	$("#p_4").html(option_defalut);
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
	        if(data.code==200){
                var list = data.msg;
                if(name=='first'){
                    var id = 'second';
                }else if(name=='second'){
                	var id = 'third';
                }else if(name=='third'){
                	var id = 'fourth';
                }else if(name=='p_1'){
                	var id = 'p_2';
                }else if(name=='p_2'){
                	var id = 'p_3';
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

//查看设备详情
$(".info").on("click",function(){
  sn = 1;
  product_id = [];
  var machine_id = $(this).attr("data-id");
  var type = $(this).attr("data-type");
  if(type>1){
	  type = 2;
  }
  get_product_list(type);
  getDetail(machine_id);
});

function get_product_list (type){
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

function getDetail(machine_id){
	$.ajax({
	      type: 'GET',
	      url: '<?php echo base_url('Machine/get_info') ?>',
	      data:{'machine_id': machine_id},
	      dataType: 'json',
	      async:false,//同步请求
	      success: function(data){
	    	  if(data.code==200){
	    		  $("#p_s").val(data.data.status);	 
	    		  if(data.data.position_id>0){
	    			  $("#p_1").val(data.data.province_id);
	    			  for(var i=0;i<data.data.city_list.length;i++)
		              {
		                  $("#p_2").append("<option value='"+data.data.city_list[i].id+"'"+">"+data.data.city_list[i].name+"</option>");                         
		              }
		    		  $("#p_2").val(data.data.city_id);
		    		  for(var i=0;i<data.data.street_list.length;i++)
		              {
		                  $("#p_3").append("<option value='"+data.data.street_list[i].id+"'"+">"+data.data.street_list[i].name+"</option>");                         
		              }
		    		  $("#p_3").val(data.data.street_id);
		    		  for(var i=0;i<data.data.village_list.length;i++)
		              {
		                  $("#p_4").append("<option value='"+data.data.village_list[i].id+"'"+">"+data.data.village_list[i].name+"</option>");                         
		              }
		    		  $("#p_5").val(data.data.position);
		          }else{
		        	  $("#p_1").val("");
		        	  $("#p_2").html(option_defalut);
		        	  $("#p_3").html(option_defalut);
		        	  $("#p_4").html(option_defalut);
		        	  $("#p_5").val("");
				  }
				  if(data.data.type==1){
					  $(".tr_type1").show();
					  $("#p_m").val(data.data.mac);
		    		  $("#p_b").val(data.data.book_id); 
		    		  $("#p_c").val(data.data.copywriting_id);
		    		  $(".tr_type2").hide();
		    		  $("#module_plate_num").val(0); 
		    		  $("#module_plate_code_num").val(0); 
			      }else{
			    	  $(".tr_type1").hide();
		    		  $("#p_m").val('');
		    		  $("#p_b").val(0); 
		    		  $("#p_c").val(0);
		    		  $(".tr_type2").show();
		    		  $("#module_plate_num").val(data.data.module_plate_num); 
		    		  $("#module_plate_code_num").val(data.data.module_plate_code_num); 
				  }
	    		  for(var i=0;i<data.data.product_id.length;i++){
	    			  $("input[name='product'][value='"+data.data.product_id[i]+"']").attr("checked", true);
	    			  $("#product_sn_"+data.data.product_id[i]).val(i+1);
	    			  sn = i+1;
	    			  product_id.push(data.data.product_id[i]);
		    	  }
	    		  $("#product_default_"+data.data.default_product_id).prop("checked",true);
	    		  sn = i+1;
		      }
		  }
	});
	$('#machine_id').val(machine_id);
	$('#detailModal').modal('show');
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

$("#btn-update").on("click",function(){
    var machine_id = $('#machine_id').val();
    var p_s = $('#p_s').val();
    var p_1 = $('#p_1').val();
    var p_2 = $('#p_2').val();
    var p_3 = $('#p_3').val();
    var p_4 = $('#p_4').val();
    var p_5 = $('#p_5').val();
    var p_m = $('#p_m').val();
    var p_b = $('#p_b').val();
    var p_c = $('#p_c').val();
    var module_plate_num = $("#module_plate_num").val(); 
	var module_plate_code_num = $("#module_plate_code_num").val(); 
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
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url('Machine/update').'?machine_id=' ?>' + machine_id,
      dataType: 'json',
      data:{
          'status': p_s,
          'province_id': p_1,
          'city_id': p_2,
          'street_id': p_3,
          'position': p_5,
          'mac': p_m,
          'book_id': p_b,
          'copywriting_id': p_c,
          'product_id': product_id,
          'default_product_id': default_product_id,
          'module_plate_num':module_plate_num,
          'module_plate_code_num':module_plate_code_num,
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

function fun(){
    obj = document.getElementsByName("product");
    check_val = [];
    for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
    return check_val;
}

$("#check0").on("click",function(){
	var check0 = $("#check0").prop('checked');
	if(check0){
		$("input[name=machine]").prop("checked",true);
	}else{
		$("input[name=machine]").prop("checked",false);
	}
});

$("#turn").on("click",function(){
	var machine_list = [];
	var machine = '';
    $("input[name=machine]:checked").each(function (index, item) {
        machine += $(this).data('machine_id')+';';
        machine_list.push($(this).val());
    });
    var machine_count = machine_list.length;
    if(machine_count){
    	$("#merchant_count").html(machine_list.length);
    	$("#merchant_list").val(machine);
    	var agent_id = $("#agent_id").val();
    	if(agent_id){
        	get_merchant_list(agent_id);
        }
    	$("#turnModal").modal('show');
    }else{
    	toastr.warning("请选择设备！"); 
    	return;
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
	var machine_list = [];
	var machine = '';
    $("input[name=machine]:checked").each(function (index, item) {
        machine += $(this).data('machine_id')+';';
        machine_list.push($(this).val());
    });
	var agent_id = $("#agent_id").val();
	var merchant_id = $("#merchant_id").val();
    if(!machine_list.length){
    	toastr.warning("请选择设备！"); 
    	return;
    }
    if(!agent_id){
    	toastr.warning("请选择代理商！"); 
    	return;
    }
    if(!merchant_id){
    	toastr.warning("请选择投放点！"); 
    	return;
    }
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('Machine/turn')?>',
        data: {
        	machine_list:machine_list,
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
        url: '<?php echo base_url('Machine/turn_record'); ?>',
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
            	      '<td>'+list[i].current_merchant_name+'</td>'+
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