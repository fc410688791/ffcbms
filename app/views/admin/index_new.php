<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$yst_date = date('Y-m-d', time()-86400);
$tmd_date = date('Y-m-d', time());
?>
<style type="text/css">@media (min-width: 1200px) {  .col-lg-3 { width: 20% !important; }  }</style>
<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-body">
            <?php if (in_array(158, $roles)){ ?>
            <div class='row'>
                <h3 style='padding-left: 10px;'><i class='fa fa-fw fa-columns'></i>平台数据</h3>
                <div>
                    <!--设备总数-->
                    <div class="col-lg-6 col-md-4 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner"><h3><?PHP echo $res_order_all_num ?></h3><p>按摩椅总订单数</p></div>
                            <div class="icon"><i class="fa fa-fw fa-file-text-o"></i></div>
                        </div>
                    </div>
                    <!--投放总数-->
                    <div class="col-lg-6 col-md-4 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner"><h3><?PHP echo $res_order_all_cash ?></h3><p>按摩椅订单总收入</p></div>
                            <div class="icon"><i class="fa fa-fw fa-cny"></i></div>
                        </div>
                    </div>  
                </div>
            </div>
            <?php } ?>
            
            <?php if (in_array(159, $roles)){ ?>
            <div>
                <h3><i class='fa fa-fw fa-line-chart'></i>增长情况</h3>
                <div class='row box-body-row'>
                    <!-- 时间筛选 -->
                    <input type="text" name="reservation" id="reservation" class="form-control pull-right" value="<?php echo isset($reservation) ? $reservation : ''; ?>" style='width: 200px;'/>
                    <!-- 类型筛选 -->
                    <input type="hidden" name="restype" id="restype" value="by_res_tp">
                    <div class="btn-group">
                        <button class="btn <?PHP echo $restype == 'by_res_tp' ? 'btn-primary' : 'btn-default'; ?> chartbtn" data-tp="by_res_tp">收入情况</button>
                        <button class="btn <?PHP echo $restype == 'by_res_dt' ? 'btn-primary' : 'btn-default'; ?> chartbtn" data-tp="by_res_dt">用户增长情况</button>
                    </div>
                </div>
                <div class="nav-tabs-custom">
                  <div class="tab-content no-padding">
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="userRiseChart" style="position: relative; height: 300px;"></div>
                  </div>
                </div>
            </div>
            <?php } ?>

            
        </div>
    </div>
</section>

<!-- Raphael Javascript是一个 Javascript的矢量库 -->
<script src='<?php echo $assets_dir ?>/bower_components/raphael/raphael.min.js'></script>
<!-- morris.js 折线图 -->
<script src='<?php echo $assets_dir ?>/bower_components/morris.js/morris.min.js'></script>

<script type="text/javascript">
    $(function () {
        // 站点信息
        get_loc(0);
        //图标按钮
        $('.chartbtn').click(function(){
            var $tis = $(this);
            $('#restype').val($tis.attr('data-tp'));
            $tis.parent().find('.btn').removeClass('btn-primary').addClass('btn-default');
            $tis.addClass('btn-primary');
            getChart();
        });
        // 时间筛选
        $('#reservation').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: '确认',
                cancelLabel: '取消',
                daysOfWeek: ['六', '日', '一', '二', '三', '四', '五'],
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                firstDay: 1,
            },
        }).on('apply.daterangepicker', function (ev, picker) {
            // 情况图表信息
            $('#userRiseChart').empty();
            getChart();
        });
        getChart();
    });

    function getChart() {
        var $type = $('#restype').val();
        var $reservation = $('#reservation').val();
        $('#userRiseChart').html('');
        $.ajax({
            type: "GET",
            url: "<?php echo base_url('admin/index') ?>",
            dataType: 'json',
            data: {
                'method': $type,
                'reservation': $reservation
            },
            success: function (data) {
                if (data.code == 200) {
                    var $chartDate = data.data;

                    Morris.Line({
                        element: 'userRiseChart',
                        resize: true,
                        data: $chartDate,
                        xkey: 'x_k',
                        ykeys: ['y_k'],
                        labels: ($type == 'by_res_dt' ? ['用户数量'] : ['收入金额'])
                    });
                } else {
                    alert(data.msg);
                }
            },
            error: function () {
                alert('图表: 未知错误');
            },
            complete: function () {

            }
        });
    }
    
    function get_loc($p) {
        if ($p <= 2) {
            $("#dev_sel_2").html('');
        }
        if ($p <= 1) {
            $("#dev_sel_1").html('');
        }

        var $pid = $("#dev_sel_" + ($p - 1)).val();
        $.ajax({
            url: "<?php echo base_url("admin/index") ?>",
            type: 'GET',
            dataType: 'json',
            data: {'method':'get_city', 'p': $pid},
            success: function (result) {
                if (result['code'] == 200) {
                    var $data = result['data'];
                    if ($data.length) {
                        for (var $d in $data) {
                            var $item = $data[$d];
                            $('<option value="' + $item['id'] + '">' + $item['name'] + '</option>').appendTo("#dev_sel_" + $p);
                        }
                        if ($p == 1) {
                            get_iloc('station', 2);
                        } else {
                            get_loc($p + 1);
                        }
                    }
                    clear_tr();
                }else{
                    alert('失败');
                }
                
            },
            error: function () {
                alert('出错');
            },
            complete: function () {
                //                $order_tout = setTimeout(get_order,$("#order_time").val()*1000);
            }
        });
    }
    
    // 获得省-市对应的站点/厅/区域/投站 数据
    function get_iloc($tp, $p) {
        if ($p <= 2) {
            $("#dev_sel_2").html('');
        }
        
        var $pid = $("#dev_sel_" + ($p - 1)).val();
        if(!$pid || $pid < 1){return;}
        $.ajax({
        url: "<?php echo base_url("admin/index") ?>",
        type: 'GET',
        dataType: 'json',
        data: {'method':'get_iloc', 'tp': $tp,'s':$pid},
        success: function (result) {
            if (result['code'] == 200) {
                var $data = result['data'];
                var $sel = $("#dev_sel_" + $p);
                if ($data.length) {
                    for (var $d in $data) {
                        var $item = $data[$d];
                        $('<option value="' + $item['id'] + '">' + $item['name'] + '</option>').appendTo($sel);
                    }
                }
                else {
                    $('<option value="-1">暂无</option>').appendTo($sel);
                    $('.tb_tt').removeClass('hide');
                }
                
                $sel.change();
                clear_tr();
            }else{
                alert('失败');
            }
            
        },
        error: function () {
            alert('出错');
        },
        complete: function () {
        //                $order_tout = setTimeout(get_order,$("#order_time").val()*1000);
        }
        });
    }

    // 获得站点数据
    function get_dev() {
        var $sid = $("#dev_sel_2").val();
        if (!$sid || $sid < 1) {
            return;
        }

        // 清空表中中的数据
        clear_tr();

        $.ajax({
            url: "<?php echo base_url("admin/index") ?>",
            type: 'GET',
            dataType: 'json',
            data: {'method':'get_index_site', 'sid': $sid},
            success: function (result) {
                if (result['code'] == 200) {
                    var item = result.data;

                    var html = '';
                    for (var index in item)
                    {
                        var dt = item[index];
                        html = html + '<tr><td>'+index+'</td><td>'+dt['succ_o_num']+'</td><td>'+dt['total_p_num']+'</td><td>'+dt['err_o_num']+'</td><td>'+dt['ref_o_num']+'</td><td>'+dt['ref_p_num_actual']+'</td><td>'+dt['user_num']+'</td><td>'+dt['user_one_num']+'</td></tr>';
                    }
                    $('.sta_data_tb .tb_head').after(html);
                    $('.tb_tt').addClass('hide');
                } else {
                    alert(result.msg);
                }
            },
            error: function () {
                alert('获取站点信息: 未知错误');
            },
            complete: function () {
            }
        });
    }

    // 清空表中中的数据
    function clear_tr()
    {
        $('.tb_head').nextAll().remove();// $(".sta_data_tb tr[class != tb_head]").remove();
    }

</script>