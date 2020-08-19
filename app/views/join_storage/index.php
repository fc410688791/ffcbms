<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.file_image{
	widtht:50px;
	height:50px;
	cursor:pointer;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class='box'>
                <div class='box-body table-responsive'>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('JoinStorage/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="操作人" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" id='search'><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('JoinStorage/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select class="form-control" name="agent_product_type" style="width:140px;">
                                        <option value=''>采购商品类型</option>
                                        <?php foreach( $agent_product_type_option as $k => $v){ ?>
                                          <option value="<?php echo $k ?>" <?php echo isset($agent_product_type)? ($agent_product_type == $k)?'selected':'':''; ?> ><?php echo $v ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                    <button type="submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/JoinStorage/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body table-responsive">
                    <div style="float:right;">
                        <button id="download" class="btn btn-success">导出</button>
                    </div>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>设备图片</td>
                                <td>商品名称</td>
                                <td>采购商品类型</td>
                                <td>设备类型</td>
                                <td>入库数</td>
                                <td>入库时间</td>
                                <td>操作人</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $k=>$info): ?>
                            <tr>
                                <td><?php echo $k+1; ?></td>
                                <td><img class="file_image" alt="" src="<?php echo $info['thumbnail_file']; ?>"></td>
                                <td><a href="<?php echo base_url('AgentProduct/index').'?id='.$info['agent_product_id']; ?>"><?php echo $info['name']; ?></a></td>
                                <td><?php echo $info['agent_product_type_name']; ?></td>
                                <td><?php echo $info['type_name']; ?></td>
                                <td><a href="<?php echo base_url('JoinStorage/info').'?id='.$info['id'] ?>"><?php echo $info['op_type_storage_num']; ?></a></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['user_name']; ?></td>
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

<!--图片预览-->
<div class="modal fade bs-example-modal-lg text-center" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:1150px;">
      <div style="background-color:rgba(180,180,180,0);box-shadow:none;border:0;" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="break-label">图片预览</h3>
      </div>
      <div class="modal-body">
         <!-- <h2>Slideshow #3</h2> -->
         <div style="padding-top: 0px;" class="ss3_wrapper">
            <a href="#" class="slideshow_prev hidden"><span>Previous</span></a>
            <a href="#" class="slideshow_next hidden"><span>Next</span></a>
            <div class="slideshow_box">
                <div class="data"></div>
            </div>
            <div id="image_view" class="slideshow">
                     
            </div>
        </div><!-- .ss3_wrapper -->
      </div>
      <div class="modal-footer" style="display: none">
        <button class="big-img">+</button>
        <button class="small-img">-</button>
      </div>
    </div>
  </div>
</div>
<!--图片预览结束-->

<script>
$(function(){
    $('#reservation').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '确认',
            cancelLabel: '取消',
            daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            firstDay: 1
        }
    });
});

//图片预览
$(document).on("click",".file_image",function(){
    var $image_view = $("#image_view");
    var html = "";
    var img_url = $(this).attr("src");
      html+='<div class="slideshow_item">'
             +'<div class="image"><a href="#"><img style="width:600px;height:auto;" src="'+img_url+'" alt="photo 1"/></a></div>'
             +'<div class="data">'
                   +'<h4><a href="#"></a></h4>'
               +'</div>'
           +'</div>';
    $image_view.html(html);
    $(".slideshow_next").addClass("hidden");
    $(".slideshow_prev").addClass("hidden");           
    $("#imgModal").modal("show");
});

$("#download").on("click",function(){
    var to_url = '<?php echo base_url('JoinStorage/index');?>'+'?operation=download&key='+'<?php echo $key; ?>'+'&agent_product_type='+'<?php echo $agent_product_type; ?>'+'&reservation='+'<?php echo $reservation; ?>';
    location.href = to_url;
});
</script>