<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script src="<?php echo $assets_dir; ?>/js/echarts.min.js"></script>
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
.blue{
	color:blue;
}
</style>
<section class="content">

    <div class="box">
        <div class="box-body table-responsive">
            <div class='col-xs-12'>
                <h3>平台数据</h3>
                <!-- <div style="padding: 0 30px;"> -->
                    <table style="margin-top:30px;width:100%;min-width:800px;text-align:center;">
                        <tr style="height:120px;">
                        <td onclick="jump_to_AgentMerchant()"><div style="height:40px;line-height:40px;">投放点总数<img alt="" style="margin-left:10px;width:20px;height:20px;line-height:40px;" src="<?php echo $assets_dir ?>/img/title.png" title="所有包含设备的投放点总数"></div><div class="data green"><?php echo $merchant_count;?></div></td>
                        <td onclick="jump_to_Agent()"><div style="height:40px;line-height:40px;">代理商总数<img alt="" style="margin-left:10px;width:20px;height:20px;line-height:40px;" src="<?php echo $assets_dir ?>/img/title.png" title="所有代理商总数"></div><div class="data green"><?php echo $agent_count;?></div></td>
                        <td onclick="jump_to_Agent_is_verification(1)"><div style="height:40px;line-height:40px;">已认证代理数<img alt="" style="margin-left:10px;width:20px;height:20px;line-height:40px;" src="<?php echo $assets_dir ?>/img/title.png" title="已认证代理商总数"></div><div class="data green"><?php echo $is_verification_agent_count;?></div></td>
                        <td onclick="jump_to_Agent_proxy_pattern(3)"><div style="height:40px;line-height:40px;">0元代理数<img alt="" style="margin-left:10px;width:20px;height:20px;line-height:40px;" src="<?php echo $assets_dir ?>/img/title.png" title="0元代理总数"></div><div class="data green"><?php echo $proxy_pattern_agent_count;?></div></td>
                        <td onclick="jump_to_Agent_Order()"><div style="height:40px;line-height:40px;">总采购订单数<img alt="" style="margin-left:10px;width:20px;height:20px;line-height:40px;" src="<?php echo $assets_dir ?>/img/title.png" title="已支付订单数"></div><div class="data green"><?php echo $order_count;?></div></td>
                        <td onclick="jump_to_Agent_Order()"><div style="height:40px;line-height:40px;">总采购收入<img alt="" style="margin-left:10px;width:20px;height:20px;line-height:40px;" src="<?php echo $assets_dir ?>/img/title.png" title="总支付金额"></div><div class="data green"><?php echo $fee;?></div></td>
                        </tr>
                    </table>
                <!-- </div> -->
            </div>
            
            <div>
                <div class="col-xs-12">
                    <h3>平台数据</h3>
                    <div class="col-xs-12" style="margin:20px 0px;padding-left:0px;">
                        <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('AgentData/index') ?>'>
                            <div class="control-group">
                              <div class="controls">
                                <div class="input-prepend input-group">
                                  <span class="add-on input-group-addon">时间</span>
                                  <input type="text" name="reservation" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                </div>
                              </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class='row box-body-row'>
                    <div class="col-xs-6" id="echarts-merchant" style="height:500px;min-width: 600px;"></div>
                    <script type="text/javascript">
                        // 基于准备好的dom，初始化echarts实例
                        var myChart = echarts.init(document.getElementById('echarts-merchant'));                             
                        var merchant_name = [];
                        var merchant_cash_fee_statistics = [];
                        var name;
                        var cash_fee_statistics;
                        <?php foreach ($merchant_list as $v){ ?>
                            name = '<?php echo $v['name']; ?>';
                            cash_fee_statistics = '<?php echo $v['cash_fee_statistics']; ?>';                 
                            merchant_name.push(name);
                            merchant_cash_fee_statistics.push(cash_fee_statistics);
                        <?php }?>
                        // 指定图表的配置项和数据
                         option = {
                            title: {
                                    text: '投放点数据'
                            },
                            tooltip: {
                                trigger: 'item',
                                formatter: "{a}：{b} <br/> 流水：{c}"
                            },
                            xAxis: {
                                type: 'category',
                                data: merchant_name,
                                nameLocation:'end',//坐标轴名称显示位置。
                                axisLabel : {//坐标轴刻度标签的相关设置。
                                    interval:0,
                                    rotate:"30"
                                }
                            },
                            yAxis: {
                                type: 'value'
                            },
                            series: [
                                {
                                	name:'投放点',
                                    data: merchant_cash_fee_statistics,
                                    type: 'bar'
                                }
                            ]
                        };  
                        // 使用刚指定的配置项和数据显示图表。
                        myChart.setOption(option);
                    </script>
                    <div class="col-xs-6" id="echarts-scene" style="height:500px;min-width: 600px;"></div>
                    <script type="text/javascript">
                        // 基于准备好的dom，初始化echarts实例
                        var myChart = echarts.init(document.getElementById('echarts-scene'));
                        var scene_list = [];
                        var name_list = [];
                        <?php foreach ($scene_list as $v){ ?>
                            var info = new Object();
                            info.name = '<?php echo $v['name']; ?>';
                            info.value = '<?php echo $v['cash_fee_statistics']; ?>'; 
                            name_list.push(info.name);                     
                            scene_list.push(info);
                        <?php }?>                           
                        // 指定图表的配置项和数据
                         option = {
                            title: {
                                    text: '场景数据'
                            },
                            tooltip: {
                                trigger: 'item',
                                formatter: "{a}：{b} <br/> 流水: {c} ({d}%)"
                            }, 
                            legend: {
                                orient: 'vertical',
                                x: 'right',
                                data:name_list
                            },
                            //color: ['#ffa0f2','#f468e0','#01d2d6','#00a3a3','#fdcb5a','#ff9d44','#569ffd','#2fd5bc','#fe6c69','#ec5453','#5f27ca','#341d99','#47ddfa','#0cbec2','#c9d6e6','#8295aa','#1cd0a1','#10ac85','#596573','#223040','#7498fd','#ff9196'],
                            series: [
                                {
                                    name:'场景',
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
                                    data:scene_list
                                }
                            ]
                        };  
                        // 使用刚指定的配置项和数据显示图表。
                        myChart.setOption(option);
                    </script> 
                </div>
                <div class="nav-tabs-custom">
                  <div class="tab-content no-padding">
                  </div>
                </div>
            </div>

            
        </div>
    </div>
</section>

<!-- datepicker 单个日期选择 -->
<script src='<?php echo $assets_dir ?>/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js'></script>
<script type="text/javascript">
$(function () {
	$('#reservation').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        },
    }).on('apply.daterangepicker', function(ev, picker) {
    	$('#activeRetentionForm').submit();
    });
});

function jump_to_AgentMerchant() {
    var to_url = '<?php echo base_url('AgentMerchant/index'); ?>';
    location.href = to_url;
}

function jump_to_Agent() {
    var to_url = '<?php echo base_url('Agent/index'); ?>';
    location.href = to_url;
}

function jump_to_Agent_is_verification(is_verification) {
    var to_url = '<?php echo base_url('Agent/index'); ?>'+'?is_verification='+is_verification;
    location.href = to_url;
}

function jump_to_Agent_proxy_pattern(proxy_pattern) {
    var to_url = '<?php echo base_url('Agent/index'); ?>'+'?proxy_pattern='+proxy_pattern;
    location.href = to_url;
}

function jump_to_Agent_Order() {
    var to_url = '<?php echo base_url('AgentOrder/index'); ?>';
    location.href = to_url;
}
</script>