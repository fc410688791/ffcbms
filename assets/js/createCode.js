/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



  var pd_ids = new Array();
  $(function(){
    //Date range picker

    $('.select2').select2();

    $('#download').hide();

    $("#addProd").click(function(){
        setcheckbox();
        $("#addproModal").modal();
    });
    //组合商品 - 添加商品
    $('#group-addpro').click(function(){
       setRadio();
       $("#addproGroupModal").modal("hide");
       setTimeout(function(){
           $("#group-addproModal").modal("show");     
       },500);
    });
    //组合商品 - 取消添加商品
    $("#addpro-cancel").click(function(){
          $("#group-addproModal").modal("hide");
          setTimeout(function(){
             $("#addproGroupModal").modal("show");     
          },500);
    });
    //活动商品-选择商品
    $("#sure_add_pro").on('click',function(){
      var checked_count=$("input[type=checkbox]:checked").length;
      if(checked_count<=0) {
        toastr.error('请选择至少一个商品');
         return false;
      }
      $('#tbody_pd').html('');
      $("input[type='checkbox']:checked").each(function(i, n){ 
          var domThis    = $(this);
          pd_id          = domThis.val();
          pd_ids[i]      = pd_id;
          // var nextAll = domThis.parents("tr");
          var nextAll    = domThis.parent().parent();
          var num        = i+1;
          var pd_name    = nextAll.find('td.pd_name').text();
          var pd_price   = nextAll.find('td.pd_price').text();
          var pd_time    = nextAll.find('td.pd_time').text();
          var pd_type    = nextAll.find('td.pd_type').text();

          $('#tbody_pd').append("<tr data-id='"+pd_id+"'><td>"+num+"</td><td>"+pd_name+"</td><td>"+pd_price+"</td><td>"+pd_time+"</td><td>"+pd_type+"</td><td><a href='javascript:void(0)' class='pd_id_remove' data-id="+pd_id+">移除</a></td></tr>");
          
         $("#addproModal").modal('hide');
      });
    });
    //组合商品-选择商品
    $("#group-addpro-contirm").on('click',function(){
      var checked_count=$("#group-addproModal").find("input[type=checkbox]:checked").length;
      if(checked_count<=0) {
        toastr.error('请选择至少一个商品');
         return false;
      }
      $('#group-pro-table').html('');
      $("#group-addproModal").find("tbody").find("input[type='checkbox']:checked").each(function(i, n){ 
          var domThis    = $(this);
          pd_id          = domThis.val();
          pd_ids[i]      = pd_id;
          // var nextAll = domThis.parents("tr");
          var nextAll    = domThis.parent().parent();
          var num        = i+1;
          var pd_name    = nextAll.find('td.pd_name').text();
          var pd_price   = nextAll.find('td.pd_price').text();
          var pd_time    = nextAll.find('td.pd_time').text();
          var pd_type    = nextAll.find('td.pd_type').text();

          $('#group-pro-table').append("<tr data-id='"+pd_id+"' data-name='"+pd_name+"' data-price='"+pd_price+"' data-pd-time='"+pd_time+"' data-type='"+pd_type+"'><td>"+num+"</td><td>"+pd_name+"</td><td>"+pd_price+"</td><td>"+pd_time+"</td><td>"+pd_type+"</td><td><a href='javascript:void(0)' class='pd_id_remove' data-id="+pd_id+">移除</a></td></tr>");

          $("#group-addproModal").modal("hide");
          setTimeout(function(){
             $("#addproGroupModal").modal("show");     
          },500);
      });
    });
    //添加组合商品
    $("#group-addprogroup-contirm").click(function(){
        var $group_pro_table = $("#group-pro-table");
        var $select_part = $("select[name=part]");
        var $select_age  = $("select[name=age]");
        var $select_sex  = $("select[name=sex]");
        var $group_progroup_table = $("#group-progroup-table");
        var group_pro_json = {
             part:$select_part.val(),
             age:$select_age.val(),
             sex:$select_sex.val(),
             product:[],
             product_name:[]
             
        };
        $group_pro_table.find("tr").each(function(){
            var detial = {
                name:$(this).attr("data-name"),
                id:$(this).attr("data-id"),
                price:$(this).attr("data-price"),
                pd_time:$(this).attr("data-pd-time"),
                pd_type:$(this).attr("data-type")
            };
            group_pro_json.product.push(detial);
            group_pro_json.product_name.push($(this).attr("data-name"));
        });
        var json_data =  JSON.stringify(group_pro_json);
        var $tpt_tr =  $group_progroup_table.find("tr");
        var $cutommer_product_table_id =   $('#cutommer_product_table_id');
        var num = $tpt_tr.length + 1;
        console.log($cutommer_product_table_id.val());
        if(isSetThisGroup(group_pro_json.part,group_pro_json.age,group_pro_json.sex,num)){
            toastr.error("组合无法重复添加");
            $("#addproGroupModal").modal("hide");   
            $group_pro_table.html("");
            return false;
        }
       if($cutommer_product_table_id.val() !== ""){
                  var html ="<td>"+$cutommer_product_table_id.val()+"</td>"  
                          +"<td>"+getPartText(group_pro_json.part)+"</td>"
                    +"<td>"+getAgeText(group_pro_json.age)+"</td>"
                    +"<td>"+getSexText(group_pro_json.sex)+"</td>"
                    +"<td>"+group_pro_json.product_name.join(",")+"</td>"
                    +"<td><a class='group-pro-edit' href='javascript:void(0)'>编辑</a> <a class='group-pro-rm' href='javascript:void(0)'>移除</a></td>";
                   $("#tpt_tr_"+$cutommer_product_table_id.val()).html(html);
                   $("#tpt_tr_"+$cutommer_product_table_id.val()).attr("data-json",json_data);
       }
       else
       {
                 var html =  "<tr data-num='"+num+"' id='tpt_tr_"+num+"' data-json='"+json_data+"'>"
                    +"<td>"+num+"</td>"
                    +"<td>"+getPartText(group_pro_json.part)+"</td>"
                    +"<td>"+getAgeText(group_pro_json.age)+"</td>"
                    +"<td>"+getSexText(group_pro_json.sex)+"</td>"
                    +"<td>"+group_pro_json.product_name.join(",")+"</td>"
                    +"<td><a class='group-pro-edit' href='javascript:void(0)'>编辑</a> <a class='group-pro-rm' href='javascript:void(0)'>移除</a></td></tr>";
                 $group_progroup_table.append(html);
       }
       
       $("#addproGroupModal").modal("hide");   
       $cutommer_product_table_id.val("");
       $group_pro_table.html("");
    });
    //编辑组合商品
    $(document).on("click",".group-pro-edit",function(){
           var $group_pro_table = $("#group-pro-table");
           var $cutommer_product_table_id =   $('#cutommer_product_table_id');
           $group_pro_table.html("");
           var data_json = $(this).parents("tr").attr("data-json");
           var data_obj =  eval("("+data_json+")");
           $cutommer_product_table_id.val($(this).parents("tr").attr("data-num"));
           var products = data_obj.product;
           for(var i =0;i<products.length;i++){
               var num = i+1;
               $('#group-pro-table').append("<tr data-id='"+products[i].id+"' data-name='"+products[i].name+"' data-price='"+products[i].price+"' data-pd-time='"+products[i].pd_time+"' data-type='"+products[i].pd_type+"'><td>"+num+"</td><td>"+products[i].name+"</td><td>"+products[i].price+"</td><td>"+products[i].pd_time+"</td><td>"+products[i].pd_type+"</td><td><a href='javascript:void(0)' class='pd_id_remove' data-id="+products[i].id+">移除</a></td></tr>");
           }
           $("select[name=part]").val(data_obj.part);
           $("select[name=age]").val(data_obj.age);
           $("select[name=sex]").val(data_obj.sex);
           $("#addproGroupModal").modal("show");  
    });
    //移除组合商品
    $(document).on("click",".group-pro-rm",function(){
        $(this).parents("tr").remove();
        resetSort();
    });
    //重新排序组合商品
    function resetSort(){
        $("#group-progroup-table").find("tr").each(function(key){
               var num = key+1;
               $(this).attr("data-num",num);
               $(this).attr("id","tpt_tr_"+num);
               $(this).find("td").eq(0).html(num);
        });
    }
    //获取所有已添加的组合，并判断是否已存在
    function isSetThisGroup(part,age,sex,num){
        var isset = false;
        var $cutommer_product_table_id = $("#cutommer_product_table_id").val();
        $("#group-progroup-table").find("tr").each(function(){
           var data_json = $(this).attr("data-json");
           var id  = $(this).attr("data-num");
           var data_obj =  eval("("+data_json+")");
           if(id !== num && $cutommer_product_table_id == "" || $cutommer_product_table_id != "" && $cutommer_product_table_id !== id){
            if(data_obj.sex == sex && data_obj.age == age&& data_obj.part == part){
                   isset = true;
            }
           }
        });
        return isset;
    }
    //获取部位文本
    function getPartText(id){
        var text = "";
        switch(parseInt(id)){
            case 1:text="肩部";
            break;
            case 2:text="背部";
            break;
            case 3:text="腰部";
            break;
        }
        return text;
    }
    //获取性别文本
    function getSexText(id){
        var text = "";
        switch(parseInt(id)){
            case 1:text="男";
            break;
            case 2:text="女";
            break;
        }
        return text;
    }
    //获取年龄段文本
    function getAgeText(id){
        var text = "";
        switch(parseInt(id)){
            case 1:text="青年";
            break;
            case 2:text="中年";
            break;
            case 3:text="老年";
            break;
        }
        return text;
    }
    //移除活动商品
    $('#tbody_pd').on('click', 'a.pd_id_remove', function(){
      var pd_id = $(this).data('id');
      var ri = $.inArray(parseInt(pd_id),pd_ids);
      var ri = $.inArray(String(pd_id),pd_ids);
      pd_ids.splice(ri, 1);
      $(this).parents('tr').remove();
    });
  });
  //移除组合里商品列表
  $("#group-pro-table").on("click","a.pd_id_remove",function(){
        $(this).parents('tr').remove();
  });
  //切换活动类型触发事件
  $("#type_id").on("change",function(){
         var $property = $("#property");
         var $pro = $("#input-actpro");
         var $img = $("#input-images");
         var $group = $("#input-actgrouppro");
         if($(this).val() == "1" || $(this).val() == "2"){
             $pro.removeClass("hidden");
             $group.addClass("hidden");
             $img.addClass("hidden");
         }
         if($(this).val() == "3"){
             $pro.addClass("hidden");
             $group.removeClass("hidden");
             $img.removeClass("hidden");
         }
         if($(this).val() == ""){
             $pro.addClass("hidden");
             $group.addClass("hidden");
             $img.addClass("hidden");
         }
         $.ajax({
            url:'/ActivityManage/createCode',
            type:"get",
            dataType:"json",
            data:{
                method:"getProperty",
                type_id:$(this).val()
            },
            success:function(res){
                var thtml = "";
                if(res.code == 200){
                    for(var i = 0;i<res.body.property.length;i++){
                        if(res.body.property[i].property_key !== "report_img_ids"){
                            thtml+= "<div class='form-group'>"
                                   +"<label>"+res.body.property[i].property_name+"</label>"
                                   +"<div>"
                                   +"<div class='form-group'>"
                                   + getControl(res.body.property[i].property_key)
                                   + "</div>"
                                   +"</div>"
                                   +"</div>";
                        }
                    }
                    $property.html(thtml);
                }
            }
         });
   });
   //复选框设置
   function setRadio(){
       var ids = [];
       $("#group-pro-table").find("tr").each(function(){
           ids.push(parseInt($(this).attr("data-id")));
       });
       $("#group-addproModal").find("input[type=checkbox]").each(function(){
           if(in_array($(this).val(),ids)){
               $(this).prop("checked",true);
           }
           else
           {
               $(this).prop("checked",false);
           }
       });
   }
    function setcheckbox(){
       var ids = [];
       $("#tbody_pd").find("tr").each(function(){
           ids.push(parseInt($(this).attr("data-id")));
       });
       $("#example2").find("input[type=checkbox]").each(function(){
           if(in_array($(this).val(),ids)){
               $(this).prop("checked",true);
           }
           else
           {
               $(this).prop("checked",false);
           }
       });
   }
   //检查是否包含在数组内
   function in_array(val,Arr){
       var is_child = false;
       for(var i =0;i<Arr.length;i++){
           if(val == Arr[i]){
               is_child = true;
               break;
           }
       }
       return is_child;
   }
   //获取控件
   function getControl(key){
        var control = "";
        switch(key){
            case "exchange_more":
            control = "<input type='radio' name='"+key+"' value='1' >是"
                      +"<input type='radio' name='"+key+"' value='0' checked=checked>否";
            break;
            case  "valid_time":
            control =   '<input name="'+key+'" style="width: 25%;float: left;"  class="form-control" type="number">'
                        +'<select name="unit"  style="width:5%;" class="form-control">'
                        + '<option value="month">月</option>'
                        + '<option value="day">天</option>'
                        +'<option value="hour" selected=selected>小时</option>'
                        +'</select>';
            break;
            case  "number":
            control = "<input type='number' name='"+key+"' class='form-control' value=''>"
                      +"<div>活动类型为多码一用时，此数量为活动码数量；当活动类型为一码多用时，此数量为活动码使用次数。</div>";
            break;
            case "banner_img_id":
            control = '<div style="width:100px;" class="plus-img" title="点击添加图片">+</div>'
                      +'<img style="width:100px;heigth:auto;cursor:pointer" id="banner_img" src="">'
                      +'<div> <a id="uploadimage" href="javascript:void(0)">上传图片</a> <a id="cleanimage" href="javascript:void(0)">清除图片</a></div>'
                      +'<div>图片类型必须是:png。图片尺寸为680*374</div>'
                      +'<input onchange="fileOnchange(this)" id="'+key+'" name="'+key+'" type="file" style="display:none">'
                      +'';
        }
       return control;
   }
  $('#form_button').click(function(){
      var name        = $('#name').val();
      var type_id     = $('#type_id').val();
      var number      = $('input[name=number]').val();
      var valid_time  = $('input[name=valid_time]').val();
      var reservation = $('#reservation').val();
      var station_id  = $('#station_id').val();
      var unit        = $('select[name=unit]').val();
      var exchange_more = $('input[name=exchange_more]:checked').val();
      if(name == ''){
         toastr.error('请输入活动名称');
         return false;
      }
      if(name.length >= 30){
        toastr.error('活动名称字数最多填写30个字符');
        return false;
      }
      if(station_id == ''){
         toastr.error('请添加活动站点');
         return false;
      }
      if (type_id == '' || name == '' || number == '' || reservation == '' || valid_time == '' || station_id == '') {
        toastr.error('缺少参数');
        return false;
      }
      if($("#type_id").val() !=="3"){
        var prx = /^[0-9]*[1-9][0-9]*$/　　//正整数 \
        if ( ! prx.test(number)) {
          toastr.error('活动数量不能小于零的正整数');
          return false;
        }
        if ( ! prx.test(valid_time)) {
          toastr.error('活动码有效时间不能小于零的正整数');
          return false;
        }
        if (pd_ids == '') {
          toastr.error('请添加活动商品');
          return false;
        }
         var data = {
          name:name,
          type_id:type_id,
          number:number,
          reservation:reservation,
          valid_time:valid_time,
          station_id:station_id,
          product_ids:pd_ids,
          unit:unit,
          exchange_more:exchange_more
        };
        $('#download').hide();
        layer.load();
        $.ajax({
            type: 'POST',
            url: '/ActivityManage/createCode',
            data: data,
            dataType: 'json',
            async:true,
            success: function(data){
             if(data.code== 0){
                  window.location.href = "/ActivityManage/index";
              }else{
                toastr.error(data.msg);
              }
            },
            error: function(xhr, type){
               toastr.error("生成二维码: 未知错误");
            }
        });
     }
     else
     {
         var products = [];
         $("#group-progroup-table").find("tr").each(function(){
                var data_obj = $.parseJSON($(this).attr("data-json"));
                products.push(data_obj);
         });
         var formdata = new FormData();
         console.log($("#banner_img_id"));
         formdata.append("name",name);
         formdata.append("type_id",type_id);
         formdata.append("reservation",reservation);
         formdata.append("station_id",station_id);
         formdata.append("products",JSON.stringify(products));
         formdata.append("report_img_ids",$('#report_img_ids').val());
         formdata.append("banner",$("#banner_img_id")[0].files[0]);
         $('#download').hide();
         //layer.load();
         $.ajax({
           type: 'POST',
           url: '/ActivityManage/createCode',
           data: formdata,
           dataType: 'json',
           async:true,
           processData:false,
           contentType:false,
           success: function(data){
            if(data.code== 0){
                 window.location.href = "/ActivityManage/index";
             }else{
               toastr.error(data.msg);
             }
           },
           error: function(xhr, type){
              toastr.error("生成二维码: 未知错误");
           }
        });
     }


  });
  $(document).on("click",".plus-img,#uploadimage",function(){
       $("input[name=banner_img_id]").click();
  });
  $(document).on("click","#cleanimage",function(){
      $("#banner_img").attr("src",''); 
      $("#banner_img_id").val(""); 
      $(".plus-img").removeClass("hidden");
  });
  //上传商品图片
  $("#addImage").click(function(){
      $("#upload-images").click();
  });
  $("#upload-images").change(function(){
      var formdata = new FormData();
      for(var i = 0;i<$(this)[0].files.length;i++){
         formdata.append("files"+i,$(this)[0].files[i]);
      }
      $.ajax({
         url:"/ActivityManage/createCode?method=uploadImages",
         type:"post",
         data:formdata,
         processData:false,
         contentType:false,
         dataType:"json",
         success:function(data){
             console.log(data.code);
             if(data.code == 200){
                var str = "";
                var ids =  $("#report_img_ids").val().split(",") == ""?[]:$("#report_img_ids").val().split(",");
                console.log(ids);
                for(var i = 0;i<data.body.length;i++){
                    var num = $("#tbody_image").find("tr").length + (i + 1);
                    var url = "http://"+data.body[i].url;
                    str+="<tr data-url='"+url+"' data-id='"+data.body[i].id+"'>"
                         +"<td>"+num+"</td>"
                         +"<td><img style='width:100px;heigth:auto' src='"+url+"'></td>"
                         +"<td>"+data.body[i].name+"</td>"
                         +"<td><a class='im_view' href='javascript:void(0)'>预览</a> <a class='im_r' href='javascript:void(0)'>移除</a></td>"
                         +"</tr>";
                    ids.push(data.body[i].id);
                }
                $("#tbody_image").append(str);
                $("#report_img_ids").val(ids.join(","));
                $("#upload-images").val(""); 
             }
             else
             {
                 toastr.error(data.message);
             }
         }
      });
  });
  //图片预览
  $(document).on("click","#banner_img",function(){
      var $image_view = $("#image_view");
      var html = "";
      var img_url = $(this).attr("src");
           html+='<div class="slideshow_item">'
                  +'<div class="image"><a href="#"><img src="'+img_url+'" alt="photo 1"/></a></div>'
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
  //预览图片
  $(document).on("click",".im_view",function(){
       var $image_view = $("#image_view");
       var tbody_image = $("#tbody_image");
       var this_img_index = $(this).parents("tr").index();
       var this_img_url  = $(this).parents("tr").find("img").attr("src");
       var img_urls = [$(this).parents("tr").find("img").attr("src")];
       var html = "";
       tbody_image.find("tr").each(function(){
           var img_url = $(this).attr("data-url");
           var img_index = $(this).index();
           if(this_img_index !== 0){
                   if(this_img_index < img_index){
                       img_urls.push(img_url);
                   }
            }
       });
       tbody_image.find("tr").each(function(){
           var img_url = $(this).attr("data-url");
           var img_index = $(this).index();
           if(this_img_index !== 0){
                   if(this_img_index > img_index){
                       img_urls.push(img_url);
                   }
            }
            else
            {
                if(img_url !== this_img_url){
                    img_urls.push(img_url);
                }
            }
       });
       for(var i = 0 ;i<img_urls.length;i++){
            html+='<div class="slideshow_item">'
                  +'<img src="'+img_urls[i]+'" alt="photo 1" width="100%" />'
                +'</div>';
        }
       $image_view.html(html);
       if(img_urls.length > 1){
            $(".slideshow_next").removeClass("hidden");
            $(".slideshow_prev").removeClass("hidden");
       }
       else
       {
            $(".slideshow_next").addClass("hidden");
            $(".slideshow_prev").addClass("hidden");
       }
       /*图片轮播*/
       $('#image_view').cycle({
                 fx: 'fade',      
                 speed:  700, 
                 timeout: false, 
                 pager: '.ss3_wrapper .slideshow_paging', 
                 pagerAnchorBuilder: pager_create,
                 prev: '.ss3_wrapper .slideshow_prev',
                 next: '.ss3_wrapper .slideshow_next'
        });
        resetModalSize($("#imgModal"));
        $("#imgModal").modal("show");
       
  });
  $(document).on("click",".im_r",function(){
        var tbody_image = $("#tbody_image");
        var tr    = $(this).parents("tr");
        var ids = [];
        tr.remove();
        tbody_image.find("tr").each(function(key){
             ids.push($(this).attr("data-id"));
             var num = key+1;
             $(this).find("td").eq(0).html(num);
        });
        $("#report_img_ids").val(ids.join(","));
  });
  $("#addProdGroup").on("click",function(){
        $("#group-pro-table").html("");

        $("#addproGroupModal").modal("show");
  });
function fileOnchange(){
          var reader = new FileReader();  
          reader.readAsDataURL($("#banner_img_id")[0].files[0]);
          reader.onload = function(evt){ 
               var  base64Code = this.result;
               $("#banner_img").attr("src",base64Code); 
               $(".plus-img").addClass("hidden");
         };    
}
function pager_create(id, slide) {
    var thumb = $('.thumb', $(slide)).html();
    var title = $('h4 a', $(slide)).html();
    var add_first = (id==0)?' class="first"':'';
    return '<li><a href="#" title="'+title+'"'+add_first+'>'+thumb+'</a></li>';
};
$(".slideshow_prev").click(function(){
    resetModalSize($("#imgModal"));
});
$(".slideshow_next").click(function(){
   resetModalSize($("#imgModal"));
});
function resetModalSize($modal){
    $modal.find(".modal-content").find(".slideshow_item").each(function(key){
            console.log($(this).find("img").height());
           $(this).css({
                     "background-color":"rgba(0,0,0,0)"
           });
    });
}
$("#addprogroup-all-checked").on("change",function(){
    var that = this;
    $("#group-addproModal").find("tbody").find("input").each(function(){
            if(!$(that).prop("checked")){
                $(this).prop("checked",false);
            }
            else
            {
                $(this).prop("checked",true);
            }
    });
});
$("#addpro-all-checked").on("change",function(){
    var that = this;
    $("#addproModal").find("tbody").find("input").each(function(){
            if(!$(that).prop("checked")){
                $(this).prop("checked",false);
            }
            else
            {
                $(this).prop("checked",true);
            }
    });
});
