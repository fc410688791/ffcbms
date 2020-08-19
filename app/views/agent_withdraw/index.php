<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.num{
	width:100px;
}
.file_image{
	widtht:50px;
	height:50px;
	cursor:pointer;
}
.info{
	widht:100%;
	border:1px solid #ccc;
	padding-bottom:20px;
	margin-bottom:20px;"
}
.file_image{
	widtht:50px;
	height:50px;
	cursor:pointer;
}
.pointer{
	cursor:pointer;
}
.bold{
	font-weight:bold;
}
.p-line{
	width:100%;
	height:30px;
	line-height:30px;
}
.text-left{
	text-align:left;
	float:left;
}
.field{
	width:80px;
}
.param{
	width:200px;
	overflow:hidden;
    white-space:nowrap;	
}
.first-span{
	margin-left:20px;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">          
            <div class="box">
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('AgentWithdraw/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="昵称/姓名/电话/代理商ID" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('AgentWithdraw/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                    <select id='status' name="status" class="form-control" style="width:120px;">
                                        <option value='99'>结算状态</option>
                                        <option value='0' <?php echo isset($status)? ($status == 0)? 'selected':'':''; ?>>待提现</option>
                                        <option value='1' <?php echo isset($status)? ($status == 1)? 'selected':'':''; ?>>结算中</option>
                                        <option value='2' <?php echo isset($status)? ($status == 2)? 'selected':'':''; ?>>已结账</option>
                                        <option value='3' <?php echo isset($status)? ($status == 3)? 'selected':'':''; ?>>提现失败</option>
                                        <option value='4' <?php echo isset($status)? ($status == 4)? 'selected':'':''; ?>>驳回申请</option>
                                        <option value='5' <?php echo isset($status)? ($status == 4)? 'selected':'':''; ?>>审核通过</option>
                                    </select>
                                    <select id='invoice_status' name="invoice_status" class="form-control" style="width:120px;">
                                        <option value='99'>发票状态</option>
                                        <option value='0' <?php echo isset($invoice_status)? ($invoice_status == 0)? 'selected':'':''; ?>>无发票</option>
                                        <option value='1' <?php echo isset($invoice_status)? ($invoice_status == 1)? 'selected':'':''; ?>>待确认</option>
                                        <option value='2' <?php echo isset($invoice_status)? ($invoice_status == 2)? 'selected':'':''; ?>>确认正确</option>
                                        <option value='3' <?php echo isset($invoice_status)? ($invoice_status == 3)? 'selected':'':''; ?>>确认错误</option>
                                    </select>
                                    <select id='proxy_pattern' name="proxy_pattern" class="form-control select2" style="width:120px;">
                                        <option value=''>全部角色</option>
                                        <option value='1' <?php echo isset($proxy_pattern)? ($proxy_pattern == 1)? 'selected':'':''; ?>>普通代理</option>
                                        <option value='2' <?php echo isset($proxy_pattern)? ($proxy_pattern == 2)? 'selected':'':''; ?>>内部自营</option>
                                        <option value='3' <?php echo isset($proxy_pattern)? ($proxy_pattern == 3)? 'selected':'':''; ?>>0元代理</option>
                                        <option value='4' <?php echo isset($proxy_pattern)? ($proxy_pattern == 4)? 'selected':'':''; ?>>商户</option>
                                    </select>
                                    <select id='commission_type' name="commission_type" class="form-control" style="width:120px;">
                                        <option value=''>结算方式</option>
                                        <option value='1' <?php echo isset($commission_type)? ($commission_type == 1)? 'selected':'':''; ?>>即时</option>
                                        <option value='2' <?php echo isset($commission_type)? ($commission_type == 2)? 'selected':'':''; ?>>月结</option>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/AgentWithdraw/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>ID</td>
                                <td>代理商ID</td>
                                <td>姓名</td>
                                <td>角色</td>
                                <td>结算方式</td>
                                <td>提现额度(元)</td>
                                <td>提现时间(号)</td>
                                <td>可提现(元)</td>
                                <td>申请提现金额(元)</td>
                                <td>手续费(元)</td>
                                <td>税金(元)</td>
                                <td>结算金额(元)</td>
                                <td>发票凭证</td>
                                <td>申请时间</td>
                                <td>结算时间</td>
                                <td>发票状态</td>
                                <td>结算状态</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><a class="pointer detail" data-id="<?php echo $info['id'] ?>" title="点击查看详情"><?php echo $info['id']?></a></td>
                                <td><a class="pointer" href="<?php echo base_url('Agent/index')."?key={$info['agent_id']}"; ?>" title="点击跳转代理商"><?php echo $info['agent_id']?></a></td>
                                <td><?php if ($info['c_commission_type']==2){?><a href="<?php echo base_url('AgentUser/index')."?key={$info['agent_user_id']}"; ?>"><?php echo $info['card_name']?></a><?php }else {?><?php echo $info['card_name']?><?php }?></td>
                                <td><?php echo $info['proxy_pattern']?></td>
                                <td><?php echo $info['commission_type']?></td>
                                <td><?php echo $info['commission_withdrawal_amount']?></td>
                                <td><?php echo $info['commission_withdrawal_time']?></td>
                                <td><?php echo $info['pre_withdraw_amount']?></td>
                                <td><?php echo $info['withdraw_amount']?></td>
                                <td><?php echo round($info['withdraw_card_amount']+$info['withdraw_rate_amount'],2)?></td>
                                <td><?php echo $info['deduction_amount']?></td>
                                <td><?php echo $info['real_withdraw_amount']?></td>
                                <td>
                                    <?php if (isset($info['invoice_img_url'])){?>
                                    <img class="file_image" alt="" src="<?php echo $info['invoice_img_url']; ?>">
                                    <?php }else { echo '无发票'; }?>
                                </td>
                                <td><?php echo $info['create_time']?></td>
                                <td><?php echo $info['pay_time']?></td>
                                <td style="color:<?php echo $info['invoice_status_name_color'];?>"><?php echo $info['invoice_status_name']?></td>
                                <td style="color:<?php echo $info['status_name_color'];?>"><?php echo $info['status_name']?></td>
                                <td>
                                    <a target="_blank" href="<?php echo base_url('AgentWithdraw/monthBill')."?agent_id={$info['agent_id']}"; ?>">月账单</a>
                                    <?php if ($info['status']==0){?>
                                    <a class="examine-withdraw" data-id="<?php echo $info['id'] ?>" data-card_name="<?php echo $info['card_name'] ?>">账单过审</a>
                                    <a class="refuse-withdraw" data-id="<?php echo $info['id'] ?>" data-card_name="<?php echo $info['card_name'] ?>">驳回申请</a>
                                    <?php }elseif ($info['status']==5){?>
                                    <a class="confirm-withdraw" data-id="<?php echo $info['id'] ?>" data-card_name="<?php echo $info['card_name'] ?>">确认提现</a>
                                    <a class="refuse-withdraw" data-id="<?php echo $info['id'] ?>" data-card_name="<?php echo $info['card_name'] ?>">驳回申请</a>
                                    <?php }?>
                                    <?php if ($info['is_have_invoice']==1){?>
                                    <a class="confirm-invoice" data-id="<?php echo $info['id']; ?>" data-invoice_status="2">发票有效</a>
                                    <a class="refuse-invoice" data-id="<?php echo $info['id']; ?>" data-invoice_status="3">发票无效</a>
                                    <?php }?>
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
  <div class="modal-dialog modal-lg" style="width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">详情</h3>
      </div>

      <div class="modal-body" >
        <div class="info">
            <div style="width:100%;height:30px;background:#ccc;line-height:30px;"><span style="float:left;margin-left:10px;">信息</span></div>
            <div class="p-line"><span class="first-span text-left field">ID</span><span id="id" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">手机号</span><span id="mobile" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">驳回原因</span><span id="reason_content" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">公司名称</span><span id="invoice_company_name" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">发票号</span><span id="invoice_num" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">发票金额</span><span id="invoice_amount" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">提现银行</span><span id="card_name" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">提现卡号</span><span id="card_no" class="text-left param bold"></span></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<div class="modal fade bs-example-modal-lg text-center" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
      </div>

      <div class="modal-body">
          <h4><span></span>确认给【</span><span id="w-card_name"></span><span>】结算吗？</span></h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" id='cancel' style="float:left;">取消</button>
        <button type="button" class="btn btn-primary" id='confirm' style="float:right;">确定</button>
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
        <h4 class="modal-title" id="detailLabel">拒绝结算</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>ID：</th>
              <th colspan="3">
                  <input id="s-id" name="id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>请填写原因：</th>
              <th colspan="3">
                  <textarea id="s-content" style="width:100%;padding:0px;min-height:100px;" name="content" rows="3" required></textarea>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='refuseWithdraw' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<!--图片预览-->
<div class="modal fade bs-example-modal-lg text-center" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg">
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

<script>
$(function(){
	//图片预览
	$(document).on("click",".file_image",function(){
	    var $image_view = $("#image_view");
	    var html = "";
	    var img_url = $(this).attr("src");
	      html+='<div class="slideshow_item">'
	             +'<div class="image"><a href="#"><img style="width:600px;height:auto;" src="'+img_url+'" alt="photo 1"/></a></div>'
	             +'<div class="thumb"><img src="'+img_url+'" alt="photo 1" width="140" height="63" /></div>'
	             +'<div class="data">'
	                   +'<h4><a href="#"></a></h4>'
	               +'</div>'
	           +'</div>';
	    $image_view.html(html);
	    $(".slideshow_next").addClass("hidden");
	    $(".slideshow_prev").addClass("hidden");           
	    $("#imgModal").modal("show");
	});
	
    $('#reservation').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        }
    }).on('apply.daterangepicker', function (ev, picker) {
        $('#selectTime').val('1');
        $('#query_submit').click();
    });
});

//查看设备详情
$(".detail").on("click",function(){
  var id = $(this).attr("data-id");
  getDetail(id);
});
function getDetail(id){
	$.ajax({
	      type: 'GET',
	      url: '<?php echo base_url('AgentWithdraw/info') ?>',
	      data:{'id': id},
	      dataType: 'json',
	      async:false,//同步请求
	      success: function(data){
	    	  if(data.code==200){
	    		  $("#id").html(data.data.id);
	    		  $("#mobile").html(data.data.mobile);
	    		  $("#reason_content").html(data.data.reason_content);
	    		  $("#invoice_company_name").html(data.data.invoice_company_name);
	    		  $("#invoice_amount").html(data.data.invoice_amount);
	    		  $("#invoice_num").html(data.data.invoice_num);
	    		  $("#card_name").html(data.data.card_name);
	    		  $("#card_no").html(data.data.card_no);
	    		  //console.log(data.data);
		      }
		  }
	});
	$('#detailModal').modal('show');
}

var submit = true;
$(".confirm-withdraw").on("click",function(){
    var id = $(this).data("id");
    var card_name = $(this).data("card_name");
    $('#w-card_name').html(card_name);
    $("#w-card_name").data("id",id);
    $('#confirmModal').modal('show');
});
$("#cancel").on("click",function(){
    $('#confirmModal').modal('hide');
});
$("#confirm").on("click",function(){
    var url = '<?php echo base_url('AgentWithdraw/withdraw')?>';
    var id = $("#w-card_name").data("id");
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            'id':id
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

$(".examine-withdraw").on("click",function(){
	var id = $(this).data('id');
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('AgentWithdraw/examine') ?>',
          dataType: 'json',
          data:{
              'id': id
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

$(".refuse-withdraw").on("click",function(){
	var id = $(this).data('id');
	$("#s-id").val(id);
	$("#refuseModal").modal('show');
});
$("#refuseWithdraw").on("click",function(){
	var id = $('#s-id').val();
	var content = $('#s-content').val();
	if(!content){
		toastr.warning('请输入原因!');
		return;
	}
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentWithdraw/refuseWithdraw').'?id=' ?>' + id,
          dataType: 'json',
          data:{
              'content': content
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

$(".confirm-invoice,.refuse-invoice").on("click",function(){
    var id = $(this).data("id");
    var invoice_status = $(this).data("invoice_status");
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentWithdraw/invoice').'?id=' ?>' + id,
          dataType: 'json',
          data:{
              'invoice_status': invoice_status
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