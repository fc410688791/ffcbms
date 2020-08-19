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
                <div class="box-header with-border">
                    <div style="margin-top:10px;">
                        <input id="" type="radio" name="topic" <?php if(!$is_default){ echo "checked='checked'";}?> value="1"><sapn style="margin-right:20px;">默认主题</sapn>
                    </div>
                    <div style="margin-top:10px;">
                        <input id="" type="radio" name="topic" <?php if($is_default){ echo "checked='checked'";}?> value="0"><sapn style="margin-right:20px;">自定义主题</sapn>
                    </div>
                    <div style="margin-top:10px;">
                        <button id="add" class="btn btn-success">创建</button>
                        <button id="release" class="btn btn-primary">发布</button>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                               <td>序号</td>
                               <td>当前界面</td>
                               <td>功能名称</td>
                               <td>颜色/图标</td>
                               <td>状态</td>
                               <td>编辑</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key=>$info): ?>
                            <tr>
                               <td><?php echo $key+1;?></td>
                               <td><?php echo $topic_type_option[$info['topic_type']];?></td>
                               <td><?php echo $info['function_name'];?></td>
                               <td>
                                   <?php if ($info['element_type']==1){ ?>
                                   <div style="height:10px;background:<?php echo $info['element_value'];?>;"></div><?php echo $info['element_value']?>
                                   <?php }elseif ($info['element_type']==2){ ?>
                                   <img class="file_image" alt="" src="<?php echo $info['element_url']; ?>">
                                   <?php }?>
                               <td><?php echo $info['is_show'];?></td>
                               <td><a href="javascript:;" class="info" data-id="<?= $info['id']?>" data-topic_type="<?= $info['topic_type']?>" data-function_name="<?= $info['function_name']?>"  data-function_locator="<?= $info['function_locator']?>" data-element_type="<?= $info['element_type']?>" data-element_value="<?= $info['element_value']?>" data-is_show="<?= $info['is_show']?>">修改</a></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.row -->
</section>

<!-- 添加 addModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="detailLabel">创建</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr id='u_id'>
              <th>ID:</th>
              <th>
                <input id="id" type="text" disabled="disabled">
              </th>
            </tr>
            <tr>
              <th style="width:120px;">当前界面:</th>
              <th colspan="3">
                <select id="topic_type">
                    <?php foreach($topic_type_option as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr>
              <th>功能名称:</th>
              <th>
                <input id="function_name">
              </th>
            </tr>
            <tr>
              <th>定位符:</th>
              <th>
                <input id="function_locator">
              </th>
            </tr>
            <tr>
              <th>类型:</th>
              <th>
                <select id="element_type">
                    <?php foreach($element_type_option as $key => $value){ ?>
                    <option value='<?php echo $key ?>'><?php echo $value ?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr id='col'>
              <th>web色编码:</th>
              <th>
                <input id="col_val">请填写正确编码
              </th>
            </tr>
            <tr id='img'>
              <th>上传图标:</th>
              <th>
                <div>
                  <input id="img_src" type="file" style="float:left;width:200px;"><input id="img_id" style="display:none;">
                </div>
              </th>
            </tr>
            <tr>
              <th>展示状态：</th>
              <th>
                <input id="r1" type="radio" name="is_show" value="1"><sapn style="margin-right:20px;">展示</sapn>
                <input id="r2" type="radio" name="is_show" value="0"><sapn style="margin-right:20px;">不展示</sapn>
              </th>
            </tr>
          </tbody>
        </table> 
      </div>
      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary" id='addSubmit' style='width:250px'>确定</button>
        </center>
      </div>
    </div>
  </div>
</div>

<script src='<?php echo $assets_dir ?>/bower_components/layer-v3.1.1/layer/layer.js'></script>
<script>
$('#add').click(function(){
	$("#u_id").hide();
	$("#id").val('');
	$("#topic_type").val(1);
	$("#function_name").val('');
	$("#function_locator").val('');
	$("#element_type").val(1);
	$("#col_val").val('');
	$("#img_id").val('');
	$("#img").hide();
	$('#r1').prop('checked', true);
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});
$('#release').click(function(){
	var url = '<?php echo base_url('UiTopic/release')?>';
    var is_default  = $('input:radio[name=topic]:checked').val();
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "is_default":is_default,
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
});
$('.info').click(function(){
	var id = $(this).data('id');
	var topic_type = $(this).data('topic_type');
	var function_name = $(this).data('function_name');
	var function_locator = $(this).data('function_locator');
	var element_type = $(this).data('element_type');
	var element_value = $(this).data('element_value');
	var is_show = $(this).data('is_show');
	$("#id").val(id);
	$("#topic_type").val(topic_type);
	$("#function_name").val(function_name);
	$("#function_locator").val(function_locator);
	$("#element_type").val(element_type);
	if(element_type==1){
		$("#col_val").val(element_value);
		$("#img").hide();
		$("#col").show();
	}else{
		$("#img_id").val(element_value);
		$("#col").hide();
		$("#img").show();
	}
	if(is_show=='展示'){
		$('#r1').prop('checked', true);
		$('#r2').prop('checked', false);
	}else{
		$('#r1').prop('checked', false);
		$('#r2').prop('checked', true);
    }
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});
$('#element_type').change(function(){
	element_type();
});
$("#img_src").change(function(){
	var file = document.getElementById('img_src').files[0];
	if(file){
		var data = upload(file);
		$("#img_id").val(data.id);
		//$("#img_id").show();
	}
});
$('#addSubmit').click(function(){
    var url;
    var id = $('#id').val();
    if(id){
    	url = '<?php echo base_url('UiTopic/update')?>'+'?id='+id;
    }else{
    	url = '<?php echo base_url('UiTopic/add')?>';
    }
    var topic_type = $('#topic_type').val();
    var function_name = $('#function_name').val();
    var function_locator = $('#function_locator').val();
    var element_type = $('#element_type').val();
    var col = $('#col_val').val();
    var img = $('#img_id').val();
    var is_show  = $('input:radio[name=is_show]:checked').val();
    if(!function_name){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "topic_type":topic_type,
          "function_name":function_name,
          "function_locator":function_locator,
          "element_type":element_type,
          "col":col,
          "img":img,
          "is_show":is_show
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
});

function element_type(){
	var element_type = $('#element_type').val();
	if(element_type==1){
		$('#col').show();
		$('#img').hide();
	}else{
		$('#img').show();
		$('#col').hide();
	}
}

function upload(file){
	var url = '<?php echo base_url('Index/upload')?>';
	layerIndex = layer.load(0, {
	      shade: [0.1,'#333'] //0.1透明度的白色背景
	});
	var re;
	var from_data = new FormData();
	from_data.append('file', file);
	from_data.append('source', 11);
    $.ajax({
        type: 'POST',
        url: url,
        data: from_data,
        processData: false,
        contentType: false,
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