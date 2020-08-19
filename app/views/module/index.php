<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>模块名</th>
              <th>模块链接</th>
              <th>排序</th>
              <th>是否在线</th>
              <th>描述</th>
              <th>图标</th>
              <th width="100px">操作</th>
            </tr>
            </thead>
            <tbody>
            
            <div class='row'>
              <div class="col-md-2 form-group">
                <div class="control-group">
                  <div class="input-prepend input-group">
                  
                    <?php if (in_array(9, $roles)){ ?>
                      <a class="btn btn-primary" href="<?php echo base_url('module/add') ?>" ><i class='fa fa-fw fa-plus-square'></i> 添加模块</a>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            
            <?php foreach($modules as $data){ ?>
            <tr>
              <td><?php echo $data['module_name'] ?></td>
              <td><?php echo $data['module_url'] ?></td>
              <td><?php echo $data['module_sort'] ?></td>
              <td><?php echo ($data['online'] == 1)?'<span class="label label-success">在线</span>':'<span class="label label-warning">不在线</span>'; ?></td>
              <td><?php echo $data['module_desc'] ?></td>
              <td><i class="fa fa-fw <?php echo $data['module_icon'] ?>"></i></td>
              <td>
                <?php if (in_array(10, $roles)){ ?>
                  <a href="<?php echo base_url("module/list")."?module_id={$data['module_id']}" ?>"><i class='fa fa-fw fa-list-ol'></i></a>&nbsp;
                <?php } ?>
                
                <?php if (in_array(11, $roles)){ ?>
                  <a href="<?php echo base_url("module/modify")."?module_id={$data['module_id']}" ?>"><i class='fa fa-fw fa-pencil-square-o'></i></a>&nbsp;
                <?php } ?>
                
                <?php if (in_array(12, $roles)){ ?>
                  <a href="javascript:;" title= "删除模块"><i class='fa fa-fw fa-trash-o' data-id="<?php echo $data['module_name'] ?>" data-href="<?php echo base_url('module/del')."?module_id={$data['module_id']}"; ?>"></i></a>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>

            <tfoot>
              <tr>
                <th colspan="11"><?php echo $pagination; ?></th>
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
<!-- /.content -->

<?php echo $del_confirm; ?>
