<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">  
      <div class="box-body" style="padding-left:0px;">
          <form id="activeRetentionForm" class="" method="GET">
              <div class="col-lg-3" style="padding-left:0px;">
                <div class="control-group">
                  <div class="controls">
                    <div class="input-prepend input-group">
                      <span class="add-on input-group-addon">时间</span>
                      <input type="text" name="reservation" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2">
                <div class="control-group">
                  <div class="controls">
                    <div class="input-prepend input-group">
                        <select id='pay_type' name="pay_type" class="form-control">
                            <option value=''>支付类型</option>
                            <?php foreach( $pay_type_option as $ptk => $ptv){ ?>
                              <option value="<?php echo $ptk ?>" <?php echo isset($pay_type)? ($pay_type == $ptk)?'selected':'':''; ?> ><?php echo $ptv ?></option>
                            <?php } ?>
                        </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2">
                <div class="control-group">
                  <div class="controls">
                    <div class="input-prepend input-group">
                        <button id="search-submit" type="button" class="btn btn-success">查询</button>&nbsp;<a href="/Finance/index" class="btn btn-default">重置</a>
                    </div>
                  </div>
                </div>
              </div>
          </form>
      </div>
      <!-- /.box -->
      
      <h3>数据总览</h3>
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>总订支付单(个)</p>
              <h3><?php echo $order_count['order_count'];?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>总付款金额(元)</p>
              <h3><?php echo round($order_count['order_sum'],2);?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>总退款订单(个)</p>
              <h3><?php echo $refund_count['order_count'];?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>总退款金额(元)</p>
              <h3><?php echo round($refund_count['order_sum'],2);?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>净收入总额(元)</p>
              <h3><?php $income=$order_count['order_sum']-$refund_count['order_sum'];echo round($income,2);?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>银行入账金额(元)</p>
              <h3><?php echo round($income*0.994,2);?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>不含税总收入(元)</p>
              <h3><?php echo round($income/1.06,2);?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>总税额(元)</p>
              <h3><?php echo round($income/1.06*0.06,2);?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <p>手续费(元)</p>
              <h3><?php echo round($income*0.006,2);?></h3>
            </div>
          </div>
        </div>
      <div class="row" style="padding:0px 15px;">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
                <h3 class="box-title" style="font-size: 24px;">月账单</h3>
                <div class="box-tools">
                    <div class="input-group input-group-sm" style="width: 50px;">
                      <div class="input-group-btn">
                          <a id="output"  class="btn btn-success">导出</a>
                      </div>
                    </div>
                </div>
            </div>
            
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                          <th style="line-height: 30px;text-align: center;">日期</th>
                          <th style="line-height: 30px;text-align: center;">总订单数(个)</th>
                          <th style="line-height: 30px;text-align: center;">总收入(元)</th>
                          <th style="line-height: 30px;text-align: center;">退款订单(个)</th>
                          <th style="line-height: 30px;text-align: center;">退款金额(元)</th>
                          <th style="line-height: 30px;text-align: center;">净收入总额(元)</th>
                          <th style="line-height: 30px;text-align: center;">银行入账金额(元)</th>
                          <th style="line-height: 30px;text-align: center;">不含税净收入总额(元)</th>
                          <th style="line-height: 30px;text-align: center;">税率</th>
                          <th style="line-height: 30px;text-align: center;">总税额(元)</th>
                          <th style="line-height: 30px;text-align: center;">手续费(元)</th>
                          <th style="line-height: 30px;text-align: center;">操作</th>
                        </tr>
                        <?php foreach($order_data as $k=>$v){ ?>
                        <tr style="text-align: center;">
                          <td><?php echo $k ?></td>
                          <td><?php echo $v['order_count']['order_count']; ?></td>
                          <td><?php echo round($v['order_count']['order_sum'],2); ?></td>
                          <td><?php echo $v['refund_count']['order_count']; ?></td>
                          <td><?php echo round($v['refund_count']['order_sum'],2); ?></td>
                          <td><?php echo round(($v['order_count']['order_sum']-$v['refund_count']['order_sum']),2); ?></td>
                          <td><?php echo round(($v['order_count']['order_sum']-$v['refund_count']['order_sum'])*0.994,2); ?></td>
                          <td><?php echo round(($v['order_count']['order_sum']-$v['refund_count']['order_sum'])/1.06,2); ?></td>
                          <td>6%</td>
                          <td><?php echo round(($v['order_count']['order_sum']-$v['refund_count']['order_sum'])/1.06*0.06,2); ?></td>
                          <td><?php echo round(($v['order_count']['order_sum']-$v['refund_count']['order_sum'])*0.006,2); ?></td>
                          <td>
                            <div class="input-group input-group-sm" style="width: 50px;">
                              <div class="input-group-btn">
                                  <a class="btn" href="/Finance/outputOrderExcel?date=<?php echo $k?>&pay_type=<?php echo $pay_type ?>">导出</a>
                              </div>
                            </div>
                          </td>
                        </tr>
                        <?php } ?>
                  </tbody>
              </table>
                
            </div>
            <!-- /.box-body -->
          </div>
        </div>
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->  
</section>
<!-- /.content -->

<script>
$(function(){     
	$('#reservation').daterangepicker({
		minDate : "2019-04-01", //最小时间
		maxDate : moment(), //最大时间
        dateLimit : {
            days : 30
        }, //起止时间的最大间隔
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        }
    });
    
    var $Form =  $('#activeRetentionForm');
    $('#search-submit').on("click",function(){
        $Form.attr("action","/Finance/index");
        $Form.submit();
    });

    $('#output').on("click",function(){
        $Form.attr("action","/Finance/outputExcel");
        $Form.submit();
    });
});
</script>
