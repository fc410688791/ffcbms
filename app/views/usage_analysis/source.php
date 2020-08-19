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
                <div class="box-title">
                    <div class='with-border'>
                        <div class="btn-group menuBar">
                            <span class="btn btn-default chartbtn" data-viem='behavior'>行为分析</span>
                            <span class="btn btn-primary chartbtn" data-viem='source'>来源分析</span>
                            <span class="btn btn-default chartbtn" data-viem='page'>页面分析</span>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <h3>来源分析</h3>
                    <div style="margin-top:25px;" class="col-xs-12">
                        <select name="time_type" id="time_type" style="height:30px;width:100px;">
                            <option value="1">最近7天</option>
                            <option value="2">最近30天</option>
                            <option value="3">自定义</option>
                        </select>
                        <input type="text" name="reservation" id="reservation" style="margin-left:25px;height:30px;border:0px;"/>
                        <span style="float:right;">
                            <a id="download" href="javascript:void(0);">下载</a>
                        </span>
                    </div>
                    <div class="col-xs-12" id="echarts-source" style="margin-top:50px;height:500px;"></div>
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
	get_series();
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
    var url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem=source';
    $.ajax({
        type: 'GET',
        url: url,
        data: {
        	time_type:time_type,
        	reservation:reservation
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
            console.log(data.data);
            // 基于准备好的dom，初始化echarts实例
            var myChart = echarts.init(document.getElementById('echarts-source'));
            // 指定图表的配置项和数据
            option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    type:'scroll',
                },
                xAxis: {
                    type: 'category',
                    data: data.data.category,
                    nameLocation:'end',//坐标轴名称显示位置。
                    axisLabel : {//坐标轴刻度标签的相关设置。
                        interval:0,
                        rotate:"30"
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: data.data.series
            };  
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option,true);
          }else{
            toastr.error(data.msg);
          }        
        },
        error: function(xhr, type){
           toastr.error("未知错误");
        }
    });
}
$('#download').on('click', function(){
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var to_url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem=source&operate=download';
    location.href = to_url+'&time_type='+time_type+'&reservation='+reservation;
});
$('.chartbtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem='+viem;
    location.href = to_url;
})
</script>