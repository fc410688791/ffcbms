<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <table id="example1" class="table table-bordered table-hover">
            <thead>
            <tr>
              <th >key_name</th>
              <th >key_value</th>
              <th style="width:80px">操作</th>
            </tr>
            </thead>
            <tbody>
            
            <div class='row'>
              <div class="col-md-2 form-group">
                <div class="control-group">
                  <div class="input-prepend input-group">

                    <?php if (in_array(16, $roles)) { ?>
                      <a class="btn btn-primary" href="<?php echo base_url('sys/add') ?>" ><i class='fa fa-fw fa-plus-square'></i> 添加系统默认值</a>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            
            <?php foreach($systems as $data){ ?>
              <tr>
                <td><?php echo $data['key_name']; ?></td>
                <td><?php echo $data['key_value']; ?></td>
                <td>
                  <?php if (in_array(17, $roles)) { ?>
                   <a href="<?php echo base_url("sys/modify")."?key_name={$data['key_name']}" ?>"><i class='fa fa-fw fa-pencil-square-o'></i></a>&nbsp;
                  <?php } ?>
          
                  <?php if (in_array(18, $roles)) { ?>
                    <?php if ($data['key_name'] != 1){ ?>
                    <a href="javascript:;" title= "删除默认值"><i class='fa fa-fw fa-trash-o' data-id="<?php echo $data['key_name'] ?>" data-href="<?php echo base_url('sys/del')."?key_name={$data['key_name']}"; ?>"></i></a>
                    <?php } ?>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>

      			<tfoot>
              <th colspan="11"><?php echo $pagination; ?></th>
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
<!-- /.content -->

<?php echo $del_confirm; ?>




