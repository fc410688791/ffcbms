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
                <div class="box-body table-responsive">
                    <h3 style="margin:30px 0;">交易分析</h3>
                    <div class="btn-group">
                        <span class="btn btn-default chartbtn" data-viem='behavior'>指标对比</span>
                        <span class="btn btn-primary chartbtn" data-viem='behavior_time'>时间对比</span>
                    </div>
                    <div style="margin-top:25px;" class="col-xs-12">
                        <select name="tag" id="tag" style="height:30px;">
                            <option value="1">打开次数</option>
                            <option value="2">访问人数</option>
                            <option value="3">支付笔数</option>
                            <option value="4">支付金额</option>
                            <option value="5">支付人数</option>
                        </select>
                        <select name="time_type" id="time_type" style="height:30px;margin-left:25px;">
                            <option value="1">最近7天</option>
                            <option value="2">最近30天</option>
                            <option value="3">自定义</option>
                        </select>
                        <input type="text" name="reservation" id="reservation" style="margin-left:25px;height:30px;border:0px;"/>
                        <span style="float:right;">
                            <a id="download" href="javascript:void(0);">下载</a>
                        </span>
                    </div>
                    <div class="col-xs-12" id="echarts-time" style="margin-top:50px;height:500px;"></div>
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
$("#tag").on('change',function(){
	var tag = $(this).val();
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
	var tag = $("#tag").val();
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var url = '<?php echo base_url('TransactionAnalysis/index');?>'+'?viem=behavior_time';
    $.ajax({
        type: 'GET',
        url: url,
        data: {
        	tag:tag,
        	time_type:time_type,
        	reservation:reservation
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
            console.log(data.data);
            // 基于准备好的dom，初始化echarts实例
            var myChart = echarts.init(document.getElementById('echarts-time'));
            var tag_name = $("#tag").find("option:selected").text();
            // 指定图表的配置项和数据
            var colors = ['#5793f3', '#d14a61'];
            option = {
                color: colors,
                tooltip: {
                    trigger: 'none',
                    axisPointer: {
                        type: 'cross'
                    }
                },
                legend: {
                    data:[data.data.label1+' '+tag_name, data.data.label2+' '+tag_name]
                },
                grid: {
                    top: 70,
                    bottom: 50
                },
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            alignWithLabel: true
                        },
                        axisLine: {
                            onZero: false,
                            lineStyle: {
                                color: colors[1]
                            }
                        },
                        axisPointer: {
                            label: {
                                formatter: function (params) {
                                    return tag_name + params.value
                                        + (params.seriesData.length ? '：' + params.seriesData[0].data : '');
                                }
                            }
                        },
                        data: data.data.category1
                    },
                    {
                        type: 'category',
                        axisTick: {
                            alignWithLabel: true
                        },
                        axisLine: {
                            onZero: false,
                            lineStyle: {
                                color: colors[0]
                            }
                        },
                        axisPointer: {
                            label: {
                                formatter: function (params) {
                                    return tag_name + params.value
                                        + (params.seriesData.length ? '：' + params.seriesData[0].data : '');
                                }
                            }
                        },
                        data: data.data.category2
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name:data.data.label2+' '+tag_name,
                        type:'line',
                        xAxisIndex: 1,
                        smooth: true,
                        data: data.data.data2
                    },
                    {
                        name:data.data.label1+' '+tag_name,
                        type:'line',
                        smooth: true,
                        data: data.data.data1
                    }
                ]
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
	var tag = $("#tag").val();
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var to_url = '<?php echo base_url('TransactionAnalysis/index');?>'+'?viem=behavior_time&operate=download';
    location.href = to_url+'&tag='+tag+'&time_type='+time_type+'&reservation='+reservation;
});
$('.pagebtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('TransactionAnalysis/index');?>'+'?viem='+viem;
    location.href = to_url;
});
$('.chartbtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('TransactionAnalysis/index');?>'+'?viem='+viem;
    location.href = to_url;
});
</script>