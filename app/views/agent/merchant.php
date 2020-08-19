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
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号</td>
                                <td>投放点图片</td>
                                <td>投放点名称</td>
                                <td>投放地址</td>
                                <td>场景领域</td>
                                <td>营业时间</td>
                                <td>联系人手机</td>
                                <td>联系人姓名</td>
                                <td>创建时间</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $k=>$info): ?>
                            <tr>
                                <td><?php echo $k+1;?></td>
                                <td><img class="file_image" alt="" src="<?php echo $info['url'];?>"></td>
                                <td><?php echo $info['name'];?></td>
                                <td><?php echo $info['address'];?></td>
                                <td><?php echo $info['s_name'];?></td>
                                <td><?php echo $info['business_hours'];?></td>
                                <td><?php echo $info['mobile'];?></td>
                                <td><?php echo $info['user_name'];?></td>
                                <td><?php echo $info['create_time'];?></td>
                            </tr>
                        <?php endforeach;?>
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
        <!-- /.col -->
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
<script>
//图片预览
$(document).on("click",".file_image",function(){
    var $image_view = $("#image_view");
    var html = "";
    var img_url = $(this).attr("src");
      html+='<div class="slideshow_item">'
             +'<div class="image"><a href="#"><img style="width:600px;height:auto;" src="'+img_url+'" alt="photo 1"/></a></div>'
             +'<div class="thumb"><img src="'+img_url+'" alt="photo 1" width="140" height="63" /></div>'
             +'<div class="data">'
                   +'<h4><a href="#"></a></h4>'
               +'</div>'
           +'</div>';
    $image_view.html(html);
    $(".slideshow_next").addClass("hidden");
    $(".slideshow_prev").addClass("hidden");           
    $("#imgModal").modal("show");
});
</script>