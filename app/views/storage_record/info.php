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
                                <td>设备ID</td>
                                <td>设备图片</td>
                                <td>商品名称</td>
                                <td>采购商品类型</td>
                                <td>设备类型</td>
                                <td>批次</td>
                                <td>入库时间</td>
                                <td>操作人</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><?php echo $offset+$key+1; ?></td>
                                <td><a href="<?php echo base_url('Machine/index').'?bind_triad_mark='.$info['bind_triad_mark']; ?>" class="mac" data-bind_triad_mark="<?php echo $info['bind_triad_mark']; ?>"><?php echo $info['mac']; ?></a></td>
                                <td><img class="file_image" alt="" src="<?php echo $info['thumbnail_file']; ?>"></td>
                                <td><a href="<?php echo base_url('AgentProduct/index').'?id='.$info['agent_product_id']; ?>"><?php echo $info['name']; ?></a></td>
                                <td><?php echo $info['agent_product_type_name']; ?></td>
                                <td><?php echo $info['type_name']; ?></td>
                                <td><?php echo $info['batch_storage_num']; ?></td>
                                <td><?php echo $info['storage_time']; ?></td>
                                <td><?php echo $info['storage_user']; ?></td>
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
    var to_url = '<?php echo base_url('StorageRecord/info');?>'+'?operation=download&agent_product_id='+'<?php echo $agent_product_id;?>';
    location.href = to_url;
});

$(".mac").on({
	mouseover : function(){
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
	}
});

</script>