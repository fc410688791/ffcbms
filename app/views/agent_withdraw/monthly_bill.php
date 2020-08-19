<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <div style="float:right;">
                        <button id="download" class="btn btn-success">导出</button>
                    </div>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>月份</td>
                                <td>月流水</td>
                                <td>可提现金额<?php echo $commission_info['commission_proportion'].'%';?></td>
                                <td>手续费0.6%</td>
                                <td>增值税6%</td>
                                <td>城建税7%</td>
                                <td>教育税附加3%</td>
                                <td>地方教育税附加2%</td>
                                <td>提现手续费0.1%</td>
                                <td>实际提现金额</td>
                                <td>代理商姓名</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['day'];?></td>
                                <td><?php echo '￥'.$info['income'];?></td>
                                <td><?php echo '￥'.$info['a'];?></td>
                                <td><?php echo '￥'.$info['b'];?></td>
                                <td><?php echo '￥'.$info['c'];?></td>
                                <td><?php echo '￥'.$info['d'];?></td>
                                <td><?php echo '￥'.$info['e'];?></td>
                                <td><?php echo '￥'.$info['f'];?></td>
                                <td><?php echo '￥'.$info['g'];?></td>
                                <td><?php echo '￥'.$info['h'];?></td>
                                <td><?php echo $agent_info['card_name'];?></td>
                                <td>
                                    <a target="_blank" href="<?php echo base_url('AgentWithdraw/dayBill').'?agent_id='.$agent_info['id'].'&start_time='.$info['start_time'].'&end_time='.$info['end_time']; ?>">日账单</a>
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

<script>
$("#download").on("click",function(){
    var to_url = '<?php echo base_url('AgentWithdraw/monthBill');?>'+'?operation=download&agent_id='+'<?php echo $agent_info['id'];?>';
    location.href = to_url;
});
</script>