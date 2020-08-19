<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
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
.btn-recovery{
	color:#fff;
	border:1px solid #fff;
	background-color:rgba(211, 55, 36, 1);
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Agent/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="代理商ID/昵称/真实姓名" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Agent/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                    <select name="proxy_pattern" class="form-control select2" style="width:120px;">
                                        <option value=''>全部角色</option>
                                        <option value='1' <?php echo isset($proxy_pattern)? ($proxy_pattern == 1)? 'selected':'':''; ?>>普通代理商</option>
                                        <option value='2' <?php echo isset($proxy_pattern)? ($proxy_pattern == 2)? 'selected':'':''; ?>>内部自营</option>
                                        <option value='3' <?php echo isset($proxy_pattern)? ($proxy_pattern == 3)? 'selected':'':''; ?>>0元代理商</option>
                                    </select>
                                    <select name="is_verification" class="form-control select2" style="width:120px;">
                                        <option value=''>实名认证</option>
                                        <option value='1' <?php echo isset($is_verification)? ($is_verification == 1)? 'selected':'':''; ?>>已认证</option>
                                        <option value='0' <?php echo isset($is_verification)? ($is_verification == 0)? 'selected':'':''; ?>>未认证</option>
                                    </select>
                                    <select name="commission_status" class="form-control" style="width:120px;">
                                        <option value=''>所有代理商</option>
                                        <option value='1' <?php echo isset($commission_status)? ($commission_status == 1)? 'selected':'':''; ?>>正常分佣</option>
                                        <option value='0' <?php echo isset($commission_status)? ($commission_status == 0)? 'selected':'':''; ?>>不可分佣</option>
                                        <option value='2' <?php echo isset($commission_status)? ($commission_status == 2)? 'selected':'':''; ?>>暂停分佣</option>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/Agent/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>代理商ID</td>
                                <td>昵称</td>
                                <td>真实姓名</td>
                                <td>商户</td>
                                <td>投放点总数</td>
                                <td>设备总数</td>
                                <td>分润比例</td>
                                <td>折扣</td>
                                <td>0元代理申请</td>
                                <td>分佣状态</td>
                                <td>角色</td>
                                <td>上级代理</td>
                                <td>操作</td> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><a class="pointer" href="javascript:;" onclick="showInfo(this);" data-id='<?php echo $info['id'];?>'><?php echo $info['id'];?></a></td>
                                <td><?php echo $info['user_name'];?></td>   
                                <td><?php echo $info['card_name'];?></td>
                                <td><a class="pointer" href="<?php echo base_url('AgentUser/index')."?agent_id={$info['id']}"; ?>"><?php echo $info['store_count'];?></a></td>
                                <td><a class="pointer" href="<?php echo base_url('Agent/merchant')."?agent_id={$info['id']}"; ?>"><?php echo $info['merchant_count'];?></a></td>
                                <td><?php echo $info['machine_count'];?></td>
                                <td><?php echo $info['commission_proportion'];?></td>
                                <td><?php echo $info['onetime_discount_rate']*100 . '%';?></td>
                                <td><?php echo $info['onetime_share'];?></td>
                                <td><?php echo $info['commission_status_name'];?></td>
                                <td><?php if ($info['proxy_pattern']==1){
                                              echo "普通代理商";
                                          }elseif ($info['proxy_pattern']==2){
                                              echo "内部自营";
                                          }elseif ($info['proxy_pattern']==3){
                                              echo "0元代理商";
                                          }
                                    ?>
                                </td>
                                <td><?php echo $info['rel_agent_card_name'];?></td>
                                <td>
                                    <a class="pointer" href="<?php echo base_url('Agent/address')."?agent_id={$info['id']}&default_address_id={$info['default_address_id']}"; ?>">收货地址</a>
                                    <!-- <a class="pointer" href="<?php echo base_url('Agent/merchant')."?agent_id={$info['id']}"; ?>">投放点</a> -->
                                    <a class="pointer" href="<?php echo base_url('Agent/user')."?agent_id={$info['id']}"; ?>">员工</a>
                                    <a class="pointer" href="<?php echo base_url('Secretary/index')."?agent_id={$info['id']}"; ?>">秘书</a>
                                    <a class="pointer" href="javascript:;" onclick="showUpdate(this);" data-id='<?php echo $info['id'];?>'>编辑</a>
                                    <?php if ($info['onetime_share']=='-'&&$info['proxy_pattern']==2){?>
                                        <a class="approval" data-id="<?php echo $info['id'] ?>">审批</a>
                                    <?php }?>
                                    <?php if ($info['commission_status']==='1'){?>
                                        <a class="confirm-stop" data-id="<?php echo $info['id'] ?>">结束返佣</a>
                                    <?php }elseif ($info['commission_status']==='0'||$info['commission_status']==='2'){?>
                                        <a class="confirm-start" data-id="<?php echo $info['id'] ?>" data-card_name="<?php echo $info['card_name'] ?>">开始返佣</a>
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">详情</h3>
      </div>

      <div class="modal-body" >
        <div class="info">
            <div style="width:100%;height:30px;background:#ccc;line-height:30px;"><span style="float:left;margin-left:10px;">代理商信息</span></div>
            <div class="p-line"><span class="first-span text-left field">代理商ID</span><span id="id" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">真实姓名</span><span id="card_name" class="text-left param bold"></span><span class="text-left field">昵称</span><span id="user_name" class="text-left param bold"></span><span class="text-left field">开户地址</span><span id="open_place" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">性别</span><span id="sex" class="text-left param bold"></span><span class="text-left field">年龄</span><span id="age" class="text-left param bold bold"></span><span class="text-left field">手机号码</span><span id="mobile" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">注册时间</span><span id="create_time" class="text-left param bold"></span><span class="text-left field">最后登陆</span><span id="login_time" class="text-left param bold"></span></div>
        </div>
        <div class="info">
            <div style="width:100%;height:30px;background:#ccc;line-height:30px;"><span style="float:left;margin-left:10px;">实名认证</span></div>
            <div class="p-line"><span class="first-span text-left field">地址</span><span id="card_address" class="text-left param bold"></span><span class="text-left field">有效期限</span><span id="card_valid_date" class="text-left param bold" ></span><span class="text-left field">签发机关</span><span id="organization" class="text-left param bold"></span></div>
            <div class="p-line"><span class="first-span text-left field">证件号</span><span id="card" class="text-left param bold"></span><span class="text-left field">上传时间</span><span id="verify_time" class="text-left param bold bold"></span><span class="text-left field">协议状态</span><span id="is_agreement" class="text-left param bold"></span></div>
            <div style="width:100%;height:50px;line-height:50px;">
                <span class="first-span text-left field">证件正面</span><span class="text-left param bold"><img id="f_url" class="file_image" alt="" src=""></span>
                <span class="text-left field">证件反面</span><span class="text-left param bold"><img id="b_url" class="file_image" alt="" src=""></span>
                <span class="text-left field">手持身份证</span><span class="text-left param bold"><img id="h_url" class="file_image" alt="" src=""></span>
            </div>
            </br>
            <div class="p-line">
                <span class="first-span text-left field">实名认证</span><span id="is_verification" class="text-left param bold"></span>
            </div>
        </div>
        <div class="info">
            <div style="width:100%;height:30px;background:#ccc;line-height:30px;"><span style="float:left;margin-left:10px;">银行卡</span></div>
            <div id="card_list">
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->
<!-- 修改 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">编辑</h3>
      </div>
      <div class="modal-body">
          <div style="widht:100%;border:1px solid #ccc;margin-bottom:30px;">
            <div style="width:100%;height:30px;background:#ccc;line-height:30px;"><span style="float:left;margin-left:10px;">基础设置：</span></div>
            <div style="padding:18px;">
                <span>代理商ID: </span>
                <input id="u_id" name='id' value='' disabled="disabled" style="width:120px;">
                <span style="margin-left:50px;">角色：</span>
                <select id='u_proxy_pattern' name="proxy_pattern">
                    <option value=''>全部角色</option>
                    <option value='1'>普通代理</option>
                    <option value='2'>内部自营</option>
                    <option value='3'>0元代理</option>
                </select>
                <span style="margin-left:50px;">折扣：</span>
                <input id='u_onetime_discount_rate' name="onetime_discount_rate" style="width:50px;">%
                <span style="margin-left:50px;">折扣设备数量：</span>
                <input id='u_onetime_num' name="onetime_num" style="width:50px;">
            </div>
            <div class="modal-footer">
              <right>
                  <button id="btn-update" class="btn btn-success">修改</button>
              </right>
            </div>
          </div>
          <div style="widht:100%;border:1px solid #ccc;margin-bottom:30px;">
            <div style="width:100%;height:30px;background:#ccc;line-height:30px;"><span style="float:left;margin-left:10px;">分佣设置：</span></div>
            <div style="padding:20px;text-align:left;">
                <div>
                    <span style="">分润比例：</span>
                    <input id='u-commission_proportion' name="commission_proportion" style="width:50px;" type="number">%
                    <span style="margin-left:25px;">结算方式：</span>
                    <select id='u-commission_type' name="commission_type" style="width:80px;">
                        <option value='1'>即时</option>
                        <option value='2'>月结</option>
                    </select>
                    <span style="margin-left:25px;">提现额度：大于等于</span>
                    <input id='u-commission_withdrawal_amount' name="commission_withdrawal_amount" style="width:100px;" type="number">
                    <span>元</span>
                    <span class="u-commission_withdrawal_time" style="margin-left:25px;">提现时间： 每月</span>
                    <select id='u-commission_withdrawal_time' class="u-commission_withdrawal_time" name="commission_withdrawal_time" style="width:50px;">
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                        <option value='5'>5</option>
                        <option value='6'>6</option>
                        <option value='7'>7</option>
                        <option value='8'>8</option>
                        <option value='9'>9</option>
                        <option value='10'>10</option>
                        <option value='11'>11</option>
                        <option value='12'>12</option>
                        <option value='13'>13</option>
                        <option value='14'>14</option>
                        <option value='15'>15</option>
                        <option value='16'>16</option>
                        <option value='17'>17</option>
                        <option value='18'>18</option>
                        <option value='19'>19</option>
                        <option value='20'>20</option>
                        <option value='21'>21</option>
                        <option value='22'>22</option>
                        <option value='23'>23</option>
                        <option value='24'>24</option>
                        <option value='25'>25</option>
                        <option value='26'>26</option>
                        <option value='27'>27</option>
                        <option value='28'>28</option>
                        <option value='29'>29</option>
                        <option value='30'>30</option>
                        <option value='31'>31</option>
                    </select>
                    <span class="u-commission_withdrawal_time">号</span>
                </div>
                <div style="margin-top: 30px;">
                    <span>合同时间：</span><input type="text" autocomplete="off" id="u-contract_time" style='width: 200px;'/>
                </div>
                <div style="margin-top: 30px;">
                    <span>请选择返佣时间：</span>
                    <div id="r1" style="height:34px;line-height:34px;">
                        <input id="u_r1" type="radio" name="time" value="1"><span style="margin-right:20px;">设置当前时间开始返佣</span><span id="now" style="margin-right:20px;"><?php echo date('Y-m-d H:i:s', time());?></span>
                    </div>
                    <div id="r2" style="height:34px;line-height:34px;display:inline">
                        <input id="u_r2" type="radio" name="time" value="2">
                        <span style="margin-right:20px;">请选择返佣开始时间</span>
                        <input type="text" name="date" id="date" class="form-control"/>
                    </div>
                    <div id="r3"  style="height:34px;line-height:34px;">
                        <input id="u_r3" type="radio" name="time" value="3"><span style="margin-right:20px;">使用上一次设置时间</span><span id="back" style="margin-right:20px;"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <right>
                  <button id="btn-recovery" class="btn btn-cancel">恢复最初设置</button>
                  <button id="btn-commission" class="btn btn-success">修改</button>
              </right>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<div class="modal fade bs-example-modal-lg text-center" id="startModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">开始返佣</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>代理商ID：</th>
              <th>
                  <input id="start-agent_id" name="agent_id" value='' disabled="disabled">
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='startConfirm' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg text-center" id="stopModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">暂停返佣</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>代理商ID：</th>
              <th colspan="3">
                  <input id="s-agent_id" name="agent_id" value='' disabled="disabled">
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
          <button type="button" class="btn btn-primary" id='stopConfirm' style='width:400px'>确定</button>
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

<div class="modal fade bs-example-modal-lg text-center" id="recoveryModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content" style="background-color:rgba(211, 55, 36, 1);">
      <div class="modal-body">
          <h3 style="color:#fff;"><span>{</span><span id="id-recovery"></span><span>}--</span><span>你确定恢复最初设置吗？</span></h3>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-recovery" id='cancel-recovery' style="float:left;">取消</button>
        <button type="button" class="btn-recovery" id='confirm-recovery' style="float:right;">确定</button>
      </div>
    </div>
  </div>
</div>

<script>
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

$(function(){
    $('#reservation,#u-contract_time').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        }
    });

    $('#date').daterangepicker({
      singleDatePicker: true,
      locale: {
          format: 'YYYY-MM-DD',
          applyLabel: '确认',
          cancelLabel: '取消',
          daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
          monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
          firstDay: 1
      }
    }).on('apply.daterangepicker', function (ev, picker) {
      //$('#selectTime').val('1');
      $('#query_submit').click();
    });
});

function showInfo(source) {
	var id = $(source).data('id');
	$("#u_id").val(id);
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('Agent/info').'?id=' ?>' + id,
          dataType: 'json',
          data:{
              'block':'info',
          },
          async:false,//同步请求
          success: function(data){
              var info = data.data;
              /* console.log(info); */
        	  $('#id').html(id);
        	  $('#user_name').html(info.user_name);
        	  $('#card_name').html(info.card_name);
        	  $('#open_place').html(info.open_place);
        	  $('#sex').html(info.sex);
        	  $('#age').html(info.age);
        	  $('#mobile').html(info.mobile);
        	  $('#create_time').html(info.create_time);
        	  $('#login_time').html(info.login_time);

        	  $('#card_address').html(info.card_address);
        	  $('#card_address').attr("title",info.card_address);
        	  $('#card_valid_date').html(info.card_valid_date);
        	  $('#organization').html(info.organization);
        	  $('#card').html(info.card);
        	  $('#verify_time').html(info.verify_time);
        	  $('#is_agreement').html(info.is_agreement);
        	  $("#f_url").attr('src',info.f_url);
        	  $("#b_url").attr('src',info.b_url);
        	  $("#h_url").attr('src',info.h_url);
        	  $('#is_verification').html(info.is_verification);

        	  if(info.card_list){
            	  var html = '<table style="width:100%;">';
            	  for(var i=0;i<info.card_list.length;i++){
            		  html += '<tr><td>银行<span style="font-weight:bold;margin-left:20px;">'+info.card_list[i].card_name+'</span></td><td>卡号<span style="font-weight:bold;margin-left:20px;">'+info.card_list[i].card_no+'</span></td><td>添加时间<span style="font-weight:bold;margin-left:20px;">'+info.card_list[i].create_time+'</span></td></tr>';
                  }
            	  html += '</table>';
            	  $('#card_list').html(html); 
              }else{
            	  $('#card_list').html(''); 
              }
          },
        error: function () {
          toastr.error('请求错误!');
        },
        complete: function () {   
        }
    });
	$('#detailModal').modal('show');
}


function showUpdate(source) {
	var id = $(source).data('id');
	$("#u_id").val(id);
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('Agent/info').'?id=' ?>' + id,
          dataType: 'json',
          data:{
        	  'block':'update',
          },
          async:false,//同步请求
          success: function(data){
            var info = data.data;
            console.log(info);
            $("#u_proxy_pattern").val(info.proxy_pattern); 
            $("#u_onetime_discount_rate").val(info.onetime_discount_rate*100);
            $("#u_onetime_num").val(info.onetime_num);

            $("#u-commission_proportion").val(info.commission_proportion);
            $("#u-commission_type").val(info.commission_type);
            if(info.commission_type==1){
            	$(".u-commission_withdrawal_time").hide();
            }else if(info.commission_type==2){
            	$(".u-commission_withdrawal_time").show();
            }
            
            $("#u-commission_withdrawal_amount").val(info.commission_withdrawal_amount);
            $("#u-commission_withdrawal_time").val(info.commission_withdrawal_time);
            $("#u-contract_time").val(info.contract_time);
            if(info.commission_time=='无'){
            	$('#r3').hide();
            	$('#u_r1').attr("checked","checked");
            }else{
            	$('#r3').show();
            	$('#u_r3').attr("checked","checked");
            	$('#back').html(info.commission_time);
            }
            if(info.recovery){
            	$('#btn-recovery').data('id',info.id);
            	$('#btn-recovery').show();
            }else{
            	$('#btn-recovery').data('id','');
            	$('#btn-recovery').hide();
            }
          },
        error: function () {
          toastr.error('请求错误!');
        },
        complete: function () {   
        }
    });
    $("#updateModal").modal('show');
}

$("#u-commission_type").change(function () {
	var commission_type = $('#u-commission_type').val();
	if(commission_type==1){
		$('.u-commission_withdrawal_time').hide();
	}else if(commission_type==2){
		$('.u-commission_withdrawal_time').show();
    }
});
$("#btn-update").on("click",function(){
	var id      = $('#u_id').val();
	var proxy_pattern    = $('#u_proxy_pattern').val();
    var onetime_discount_rate = $('#u_onetime_discount_rate').val();
    var onetime_num = $('#u_onetime_num').val();
    if(0<=Number(onetime_discount_rate)&&Number(onetime_discount_rate<=100)){
    	$.ajax({
            type: 'POST',
            url: '<?php echo base_url('Agent/update').'?id=' ?>' + id,
              dataType: 'json',
              data:{
                  'proxy_pattern': proxy_pattern,
                  'onetime_discount_rate': onetime_discount_rate*0.01,
                  'onetime_num': onetime_num
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
    }else {
    	toastr.warning("请填充0-100"); 
    	return;
    }
});

$("#btn-commission").on("click",function(){
	var id                              = $('#u_id').val();
	var commission_proportion           = $('#u-commission_proportion').val();
	var commission_type                 = $('#u-commission_type').val();
	var commission_withdrawal_amount    = $('#u-commission_withdrawal_amount').val();
	var commission_withdrawal_time      = $('#u-commission_withdrawal_time').val();
	var contract_time                   = $('#u-contract_time').val();
	if(commission_type==2&&commission_withdrawal_time<1){
		toastr.warning("请选择提现时间");
		return;
	}
	var type  = $('input:radio[name=time]:checked').val();
	if(type==1){
		var commission_time = $('#now').html();
	}else if(type==2){
		var commission_time = $('#date').val();
	}else if(type==3){
		var commission_time = $('#back').html();
	}else{
		return;
    }
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url('Agent/commission').'?id=' ?>' + id,
          dataType: 'json',
          data:{
              'commission_proportion': commission_proportion,
              'commission_type': commission_type,
              'commission_withdrawal_amount': commission_withdrawal_amount,
              'commission_withdrawal_time': commission_withdrawal_time,
              'contract_time': contract_time,
              'commission_time': commission_time
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

$(".approval").on("click",function(){
	var id = $(this).data('id');
	if(window.confirm('你确定要给'+id+'分享0元代理商二维码的权限吗？')){
		$.ajax({
	        type: 'POST',
	        url: '<?php echo base_url('Agent/update').'?id=' ?>' + id,
	          dataType: 'json',
	          data:{
	              'onetime_share': 1
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
     }else{
        return false;
    }
});

$(".confirm-start").on("click",function(){
	var id = $(this).data('id');
	var card_name = $(this).data('card_name');
	$('#start-agent_id').val(id);
	$("#startModal").modal('show');
});
$("#startConfirm").on("click",function(){
	var id = $('#start-agent_id').val();
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('Agent/confirm').'?id=' ?>' + id,
          dataType: 'json',
          data:{
              'commission_status': 1,
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

$(".confirm-stop").on("click",function(){
	var id = $(this).data('id');
	$("#s-agent_id").val(id);
	$("#stopModal").modal('show');
});
$("#stopConfirm").on("click",function(){
	var id = $('#s-agent_id').val();
	var content = $('#s-content').val();
	if(!content){
		toastr.warning('请输入原因!');
		return;
	}
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('Agent/confirm').'?id=' ?>' + id,
          dataType: 'json',
          data:{
              'commission_status': 2,
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

$("#btn-recovery").on("click",function(){
    var id = $(this).data("id");
    if(id){
        $('#id-recovery').html(id);
        $('#recoveryModal').modal('show');
    }else{
        return;
    }
});
$("#cancel-recovery").on("click",function(){
    $('#recoveryModal').modal('hide');
});
$("#confirm-recovery").on("click",function(){
    var url = '<?php echo base_url('Agent/recovery')?>';
    var id = $("#id-recovery").html();
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            'id':id,
            'c_commission_type':1
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
</script>