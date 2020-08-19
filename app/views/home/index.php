<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">@media (min-width: 1200px) {  .col-lg-3 { width: 20% !important; }  }</style>
<!-- Main content -->
<style type="text/css">
.data{
	font-size:24px;
	color:green;
	margin:20px 0px;
}
.green{
	color:green;
}
.red{
	color:red;
}
.blue{
	color:blue;
}
#untreated a{
	color:black;
}
#untreated a:hover{
	color:blue;
}
</style>
<section class="content">
    <div class="box">
        <div class="box-body" style="background-color:#ECF0F5;padding:0px;">
            <div style="margin-bottom:30px;background-color:#FFF;padding:20px;">
                <h3 style="margin:0px;"><img alt="" src="<?php echo $assets_dir; ?>/img/u89.png"> 待处理</h3>
                <div style="margin-top:10px;width:100%;height:100px;background-color:#F2F2F2;padding:0 20px;">
                    <table id="untreated" style="width:100%;">
                        <tr style="height:50px;">
                            <td>
                                <div style="width:120px;float:left;">
                                    <a href='<?php echo base_url('AgentOrder/index'); ?>?orderSelect=payed'>采购待确认订单：</a>
                                </div>
                                <span style="float:left;<?php if($c1>0){echo 'color:red;';}?>"><?php echo $c1;?></span>
                            </td>

                            <td>
                                <div style="width:130px;float:left;">
                                    <a href='<?php echo base_url('Ageing/index'); ?>?status=2'>设备老化完成故障：</a>
                                </div>
                                <span style="float:left;<?php if($c2>0){echo 'color:red;';}?>"><?php echo $c2;?></span>
                            </td>
                            
                            <td>
                                <div style="width:90px;float:left;">
                                    <a href='<?php echo base_url('MachineOffLine/index'); ?>'>设备离线：</a>
                                </div>
                                <span style="float:left;<?php if($off_line_count>0){echo 'color:red;';}?>"><?php echo $off_line_count;?></span>
                            </td>
                            
                            <td>
                                <div style="width:90px;float:left;">
                                    <a href='<?php echo base_url('Agent/index'); ?>?commission_status=0'>分佣待确定：</a>
                                </div>
                                <span style="<?php if($c3>0){echo 'color:red;';}?>"><?php echo $c3;?></span>
                            </td>
                        </tr>
                        <tr style="height:50px;">
                            <td>
                                <div style="width:120px;float:left;">
                                    <a href='<?php echo base_url('AgentWithdraw/index'); ?>?status=0'>提现待审核订单：</a>
                                </div>
                                <span style="<?php if($c4>0){echo 'color:red;';}?>"><?php echo $c4;?></span>
                            </td>
                            <td>
                                <div style="width:130px;float:left;">
                                    <a href='<?php echo base_url('AgentWithdraw/index'); ?>?status=5'>提现待确定订单：</a>
                                </div>
                                <span style="<?php if($c5>0){echo 'color:red;';}?>"><?php echo $c5;?></span>
                            </td>
                            
                            <td>
                                <div style="width:90px;float:left;">
                                    <a href='<?php echo base_url('CustomerService/index'); ?>'>待退款订单：</a>
                                </div>
                                <span style="<?php if($c6>0){echo 'color:red;';}?>"><?php echo $c6;?></span>
                            </td>
                        </tr>
                    </table>
                    <!-- 
                    <div class="col-xs-3" style="height:100px;">
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('AgentOrder/index'); ?>?orderSelect=payed'>采购待确认订单：</a></div>
                            <div style="float:right;<?php if($c1>0){echo 'color:red;';}?>"><?php echo $c1;?></div>
                        </div>
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('AgentWithdraw/index'); ?>?status=0'>提现待审核订单：</a></div>
                            <div style="float:right;<?php if($c4>0){echo 'color:red;';}?>"><?php echo $c4;?></div>
                        </div>
                    </div>
                    <div class="col-xs-3" style="height:100px;">
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('Ageing/index'); ?>?status=2'>设备老化完成故障：</a></div>
                            <div style="float:right;<?php if($c2>0){echo 'color:red;';}?>"><?php echo $c2;?></div>
                        </div>
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('AgentWithdraw/index'); ?>?status=5'>提现待确定订单：</a></div>
                            <div style="float:right;<?php if($c5>0){echo 'color:red;';}?>"><?php echo $c5;?></div>
                        </div>
                    </div>
                    <div class="col-xs-3" style="height:100px;">
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('MachineOffLine/index'); ?>'>设备离线：</a></div>
                            <div style="float:right;<?php if($off_line_count>0){echo 'color:red;';}?>"><?php echo $off_line_count;?></div>
                        </div>
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('CustomerService/index'); ?>'>待退款订单：</a></div>
                            <div style="float:right;<?php if($c6>0){echo 'color:red;';}?>"><?php echo $c6;?></div>
                        </div>
                    </div>
                    <div class="col-xs-3" style="height:100px;">
                        <div style="height:50px;line-height:50px;">
                            <div style="float:left;"><a href='<?php echo base_url('Agent/index'); ?>?commission_status=0'>分佣待确定：</a></div>
                            <div style="float:right;<?php if($c3>0){echo 'color:red;';}?>"><?php echo $c3;?></div>
                        </div>
                    </div>
                </div>
                -->
            </div>
            <div style="margin-bottom:30px;background-color:#FFF;padding:20px;">
                <h3 style="margin:0px;"><i class='fa fa-fw fa-database'></i>平台数据</h3>
                <table style="margin-top:10px;width:100%;text-align:center;">
                    <tr style="height:120px;">
                    <td onclick="jump_to_machine(2)"><div>待绑定设备</div><div class="data green"><?php echo $dev_count[2];?></div></td>
                    <td onclick="jump_to_machine(3)"><div>待激活设备</div><div class="data green"><?php echo $dev_count[3];?></div></td>
                    <td onclick="jump_to_machine()"><div>设备总数</div><div class="data green"><?php echo $dev_count['dev_sum'];?></div></td>
                    <td onclick="jump_to_machine(1)"><div>运营中设备总数</div><div class="data green"><?php echo $dev_count[1];?></div></td>
                    <td onclick="jump_to_order()"><div>总收入</div><div class="data green"><?php echo $all_order_count['order_sum'];?></div></td>
                    <td onclick="jump_to_order()"><div>总订单数</div><div class="data green"><?php echo $all_order_count['order_count'];?></div></td>
                    </tr>
                    <tr style="height:150px;">
                    <td onclick="jump_to_order()"><div>今日订单总数</div><div class="data blue"><?php echo $today_order_count['order_count'];?></div><div>昨日：<?php echo $yesterday_order_count['order_count'];?></div></td>
                    <td onclick="jump_to_order()"><div>今日总收入</div><div class="data blue"><?php echo $today_order_count[1]['order_sum'];?></div><div>昨日：<?php echo $yesterday_order_count[1]['order_sum'];?></div></td>
                    <td onclick="jump_to_order()"><div>今日未完成订单数</div><div class="data blue"><?php echo $today_order_count[0]['order_count']+$today_order_count[2]['order_count'];?></div><div>昨日：<?php echo $yesterday_order_count[0]['order_count']+$yesterday_order_count[2]['order_count'];?></div></td>
                    <td onclick="jump_to_refund()"><div>今日退款总额</div><div class="data blue"><?php echo $today_order_count ['refund']['order_sum'];?></div><div>昨日：<?php echo $yesterday_order_count ['refund']['order_sum'];?></div></td>
                    <td onclick="jump_to_refund()"><div>今日退款订单数</div><div class="data blue"><?php echo $today_order_count ['refund']['order_count'];?></div><div>昨日：<?php echo $yesterday_order_count ['refund']['order_count'];?></div></td>
                    <td><div>今日客单价</div><div class="data blue"><?php echo $today_order_count[1]['order_sum']?round($today_order_count[1]['order_sum']/$today_order_count[1]['order_count'], 2):0;?></div><div>昨日：<?php echo $yesterday_order_count[1]['order_sum']?round($yesterday_order_count[1]['order_sum']/$yesterday_order_count[1]['order_count'], 2):0;?></div></td>
                    </tr>
                </table>
            </div>
            <div style="background-color:#FFF;padding:20px;">
                <h3 style="margin:0px;margin-bottom:20px;"><i class='fa fa-fw fa-line-chart'></i>平台数据</h3>
                <div>
                    <!-- 时间筛选 -->
                    <input id="reservation" name="reservation" autocomplete="off" class="form-control pull-right" style="width: 100px;" value="<?php echo $reservation; ?>">
                    <div class="btn-group">
                        <button class="btn <?PHP echo $restype == 'by_res_tp' ? 'btn-primary' : 'btn-default'; ?> chartbtn" data-tp="by_res_tp">收入情况</button>
                    </div>
                </div>
                <div class="nav-tabs-custom">
                  <div class="tab-content no-padding">
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="userRiseChart" style="position: relative; height: 400px;"></div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Raphael Javascript是一个 Javascript的矢量库 -->
<script src='<?php echo $assets_dir ?>/bower_components/raphael/raphael.min.js'></script>
<!-- morris.js 折线图 -->
<script src='<?php echo $assets_dir ?>/bower_components/morris.js/morris.min.js'></script>
<!-- <script src="<?php echo $assets_dir; ?>/js/echarts.min.js"></script> -->
<!-- datepicker 单个日期选择 -->
<script src='<?php echo $assets_dir ?>/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js'></script>
<script type="text/javascript">
$(function () {
	//汉化日期插件
    $.fn.datetimepicker.dates['zh-CN'] = {  
        days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],  
        daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],  
        daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],  
        months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],  
        monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],  
        today: "今天",  
        suffix: [],  
        meridiem: ["上午", "下午"]  
    };
    // 时间筛选
    $('#reservation').datetimepicker({
        format: 'yyyy-mm',
        autoclose: true,
        todayBtn: true,
        startView: 'year',
        minView:'year',
        maxView:'decade',
        language: 'zh-CN', 
        startDate: "2019-04-01",
        endDate:new Date(),
        setDate:new Date(),
        defaultSelect:true,
        applyLabel: '确认',
        cancelLabel: '取消',
    }).on('changeDate', function(ev){
        get_order_data();
    });
    //获取初始图表数据
    get_order_data();       
});

function get_order_data() {
    var type = $('#restype').val();
    var reservation = $('#reservation').val();
    $('#userRiseChart').empty();
    $.ajax({
        type: "GET",
        url: "<?php echo base_url('Home/index') ?>",
        dataType: 'json',
        data: {
            'reservation': reservation
        },
        success: function (data) {
            if (data.code == 200) {
                console.log(data);
                var orderDate = data.data;
                Morris.Line({
                    element: 'userRiseChart',
                    resize: true,
                    data: orderDate,
                    xkey: 'x_k',
                    ykeys: ['y_k'],
                    labels: ['收入金额']
                });
                // 基于准备好的dom，初始化echarts实例
                /* var myChart = echarts.init(document.getElementById('userRiseChart'));                             
                var x_data = [];
                var y_data = [];
                for(var i=0;i<orderDate.length;i++){
                	x_data.push(orderDate[i].x_k);
                	y_data.push(orderDate[i].y_k);
                }
                // 指定图表的配置项和数据
                option = {
                		tooltip: {
                            trigger: 'item',
                            formatter: "日期：{b} <br/> 收入：{c}"
                        },
                        color: ['#009AFE'],
                	    xAxis: {
                	        type: 'category',
                	        data: x_data
                	    },
                	    yAxis: {
                	        type: 'value'
                	    },
                	    series: [{
                	        data: y_data,
                	        type: 'line',
                	        smooth: true
                	    }]
                }; 
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option); */
            } else {
                alert(data.msg);
            }
        },
        error: function () {
            alert('图表: 获取数据错误');
        },
        complete: function () {

        }
    });
}

function jump_to_machine(status) {
    var status = status;
    var to_url = '<?php echo base_url('Machine/index'); ?>';
    if(status){
    	to_url +='?status='+status;
    }
    location.href = to_url;
}

function jump_to_order() {
    var to_url = '<?php echo base_url('Order/index'); ?>';
    location.href = to_url;
}
function jump_to_refund() {
    var to_url = '<?php echo base_url('CustomerService/index'); ?>';
    location.href = to_url;
}
</script>