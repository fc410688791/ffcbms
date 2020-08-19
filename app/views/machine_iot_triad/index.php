<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <!-- box-header -->
                <div class="box-header with-border">
                    <h3 class="box-title">导入数据</h3>
                    <!-- form start -->
                    <form role="form" action="<?php echo base_url('MachineIotTriad/import') ?>" method='POST' enctype="multipart/form-data">
                        <div class="box-body">
                            <input  name="file" class="form-control" type="file" accept=".xlsx">
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary form_button"><i class='fa fa-spinner fa-spin hide'></i>提交</button>
                            <a class="btn btn-default" href="">取消</a>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->
    
                <div class='box-body'>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('MachineIotTriad/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 200px;">
                                  <input type="text" name="deviceName" class="form-control pull-left " placeholder="IMEI/deviceName" value="<?php echo ($deviceName = $this->input->get('deviceName'))?$deviceName:''; ?>">
                                  <div class="input-group-btn">
                                      <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>id</td>
                                <td>key</td>
                                <td>name</td>
                                <td>secret</td>
                                <td>创建时间</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['id']?></td>
                                <td><?= $info['product_key']?></td>
                                <td><a class="pointer" href="<?php echo base_url('Machine/index')."?triad_id={$info['id']}"; ?>"><?= $info['device_name']?></a></td>
                                <td><?= $info['device_secret']?></td>
                                <td><?= date('Y-m-d H:i:s', $info['create_time'])?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
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
    </div>
    <!-- /.row -->
</section>