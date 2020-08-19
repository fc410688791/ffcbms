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
              <th style="width:50px">操作员</th>
              <th style="width:35px">行为</th>
              <th style="width:70px">类型</th>
              <th style="width:35px">对象</th>
              <th style="width:250px">操作结果</th>
              <th style="width:100px">操作时间</th>
            </tr>
            </thead>
            <tbody>
            
          	<div class="row">
          		<form id="activeRetentionForm" class="form-horizontal form-label-left" method="GET">
                <div class="col-md-2">
                	<div class="control-group">
                		<div class="input-prepend input-group">
                			<span class="input-group-addon">记录类型</span>
                			<select class="form-control select2" name="class_name">
                				<option value="">所有记录类型</option>
                        <?php foreach($log_type as $k => $v){ ?>
                          <option value="<?php echo $k ?>" <?php echo ($form['class_name'] == $k)? 'selected':''; ?>><?php echo $v; ?></option>
                        <?php } ?>
                			</select>
                		</div>
                	</div>
                </div>

                <div class="col-md-3">
          				<div class="control-group">
          					<div class="controls">
          						<div class="input-prepend input-group">
          							<span class="add-on input-group-addon">操作时间</span>
          							<input type="text" name="reservation" id="reservation" class="form-control" value="<?php echo $form['reservation']; ?>" />
          						</div>
          					</div>
          				</div>
                </div>

                <div class="col-md-2 form-group">
                  <div class="box-tools">
                    <div class="input-group" style="width: 280px;">
                      <span class="input-group-addon">用户名</span>
                      <input type="text" class='form-control' name="user_name" placeholder="查询所有用户名请留空" value="<?php echo $form['user_name'] ?>">
                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

          		</form>
          	</div>

            <?php foreach($sys_logs as $data){ ?>
            <tr>
              <td><?php echo $data['user_name'] ?></td>
              <td><?php echo $data['action'] ?></td>
              <td><?php echo $log_type[$data['class_name']] ?></td>
              <td><?php echo $data['class_obj'] ?></td>
              <td style = "word-break: break-all; word-wrap:break-word;"><?php print_r(json_decode($data['result'], true)); ?></td>
              <td><?php echo date('Y-m-d H:i:s', $data['op_time']) ?></td>
            </tr>
            <?php } ?>
			<tfoot>
			<tr>
			  <th colspan="7"><?php echo $pagination; ?></th>
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

<!-- Page script -->
<script>
  $(function () {
    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    $('.select2').select2();
    
  })
</script>

