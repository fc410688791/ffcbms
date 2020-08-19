<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">

        <div class="box-header with-border">
          <?php if (in_array(116, $roles)){ ?>
            <a class="btn btn-primary" href="<?php echo base_url('user/add') ?>"><i class='fa fa-fw fa-user-plus'></i> 添加账号</a>
          <?php } ?>
        </div>
        <!-- /.box-header -->

        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>账号</th>
              <th>姓名</th>
              <th>手机号码</th>
              <th>邮箱</th>
              <th>创建时间</th>
              <th>角色</th>
              <th style="width:110px">操作</th>
            </tr>
            </thead>
            <tbody>

            <div class="row box-body-row">
          		<form id="activeRetentionForm" class="form-horizontal form-label-left" method="GET">

                <div class="pull-left form-group">
                  <div class="box-tools">
                    <div class="input-group" style="width: 250px;">
                      <input type="text" name="like_field" class="form-control pull-left " placeholder="账号/姓名/手机号/邮箱搜索" value="<?php echo $form['like_field'] ?>">
                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="pull-right form-group" >
                  <div class="control-group">
                    <div class="input-prepend input-group">
                      <select class="form-control" name="user_group" style='width:250px;' onchange="$('#search').click();">
                        <option value="">所有角色</option>
                        <?php foreach($user_group_option as $k => $v){ ?>
                        <option value="<?php echo $k ?>" <?php echo ($form['user_group'] == $k)? 'selected': ''; ?>><?php echo $v ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

          		</form>
          	</div>

            <?php foreach($users as $data){ ?>
            <tr>
              <td><?php echo $data['user_name'] ?></td>
              <td><?php echo $data['real_name'] ?></td>
              <td><?php echo $data['mobile'] ?></td>
              <td><?php echo $data['email'] ?></td>
              <td><?php echo isset($data['create_time'])?date('Y-m-d H:i:s', $data['create_time']):''; ?></td>
              <td><?php echo $data['user_group'] ?></td>
              <td>

                <?php if (in_array(117, $roles)){ ?>
                  <!-- 行数据详情 -->
                  <a data-detail='<?php echo json_encode($data); ?>' data-cre_time='<?php echo date('Y-m-d H:i:s', $data['create_time']); ?>' onclick="showdetails(this);" title='详情页' data-user_sex='<?php echo $data['sex']?'女':'男'; ?>' href="#" ><i class='fa fa-fw fa-file-text-o'></i></a>

                  <a href="<?php echo base_url("user/modify")."?user_id={$data['user_id']}" ?>"><i class='fa fa-fw fa-pencil-square-o'></i></a>
                <?php } ?>

                <?php if ($data['user_id'] != 1){ ?>
                  <?php if (in_array(118, $roles)){ ?>
                    <?php if ($data['status'] == 1){ ?>
                    <a href="javascript:;" title= "封停账号"><i class='fa fa-fw fa-pause' data-id="<?php echo $data['user_id'] ?>" data-href="<?php echo base_url('user/manage_state')."?act=pause&user_id={$data['user_id']}"; ?>"></i></a>
                    <?php } ?>
                    <?php if ($data['status'] == 0){ ?>
                    <a href="javascript:;" title= "解封账号"><i class='fa fa-fw fa-play' data-id="<?php echo $data['user_id'] ?>" data-href="<?php echo base_url('user/manage_state')."?act=play&user_id={$data['user_id']}"; ?>"></i></a>
                    <?php } ?>
                  <?php } ?>

                  <?php if (in_array(119, $roles)){ ?>
                    <a href="javascript:;" title= "删除账号"><i class='fa fa-fw fa-trash-o' data-id="<?php echo $data['user_name'] ?>" data-href="<?php echo base_url('user/del')."?user_id={$data['user_id']}"; ?>"></i></a>
                  <?php } ?>
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
<?php echo $pause_confirm; ?>
<?php echo $play_confirm; ?>
<?php echo $del_confirm; ?>


<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">详情页</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr class='det_tr'>
              <th>性别</th>
              <th id="user_sex">Task</th>
            </tr>
            <tr class='det_tr'>
              <th>身份证</th>
              <th id="card_id">Task</th>
            </tr>
            <tr class='det_tr'>
              <th>所在地区:</th>
              <th id="address">Task</th>
            </tr>
            <tr class='det_tr'>
              <th>详细地址:</th>
              <th id="addr_detail">Task</th>
            </tr>
            <tr class='det_tr'>
              <th>创建时间:</th>
              <th id="create_time">Task</th>
            </tr>
            <tr class='det_tr'>
              <th>备注:</th>
              <th id="user_desc">Task</th>
            </tr>
          </tbody>
        </table>
      </div>
     
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->
<script type="text/javascript">
  /*点击弹出 modal 查看详情方法*/
  function showdetails(source) {
      var detail = $(source).data("detail");
      var det_tr=$(".det_tr");
      for (var i = 0; i < det_tr.length; i++) {
        var id_name=det_tr.eq(i).find("th").eq(1).attr('id');
        $('#'+id_name).css('font-weight','normal');
        $('#'+id_name).html(detail[id_name]);
      }

      // 附加信息
      var create_time = $(source).data("cre_time");
      var user_sex = $(source).data("user_sex");
      $('#create_time').html(create_time);
      $('#user_sex').html(user_sex);

      
      $("#detailModal").modal('show');
  }
  /*点击弹出 modal 查看详情方法 -end*/
</script>




