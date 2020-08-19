<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        
        <div class="box-header with-border">
            <?PHP if (in_array(13, $roles)) { ?>
                <a class="btn btn-primary" href="<?php echo base_url('menu_url/add') ?>"><i class='fa fa-fw fa-plus-square'></i> 添加功能</a>
            <?PHP } ?>
        </div>
        <!-- /.box-header -->

        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-hover">
            <thead>
            <tr>
              <th style="width:10px">#</th>
              <th style="width:90px">功能名称</th>
              <th style="width:180px">功能链接</th>
              <th style="width:80px">所属模块</th>
              <th style="width:80px">是否菜单</th>
              <th style="width:80px">所属菜单</th>
              <th style="width:80px">是否在线</th>
              <th style="width:180px">描述</th>
              <th style="width:20px">操作</th>
            </tr>
            </thead>
            <tbody>

            <div class="row box-body-row">
          		<form id="activeRetentionForm" class="form-horizontal form-label-left" method="GET">
                <div class="pull-left form-group">
                  <div class="box-tools">
                    <div class="input-group" style="width: 250px;">
                      <input type="text" name="like_field" class="form-control pull-left " placeholder="功能名称/功能链接" value="<?php echo $form['like_field'] ?>">
                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="pull-right form-group" >
                  <div class="control-group">
                    <div class="input-prepend input-group">
                      <select class="form-control" name="module_id" style='width:250px;' onchange="$('#search').click();">
                        <option value="">所有模块</option>
                        <?php foreach($module_name_option as $k => $v){ ?>
                        <option value="<?php echo $k ?>" <?php echo ($form['module_id'] == $k)? 'selected': ''; ?>><?php echo $v ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

          		</form>
          	</div>

            <?php foreach($menu_urls as $data){ ?>
            <tr>
              <td><?php echo $data['menu_id'] ?></td>
              <td><?php echo $data['menu_name'] ?></td>
              <td><?php echo $data['menu_url'] ?></td>
              <td><?php echo $module_name_option[$data['module_id']] ?></td>
              <td><?php echo ($data['is_show'] == 1)?'是':'否'; ?></td>
              <td><?php echo $data['father_menu']? $menu_name_option[$data['father_menu']]:'无'; ?></td>
              <td><?php echo ($data['online'] == 1)?'<span class="label label-success">在线</span>':'<span class="label label-warning">不在线</span>'; ?></td>
              <td><?php echo $data['menu_desc'] ?></td>
              <td>
                
                <?php if (in_array(14, $roles)) { ?>
                  <a href="<?php echo base_url("menu_url/modify")."?menu_id={$data['menu_id']}" ?>"><i class='fa fa-fw fa-pencil-square-o'></i></a>&nbsp;
                <?php } ?>

                <?php if (in_array(15, $roles)) { ?>
                  <a href="javascript:;" title= "删除功能"><i class='fa fa-fw fa-trash-o' data-id="<?php echo $data['menu_name'] ?>" data-href="<?php echo base_url('menu_url/del')."?menu_id={$data['menu_id']}"; ?>"></i></a>
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
<?php //echo $pause_confirm; ?>
<?php //echo $play_confirm; ?>
<?php echo $del_confirm; ?>