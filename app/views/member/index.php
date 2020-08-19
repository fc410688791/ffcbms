<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Member/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="用户ID/昵称/手机号" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>用户ID</td>
                                <td>昵称</td>
                                <td>手机号</td>
                                <td>总剩余充币数(个)</td>
                                <td>性别</td>
                                <td>注册地点</td>
                                <td>最初来源</td>
                                <td>终端类型</td>
                                <td>注册时间</td>
                                <!-- <td>操作</td> -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['uuid']?></td>
                                <td><?= $info['nickname']?></td>
                                <td><?= $info['mobile']?></td>
                                <td><a href="<?php echo base_url('MemberCurrencyRecord/index').'?key='.$info['uuid'] ?>"><?= $info['currency_balance']?></a></td>
                                <td><?= $info['gender']?></td>
                                <td><?= $info['p_name']?></td>
                                <td><?= $info['client_type']?></td>
                                <td><?= $info['device_type']?></td>
                                <td><?= $info['create_time']?></td>
                                <!-- <td>
                                  <a href="javascript:;" title='查看详情' data-id="<?php echo $info['uuid']; ?>" class="edit"><i class='fa fa-fw fa-file-text-o'></i></a>
                                </td> -->
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
<script>
</script>