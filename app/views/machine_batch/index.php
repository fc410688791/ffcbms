<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-primary" id='add'><i class='fa fa-fw fa-plus-square'></i>生产设备</a>
                    <p style="float:right;color:red;">*设备码;前缀（f）+ 年份后两位数 + 批次（三位数）+ 设备数量递增（5位数）</p>
                </div>
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="text-align: center;">
                                <td>序号ID</td>
                                <td>密码本ID</td>
                                <td>文案ID</td>
                                <td>产品编号</td>
                                <td>产品数量</td>
                                <td>批次编号</td>
                                <td>创建时间</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $info): ?>
                            <tr>
                                <td><?= $info['id'];?></td>
                                <td><?= $info['book_id']?$info['book_id']:'-';?></td>
                                <td><?= $info['copywriting_id']?$info['copywriting_id']:'-';?></td>
                                <td><a class="pointer" href="<?php echo base_url('Machine/index')."?batch_id={$info['id']}"; ?>"><?= $info['name'];?></a></td>
                                <td><?= $info['number'];?></td>
                                <td><?= $info['batch_no'];?></td>
                                <td><?= $info['create_time'];?></td>
                                <td>
                                    <a href="javascript:;" title='查看' onclick='clip_modal_ft(this)' data-clip_cont='<?php echo $info['clip_contents']; ?>'>查看</a>
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

<div class="modal fade bs-example-modal-lg text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="detailLabel">生产设备</h3>
      </div>

      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr>
              <th>商品类型:</th>
              <th>
                <select id="product_type" class="form-control" style="width:200px;">
                    <option value='1'>密码器</option>
                    <option value='2'>物联网</option>
                </select>
              </th>
            </tr>
            <tr class='type1'>
              <th>密码本ID:</th>
              <th>
                <select id="book_id" class="form-control" style="width:200px;">
                    <option value="0">请选择</option>
                    <?php foreach($password_book_list as $p_b){ ?>
                    <option value="<?php echo $p_b['id'] ?>"><?php echo $p_b['id'].'('.$p_b['name'].')';?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr class='type1'>
              <th>文案ID:</th>
              <th>
                <select id="copywriting_id" class="form-control" style="width:200px;">
                    <option value="0">请选择</option>
                    <?php foreach($password_copywriting_list as $p_c){ ?>
                    <option value="<?php echo $p_c['id'] ?>"><?php echo $p_c['id'].'('.$p_c['button_text'].')';?></option>
                    <?php } ?>
                </select>
              </th>
            </tr>
            <tr>
              <th>产品编号:</th>
              <th>
                <input type="text" id='name' maxlength="255">
              </th>
            </tr>
            <tr>
              <th>生产数量:</th>
              <th>
                <input type="number" id='bash_number' maxlength="5"  oninput = "value=value.replace(/[^\d]/g,'')">
              </th>
            </tr>
            <tr class='type2'>
              <th>亚克力板数:</th>
              <th>
                <input type="number" id='module_plate_num' maxlength="5"  oninput = "value=value.replace(/[^\d]/g,'')">
                <p><span style="color:red;">*亚克力板数：一个“模块数”对应几个“亚克力板数”；</span></p>
              </th>
            </tr>
            <tr class='type2'>
              <th>二维码数:</th>
              <th>
                <input type="number" id='module_plate_code_num' maxlength="5"  oninput = "value=value.replace(/[^\d]/g,'')">
                <p><span style="color:red;">*二维码数：一个“亚克力板数”对应几个“二维码数”；</span></p>
              </th>
            </tr>
            <tr>
              <th>商品:</th>
              <th id='default_product_list'><th>
            </tr>
            <tr>
                <th colspan="2"><center><button type="button" class="btn" id='change'>更换默认商品</button></center></th>
            </tr>
          </tbody>
        </table>
        <div class="modal-footer">
            <right>
              <button type="button" class="btn btn-primary" id='submit'>确定</button>
            </right>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg text-center" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="detailLabel">商品列表</h3>
      </div>

      <div class="modal-body" id="product_list">
      </div>
    </div>
  </div>
</div>

<!-- 文本模态框 -->
<div class="modal fade manageGoodsType" id="copyModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="ture">
  <div class="modal-dialog">
    <div class="modal-content">  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">二维码信息</h4>
      </div>
      <div class="modal-body" id='clip_content' style="overflow-x:auto"></div>      
    </div><!-- /.modal-content -->
  </div>
</div>
<!-- 文本模态框 - end -->
<script>
var sn = 1;
product_id = [];

$('#add').click(function(){
	sn = 1;
	product_id = [];
    $('#addModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
    $('#product_type').val(2);
    $('.type1').hide();
    $('.type2').show();
    $('#name').val('');
    $('#bash_number').val('');
    $('#module_plate_num').val('');
    $('#module_plate_code_num').val('');
    get_product_list();
});

$('#change').click(function(){
    $('#productModal').modal({
      backdrop: 'static', // 空白处不关闭.
      keyboard: false // ESC 键盘不关闭.
    });
});

$("#product_type").change(function(){
	var type = $("#product_type").val();
	if(type==1){
		 $('.type1').show();
		 $('.type2').hide();
	}else{
		 $('.type1').hide();
		 $('.type2').show();
	}
	product_id = [];
	sn = 1;
    get_product_list();
});

$('#submit').click(function(){   
    var url = '<?php echo base_url('MachineBatch/add')?>';
    var product_type = $("#product_type").val();   //商品类型1：密码器、2：物联网。
    var book_id = $("#book_id option:selected").val();   //获取选中的密码本ID
    var copywriting_id = $("#copywriting_id option:selected").val();   //获取选中的文案ID
    var name = $('#name').val();  //产品编号
    var number = $('#bash_number').val();  //生产数量
    var module_plate_num = $('#module_plate_num').val();  //亚克力板数
    var module_plate_code_num = $('#module_plate_code_num').val();  //二维码数
    product_id = product_id;
    var default_product_id = $('input:radio[name=default_product_id]:checked').val();
    if(product_type == 1&&(book_id==0||copywriting_id==0)){
    	toastr.warning("请选择密码本、文案"); 
    	return;
    }
    if(product_id.length == 0){
    	toastr.warning("请选择商品！"); 
    	return;
    }
    if(!default_product_id){
    	default_product_id = product_id[0];
    }else{
        if($.inArray( default_product_id, product_id )==-1){
        	toastr.warning("默认商品未选中！"); 
        	return;
        }
    }
    if(!name||number<1||module_plate_num<1||module_plate_code_num<1){ 
    	toastr.warning("请添按要求将数据填充完整！"); 
    	return;
    }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
          "book_id":book_id,
          "copywriting_id":copywriting_id,
          "product_id":product_id,
          "default_product_id":default_product_id,
          "name":name,
          "number":number,
          "module_plate_num":module_plate_num,
          "module_plate_code_num":module_plate_code_num
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

function get_product_list (){
	var type = $("#product_type").val();
	$.ajax({
        type: 'GET',
        url: '<?php echo base_url('Index/get_product_list')?>',
        data: {
      	  "type":type
        },
        dataType: 'json',
        async:false,//同步请求
        success: function(data){
          if(data.code==200){
              var html = "<table>";
              html += "<tr><td>商品</td><td style='width:50px;text-align:center'>价格</td><td style='width:50px;text-align:center'>优惠</td><td style='width:50px;text-align:center'>默认</td><td style='width:50px;text-align:center'>排序</td></tr>";
              for(var i=0;i<data.data2.length;i++){
            	  html += "<tr><td><input name='product' type='checkbox' value='"+data.data2[i].id+"' onclick='sort(this)'/> "+data.data2[i].name+"</td><td style='width:50px;text-align:center'>"+data.data2[i].price+"</td><td style='width:50px;text-align:center'>"+data.data2[i].incentive_price+"</td><td style='width:50px;text-align:center'><input id='product_default_"+data.data2[i].id+"' name='default_product_id' type='radio' value='"+data.data2[i].id+"'/></td><td><input id='product_sn_"+data.data2[i].id+"' style='width:50px;text-align:center' disabled='disabled'></td></tr>";
              }
              html += "</table>";
              $('#default_product_list').html(html);

              var html = "<table style='width:100%;margin:auto;'>";
              html += "<tr><td>商品</td><td style='width:100px;text-align:center'>价格</td><td style='width:100px;text-align:center'>优惠</td><td style='width:50px;text-align:center'>默认</td><td style='width:50px;text-align:center'>排序</td></tr>";
              for(var i=0;i<data.data.length;i++){
            	  html += "<tr><td style='text-align:left'><input name='product' type='checkbox' value='"+data.data[i].id+"' onclick='sort(this)'/> "+data.data[i].name+"</td><td style='text-align:center'>"+data.data[i].price+"</td><td style='text-align:center'>"+data.data[i].incentive_price+"</td><td style='width:50px;text-align:center'><input id='product_default_"+data.data[i].id+"' name='default_product_id' type='radio' value='"+data.data[i].id+"'/></td><td><input id='product_sn_"+data.data[i].id+"' style='width:50px;text-align:center' disabled='disabled'></td></tr>";
              }
              html += "</table>";
              $('#product_list').html(html);
            }else{
              toastr.error(data.msg);
            }        
        },
        error: function(xhr, type){
          toastr.error(detailLabel+"未知错误");
        }
    });
}

function sort(obj){
	var checked = $(obj).prop("checked");
	var val = $(obj).val();
	if(checked==true){
		product_id.push(val);
		$("#product_sn_"+val).val(sn);
		sn = sn+1;
    }else{
    	product_id.forEach(function(item, index, arr) {
            if(item == val) {
                arr.splice(index, 1);
            }
        });
    	$("#product_default_"+val).prop("checked",false);
    	$("#product_sn_"+val).val('');
        sn = 1;
        for(var i=0;i<product_id.length;i++){
        	val = product_id[i];
        	$("#product_sn_"+val).val(sn);
        	sn = sn+1;
        }
    }
	console.log(product_id);
}

function fun(){
    obj = document.getElementsByName("product");
    check_val = [];
    for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
    return check_val;
}

//可复制信息的模态框
function clip_modal_ft(thisDom) {
	$('#copyModal').modal({
	    backdrop: 'static', // 空白处不关闭.
	    keyboard: false // ESC 键盘不关闭.
	});
	var $clip_cont = $(thisDom).data('clip_cont');
    $('#clip_content').html($clip_cont);
}
</script>