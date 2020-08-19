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
              <th style="width:5%"><input type="checkbox" id="checkAll"> 全选</th>
              <th style="width:30px">#</th>
              <th style="width:90px">功能名称</th>
              <th style="width:180px">功能链接</th>
              <th style="width:80px">所属模块</th>
              <th style="width:80px">是否菜单</th>
              <th style="width:80px">所属菜单</th>
              <th style="width:80px">是否在线</th>
              <th style="width:180px">描述</th>
            </tr>
            </thead>
            <tbody>
            
            <div class='row'>
              <div class="col-md-2 form-group">
                <div class="control-group">
                  <div class="input-prepend input-group">
                    <span><b><i><?php echo $module['module_name'] ?></i> - 功能列表</b></span>
                  </div>
                </div>
              </div>
            </div>
            
            <form method="post" action="<?php echo base_url('module/list'); ?>">

            <!-- 自定义表单验证失败的错误提示 -->
            <?php echo $form_error_contents;?>

            <?php foreach($menus as $data){ ?>
            <tr>
              <td><input type="checkbox" name="menu_ids[]" value="<?php echo $data['menu_id'] ?>" <?php echo (set_value('menu_ids[]') == $data['menu_id'])? 'selected':''; ?> ></td>
              <td><?php echo $data['menu_id'] ?></td>
              <td><?php echo $data['menu_name'] ?></td>
              <td><?php echo $data['menu_url'] ?></td>
              <td><?php echo $data['module_name'] ?></td>
              <td><?php echo ($data['is_show'] == 1)?'是':'否'; ?></td>
              <td><?php echo $data['father_menu']? $menu_name_option[$data['father_menu']]:'无'; ?></td>
              <td><?php echo ($data['online'] == 1)?'<span class="label label-success">在线</span>':'<span class="label label-warning">不在线</span>'; ?></td>
              <td><?php echo $data['menu_desc'] ?></td>
            </tr>
            <?php } ?>
            
      			<tfoot>
              <tr>
                <th colspan="11">
                    <div class="form-group">
                      <label for="new_module_id">选择所属菜单</label>
                      <select class="form-control" name="new_module_id" style='width:250px;'>
                        <?php foreach($module_name_option as $k => $v){ ?>
                        <option value="<?php echo $k ?>" <?php echo ($module['module_id'] == $k)? 'selected': ''; ?>><?php echo $v ?></option>
                        <?php } ?>
                      </select>

                      <input type="hidden" name="module_id" value=<?php echo $module['module_id'] ?>>
                    </div>
                    
                    <button type="submit" class="btn btn-primary form_button"><i class='fa fa-spinner fa-spin hide'></i>提交</button>
                    <a class="btn btn-default" href="<?php echo base_url("$curr_controller/index") ?>">取消</a>

                  </form>
                </th>
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

<script type="text/javascript">
  $('#checkAll').click(function(){   
      if($(this).is(':checked')){  
          $('input[name="menu_ids[]"]').each(function(){
              $(this).prop("checked", true);
          });  
      }else{  
          $('input[name="menu_ids[]"]').each(function(){  
              $(this).prop("checked", false);
          });  
      } 
  });
</script>