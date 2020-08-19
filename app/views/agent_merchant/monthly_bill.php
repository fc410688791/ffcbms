<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-header with-border"></div>
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>月份</td>
                                <td>投放点名称</td>
                                <td>设备数量</td>
                                <td>交易量</td>
                                <td>完成率</td>
                                <td>流水</td>
                                <td>代理商姓名</td>
                                <td>创建时间</td>
                                <td>创建人</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><?php echo $key;?></td>
                                <td><?php echo $merchant_info['name'];?></td>
                                <td><a target="_blank" href="<?php echo base_url('Machine/index').'?merchant_id='.$merchant_info['id'].'&merchant_name='.$merchant_info['name'] ?>"><?php echo $info['merchant_count'];?></a></td>
                                <td><a target="_blank" href="<?php echo base_url('Order/index').'?merchant_id='.$merchant_info['id'].'&selectTime=1&reservation='.$info['reservation'] ?>"><?php echo $info['pay_count'];?></a></td>
                                <td><?php if ($info['merchant_count']){echo round(($info['pay_count']/$info['merchant_count']),2)*100 . '%';}else{echo '0%';}?></td>
                                <td><?php echo $info['cash_fee_statistics']?'￥'.$info['cash_fee_statistics']:"-";?></td>
                                <td><?php echo $merchant_info['card_name'];?></td>
                                <td><?php echo date('Y-m-d H:i:s', $merchant_info ['create_time']);?></td>
                                <td><?php echo $merchant_info['a_u_name']?$merchant_info['a_u_name']:$merchant_info['card_name'];?></td>
                                <td>
                                    <a target="_blank" href="<?php echo base_url('AgentMerchant/dailyBill').'?merchant_id='.$merchant_info['id'].'&start_time='.$info['start_time'].'&end_time='.$info['end_time']; ?>">日账单</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <tfoot>
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