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
              <th><input type="checkbox" id="checkAll"> 全选</th>
              <th>成员编号</th>
              <th>登录名</th>
              <th>姓名</th>
              <th>手机</th>
              <th>邮箱</th>
              <th>登录时间</th>
              <th>登录IP</th>
              <th>Group#</th>
              <th>描述</th>
            </tr>
            </thead>
            <tbody>
            
            <div class='row'>
              <div class="col-md-2 form-group">
                <div class="control-group">
                  <div class="input-prepend input-group">
                    <span><b><i><?php echo $group_name ?></i> - 成员列表</b></span>
                  </div>
                </div>
              </div>
            </div>
            
            <form method="post" action="<?php echo base_url('user_group/list'); ?>">

            <!-- 自定义表单验证失败的错误提示 -->
            <?php echo $form_error_contents;?>

            <?php foreach($users as $user){ ?>
            <tr>
              <td><input type="checkbox" name="user_ids[]" value="<?php echo $user['user_id'] ?>" <?php echo ($user['user_id'] == 1)? 'disabled':''; ?>></td>
              <td><?php echo $user['user_id'] ?></td>
              <td><?php echo $user['user_name'] ?></td>
              <td><?php echo $user['real_name'] ?></td>
              <td><?php echo $user['mobile'] ?></td>
              <td><?php echo $user['email'] ?></td>
              <td><?php echo date('Y-m-d H:i:s', $user['login_time']) ?></td>
              <td><?php echo $user['login_ip'] ?></td>
              <td><?php echo $user['user_group'] ?></td>
              <td><?php echo $user['user_desc'] ?></td>
            </tr>
            <?php } ?>

      			<tfoot>
              <tr>
                <th colspan="11">
                    <div class="form-group">
                      <label for="user_group">选择角色</label>
                      <select class="form-control" name="user_group" style='width:250px;'>
                        <?php foreach($user_group_option as $k => $v){ ?>
                        <option value="<?php echo $k ?>" <?php echo ($group_id == $k)? 'selected': ''; ?>><?php echo $v ?></option>
                        <?php } ?>
                      </select>

                      <input type="hidden" name="group_name" value=<?php echo $group_name ?>>
                      <input type="hidden" name="group_id" value=<?php echo $group_id ?>>
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
          $("input[name='user_ids[]']").each(function(){
              $(this).prop("checked", true);
          });  
      }else{  
          $("input[name='user_ids[]']").each(function(){ 
              $(this).prop("checked", false);
          });  
      } 
  });
</script>

