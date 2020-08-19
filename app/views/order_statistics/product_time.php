<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script src="<?php echo $assets_dir; ?>/js/echarts.min.js"></script>
<style type="text/css">
.product_name{
	margin:0 10px;
	cursor:pointer;
	background:#ccc;
	padding: 5px;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-title">
                    <div class='with-border'>
                        <div class="btn-group menuBar">
                            <span class="btn btn-primary pagebtn" data-viem='product'>商品数据</span>
                            <span class="btn btn-default pagebtn" data-viem='charge'>充值数据</span>
                            <span class="btn btn-default pagebtn" data-viem='refund'>客诉数据</span>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <h3>充电数据</h3>
                    <div class="btn-group">
                        <span class="btn btn-default chartbtn" data-viem='product'>充电分布</span>
                        <span class="btn btn-primary chartbtn" data-viem='product_time'>充电数据</span>
                    </div>
                    <div style="margin-top:25px;" class="col-xs-12">
                        <select id="product_id" style="height:30px;width:150px;">
                            <option value="">请选择</td>
                            <?php foreach($product_option as $pk => $pv){ ?>
                                <option value="<?php echo $pk ?>"><?php echo $pv; ?></td>
                            <?php }?>
                        </select >
                    </div>
                    <div id="tag-div" class="col-xs-12" style="margin:15px 0px;width:80%;"></div>
                    <div class="col-xs-12">
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
$("#product_id").on('change',function(){
	var product_id = $(this).find("option:selected").val();
	if(product_id){
		var tag = $('#product_'+product_id).html();
		if(!tag){
			var length = $("#tag-div > .product_name").length;
			if(length>4){
				toastr.warning("请不要选择太多对比项！"); 
		    	return;
			}
			var product_name = $(this).find("option:selected").text();
			var html = '<sapn id="product_'+product_id+'" title="删除" class="product_name" data-id="'+product_id+'">'+product_name+'</sapn>'
			$('#tag-div').append(html);
			get_series();
		}else{
			toastr.warning("已选择！"); 
	    	return;
		}
	}	
});
$("#tag-div").delegate(".product_name","click",function(){
	$(this).remove();
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
    var url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem=product_time';
    var tag = [];
    $('#tag-div .product_name').each(function(index){
    	var id = $(this).data('id');
    	tag.push(id);
    });
    if(tag.length>0){
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
                var myChart = echarts.init(document.getElementById('echarts-product'));
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
    }else{
    	var myChart = echarts.init(document.getElementById('echarts-product'));
    	var option = {};
    	myChart.setOption(option,true);
    }
}
$('#download').on('click', function(){
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
	var to_url = '<?php echo base_url('OrderStatistics/index');?>'+'?viem=product_time&operate=download';
	var tag = [];
    $('#tag-div .product_name').each(function(index){
    	var id = $(this).data('id');
    	tag.push(id);
    });
    if(tag.length>0){
    	location.href = to_url+'&tag='+tag+'&time_type='+time_type+'&reservation='+reservation;
    }else{
    	toastr.error('未选择商品！');
    }
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