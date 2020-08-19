<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script src="<?php echo $assets_dir; ?>/js/echarts.min.js"></script>
<style type="text/css">
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="btn-group">
                    <span class="btn btn-default pagebtn" data-viem='product'>商品数据</span>
                    <span class="btn btn-default pagebtn" data-viem='charge'>充值数据</span>
                    <span class="btn btn-primary pagebtn" data-viem='refund'>客诉数据</span>
                </div>
                <div class="box-body table-responsive">
                    <h3>退款分析</h3>
                    <div class="btn-group">
                        <span class="btn btn-default chartbtn" data-viem='refund'>指标对比</span>
                        <span class="btn btn-default chartbtn" data-viem='refund_time'>时间对比</span>
                        <span class="btn btn-primary chartbtn" data-viem='refund_list'>退款列表</span>
                    </div>
                    <div style="margin-top:25px;" class="col-xs-12">
                        <select id="time_type" name="time_type" style="height:30px;">
                            <option value="1" <?php echo isset($time_type)? ($time_type == 1)? 'selected':'':''; ?>>最近7天</option>
                            <option value="2" <?php echo isset($time_type)? ($time_type == 2)? 'selected':'':''; ?>>最近30天</option>
                            <option value="3" <?php echo isset($time_type)? ($time_type == 3)? 'selected':'':''; ?>>自定义</option>
                        </select>
                        <input id="reservation" name="reservation" style="margin-left:25px;height:30px;border:0px;" type="text" value="<?php echo isset($reservation)?$reservation:''; ?>"/>
                        <span style="float:right;">
                            <a id="download" href="javascript:void(0);">下载</a>
                        </span>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                               <th>用户ID</th>
                               <th>投放点名称</th>
                               <th>设备ID</th>
                               <th>下单时间</th>
                               <th>申请退款时间</th>
                               <th>退款类型</th>
                               <th>退款原因</th>
                               <th>设备类型</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <?php foreach ($list as $key=>$info) { ?>
                            <tr>
                                <th><?php echo $info['uuid']; ?></th>
                                <th><?php echo $info['name']; ?></th>
                                <th><?php echo $info['machine_id']; ?></th>
                                <th><?php echo $info['o_create_time']; ?></th>
                                <th><?php echo $info['r_create_time']; ?></th>
                                <th><?php echo $info['text']; ?></th>
                                <th><?php echo $info['reason']; ?></th>
                                <th><?php echo $info['device_type']; ?></th>
                            </tr>
                            <?php }?>
                        </tbody>
                        <tfoot>
                            <tr>
                              <th colspan="10"><?php echo $pagination; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
      }
    }).on('apply.daterangepicker', function (ev, picker) {
    	get_series();
    });
    var time_type = $('#time_type').val();
	if(time_type==3){
		$("#reservation").show();
	}else{
		$("#reservation").hide();
	}
});

$("#time_type").on('change',function(){
	var time_type = $(this).val();
	if(time_type==3){
		$("#reservation").show();
	}else{
		$("#reservation").hide();
		get_series();
	}
});
function get_series(){
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem=refund_list'+'&time_type='+time_type+'&reservation='+reservation;
    location.href = url;
}

$('#download').on('click', function(){
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var to_url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem=refund_list&operate=download';
    location.href = to_url+'&time_type='+time_type+'&reservation='+reservation;
});
$('.pagebtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem='+viem;
    location.href = to_url;
});
$('.chartbtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem='+viem;
    location.href = to_url;
});
</script>