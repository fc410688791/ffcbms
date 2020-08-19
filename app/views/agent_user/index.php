<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
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
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='turn'>经营权转移</a>
                </div>
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('AgentUser/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="账号/姓名" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('AgentUser/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>     
                                    <select id='is_verification' name="is_verification" class="form-control select2" style="width:120px;">
                                        <option value=''>实名认证</option>
                                        <option value='1' <?php echo isset($is_verification)? ($is_verification == 1)? 'selected':'':''; ?>>已认证</option>
                                        <option value='0' <?php echo isset($is_verification)? ($is_verification == 0)? 'selected':'':''; ?>>未认证</option>
                                    </select>
                                    <select id='commission_status' name="commission_status" class="form-control" style="width:120px;">
                                        <option value=''>所有商户</option>
                                        <option value='1' <?php echo isset($commission_status)? ($commission_status == 1)? 'selected':'':''; ?>>正常分佣</option>
                                        <option value='0' <?php echo isset($commission_status)? ($commission_status == 0)? 'selected':'':''; ?>>不可分佣</option>
                                        <option value='2' <?php echo isset($commission_status)? ($commission_status == 2)? 'selected':'':''; ?>>暂停分佣</option>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/AgentUser/index" class="btn btn-danger">重置</a>
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
                                <td>序号</td>
                                <td>账号</td>
                                <td>姓名</td>
                                <td>投放点总数</td>
                                <td>设备总数</td>
                                <td>员工</td>
                                <td>分润比例</td>
                                <td>可提现</td>
                                <td>结算方式</td>
                                <td>实名认证</td>
                                <td>认证时间</td>
                                <td>分佣状态</td>
                                <td>角色</td>
                                <td>代理商</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><input type="checkbox" name="merchant" type="checkbox" value="<?php echo $info['id'];?>" data-name="<?php echo $info['user_name'];?>"></td>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['user_name']; ?></td>
                                <td><?php echo $info['name']; ?></br><?php echo $info['mobile']; ?></td>
                                <td><a class="pointer" href="<?php echo base_url('AgentMerchant/index')."?agent_user_id={$info['id']}"; ?>"><?php echo $info['merchant_count']; ?></a></td>
                                <td><a class="pointer" href="<?php echo base_url('Machine/index')."?agent_user_id={$info['id']}"; ?>"><?php echo $info['machine_count']; ?></a></td>
                                <td><a class="pointer" href="<?php echo base_url('Agent/user')."?agent_user_id={$info['id']}"; ?>"><?php echo $info['staff']?$info['staff']:'-'; ?></a></td>
                                <td><?php echo $info['commission_proportion']; ?></td>
                                <td><?php echo $info['withdraw_cash_amount']; ?></td>
                                <td><?php echo $info['commission_type']; ?></td>
                                <td><?php echo $info['is_verification']; ?></td>
                                <td><?php echo $info['verify_time']; ?></td>
                                <td><?php echo $info['commission_status_name']; ?></td>
                                <td><?php echo $info['group_id']; ?></td>
                                <td>
                                    <a class="pointer" href="<?php echo base_url('Agent/index')."?key={$info['agent_id']}"; ?>"><?php echo $info['agent_name']; ?></a>
                                </td>
                                <td>
                                    <?php if ($info['proxy_pattern']==2){?>
                                        <a class="pwd pointer" href="javascript:;" data-id='<?php echo $info['id'];?>'>密码</a>
                                        <a class="pointer" href="javascript:;" onclick="showUpdate(this);" data-id='<?php echo $info['id'];?>'>编辑</a>
                                        <?php if ($info['commission_status']==='1'){?>
                                        <a class="confirm-stop" data-id="<?php echo $info['id'] ?>">结束返佣</a>
                                        <?php }elseif ($info['commission_status']==='0'||$info['commission_status']==='2'){?>
                                        <a class="confirm-start" data-id="<?php echo $info['id'] ?>">开始返佣</a>
                                        <?php }?>
                                    <?php }?>
                                    <a class="turn_record" href="javascript:;" data-id='<?php echo $info['id'];?>'>变更</a>
                                    <?php if ($info['status']==1){?>
                                        <a class="upd" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='2' data-name="<?php echo $info['name'];?>">封停</a>
                                    <?php }elseif ($info['status']==2){?>
                                        <a class="upd" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='1' data-name="<?php echo $info['name'];?>">解封</a>
                                    <?php }?>
                                    <a class="upd" href="javascript:;" data-id='<?php echo $info['id'];?>' data-status='0' data-name="<?php echo $info['name'];?>">删除</a>
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

<!-- 修改 - updateModal  -->
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
            <div style="padding:18px;>
                <span style="width:150px;">ID: </span>
                <input style="width:150px;" id="u_id" name='id' value='' disabled="disabled">
                
                <span style="width:150px;">身份证: </span>
                <input style="width:150px;" id="card" name='card' value='' disabled="disabled" style="width:150px;">
                <span style="width:150px;">详细地址: </span>
                <input style="width:150px;" id="postion" name='postion' value='' disabled="disabled" style="width:150px;">
                
                <span style="width:150px;">备注: </span>
                <input style="width:150px;" id="describe" name='describe' value='' disabled="disabled" style="width:150px;">
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
<!-- 查看详情 - updateModal -end -->

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
              <th>商户ID：</th>
              <th>
                  <input id="start-id" disabled="disabled">
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
              <th>商户ID：</th>
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
          <button type="button" class="btn btn-primary" id='stopConfirm' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg text-center" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">修改密码</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>商户ID：</th>
              <th colspan="3">
                  <input id="p-id" disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>请填写新密码：</th>
              <th colspan="3">
                  <input id="p-password" type="text" maxlength="32" placeholder="6-32位数字或字母"/>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='passwordConfirm' style='width:400px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<!-- 转移 - turnModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="turnModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">子商户转移</h3>
      </div>

      <div class="modal-body" >
        <table class="table">
          <tbody>
              <tr>
                  <th>已选择子商户: <span id="merchant_count">0</span>个</th>
                  <th colspan="3"><input id="merchant_list" type="text" disabled="disabled"></th>
              </tr>
              <tr>
                  <th>投放点迁移类型：</th>
                  <th colspan="3">
                      <select id="is_redirect_trun_agent" class="form-control" style="width:200px;">
                          <option value='1'>迁移商户</option>
                          <option value='0'>重新绑定商户</option>
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
                  <th>待绑定商户：</th>
                  <th colspan="3">
                      <select id="merchant_id" class="form-control" style="width:200px;">   
                      </select>
                  </th>
              </tr>
          </tbody>
        </table>
          <div class="modal-footer">
            <span style="float:left;color:red;">*商户及其中投放点迁移到新的代理商后子账号将移除权限</span>
            <button id="btn-trun" class="btn btn-success" style="text-align: right;">确认</button>
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

<div class='modal modal-danger fade' id='modal-danger'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span></button>
        <h4 class='modal-title' id='title-danger'></h4>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-outline pull-left' data-dismiss='modal'>取消</button>
        <a type='button' class='btn btn-outline' id='confirm-danger' href='javascript:;'>确定</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class='modal modal-unt fade' id='modal-unt'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header' style="background-color: orange;color:#FFFFFF;border-bottom: 1px solid orange;">
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span></button>
        <h4 class='modal-title' id='title-unt'></h4>
      </div>
      <div class='modal-footer' style="background-color: orange;color:#FFFFFF;border-top: 1px solid red;">
        <button type='button' class='btn btn-outline pull-left' data-dismiss='modal'>取消</button>
        <a type='button' class='btn btn-outline' id='confirm-unt' href='javascript:;'>确定</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

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
$(function(){
    $('#reservation,#u-contract_time').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        },
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
            $('#query_submit').click();
        });
    });

function showUpdate(source) {
	var id = $(source).data('id');
	$("#u_id").val(id);
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('AgentUser/info').'?id=' ?>' + id,
          dataType: 'json',
          data:{
        	  'block':'update',
          },
          async:false,//同步请求
          success: function(data){
            var info = data.data;
            console.log(info);
            $('#card').val(info.card);
            $('#postion').val(info.postion);
            $('#describe').val(info.describe);

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
        url: '<?php echo base_url('AgentUser/commission').'?id=' ?>' + id,
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
$(".pwd").on("click",function(){
	var id = $(this).data('id');
	$('#p-id').val(id);
	$("#passwordModal").modal('show');
});
$("#passwordConfirm").on("click",function(){
	var id = $('#p-id').val();
	var password = $('#p-password').val();
	if(!password||password.length>32||password.length<6){
		toastr.warning('请按要求填写!');
		return;
	}
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentUser/update') ?>',
          dataType: 'json',
          data:{
        	  'id': id,
              'password': password
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

$(".confirm-start").on("click",function(){
	var id = $(this).data('id');
	$('#start-id').val(id);
	$("#startModal").modal('show');
});
$("#startConfirm").on("click",function(){
	var id = $('#start-id').val();
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentUser/confirm').'?id=' ?>' + id,
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
	$("#s-id").val(id);
	$("#stopModal").modal('show');
});
$("#stopConfirm").on("click",function(){
	var id = $('#s-id').val();
	var content = $('#s-content').val();
	if(!content){
		toastr.warning('请输入原因!');
		return;
	}
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentUser/confirm').'?id=' ?>' + id,
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
    	toastr.warning("请选择商户！"); 
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
        url: '<?php echo base_url('AgentUser/get_merchant_list')?>',
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
    	toastr.warning("请选择子商户！"); 
    	return;
    }
    if(!agent_id){
    	toastr.warning("请选择代理商！"); 
    	return;
    }
    if(is_redirect_trun_agent==0&&!merchant_id){
    	toastr.warning("请选择子商户！"); 
    	return;
    }
	$.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentUser/turn')?>',
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
        url: '<?php echo base_url('AgentUser/turn_record'); ?>',
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

$(".upd").on("click",function(){
	var id = $(this).data('id');
	var status = $(this).data('status');
	var name = $(this).data('name');
	if(status==1){
	    var title = '{'+name+'}--你确定要解封该账号吗 ?';
	    $('#confirm-unt').attr('data-id', id);
	    $('#confirm-unt').attr('data-status', status);
	    $('#title-unt').html(title);
	    $('#modal-unt').modal('show');
	}else if(status==2){
		var title = '{'+name+'}--你确定要封停该账号吗 ?';
	    $('#confirm-unt').attr('data-id', id);
	    $('#confirm-unt').attr('data-status', status);
	    $('#title-unt').html(title);
	    $('#modal-unt').modal('show');
	}else{
		var title = '{'+name+'}--你确定删除该内容吗？';
	    $('#confirm-danger').attr('data-id', id);
	    $('#title-danger').html(title);
	    $('#modal-danger').modal('show');
	}
});
$('#confirm-unt').click(function(){
	var id = $(this).attr('data-id');
	var status = $(this).attr('data-status');
    var url = '<?php echo base_url('AgentUser/update')?>';
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "id":id,
          "status":status,
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
$('#confirm-danger').click(function(){
	var id = $(this).attr('data-id');
    var url = '<?php echo base_url('AgentUser/update')?>';
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "id":id,
          "status":0,
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
            'c_commission_type':2
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