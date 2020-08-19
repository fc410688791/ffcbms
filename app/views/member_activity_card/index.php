<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">          
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i>添加卡券</a>
                </div>
                
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberActivityCard/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="卡劵名称" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberActivityCard/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id='card_type' name="card_type" class="form-control" style="width:110px;">
                                        <option value=''>卡券类型</option>
                                        <?php foreach($card_type_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($card_type)? ($card_type == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id='trigger_type' name="trigger_type" class="form-control" style="width:140px;">
                                        <option value=''>卡券触发类型</option>
                                        <?php foreach($trigger_type_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($trigger_type)? ($trigger_type == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id='is_show' name="is_show" class="form-control" style="width:110px;">
                                        <?php foreach($is_show_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($is_show)? ($is_show == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id='status' name="status" class="form-control" style="width:110px;">
                                        <?php foreach($status_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($status)? ($status == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/MemberActivityCard/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>卡劵号</td>
                                <td>卡劵名称</td>
                                <td>卡劵类型</td>
                                <td>触发类型</td>
                                <td>适用范围</td>
                                <td>发行量</td>
                                <td>剩余量</td>
                                <td>卡劵状态</td>
                                <td>展示状态</td>
                                <td>创建日期</td>
                                <td>生效时间</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['card_name']; ?></td>
                                <td><?php echo $info['card_type']; ?></td>
                                <td><?php echo $info['trigger_type']; ?></td>
                                <td><a class="scope pointer" href="javascript:;" data-id='<?php echo $info['id'];?>'>适用范围</a></td>
                                <td><?php echo $info['card_total']; ?></td>
                                <td><?php echo $info['card_remain']; ?></td>
                                <td><?php echo $info['status']; ?></td>
                                <td><?php echo $info['is_show']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['time_frame']; ?></td>
                                <td>
                                    <a class="info pointer" href="javascript:;" data-id='<?php echo $info['id'];?>'>编辑</a>
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

<!-- 添加 addModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">添加卡券</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr id="a_0">
              <th>卡券ID:</th>
              <th colspan="3">
                <input id='a_id' type="text" disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>卡券名称:</th>
              <th colspan="3">
                <input id='a_card_name' type="text" maxlength="32">
              </th>
            </tr>
            <tr>
              <th>卡券类型:</th>
              <th colspan="3">
                <select id="a_card_type" style="width:193px;height:26px;">
                    <?php foreach($card_type_option as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr>
              <th>使用门槛:</th>
              <th colspan="3">
                <input type="number" id="a_limit_count" style="width:100px;"/><sapn>元</sapn>
              </th>
            </tr>
            <tr id="a_a">
              <th id="a_a1"></th>
              <th colspan="3">
                <input type="number" id="a_quote" style="width:100px;"/><sapn id="a_a2"></sapn>
              </th>
            </tr>
            <tr>
              <th>适用范围:</th>
              <th colspan="3">
                  <input id="a_scope" type="submit" value="查看适用商品"/>
              </th>
            </tr>
            <tr>
              <th>触发类型:</th>
              <th colspan="3">
                <select id="a_trigger_type" style="width:193px;height:26px;">
                    <?php foreach($trigger_type_option as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr id="a_b">
              <th>生效时间:</th>
              <th colspan="3">
                <input type="text" id="a_time_frame"/>
              </th>
            </tr>
            <tr id="a_c">
              <th>卡劵发行量:</th>
              <th colspan="3">
                <input type="number" id="a_card_total"/>
              </th>
            </tr>
            <tr>
              <th>卡劵描述:</th>
              <th colspan="3">
                <textarea id="a_card_describe" rows="3" style="width:193px;"></textarea>
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th colspan="3">
                <input id="r1" type="radio" name="is_show" value="1" checked="checked"><sapn style="margin-right:20px;">展示</sapn>
                <input id="r2" type="radio" name="is_show" value="0"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>
          </tbody>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='add_submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>
<!-- end -->

<!-- 商品productModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">适用范围</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr>
              <th><input type="checkbox" id="check0">全部</th>
            </tr>
            <?php foreach($type_option as $t_k => $t_v){ ?>
            <tr>
              <th></th>
              <th><input type="checkbox" id="type_<?php echo $t_k;?>" class="type" name="type" data-id="<?php echo $t_k;?>"><?php echo $t_v;?></th>
              <th>
                  <?php foreach($product_option as $p_k => $p_v){ if ($p_v['type']==$t_k){?>
                      <input type="checkbox" id="product_<?php echo $p_v['id'];?>" class="product type_<?php echo $t_k;?>" name="product" value="<?php echo $p_v['id'];?>" data-type="<?php echo $t_k;?>"><?php echo $p_v['name'].'('.$p_v['price'].','.$p_v['incentive_price'].')';?>
                      </br>
                  <?php }} ?>
              </th>
            </tr>
            <?php } ?>
             
          </tbody>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='product_submit' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>
<!-- end -->

<!-- 卡劵适用范围scopeModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="scopeModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">卡劵适用范围</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody id="scope_tbody">  
          </tbody>
        </table> 
      </div>
    </div>
  </div>
</div>
<!-- end -->

<script>
$(function(){
	$('#a_time_frame').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        }
    });
	$("#check0").prop('checked',true);
	$("input[name=type]").prop("checked",true);
	$("input[name=product]").prop("checked",true);
});
$('#add').click(function(){
	$("#detailLabel").html('添加卡券');
	$("#a_0").hide();
	$("#a_id").val('');
    $("#a_card_name").val('');
    $("#a_card_type").val(1);
    change_card_type('a_card_type');
    $("#a_limit_count").val('');
    $("#a_quote").val('');
    $("#a_trigger_type").val(1);
	$("input").prop('checked',true);
	$("#a_time_frame").val('');
    $("#a_card_total").val('');
    $("#a_card_describe").html('');
	$('#r1').prop('checked', true);
	$('#r2').prop('checked', false);
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});
$("#a_card_type").change(function () {
	change_card_type('a_card_type');
});
$("#a_trigger_type").change(function () {
	//change_trigger_type('a_trigger_type');
});

//使用范围
$("#a_scope").click(function () {
	$('#productModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});
$("#check0").on("click",function(){
	var check = $("#check0").prop('checked');
	if(check){
		$("input[name=type]").prop("checked",true);
		$("input[name=product]").prop("checked",true);
	}else{
		$("input[name=type]").prop("checked",false);
		$("input[name=product]").prop("checked",false);
	}
});
$(".type").on("click",function(){
	var check = $(this).prop('checked');
	var id = $(this).data('id');
	if(check){
		$(".type_"+id).prop("checked",true);
	}else{
		$("#check0").prop('checked',false);
		$(".type_"+id).prop("checked",false);
	}
});
$(".product").on("click",function(){
	var check = $(this).prop('checked');
	var type = $(this).data('type');
	if(!check){
		$("#type_"+type).prop("checked",false);
		$("#check0").prop('checked',false);
	}
});
$('#product_submit').click(function(){
	$('#productModal').modal('hide');
});

$('#add_submit').click(function(){   
	var id = $("#a_id").val();
	if(id){//编辑
		var url = '<?php echo base_url('MemberActivityCard/update')?>'+'?id='+id;
	}else{//添加
		var url = '<?php echo base_url('MemberActivityCard/add')?>';
	}
    var product_list = [];
    $("input[name=product]:checked").each(function (index, item) {
        var product_id = $(this).val();
        product_list.push(product_id);
    });
    var card_name = $('#a_card_name').val();
    var card_type = $('#a_card_type').val();
    var limit_count = $('#a_limit_count').val();
    var quote = $('#a_quote').val();
    var trigger_type = $('#a_trigger_type').val();
    var time_frame = $('#a_time_frame').val();
    var card_total = $('#a_card_total').val();
    var card_describe = $('#a_card_describe').val();
    var is_show  = $('input:radio[name=is_show]:checked').val();
    quote = Number(quote);
    limit_count = Number(limit_count);
    if(!card_name||!quote){ 
    	toastr.warning("请将数据填充完整！"); 
    	return;
    }
    if(card_type==1){
    	if(quote>=10||quote<=0){
        	toastr.warning("折扣超出范围！"); 
        	return;
        }
    }else if(card_type==2){
    	if(quote>=limit_count){
        	toastr.warning("满减额必须大于减金额！"); 
        	return;
        }
    }
    if(product_list.length<1){
    	toastr.warning("请选择商品！"); 
    	return;
    }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
    	  card_name:card_name,
    	  card_type:card_type,
    	  limit_count:limit_count,
    	  quote:quote,
    	  product_list:product_list,
    	  trigger_type:trigger_type,
    	  time_frame:time_frame,
    	  card_total:card_total,
    	  card_describe:card_describe,
    	  is_show:is_show,
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

$('.scope').click(function(){
	var id = $(this).data('id');
	var url = '<?php echo base_url('MemberActivityCard/info')?>';
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            id:id
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
            if(data.code==200){
              var html = '';
              for(var i=0;i<data.data.scope.length;i++){
            	  html += "<tr><th>"+(i+1)+". "+data.data.scope[i].type_name+"【";
                  for(var j=0;j<data.data.scope[i].list.length;j++){
                      if(j>0){
                    	  html += '、'+data.data.scope[i].list[j].name+"("+data.data.scope[i].list[j].price+","+data.data.scope[i].list[j].incentive_price+")";
                      }else{
                    	  html += data.data.scope[i].list[j].name+"("+data.data.scope[i].list[j].price+","+data.data.scope[i].list[j].incentive_price+")";
                      }
                  }
            	  html += "】</th></tr>";
              }
              $('#scope_tbody').html(html);
              $('#scopeModal').modal({
                  backdrop: 'static', // 空白处不关闭.
                  keyboard: false // ESC 键盘不关闭.
              });
            }else{
              toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
        }
    });
});

$('.info').click(function(){
	var id = $(this).data('id');
	var url = '<?php echo base_url('MemberActivityCard/info')?>';
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            id:id
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
            if(data.code==200){
              console.log(data.data);
              $("#detailLabel").html('编辑卡券');
              $("#a_0").show();
              $("#a_id").val(data.data.id);
              $("#a_card_name").val(data.data.card_name);
              $("#a_card_type").val(data.data.card_type);
              $("#a_limit_count").val(data.data.limit_count);
              $("#a_quote").val(data.data.quote);
        	  $("input").prop('checked',false);
              for(var i=0;i<data.data.scope.length;i++){
            	  for(var j=0;j<data.data.scope[i].list.length;j++){
                      //console.log(data.data.scope[i].list[j]);
                      $("#product_"+data.data.scope[i].list[j].manage_id).prop("checked",true);
            	  }
              }
              $("#a_trigger_type").val(data.data.trigger_type);
              $("#a_time_frame").val(data.data.time_frame);
              $("#a_card_total").val(data.data.card_total);
              $("#a_card_describe").html(data.data.card_describe);
              change_card_type('a_card_type');
              //change_trigger_type('a_trigger_type');
              if(data.data.is_show==1){
            	    $('#r1').prop('checked', true);
            		$('#r2').prop('checked', false);
              }else{
            		$('#r1').prop('checked', false);
            		$('#r2').prop('checked', true);
              }
              $('#addModal').modal({
                  backdrop: 'static', // 空白处不关闭.
                  keyboard: false // ESC 键盘不关闭.
              });
            }else{
              toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
        }
    });
});

function change_card_type(id){
	var card_type = $('#'+id).val();
	if(card_type==1){
		$('#a_a1').html("打折:");
		$('#a_a2').html(" 折");
	}else{
		$('#a_a1').html("抵扣:");
		$('#a_a2').html(" 元");
	}
}

function change_trigger_type(id){
	var trigger_type = $('#'+id).val();
	if(trigger_type==1){
		$('#a_b,#a_c').hide();
	}else{
		$('#a_b,#a_c').show();
	}
}
</script>