<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">   
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>收货人</td>
                                <td>手机号码</td>
                                <td>收货地址</td>
                                <td>默认地址</td>
                                <td>创建时间</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $k=>$info): ?>
                            <tr>
                               <td><?php echo $k+1;?></td>
                               <td><?php echo $info['name'];?></td>
                               <td><?php echo $info['mobile'];?></td>
                               <td><?php echo $info['position'];?></td>
                               <td><?php echo $info['is_default'];?></td>
                               <td><?php echo $info['create_time'];?></td>
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