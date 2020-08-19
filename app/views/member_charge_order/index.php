<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-body">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberChargeOrder/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="订单号/用户ID/昵称/手机号" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" style="margin-left: 10px;" id='search'>搜索</button>
                                    </div>
                                </div>
                            </div>
                        </div>               
                    </form>
                    <form id="selectForm" class="" method="GET" action='<?php echo base_url('MemberChargeOrder/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                    <select class="form-control " id='pay_type' name="pay_type" style='width: 120px;'>
                                        <option value=''>支付类型</option>
                                        <?php foreach( $pay_type_option as $ptk => $ptv){ ?>
                                          <option value="<?php echo $ptk ?>" <?php echo isset($pay_type)? ($pay_type == $ptk)?'selected':'':''; ?> ><?php echo $ptv ?></option>
                                        <?php } ?>
                                    </select>
                                    <select class="form-control " id='activity_charge_id' name="activity_charge_id" style='width: 120px;'>
                                        <option value=''>所有活动</option>
                                        <?php foreach( $charge_name_option as $cnv){ ?>
                                          <option value="<?php echo $cnv['id'] ?>" <?php echo isset($activity_charge_id)? ($activity_charge_id == $cnv['id'])?'selected':'':''; ?> ><?php echo $cnv['charge_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" name="orderSelect" value="<?php echo $this->input->get('orderSelect')?? 'all'; ?>">
                                    <button type="submit" id="query_submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/MemberChargeOrder/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->

        <div class="box-body table-responsive">
            <div class='with-border'>
                <div class="btn-group menuBar">
                    <span class="btn btn-default chartbtn" data-bm='all'>全部</span>
                    <span class="btn btn-default chartbtn" data-bm='prepay'>待支付</span>
                    <span class="btn btn-default chartbtn" data-bm='payed'>已支付</span>
                    <span class="btn btn-default chartbtn" data-bm='paycancel'>已取消</span>
                </div>
            </div>
            
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                       <th>用户ID</th>
                       <th>订单编号</th>
                       <th>商品名称</th>
                       <th>金额</th>
                       <th>支付类型</th>
                       <th>下单时间</th>
                       <th>订单状态</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($list as $key => $val) { ?>
                    <tr>
                        <td><a href="<?php echo base_url('Member/index').'?key='.$val['uuid'] ?>"><?php echo $val['uuid'] ?></a></td>
                        <td><?php echo $val['out_trade_no'] ?></td>
                        <td><a href="<?php echo base_url('MemberActivityCharge/index').'?key='.$val['charge_name'] ?>"><?php echo $val['charge_name'] ?></a></td>
                        <td><?php echo $val['cash_fee'] ?></td>
                        <td><?php echo $val['pay_type']?></td>
                        <td><?php echo date("Y-m-d H:i:s", $val['create_time']) ?></td>
                        <td>
                            <span class="label <?php echo  $val['sta_color'];?> "><?php echo $val['order_status'] ?></span>
                        </td>
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

<script>
$(function(){
  //$('.select2').select2();
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
      /* $('#selectTime').val('1');
      $('#query_submit').click(); */
  });
});

activitySelectOrder();
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

</script>