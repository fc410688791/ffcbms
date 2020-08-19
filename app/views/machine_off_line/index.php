<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">            
            <div class="box">
                <div class="box-body table-responsive">
                    <div style="float:right;">
                        
                    </div>
                    <div style="margin-bottom:30px;background-color:#FFF;padding:20px;">
                        <h3 style="margin:0px;" title="数据处理同步可能有30分钟以内的延迟"><img alt="" src="<?php echo $assets_dir; ?>/img/u88.png">设备离线:<span><?php echo count($list);?></span></h3>
                        <table id="example1" class="table table-bordered table-striped" style="margin-top:30px;width:100%;text-align:center;">
                            <tr>
                                <td>投放点</td>
                                <td>具体位置</td>
                                <td>断电时长</td>
                                <td>设备ID</td>
                            </tr>
                            <?php foreach ($list as $dev): ?>
                                <tr>
                                    <td><?php echo $dev['merchant_name']; ?></td>
                                    <td><?php echo $dev['position_name'].$dev['position']; ?></td>
                                    <td><?php echo $dev['off_line_time_num']; ?></td>
                                    <td><?php echo $dev['machine_id']; ?></td>
                                </tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>

<script>
$("#download").on("click",function(){
    var to_url = '<?php echo base_url('JoinStorage/info');?>'+'?operation=download&id='+'<?php echo $id;?>';
    location.href = to_url;
});
</script>