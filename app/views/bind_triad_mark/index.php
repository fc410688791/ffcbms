<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">     
            <div class="box">
                <?php if(isset($merchant_id)){?>
                <div class="box-header with-border">
                    <div class="pull-right form-group">
                        <div class="control-group">
                            <div class="nput-prepend input-group">
                                <select id="time_type" class="form-control" style="width:150px;">
                                    <option value='7'>最近7天</option>
                                    <option value='10'>最近10天</option>
                                    <option value='30'>最近30天</option>
                                    <option value='m'>当前月份</option>
                                </select>
                                <button id='output' class="btn btn-primary">导出流水</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }?>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>设备ID</td>
                                <td>商品名称</td>
                                <td>投放点</td>
                                <td>位置</td>
                                <td>交易量</td>
                                <td>流水</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><?php echo $offset+$key+1; ?></td>
                                <td><a href="javascript:;" class="mac" data-bind_triad_mark="<?php echo $info['bind_triad_mark']; ?>"><?php echo $info['mac']; ?></a></td>
                                <td><a href="<?php echo base_url('AgentProduct/index').'?id='.$info['agent_product_id']; ?>"><?php echo $info['name']; ?></a></td>
                                <td><?php echo $info['merchant']; ?></td>
                                <td><?php echo $info['position']; ?></td>
                                <td><?php echo $info['pay_count']; ?></td>
                                <td><?php echo $info['cash_fee']; ?></td>
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

<div class="modal fade bs-example-modal-lg text-center" id="showModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="showModal-label"></h3>
      </div>
      <div class="modal-body" id="showModal-body">
      </div>
    </div>
  </div>
</div>
<script>
$(".mac").on("click",function(){
    var bind_triad_mark = $(this).data('bind_triad_mark');
    $.ajax({
        type: 'GET',
        url: '<?php echo base_url('Index/get_machine_iot_triad'); ?>',
        dataType: 'json',
        data:{
            'bind_triad_mark': bind_triad_mark
        },
        async:false,//同步请求
        success: function(data){
            if (data.code == 200) {
                $('#showModal-label').html(data.data.mac+'设备详情');
                var html = '<div style="width:400px;margin:auto;text-align:left;">';
                html += '<p><span>模块数:</span><span>'+data.data.bind_triad_mark+'</span><p>';
                html += '<p><span>亚克力数:</span><span>'+data.data.bind_side_num+'</span><p>';
                html += '<p><span>二维码数:</span><span>'+data.data.bind_plate_code_num+'</span><p>';
                html += '<hr>';
                for(var i=0;i<data.data.list.length;i++){
                  html += '<h4 style="color:#A1A1A1;"><span>亚克力'+(i+1)+':</span><span style="margin-left:30px;">'+data.data.list[i]+'</span></h4>';
                }
                html += '</div>';
                $('#showModal-body').html(html);
            }else{
                toastr.error(data.msg);
            }
        },
        error: function () {
            toastr.error('请求错误!');
        }
    });
    $('#showModal').modal({
        backdrop: 'static', // 空白处不关闭.
        keyboard: false // ESC 键盘不关闭.
    });
});

$("#output").on("click",function(){
	var time_type = $('#time_type').val();
	var to_url = '<?php echo base_url('BindTriadMark/output');?>'+'?time_type='+time_type+'&merchant_id='+'<?php echo $merchant_id; ?>';
    location.href = to_url;
});
</script>