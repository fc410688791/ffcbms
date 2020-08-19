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
            <div class="box">
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
                                <td>库存</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><?php echo $key+1; ?></td>
                                <td><img class="file_image" alt="" src="<?php echo $info['thumbnail_file']; ?>"></td>
                                <td><a href="<?php echo base_url('AgentProduct/index').'?id='.$info['id']; ?>"><?php echo $info['name']; ?></a></td>
                                <td><?php echo $info['agent_product_type_name']; ?></td>
                                <td><?php echo $info['type_name']; ?></td>
                                <td><?php if($info['op_type_storage_num']){?><a href="<?php echo base_url('StorageRecord/info').'?agent_product_id='.$info['id'] ?>"><?php }?><?php echo $info['op_type_storage_num']; ?></a></td>
                                <td>
                                    <a class="journal pointer" href="javascript:;" data-agent_product_id='<?php echo $info['id'];?>'>日志</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <tfoot>
                            <tr>
                                <th colspan="11"><?= $pagination; ?></th>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>

<!-- 查看日志  -journalModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="journalModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">库存日志</h3>
      </div>

      <div class="modal-body" >
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" style="text-align: center;">
                <thead>
                    <tr style="text-align: center;">
                        <td>时间</td>
                        <td>操作人</td>
                        <td>类型</td>
                        <td>变动前库存</td>
                        <td>库存变动</td>
                    </tr>
                </thead>
                <tbody id="journal-list">
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看日志  -journalModal -end -->

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
    var to_url = '<?php echo base_url('StorageRecord/index');?>'+'?operation=download';
    location.href = to_url;
});
$(".journal").on("click",function(){
	$('#journal-list').html('');
	var agent_product_id = $(this).data('agent_product_id');
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('StorageRecord/journal'); ?>',
          dataType: 'json',
          data:{
              'agent_product_id':agent_product_id
          },
          async:false,//同步请求
          success: function(data){
              var list = data.list;
              //console.log(list);
              var html = '';
              for(var i=0;i<list.length;i++){
            	  html += '<tr>';
            	  html += '<td>'+list[i].create_time+'</td>' +
                	  '<td>'+list[i].user+'</td>'+
                	  '<td>'+list[i].storage_type+'</td>'+
                	  '<td>'+list[i].curr_type_storage_num+'</td>'+
                	  '<td>'+list[i].op_type_storage_num+'</td>';
                  html += '</tr>';
              }
              $('#journal-list').append(html);
          },
        error: function () {
          toastr.error('请求错误!');
        },
        complete: function () {   
        }
    });
	$("#journalModal").modal('show');
});
</script>