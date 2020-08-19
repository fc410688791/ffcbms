<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.pointer{
	cursor:pointer;
}

</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberCurrencyRecord/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="用户ID/订单ID" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberCurrencyRecord/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id='direction' name="direction" class="form-control" style="width:120px;">
                                        <option value=''>交易方向</option>
                                        <option value='1' <?php echo isset($direction)? ($direction == 1)?'selected':'':''; ?>>转入</option>
                                        <option value='2' <?php echo isset($direction)? ($direction == 2)?'selected':'':''; ?>>转出</option>
                                    </select>
                                    <select id='trade_type' name="trade_type" class="form-control" style="width:120px;">
                                        <option value=''>交易类型</option>
                                        <?php foreach( $trade_type_option as $ttk => $ttv){ ?>
                                          <option value="<?php echo $ttk ?>" <?php echo isset($trade_type)? ($trade_type == $ttk)?'selected':'':''; ?> ><?php echo $ttv ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>         
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/MemberCurrencyRecord/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>用户ID</td>
                                <td>交易单号</td>
                                <td>购买充币(个)</td>
                                <td>赠送充币(个)</td>
                                <td>总剩余充币数(个)</td>
                                <td>活动剩余充币数(个)</td>
                                <td>消费充币(个)</td>
                                <td>交易金额(元)</td>
                                <td>交易方向</td>
                                <td>交易类型</td>
                                <td>交易日期</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><a href="<?php echo base_url('Member/index').'?key='.$info['uuid'] ?>"><?php echo $info['uuid'] ?></a></td>
                                <td>
                                    <?php if ($info['trade_type']==1){?>
                                    <a href="<?php echo base_url('MemberChargeOrder/index').'?key='.$info['out_trade_no'] ?>"><?php echo $info['out_trade_no'] ?></a>
                                    <?php }elseif ($info['trade_type']==2){?>
                                    <a href="<?php echo base_url('Order/index').'?key='.$info['out_trade_no'] ?>"><?php echo $info['out_trade_no'] ?></a>
                                    <?php }elseif ($info['trade_type']==3){?>
                                    <a href="<?php echo base_url('CustomerService/index').'?key='.$info['out_trade_no'] ?>"><?php echo $info['out_trade_no'] ?></a>
                                    <?php }?>
                                </td>
                                <td><?php if ($info['trade_type']==1){echo $info['record_currency'];}else{echo '-';} ?></td>
                                <td><?php if ($info['trade_type']==1){echo $info['record_gift_currency'];}else{echo '-';} ?></td>
                                <td><?php echo $info['c_currency_balance']; ?></td>
                                <td><?php echo $info['c_currency_act_balance']; ?></td>
                                <td><?php if ($info['trade_type']!=1){echo $info['record_currency'];}else{echo '-';} ?></td>
                                <td><?php if ($info['trade_type']==1){echo round($info['record_currency']/100, 2);}else{echo '-';} ?></td>
                                <td><?php echo $info['direction']; ?></td>
                                <td><?php echo $info['trade_type_name']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
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
        $('#query_submit').click();
    });
});
</script>