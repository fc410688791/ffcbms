<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div style="float:right;">
                        <button id="download" class="btn btn-success">导出</button>
                    </div>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>日期</td>
                                <td>流水</td>
                                <td>可提现金额</td>
                                <td>代理商姓名</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['day'];?></td>
                                <td><?php echo '￥'.$info['income'];?></td>
                                <td><?php echo '￥'.$info['settlement'];?></td>
                                <td><?php echo $agent_info['card_name'];?></td>
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
    var to_url = '<?php echo base_url('AgentWithdraw/dayBill');?>'+'?operation=download&agent_id='+'<?php echo $agent_info['id'];?>'+'&start_time='+'<?php echo $start_time;?>'+'&end_time='+'<?php echo $end_time;?>';
    location.href = to_url;
});
</script>