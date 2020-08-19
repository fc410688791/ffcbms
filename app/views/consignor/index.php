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
                    <form id="activeRetentionForm" class="" method="GET" action='<?php echo base_url('Consignor/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;margin-right: 20px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="订单编号/运单号/手机号" value="<?php echo isset($key)?$key:''; ?>">
                                    <!-- 默认切换订单转状态导航 -->
                                    <input type="hidden" name="orderSelect" value="<?php echo $this->input->get('orderSelect')??0; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" style="margin-left: 10px;" id='search'>搜索</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="selectForm" class="" method="GET" action='<?php echo base_url('Consignor/index') ?>'>
                        <input type="hidden" name="selectTime" id='selectTime' value="<?php echo isset($selectTime)?$selectTime:''; ?>">
                        <!-- 日期选择开始 -->
                        <div class="col-md-3">
                          <div class="control-group">
                            <div class="controls">
                              <div class="input-prepend input-group">
                                <span class="add-on input-group-addon">下单时间</span>
                                <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- 日期选择结束 -->
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <select class="form-control " id='product_id' name="product_id" style="width:160px;">
                                        <option value=''>所有商品名称</option>
                                        <?php foreach( $product_name_option as $pnk => $pnv){ ?>
                                        <option value="<?php echo $pnk ?>" <?php echo isset($product_id)? ($product_id == $pnk)?'selected':'':''; ?> ><?php echo $pnv ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id=province name="province_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($first_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($province_id)? ($province_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="city" name="city_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($second_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($city_id)? ($city_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="street" name="street_id" class="form-control select2 position" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($third_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($street_id)? ($street_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <select id="village" name="village_id" class="form-control select2" style="width:120px;">
                                        <option value="">选择地址</option>   
                                        <?php foreach($fourth_list as $value){ ?>
                                        <option value="<?php echo $value['id'] ?>" <?php echo isset($village_id)? ($village_id == $value['id'])? 'selected':'':''; ?>><?php echo $value["name"] ?></option> 
                                        <?php } ?>
                                    </select>    
                                    <!-- 默认切换订单转状态导航 -->
                                    <input type="hidden" name="orderSelect" value="<?php echo $this->input->get('orderSelect')??0; ?>">                               
                                    <button type="submit" id="query_submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/Consignor/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box-body table-responsive">
                    <div class='with-border'>
                        <div class="btn-group menuBar">
                            <span class="btn btn-default chartbtn" data-bm='0'>待发货</span>
                            <span class="btn btn-default chartbtn" data-bm='1'>已发货</span>
                            <span class="btn btn-default chartbtn" data-bm='2'>已完成</span>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                               <th>订单编号</th>
                               <th>商品信息</th>
                               <th>数量</th>
                               <th>下单时间</th>
                               <th>支付时间</th>
                               <th>凭证</th>
                               <th>物流信息</th>
                               <th>收货信息</th>
                               <th>操作</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <?php foreach ($list as $info) { ?>
                            <tr>
                                <td style="width:140px;"><?php echo $info['purchase_trade_no']; ?></td>
                                <td><?php echo $info['a_p_name']; ?></td>
                                <td><?php echo $info['purchase_num']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['pay_time']; ?></td>
                                <td><?php if($info['url']){ ?>
                                    <img class="file_image" alt="" src="<?php echo $info['url']; ?>">
                                    <?php } ?>
                                </td>
                                <td>
                                    <span><?php echo $info['logistics_no']; ?></span>
                                    </br>
                                    <span><?php echo $info['company_name']; ?></span>
                                </td>
                                <td style="width:200px;">
                                    <span><?php echo $info['address_info']; ?></span>
                                </td>
                                <td>
                                    <a href="javascript:;" title='查看' onclick="clip_modal_ft('<?php echo $info['address_info']?>')"><i class='fa fa-fw fa-file-text-o'></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                              <th colspan="10"><?php echo $pagination; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- 复制文本模态框 -->
<div class="modal fade manageGoodsType" id="copyModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="ture">
  <div class="modal-dialog">
    <div class="modal-content">

      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">复制文本模态框</h4>
      </div>

      <div class="modal-body" id='clip_content' style="overflow-x:auto"></div>

      <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-primary copy_btn" data-clipboard-action="copy" data-clipboard-target="#clip_content">复制</button>
        </center>
      </div>
      
    </div><!-- /.modal-content -->
  </div>
</div>
<!-- 复制文本模态框 - end -->

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
<!--图片预览 - end -->

<!-- 一键复制值到剪切板 -->
<script src="<?php echo $assets_dir; ?>/bower_components/clipboard/dist/clipboard.min.js"></script>
<script>
$(function(){
  //$('.select2').select2();
  $('#reservation').daterangepicker({
          locale: {
              format: 'YYYY-MM-DD',
              applyLabel: '确认',
              cancelLabel: '取消',
              daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
              monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
              firstDay: 1
          },
      }).on('apply.daterangepicker', function (ev, picker) {
          $('#selectTime').val('1');
          $('#query_submit').click();
      });
});
var option = "<option value=''>选择地址</option>";
activitySelectOrder();

$(".position").on('change',function(){
	var name = $(this).attr('id');
	if(name=='province'){
		$("#city").html(option);
		$("#street").html(option);
		$("#village").html(option);
    }else if(name=='city'){
    	$("#street").html(option);
    	$("#village").html(option);
    }else if(name=='street'){
    	$("#village").html(option);
    }	
	var pid = $("#"+ name +" option:selected").val();
	if(pid){
		$.ajax({
	      type: 'get',
	      url: '<?php echo base_url('Location/get_list')?>',
	      data: {
	          "pid":pid
	      },
	      dataType: 'json',
	      async:false,
	      success: function(data){
            console.log(data)
	        if(data.code==200){
                var list = data.msg;
                if(name=='province'){
                    var id = 'city';
                }else if(name=='city'){
                	var id = 'street';
                }else if(name=='street'){
                	var id = 'village';
                }
                for(var i=0;i<list.length;i++)
                {
                	$("#" + id).append("<option value='"+list[i].id+"'"+">"+list[i].name+"</option>");                         
                }
	        }        
	      },
	      error: function(xhr, type){
	         toastr.error(detailLabel+"未知错误");
	      }
		});
	}
		
});
$('.chartbtn').on('click', function(){
        $("input[name=orderSelect]").val($(this).data('bm'));
        activitySelectOrder();
        var key = $("input[name=key]").val();
        var selectTime = $("input[name=selectTime]").val();
        var reservation = $("input[name=reservation]").val();
        var product_id = $("select[name=product_id]").val();
        var province_id = $("select[name=province_id]").val();
        if(key){
        	$('#activeRetentionForm').submit();
        }else if((reservation&&selectTime)||product_id||province_id){
        	$('#selectForm').submit();
        }else{
        	$('#activeRetentionForm').submit();
        }
})

/**
 * [activitySelectOrder 激活状态的状态菜单栏]
 *
 * @author breite
 * @param  {[type]} $elem [description]
 * @return {[type]}       [description]
 */
function activitySelectOrder()
{
    var actElem = $("input[name=orderSelect]").val();
    $('.chartbtn').each(function(){
        var databm = $(this).data('bm');
        if (databm == actElem) {
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        } else {
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-default');
        }
    })
}

//可复制信息的模态框
function clip_modal_ft(address_info) {
	$('#copyModal').modal({
	    backdrop: 'static', // 空白处不关闭.
	    keyboard: false // ESC 键盘不关闭.
	});
	
	$('#clip_content').html(address_info);
	$('#copyModal').modal('show');
	
    // 复制 modal 中文本
    $('.copy_btn').click(function(){
        var clipboard = new Clipboard('.btn');
        
        clipboard.on('success', function(e) {
          $('#copyModal').modal('hide');
        });
        
        clipboard.on('error', function(e) {
          console.log(e);
        });
    });
}

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