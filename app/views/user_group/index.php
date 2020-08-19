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
              <th>角色名</th>
              <th>所有者</th>
              <th>成员列表</th>
              <th width="80px">操作</th>
            </tr>
            </thead>
            <tbody>
            
            <div class='row'>
              <div class="col-md-2 form-group">
                <div class="control-group">
                  <div class="input-prepend input-group">

                    <?php if (in_array(23, $roles)){ ?>
                      <a class="btn btn-primary" href="<?php echo base_url('user_group/add') ?>" ><i class='fa fa-user-plus'></i> 添加角色</a>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            
            <?php foreach($user_groups as $group){ ?>
            <tr>
              <td><?php echo $group['group_name']; ?></td>
              <td><?php echo $user_option[$group['owner_id']]; ?></td>
              <td>
                <?php if (in_array(24, $roles)){ ?>
                  <a href="<?php echo base_url('user_group/list')."?group_id={$group['group_id']}&group_name={$group['group_name']}"; ?>"><i class='fa fa-list-ol'></i></a>
                <?php } ?>
              </td>
              <td>
                <?php if (in_array(25, $roles)){ ?>
                  <a href="<?php echo base_url("user_group/modify")."?group_id={$group['group_id']}" ?>"><i class='fa fa-pencil-square-o'></i></a>&nbsp;
                <?php } ?>

                <?php if( $group['group_id'] != 1){ ?>
                  <?php if (in_array(26, $roles)){ ?>
                    <a href="javascript:;" title= "删除角色"><i class='fa fa-trash-o' data-id="<?php echo $group['group_name'] ?>" data-href="<?php echo base_url('user_group/del')."?group_id={$group['group_id']}"; ?>"></i></a>
                  <?php } ?>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>

      			<tfoot>
              <th colspan="11"><?php echo $pagination ?></th>
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




