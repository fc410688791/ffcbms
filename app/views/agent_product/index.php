<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.num{
	width:100px;
}
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
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i> 新增商品</a>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>编号</td>
                                <td>商品名称</td>
                                <td>采购商品类型</td>
                                <td>条数区间</td>
                                <td>价格（元）/每件</td>
                                <td>排序</td>
                                <td>设备图片</td>
                                <td>适合场景</td>
                                <td>是否展示</td>
                                <td>创建时间</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['name']; ?></td>
                                <td><?php echo $info['agent_product_type_name']; ?></td>
                                <td><?php echo $info['min_num'].'-'.$info['max_num']; ?></td>
                                <td><?php echo $info['price']; ?></td>
                                <td><?php echo $info['sort']; ?></td>
                                <td><img class="file_image" alt="" src="<?php echo $info['thumbnail_file']; ?>"></td>
                                <td><?php echo $info['scene']; ?></td>
                                <td><?php echo $info['status']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td>
                                <a href="javascript:;" class="info" data-id='<?php echo $info['id']; ?>'>编辑</a>
                                </td>
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
<!--图片预览结束-->

<!-- 添加 addModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">新增商品</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr>
              <th>商品名称：</th>
              <th>
                <input id='name' name="name" type="text" maxlength="50">
              </th>
            </tr>
            <tr>
              <th>采购商品类型:</th>
              <th>
                <select name="type" id="type">
                    <?php foreach($agent_product_type_option as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr>
              <th>条数区间：</th>
              <th>
                <input id="min_num" class="num" name="min_num" type="number" min="1" step="1">
                <span style="margin:0px 15px;">至</span>
                <input id="max_num" class="num" name="max_num" type="number" min="1" step="1">
              </th>
            </tr>
            <tr>
              <th>商品价格（单位：元）：</th>
              <th>
                <input id="price" name="price" type="number" min="0.01" step="1">
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">入库参考图片:</th>
              <th colspan="3">
                  <input id="p_img" type="file" accept=".jpg,.jepg,.png"><span id="p_img_id" style="display:none;"></span>
                  <span style="color:red;">*会在《生产工具小程序》入库时展示</span>
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">设备图片:</th>
              <th colspan="3">
                  <input id="s_img" type="file" accept=".jpg,.jepg,.png"><span id="s_img_id" style="display:none;"></span>
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">设备详情图片:</th>
              <th colspan="3">
                  <input id="b_img" type="file" accept=".jpg,.jepg,.png"><span id="b_img_id" style="display:none;"></span>
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">适合场景:</th>
              <th colspan="3">
                  <div id="scene_list" class="col-xs-12">
                  </div>
                  <a class="btn btn-primary" style="background-color:green;" id='addScene'>+新增场景</a>
                  <a class="btn btn-primary" style="background-color:red;" id='delScene'>-清空场景</a>
              </th>
            </tr>
            <tr>
              <th>排序：</th>
              <th>
                <input id="sort" name="sort" type="number" step="1" placeholder="排序规则：数值越小越靠前">
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th>
                <input id="a_r1" type="radio" name="status" checked="checked" value="1"><sapn style="margin-right:20px;">展示</sapn>
                <input id="a_r2" type="radio" name="status" value="0"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>           
          </tbody>
        </table> 
      </div>
      <div class="modal-footer">
        <right>
          <button type="button" class="btn btn-success" id='submit'>确定</button>
        </<right>
      </div>
    </div>
  </div>
</div>

<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">编辑</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>商品 ID: </th>
              <th>
                <input id="u_id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>商品名称：</th>
              <th>
                <input id='u_name' type="text" maxlength="50">
              </th>
            </tr>
            <tr>
              <th>采购商品类型:</th>
              <th>
                <select id="u_type">
                    <?php foreach($agent_product_type_option as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr>
              <th>条数区间：</th>
              <th>
                <input id="u_min_num" class="num" name="min_num" type="number" min="1" step="1">
                <span style="margin:0px 15px;">至</span>
                <input id="u_max_num" class="num" name="max_num" type="number" min="1" step="1">
              </th>
            </tr>
            <tr>
              <th>商品价格（单位：元）：</th>
              <th>
                <input id="u_price" type="number" min="0.01" step="1">
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">入库参考图片:</th>
              <th colspan="3">
                  <input id="u-p_img" type="file"><span id="u-p_img_id" style="display:none;"></span>
                  <img id="u-p_url" alt="" src="" style="width:100px;height:auto;">
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">设备图片:</th>
              <th colspan="3">
                  <input id="u-s_img" type="file"><span id="u-s_img_id" style="display:none;"></span>
                  <img id="u-s_url" alt="" src="" style="width:100px;height:auto;">
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">设备详情图片:</th>
              <th colspan="3">
                  <input id="u-b_img" type="file"><span id="u-b_img_id" style="display:none;"></span>
                  <img id="u-b_url" alt="" src="" style="width:100px;height:auto;">
              </th>
            </tr>
            <tr class='det_tr'>
              <th style="min-width: 130px;">适合场景:</th>
              <th colspan="3">
                  <div id="u-scene_list" class="col-xs-12">
                  </div>
                  <a class="btn btn-primary" style="background-color:green;" id='u-addScene'>+新增场景</a>
                  <a class="btn btn-primary" style="background-color:red;" id='u-delScene'>-清空场景</a>
              </th>
            </tr>
            <tr>
              <th>排序：</th>
              <th>
                <input id="u_sort" type="number" step="1">
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th>
                <input id="u_r1" type="radio" name="u_status" checked="checked" value="1"><sapn style="margin-right:20px;">展示</sapn>
                <input id="u_r2" type="radio" name="u_status" value="0"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>           
          </tbody>
        </table>
        <div class="modal-footer">
          <right>
            <button type="button" class="btn btn-success" id="btn-update">修改</button>
          </<right>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<script src='<?php echo $assets_dir ?>/bower_components/layer-v3.1.1/layer/layer.js'></script>
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
var layerIndex;
var num = 0;
$('#add').click(function(){
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$('#submit').click(function(){   
    var url     = '<?php echo base_url('AgentProduct/add')?>';
    var name    = $('#name').val();
    var type    = $('#type').val();
    var min_num = $('#min_num').val();
    var max_num = $('#max_num').val();
    var price   = $('#price').val();
    var p_img_id = $("#p_img_id").html();
    var s_img_id = $("#s_img_id").html();
	var b_img_id = $("#b_img_id").html();
    var sort    = $('#sort').val();
    var status  = $('input:radio[name=status]:checked').val();
    if(Number(min_num)>=Number(max_num)){
    	toastr.warning("请正确填写区间数值！"); 
    	return;
    }
    if(!name||price<=0||min_num<=0){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    if(!p_img_id){
		toastr.warning("请选择入库参考图片！"); 
    	return;
	}
    if(!s_img_id){
		toastr.warning("请选择设备图片！"); 
    	return;
	}
	if(!b_img_id){
		toastr.warning("请选择设备详情图片！"); 
    	return;
	}
	var sfile = {};
	if(num>0){
		for(var i=0;i<num;i++){
			var n = i+1;
			var scene = $("#scene_"+n).val();
			var id = $("#scene_file_id_"+n).html();
			if(scene&&id){
				sfile[id] = scene;
			}else{
				toastr.warning("数据未填充完整！"); 
		    	return;
			}
		}
	}
	console.log(sfile);
	layerIndex = layer.load(0, {
	      shade: [0.1,'#333'] //0.1透明度的白色背景
	});
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "name":name,
          "type":type,
          "min_num":min_num,
          "max_num":max_num,
          "price":price,
          "p_img_id":p_img_id,
          "s_img_id":s_img_id,
    	  "b_img_id":b_img_id,
    	  "sfile":sfile,
          "sort":sort,
          "status":status
      },
      dataType: 'json',
      async:false,//同步请求
      success: function(data){
        if(data.code==200){
          toastr.success(data.msg);
          setTimeout(function(){
            location.reload();
          }, 2000);
        }else{
          toastr.error(data.msg);
        }        
      },
      error: function(xhr, type){
         toastr.error(detailLabel+"未知错误");
      }
    });
    layer.close(layerIndex);
});

$('.info').click(function(){
	var id = $(this).data('id');
	var url = '<?php echo base_url('AgentProduct/info')?>';
	layerIndex = layer.load(0, {
	      shade: [0.1,'#333'] //0.1透明度的白色背景
	});
	$("#u-scene_list").html('');
	$.ajax({
        type: 'GET',
        url: url,
        data: {"id":id},
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
            if(data.code==200){
                console.log(data.data);
                $("#u_id").val(data.data.id);
                $("#u_name").val(data.data.name);
                $("#u_type").val(data.data.type);
                $("#u_min_num").val(data.data.min_num);
                $("#u_max_num").val(data.data.max_num);
                $("#u_price").val(data.data.price);
                $("#u-p_img_id").html(data.data.product_op_file_id);
                if(data.data.product_op_file_url){
                	$("#u-p_img").hide();
                	$("#u-p_url").show();
                	$("#u-p_url").attr('src',data.data.product_op_file_url);
                }else{
                	$("#u-p_img").show();
                	$("#u-p_url").hide();
                	$("#u-p_url").attr('src','');
                }
                $("#u-s_img_id").html(data.data.thumbnail_file_id);
                if(data.data.thumbnail_file_url){
                	$("#u-s_img").hide();
                	$("#u-s_url").show();
                	$("#u-s_url").attr('src',data.data.thumbnail_file_url);
                }else{
                	$("#u-s_img").show();
                	$("#u-s_url").hide();
                	$("#u-s_url").attr('src','');
                }
                $("#u-b_img_id").html(data.data.detail_file_id);
                if(data.data.detail_file_url){
                	$("#u-b_img").hide();
                	$("#u-b_url").show();
                	$("#u-b_url").attr('src',data.data.detail_file_url);
                }else{
                	$("#u-b_img").show();
                	$("#u-b_url").hide();
                	$("#u-b_url").attr('src','');
                }
                var scene_list = data.data.scene_list;
                for(var j=0;j<scene_list.length;j++){
                    var i = j+1;
                	var html = '<div class="col-xs-4" style="margin-bottom:15px;">'+
                	'<input id="u-scene_'+i+'" placeholder="场景名称" type="text" maxlength="50" value="'+scene_list[j].name+'" style="width:100px;">'+
                	'<input id="u-scene_file_'+i+'" data-num="'+i+'" name="u-scene_file" class="u-scene_file" type="file" style="display:none;">'+
                	'<img id="u-scene_url_'+i+'" data-num="'+i+'" class="u-scene_img" alt="" src="'+scene_list[j].url+'" style="width:100px;height:auto;">'+
                	'<span id="u-scene_file_id_'+i+'" style="display:none;">'+scene_list[j].id+'</span>'+
                	'</div>';
        			$("#u-scene_list").append(html);
                }
                $("#u_sort").val(data.data.sort);
                if(data.data.status==0){
            		$('#u_r1').prop('checked', false);
            		$('#u_r2').prop('checked', true);
            	}else if (data.data.status==1){
            		$('#u_r1').prop('checked', true);
            		$('#u_r2').prop('checked', false);
                }
            }else{
                toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
        }
    });
    layer.close(layerIndex)
	$('#detailModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$("#btn-update").on("click",function(){
	var id       = $('#u_id').val();
	var name     = $('#u_name').val();
	var type     = $('#u_type').val();
    var min_num  = $('#u_min_num').val();
    var max_num  = $('#u_max_num').val();
    var price    = $('#u_price').val();
    var p_img_id = $("#u-p_img_id").html();
    var s_img_id = $("#u-s_img_id").html();
	var b_img_id = $("#u-b_img_id").html();
    var sort     = $('#u_sort').val();
    var status   = $('input:radio[name=u_status]:checked').val();
    if(Number(min_num)>=Number(max_num)){
    	toastr.warning("请正确填写区间数值！"); 
    	return;
    }
    if(!name||price<=0||min_num<=0){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    if(!p_img_id){
		toastr.warning("请选择入库参考图片！"); 
    	return;
	}
    if(!s_img_id){
		toastr.warning("请选择设备图片！"); 
    	return;
	}
	if(!b_img_id){
		toastr.warning("请选择设备详情图片！"); 
    	return;
	}
	var sfile = {};
	var num = document.getElementById("u-scene_list").getElementsByTagName("div").length;
	if(num>0){
		for(var i=0;i<num;i++){
			var n = i+1;
			var scene = $("#u-scene_"+n).val();
			var img_id = $("#u-scene_file_id_"+n).html();
			if(scene&&img_id){
				sfile[img_id] = scene;
			}else{
				toastr.warning("数据未填充完整！"); 
		    	return;
			}
		}
	}
	layerIndex = layer.load(0, {
	      shade: [0.1,'#333'] //0.1透明度的白色背景
	});
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentProduct/update').'?id=' ?>' + id,
        dataType: 'json',
        data:{
            'name': name,
            'type': type,
            'min_num': min_num,
            'max_num': max_num,
            'price': price,
            "p_img_id":p_img_id,
            "s_img_id":s_img_id,
        	"b_img_id":b_img_id,
        	"sfile":sfile,
            'sort': sort,
            'status': status
        },
        async:false,//同步请求
        success: function(data){
        	if (data.code == 200) {
        		toastr.success(data.msg);
        		setTimeout(function(){
                    location.reload();
                }, 2000);
            }else{
            	toastr.error(data.msg);
        	}
        },
        error: function () {
            toastr.error('请求错误!');
        }
    });
    layer.close(layerIndex);
});

$("#p_img").change(function(){
	var file = document.getElementById('p_img').files[0];
	if(file){
		var data = upload(file);
		$("#p_img_id").html(data.id);
	}
});
$("#s_img").change(function(){
	var file = document.getElementById('s_img').files[0];
	if(file){
		var data = upload(file);
		$("#s_img_id").html(data.id);
	}
});
$("#b_img").change(function(){
	var file = document.getElementById('b_img').files[0];
	if(file){
		var data = upload(file);
		$("#b_img_id").html(data.id);
	}
});
$("#addScene").click(function(){
	if(num==0){
		num += 1;
		var html = '<div id="scene_div_'+num+'" style="margin-bottom:15px;"><input id="scene_'+num+'" placeholder="场景名称" type="text" maxlength="50" style="width:100px;"><input id="scene_file_'+num+'" name="scene_file" data-num="'+num+'" type="file"><span id="scene_file_id_'+num+'" style="display:none;"></span></div>';
		$("#scene_list").append(html);
	}else{
		var scene = $("#scene_"+num).val();
		var id = $("#scene_file_id_"+num).html();
		if(scene&&id){
			num += 1;
			var html = '<div style="margin-bottom:15px;"><input id="scene_'+num+'" placeholder="场景名称" type="text" maxlength="50" style="width:100px;"><input id="scene_file_'+num+'" name="scene_file" data-num="'+num+'" type="file" style="width:250px;"><span id="scene_file_id_'+num+'" style="display:none;"></span></div>';
			$("#scene_list").append(html);
		}else{
			toastr.warning("请填充！"); 
	    	return;
		}
	}
});
$("#delScene").click(function(){
	$("#scene_list").empty();
	num = 0;
});
$("#scene_list").delegate("input[name='scene_file']","change",function(){
	var num = $(this).data('num');
	var file = document.getElementById('scene_file_'+num).files[0];
	if(file){
		var data = upload(file);
		$("#scene_file_id_"+num).html(data.id);
	}
});

$("#u-p_url").click(function(){
	 document.getElementById("u-p_img").click(); 
});
$("#u-p_img").change(function(){
	var file = document.getElementById('u-p_img').files[0];
	if(file){
		var data = upload(file);
		$("#u-p_img_id").html(data.id);
		$("#u-p_url").attr('src',data.url);
	}
});
$("#u-s_url").click(function(){
	 document.getElementById("u-s_img").click(); 
});
$("#u-s_img").change(function(){
	var file = document.getElementById('u-s_img').files[0];
	if(file){
		var data = upload(file);
		$("#u-s_img_id").html(data.id);
		$("#u-s_url").attr('src',data.url);
	}
});
$("#u-b_url").click(function(){
	 document.getElementById("u-b_img").click(); 
});
$("#u-b_img").change(function(){
	var file = document.getElementById('u-b_img').files[0];
	if(file){
		var data = upload(file);
		$("#u-b_img_id").html(data.id);
		$("#u-b_url").attr('src',data.url);
	}
});
$("#u-scene_list").delegate(".u-scene_img","click",function(){
	var num = $(this).data('num');
	document.getElementById("u-scene_file_"+num).click();
});
$("#u-scene_list").delegate("input[name='u-scene_file']","change",function(){
	var num = $(this).data('num');
	var file = document.getElementById("u-scene_file_"+num).files[0];
	if(file){
		var data = upload(file);
		$("#u-scene_file_id_"+num).html(data.id);
		$("#u-scene_url_"+num).attr('src',data.url);
	}
});
$("#u-addScene").click(function(){
	var num = document.getElementById("u-scene_list").getElementsByTagName("div").length;
	if(num==0){
		num += 1;
		var html = '<div class="col-xs-4" style="margin-bottom:15px;"><input id="u-scene_'+num+'" placeholder="场景名称" type="text" maxlength="50" style="width:100px;"><input id="u-scene_file_'+num+'" name="u-scene_file" data-num="'+num+'" type="file" style="width:250px;"><span id="u-scene_file_id_'+num+'" style="display:none;"></span></div>';
		$("#u-scene_list").append(html);
	}else{
		var scene = $("#u-scene_"+num).val();
		var id = $("#u-scene_file_id_"+num).html();
		if(scene&&id){
			num += 1;
			var html = '<div class="col-xs-4" style="margin-bottom:15px;">'+
			'<input id="u-scene_'+num+'" placeholder="场景名称" type="text" maxlength="50" style="width:100px;">'+
			'<input id="u-scene_file_'+num+'" class="u-scene_file" name="u-scene_file" data-num="'+num+'" type="file" style="width:250px;">'+
			'<span id="u-scene_file_id_'+num+'" style="display:none;"></span>'+
			'</div>';
			$("#u-scene_list").append(html);
		}else{
			toastr.warning("请填充！"); 
	    	return;
		}
	}
	
});
$("#u-delScene").click(function(){
	$("#u-scene_list").empty();
});

function upload(file){
	var url = '<?php echo base_url('Index/upload')?>';
	layerIndex = layer.load(0, {
	      shade: [0.1,'#333'] //0.1透明度的白色背景
	});
	var re;
	var from_data = new FormData();
	from_data.append('file', file);
	from_data.append('source', 9);
    $.ajax({
        type: 'POST',
        url: url,
        data: from_data,
        processData: false,  //tell jQuery not to process the data
        contentType: false,  //tell jQuery not to set contentType
        async:false,//同步请求
        success: function(data){
        	data = JSON.parse(data);
        	console.log(data);
            if(data.code==0){
                toastr.success(data.errMsg);
                re = data.data;
            }else{
                toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
            toastr.error(detailLabel+"未知错误");
        }
    });
    layer.close(layerIndex);
    return re;
};
</script>