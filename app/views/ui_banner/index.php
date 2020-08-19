<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.h-title{
	float:left;
	height:34px;
	line-height:34px;
	margin:0 30px;
}
.clear{
	clear:both
}
.list{
    list-style-type:none;
    margin:30px auto;
}
.block{
	margin-bottom:30px;
	float:left;
	border:2px solid #ccc;
	text-align:center;
	padding:10px 20px;
}
</style>
<script>
function sort(banner_page_type,arr){
	alert(banner_page_type);
}
</script>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <div>
                        <h3 class="h-title">首页</h3>
                        <button style="float:left;" class="add btn btn-success" data-banner_page_type='1'>+创建轮播图</button>
                    </div>
                    <div class="clear"></div>
                    <div id="list1" class="list row">
                        <?php foreach ($list[1] as $info){?>
                        <div class="card col-lg-3" id='<?php echo $info['id']; ?>'>
                            <div class="block">
                                <img style="width:100%;" alt="" src="<?php echo $info['img_url']; ?>">
                                <p><sapn>轮播主题：</sapn><span><?php echo $info['banner_desc']; ?></span></p>
                                <p><sapn>添加时间：</sapn><span><?php echo $info['create_time']; ?></span></p>
                                <div>
                                    <button style="float:left;" class="upd btn btn-primary" data-id='<?php echo $info['id']; ?>' data-banner_page_type='<?php echo $info['banner_page_type']; ?>' data-banner_desc='<?php echo $info['banner_desc']; ?>' data-file_ids='<?php echo $info['file_ids']; ?>' data-banner_url='<?php echo $info['banner_url']; ?>'>编辑</button>
                                    <button style="float:right;" class="del btn btn-primary" data-id='<?php echo $info['id']; ?>'>删除</button>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <script>
                        $(function() {
                            var sort = $( "#list1" ).sortable({
                                revert: true,
                                stop:function(){
                                    var arr = $( "#list1" ).sortable('toArray');
                                    //console.log(arr);
                                    var url = '<?php echo base_url('UiBanner/sort')?>';
                                    $.ajax({
                                      type: 'post',
                                      url: url,
                                      data: {
                                          "banner_page_type":1,
                                          "arr":arr
                                      },
                                      dataType: 'json',
                                      async:false,//同步请求
                                      success: function(data){
                                        if(data.code==200){
                                          toastr.success(data.msg);
                                          /* setTimeout(function(){
                                            location.reload();
                                          }, 2000); */
                                        }else{
                                          toastr.error(data.msg);
                                        }        
                                      },
                                      error: function(xhr, type){
                                         toastr.error(detailLabel+"未知错误");
                                      }
                                    });
                                }
                            });
                        });
                    </script>
                </div>
                <div class="clear"></div>
                <div class="box-body table-responsive">
                    <div>
                        <h3 class="h-title">商品页面</h3>
                        <button style="float:left;" class="add btn btn-success" data-banner_page_type='2'>+创建轮播图</button>
                    </div>
                    <div class="clear"></div>
                    <div id="list2" class="list row">
                        <?php foreach ($list[2] as $info){?>
                        <div class="card col-lg-3" id='<?php echo $info['id']; ?>'>
                            <div class="block">
                                <img style="width:100%;" alt="" src="<?php echo $info['img_url']; ?>">
                                <p><sapn>轮播主题：</sapn><span><?php echo $info['banner_desc']; ?></span></p>
                                <p><sapn>添加时间：</sapn><span><?php echo $info['create_time']; ?></span></p>
                                <div>
                                    <button style="float:left;" class="upd btn btn-primary" data-id='<?php echo $info['id']; ?>' data-banner_page_type='<?php echo $info['banner_page_type']; ?>' data-banner_desc='<?php echo $info['banner_desc']; ?>' data-file_ids='<?php echo $info['file_ids']; ?>' data-banner_url='<?php echo $info['banner_url']; ?>'>编辑</button>
                                    <button style="float:right;" class="del btn btn-primary" data-id='<?php echo $info['id']; ?>'>删除</button>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <script>
                        $(function() {
                            var sort = $( "#list2" ).sortable({
                                revert: true,
                                stop:function(){
                                    var arr = $( "#list2" ).sortable('toArray');
                                    var url = '<?php echo base_url('UiBanner/sort')?>';
                                    $.ajax({
                                      type: 'post',
                                      url: url,
                                      data: {
                                          "banner_page_type":2,
                                          "arr":arr
                                      },
                                      dataType: 'json',
                                      async:false,//同步请求
                                      success: function(data){
                                        if(data.code==200){
                                          toastr.success(data.msg);
                                          /* setTimeout(function(){
                                            location.reload();
                                          }, 2000); */
                                        }else{
                                          toastr.error(data.msg);
                                        }        
                                      },
                                      error: function(xhr, type){
                                         toastr.error(detailLabel+"未知错误");
                                      }
                                    });
                                }
                            });
                        });
                    </script>
                </div>
                <div class="box-body table-responsive">
                    <div>
                        <h3 class="h-title">倒计时页面</h3>
                        <button style="float:left;" class="add btn btn-success" data-banner_page_type='3'>+创建轮播图</button>
                    </div>
                    <div class="clear"></div>
                    <div id="list3" class="list row">
                        <?php foreach ($list[3] as $info){?>
                        <div class="card col-lg-3" id='<?php echo $info['id']; ?>'>
                            <div class="block">
                                <img style="width:100%;" alt="" src="<?php echo $info['img_url']; ?>">
                                <p><sapn>轮播主题：</sapn><span><?php echo $info['banner_desc']; ?></span></p>
                                <p><sapn>添加时间：</sapn><span><?php echo $info['create_time']; ?></span></p>
                                <div>
                                    <button style="float:left;" class="upd btn btn-primary" data-id='<?php echo $info['id']; ?>' data-banner_page_type='<?php echo $info['banner_page_type']; ?>' data-banner_desc='<?php echo $info['banner_desc']; ?>' data-file_ids='<?php echo $info['file_ids']; ?>' data-banner_url='<?php echo $info['banner_url']; ?>'>编辑</button>
                                    <button style="float:right;" class="del btn btn-primary" data-id='<?php echo $info['id']; ?>'>删除</button>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <script>
                        $(function() {
                            var sort = $( "#list3" ).sortable({
                                revert: true,
                                stop:function(){
                                    var arr = $( "#list3" ).sortable('toArray');
                                    var url = '<?php echo base_url('UiBanner/sort')?>';
                                    $.ajax({
                                      type: 'post',
                                      url: url,
                                      data: {
                                          "banner_page_type":3,
                                          "arr":arr
                                      },
                                      dataType: 'json',
                                      async:false,//同步请求
                                      success: function(data){
                                        if(data.code==200){
                                          toastr.success(data.msg);
                                          /* setTimeout(function(){
                                            location.reload();
                                          }, 2000); */
                                        }else{
                                          toastr.error(data.msg);
                                        }        
                                      },
                                      error: function(xhr, type){
                                         toastr.error(detailLabel+"未知错误");
                                      }
                                    });
                                }
                            });
                        });
                    </script>
                </div>
                <div class="box-body table-responsive">
                    <div>
                        <h3 class="h-title">完成页面</h3>
                        <button style="float:left;" class="add btn btn-success" data-banner_page_type='4'>+创建轮播图</button>
                    </div>
                    <div class="clear"></div>
                    <div id="list4" class="list row">
                        <?php foreach ($list[4] as $info){?>
                        <div class="card col-lg-3" id='<?php echo $info['id']; ?>'>
                            <div class="block">
                                <img style="width:100%;" alt="" src="<?php echo $info['img_url']; ?>">
                                <p><sapn>轮播主题：</sapn><span><?php echo $info['banner_desc']; ?></span></p>
                                <p><sapn>添加时间：</sapn><span><?php echo $info['create_time']; ?></span></p>
                                <div>
                                    <button style="float:left;" class="upd btn btn-primary" data-id='<?php echo $info['id']; ?>' data-banner_page_type='<?php echo $info['banner_page_type']; ?>' data-banner_desc='<?php echo $info['banner_desc']; ?>' data-file_ids='<?php echo $info['file_ids']; ?>' data-banner_url='<?php echo $info['banner_url']; ?>'>编辑</button>
                                    <button style="float:right;" class="del btn btn-primary" data-id='<?php echo $info['id']; ?>'>删除</button>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <script>
                        $(function() {
                            var sort = $( "#list4" ).sortable({
                                revert: true,
                                stop:function(){
                                    var arr = $( "#list4" ).sortable('toArray');
                                    var url = '<?php echo base_url('UiBanner/sort')?>';
                                    $.ajax({
                                      type: 'post',
                                      url: url,
                                      data: {
                                          "banner_page_type":4,
                                          "arr":arr
                                      },
                                      dataType: 'json',
                                      async:false,//同步请求
                                      success: function(data){
                                        if(data.code==200){
                                          toastr.success(data.msg);
                                          /* setTimeout(function(){
                                            location.reload();
                                          }, 2000); */
                                        }else{
                                          toastr.error(data.msg);
                                        }        
                                      },
                                      error: function(xhr, type){
                                         toastr.error(detailLabel+"未知错误");
                                      }
                                    });
                                }
                            });
                        });
                    </script>
                </div>
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
              <th>当前界面:</th>
              <th>
                <input id="banner_page_type" type="text" disabled="disabled">
                <input id="type" type="text" disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>轮播主题:</th>
              <th>
                <input id="banner_desc">
              </th>
            </tr>
            <tr id='img'>
              <th>轮播图封面:</th>
              <th>
                <div>
                  <input id="img_src" type="file" style="float:left;width:200px;"><input id="img_id" style="display:none;">
                </div>
              </th>
            </tr>
            <tr>
              <th>页面链接:</th>
              <th>
                <input id="banner_url">
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
$('.add').click(function(){
	$('#banner_page_type').hide();
	var banner_page_type = $(this).data('banner_page_type');
	$('#banner_page_type').val(banner_page_type);
	if(banner_page_type==1){
		$('#type').val('首页');
    }else if(banner_page_type==2){
    	$('#type').val('商品页面');
    }else if(banner_page_type==3){
    	$('#type').val('倒计时页面');
    }else if(banner_page_type==4){
    	$('#type').val('完成页面');
    }
	$('#banner_desc').val('');
	$('#img_id').val('');
	$('#banner_url').val('');
	$('#id').val('');
	$('#u_id').hide();
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$("#img_src").change(function(){
	var file = document.getElementById('img_src').files[0];
	if(file){
		var data = upload(file);
		$("#img_id").val(data.id);
	}
});

function upload(file){
	var url = '<?php echo base_url('Index/upload')?>';
	layerIndex = layer.load(0, {
	      shade: [0.1,'#333'] //0.1透明度的白色背景
	});
	var re;
	var from_data = new FormData();
	from_data.append('file', file);
	from_data.append('source', 10);
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

$('#addSubmit').click(function(){
    var url;
    var id = $('#id').val();
    if(id){
    	url = '<?php echo base_url('UiBanner/update')?>'+'?id='+id;
    }else{
    	url = '<?php echo base_url('UiBanner/add')?>';
    }
    var banner_page_type = $('#banner_page_type').val();
    var banner_desc = $('#banner_desc').val();
    var banner_url = $('#banner_url').val();
    var img = $('#img_id').val();
    if(!banner_desc||!img){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "banner_page_type":banner_page_type,
          "banner_desc":banner_desc,
          "banner_url":banner_url,
          "img":img
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

$('.upd').click(function(){
	var id = $(this).data('id');
	$('#banner_page_type').hide();
	var banner_page_type = $(this).data('banner_page_type');
	$('#banner_page_type').val(banner_page_type);
	if(banner_page_type==1){
		$('#type').val('首页');
    }else if(banner_page_type==2){
    	$('#type').val('商品页面');
    }else if(banner_page_type==3){
    	$('#type').val('倒计时页面');
    }else if(banner_page_type==4){
    	$('#type').val('完成页面');
    }
	var banner_desc = $(this).data('banner_desc');
	$('#banner_desc').val(banner_desc);
	var img_id = $(this).data('file_ids');
	$('#img_id').val(img_id);
	var banner_url = $(this).data('banner_url');
	$('#banner_url').val(banner_url);
	$('#id').val(id);
	$('#u_id').show();
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$('.del').click(function(){
    var url = '<?php echo base_url('UiBanner/delete')?>';
    var id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: url,
      data: {
          "id":id
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
</script>