<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">          
            <div class="box">
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberActivityReceive/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="用户ID/卡劵号" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MemberActivityReceive/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select id='receive_status' name="receive_status" class="form-control" style="width:150px;">
                                        <option value=''>卡券使用状态</option>
                                        <?php foreach($receive_status_option as $key => $value){ ?>
                                        <option value='<?php echo $key ?>' <?php echo isset($receive_status)? ($receive_status == $key)? 'selected':'':''; ?> ><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/MemberActivityReceive/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>用户ID</td>
                                <td>卡劵类型名称</td>
                                <td>卡劵号</td>
                                <td>订单编号</td>
                                <td>商品信息</td>
                                <td>生效时间范围</td>
                                <td>得到卡劵时间</td>
                                <td>状态</td>
                                <td>客户使用时间</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><a href="<?php echo base_url('Member/index').'?key='.$info['uuid'] ?>"><?php echo $info['uuid']; ?></a></td>
                                <td><?php echo $info['card_name'].'('.$info['card_type'].')'; ?></td>
                                <td><?php echo $info['activity_id']; ?></td>
                                <td><a href="<?php echo base_url('Order/index').'?key='.$info['out_trade_no'] ?>"><?php echo $info['out_trade_no']; ?></a></td>
                                <td><?php echo $info['product']; ?></td>
                                <td><?php echo $info['time_frame']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['receive_status']; ?></td>
                                <td><?php echo $info['use_time']; ?></td>
                            </tr>
                        <?php endforeach;?>
                        <tfoot>
                        <tr>
                            <th colspan="11"><?= $pagination; ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>