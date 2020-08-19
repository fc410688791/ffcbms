<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.file_image{
	widtht:50px;
	height:50px;
	cursor:pointer;
}
.confirm{
	color:#FF0000;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <form id="activeRetentionForm" method="GET" action='<?php echo base_url('AgentOrder/index') ?>'>
                        <div class="pull-left form-group">
                            <div class="box-tools">
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="key" class="form-control pull-left " placeholder="订单号/手机号" value="<?php echo isset($key)?$key:''; ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-success" style="margin-left: 10px;" id='search'>搜索</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="selectForm" method="GET" action='<?php echo base_url('AgentOrder/index') ?>'>
                        <div class="pull-right form-group" style="padding-top:0px;">
                            <div class="control-group">
                                <div class="nput-prepend input-group">
                                    <input type="hidden" name="selectTime" id='selectTime' value="<?php echo isset($selectTime)?$selectTime:''; ?>">
                                    <input type="text" name="reservation" autocomplete="off" id="reservation" class="form-control" value="<?php echo isset($reservation)?$reservation:''; ?>" style='width: 200px;'/>
                                    <select class="form-control" id='product_id' name="product_id" style="width:160px;">
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
                                    <input type="hidden" name="orderSelect" value="<?php echo $this->input->get('orderSelect')?? 'all'; ?>">
                                    <button type="submit" id="query_submit" class="btn btn-success">查询</button>&nbsp;
                                    <a href="/AgentOrder/index" class="btn btn-danger">重置</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body table-responsive">
                    <div class='with-border'>
                        <div class="btn-group menuBar">
                            <span class="btn btn-default chartbtn" data-bm='all'>全部</span>
                            <span class="btn btn-default chartbtn" data-bm='prepay'>待付款</span>
                            <span class="btn btn-default chartbtn" data-bm='payed'>待发货</span>
                            <span class="btn btn-default chartbtn" data-bm='send'>已发货</span>
                            <span class="btn btn-default chartbtn" data-bm='complete'>已完成</span>
                            <span class="btn btn-default chartbtn" data-bm='paycancel'>已取消</span>
                            <span class="btn btn-default chartbtn" data-bm='refuse'>已拒绝</span>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                               <th>编号</th>
                               <th>代理商信息</th>
                               <th>商品信息</th>
                               <th>订单编号</th>
                               <th>数量（件）</th>
                               <th>价格（元）</th>
                               <th>下单时间</th>
                               <th>收货信息</th>
                               <th>订单状态</th>
                               <th>物流凭证</th>
                               <th>收货凭证</th>
                               <th>操作</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <?php foreach ($list as $info) { ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['a_card_name'].'</br>'.$info['a_mobile'].'</br>'.$info['agent_proxy_pattern']; ?></td>
                                <td><?php echo $info['a_p_name']; ?></td>
                                <td><?php echo $info['purchase_trade_no']; ?></td>
                                <td><?php echo $info['purchase_num']; ?></td>
                                <td><?php echo $info['cash_fee']; ?></td>
                                <td><?php echo $info['create_time']; ?></td>
                                <td><?php echo $info['a_a_name'].'</br>'.$info['a_a_mobile'].'</br>'.$info['p_name'].$info['a_a_position']; ?></td>
                                <td style="color:<?php echo $info['status_color'];?>"><?php echo $info['status']; ?></td>
                                <td><?php if ($info['url']){ ?><img class="file_image" alt="" src="<?php echo $info['url']; ?>"><?php }?></td>
                                <td><?php if ($info['confirm_url']){ ?><img class="file_image" alt="" src="<?php echo $info['confirm_url']; ?>"><?php }?></td>
                                <td>
                                    <?php if ($info['status']=='待发货'){?>
                                        <?php if ($info['is_confirm']=='0'){//待确认?>
                                            <a href="javascript:;" class="confirm" data-id="<?php echo $info['id'] ?>" data-href="<?php echo base_url('AgentOrder/update')."?id={$info['id']}&is_confirm=1"; ?>">确认</a>
                                            <a href="javascript:;" class="update" data-id='<?php echo $info['id']; ?>' data-purchase_trade_no='<?php echo $info['purchase_trade_no']; ?>' onclick="showRefuse(this);" title='拒绝发货'>拒绝</a>
                                        <?php }elseif ($info['is_confirm']==1){//已确认?>
                                            <a href="javascript:;" class="update" data-id='<?php echo $info['id']; ?>' data-purchase_trade_no='<?php echo $info['purchase_trade_no']; ?>' onclick="showRefuse(this);" title='拒绝发货'>拒绝</a>
                                        <?php }?>
                                            <a href="javascript:;" class="update" data-id='<?php echo $info['id']; ?>' data-purchase_trade_no='<?php echo $info['purchase_trade_no']; ?>' data-agent_id='<?php echo $info['agent_id']; ?>' data-address_id='<?php echo $info['address_id']; ?>' onclick="showdetails(this);" title='修改收货信息'>修改</a>
                                    <?php }?>
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
<!-- 确认弹窗  -->
<?php echo $confirm; ?>

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

<!-- 查看详情 - detailModal  -->
<div class="modal fade bs-example-modal-lg text-center" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h3 class="modal-title" id="resetModal-label">详情</h3>
      </div>

      <div class="modal-body" >
        <table class="table" id='dev_content'>
          <tbody>
            <tr>
              <th>订单ID: </th>
              <th>
                <input style="width:300px;" id="u_id" name='id' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>订单号: </th>
              <th>
                <input style="width:300px;" id="u_purchase_trade_no" name='purchase_trade_no' value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>收货信息: </th>
              <th>
                <select id="address_select" style="width:300px;">
                </select>
              </th>
            </tr>
          </tbody>
        </table>
          <div class="modal-footer" style="text-align: left;">
            <button id="btn-update" class="btn btn-success">修改</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 查看详情 - detailModal -end -->

<!-- 拒绝原因 --breite 2019-07-30  -->
<div class="modal fade bs-example-modal-lg text-center" id="refuseModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="detailLabel">拒绝原因</h4>
      </div>

      <div class="modal-body">
        <table class="table">
        <form>
          <tbody>
            <tr>
              <th>ID：</th>
              <th colspan="3">
                  <input style="width:280px;" id="r-id" name="r-id" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>订单编号：</th>
              <th colspan="3">
                  <input style="width:280px;" id="r-purchase_trade_no" name="r-purchase_trade_no" value='' disabled="disabled">
              </th>
            </tr>
            <tr>
              <th>请填写原因：</th>
              <th colspan="3">
                  <textarea id="r-content" style="width:100%;padding:0px;min-height:100px;" name="r-content" rows="3" required></textarea>
              </th>
            </tr>
          </tbody>
        </form>
        </table> 
      </div>
      <div class="modal-footer">
        <div class="modal-footer" style="text-align: center;">
            <button onclick="hideRefuse()" class="btn btn-default">取消</button>
            <button id="btn-refuse" class="btn btn-primary">提交</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 拒绝原因结束 -->
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
            //console.log(data)
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
        console.log("点击订单分类"+$("input[name=orderSelect]").val());
        activitySelectOrder();
        $('#selectForm').submit();
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

/*点击弹出 modal 查看详情方法*/
function showdetails(source) {	
	var id = $(source).data('id');
    var purchase_trade_no = $(source).data('purchase_trade_no');
    var agent_id = $(source).data('agent_id');
    var address_id = $(source).data('address_id');
    $("#u_id").val(id);
    $("#u_purchase_trade_no").val(purchase_trade_no);
    $.ajax({
      type: 'GET',
      url: '<?php echo base_url('Index/get_agent_address') ?>',
      data:{
        'agent_id':agent_id
      },
      dataType:'json',
      async:false, // 同步请求
      success:function(re) {
        if(re.code==200){
            var data = re.data;
            var html = '';
            for(var i=0;i<data.length;i++){
                if(address_id==data[i].id){
                	html += "<option selected=\"selected\" value=\"" +data[i].id + "\">" + data[i].name +", "+ data[i].mobile +", "+ data[i].position + "</option>";
                }else{
                	html += "<option value=\"" +data[i].id + "\">" + data[i].name +", "+ data[i].mobile +", "+ data[i].position + "</option>";
                }
            }
            $("#address_select").html(html);
            $("#detailModal").modal('show');
         }else{
           toastr.error(data.msg);
         }
      }
    });
}
// 弹出拒绝发货原因面板
function showRefuse(source) {
  var trade_id = $(source).data('id');
  var purchase_trade_no = $(source).data('purchase_trade_no');
  $("#refuseModal").modal('show');
  $("#r-id").val(trade_id);
  $("#r-purchase_trade_no").val(purchase_trade_no);
}
// 隐藏拒绝面板
function hideRefuse(){
  $("#refuseModal").modal('hide');
}
//提交拒绝
$("#btn-refuse").on("click",function(){
  var id = $("#r-id").val();
  var purchase_trade_no = $("#r-purchase_trade_no").val();
  var content = $("#r-content").val();
  if(!content){
    toastr.warning('请输入拒绝原因!');
    return;
  }
  $.ajax({
        type: 'POST',
        url: '<?php echo base_url('AgentOrder/update').'?id=' ?>' + id + '&is_confirm=2',
          dataType: 'json',
          data:{
              'content': content,
              'purchase_trade_no':purchase_trade_no
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
        },
        complete: function () {   
        }
    });
})


$("#btn-update").on('click', function(){
	var id = $('input[id=u_id]').val();
	var address_id = $('select[id=address_select]').val();
	window.location.href = "<?php echo base_url('AgentOrder/update')."?id="; ?>"+id+"&address_id="+address_id;
});
/*点击弹出 modal 查看详情方法 -end*/
</script>