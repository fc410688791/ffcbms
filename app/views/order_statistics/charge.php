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
                    <span class="btn btn-primary pagebtn" data-viem='charge'>充值数据</span>
                    <span class="btn btn-default pagebtn" data-viem='refund'>客诉数据</span>
                </div>
                <div class="box-body table-responsive">
                    <h3>充值分析</h3>
                    <div class="btn-group">
                        <span class="btn btn-primary chartbtn" data-viem='charge'>充值分布</span>
                        <span class="btn btn-default chartbtn" data-viem='charge_time'>充值数据</span>
                    </div>
                    <div style="margin-top:25px;" class="col-xs-12">
                        <select id="time_type" name="time_type" style="height:30px;">
                            <option value="1">最近7天</option>
                            <option value="2">最近30天</option>
                            <option value="3">自定义</option>
                        </select>
                        <input id="reservation" name="reservation" style="margin-left:25px;height:30px;border:0px;" type="text"/>
                        <span style="float:right;">
                            <a id="download" href="javascript:void(0);">下载</a>
                        </span>
                    </div>
                    <div class="col-xs-12" id="echarts-product" style="margin-top:50px;height:500px;"></div>
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
    var url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem=charge';
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
            var myChart = echarts.init(document.getElementById('echarts-product'));
            // 指定图表的配置项和数据
            option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                },
                series: [
                    {
                        name:'充值',
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
    var to_url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem=charge&operate=download';
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