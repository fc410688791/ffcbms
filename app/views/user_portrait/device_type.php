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
                    <h3>终端类型</h3>
                    <div style="margin-top:25px;" class="col-xs-12">
                        <select name="time_type" id="time_type" style="height:30px;">
                            <option value="1">最近7天</option>
                            <option value="2">最近30天</option>
                            <option value="3">自定义</option>
                        </select>
                        <input type="text" name="reservation" id="reservation" style="margin-left:25px;height:30px;border:0px;"/>
                        <span style="float:right;">
                            <a id="download" href="javascript:void(0);">下载</a>
                        </span>
                    </div>
                    <div class="col-xs-12 col-lg-8" id="echarts-device_type" style="margin-top:50px;height:600px;"></div>
                    <div class="col-xs-12 col-lg-4" id="table-device_type" style="margin-top:50px;height:600px;">
                        <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                            <thead>
                                <tr style="text-align: center;">
                                    <td>机型</td>
                                    <td>用户数</td>
                                    <td>占比</td>
                                </tr>
                            </thead>
                            <tbody id="tb">
                            </tbody>
                        </table>
                    </div>
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
    var url = '<?php echo base_url('UserPortrait/index');?>'+'?viem=device_type';
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
        	$('#tb').html('');
            if(data.code==200){
                var html = '';
            	for(var i=0;i<data.data.series.length;i++){
            		html += "<tr><td>"+data.data.series[i].name+"</td><td>"+data.data.series[i].value+"</td><td>"+(data.data.series[i].value/data.data.count*100).toFixed(2)+"%</td></tr>";
                }
                $('#tb').html(html);
                // 基于准备好的dom，初始化echarts实例
                var myChart = echarts.init(document.getElementById('echarts-device_type'));
                // 指定图表的配置项和数据
                option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b}: {c} ({d}%)"
                    },
                    /* legend: {
                        orient: 'vertical',
                        x: 'left',
                    }, */
                    series: [
                        {
                            name:'用户数',
                            type:'pie',
                            radius: ['50%', '70%'],
                            avoidLabelOverlap: false,
                            label: {
                                normal: {
                                    show: false,
                                    position: 'center'
                                },
                                emphasis: {
                                    show: true,
                                    textStyle: {
                                        fontSize: '30',
                                        fontWeight: 'bold'
                                    }
                                }
                            },
                            labelLine: {
                                normal: {
                                    show: false
                                }
                            },
                            data:data.data.series
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
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var to_url = '<?php echo base_url('UserPortrait/index');?>'+'?viem=device_type&operate=download';
    location.href = to_url+'&time_type='+time_type+'&reservation='+reservation;
});
$('.pagebtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('UserPortrait/index');?>'+'?viem='+viem;
    location.href = to_url;
});
$('.chartbtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('UserPortrait/index');?>'+'?viem='+viem;
    location.href = to_url;
});
</script>