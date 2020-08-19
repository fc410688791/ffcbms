$(function () {
  // toastr 自定义样式
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "positionClass": "toast-top-center",
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "1500",
    "extendedTimeOut": "2000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }

  
});
function addUser(){
  $('#userModal').modal({
    backdrop: 'static', // 空白处不关闭.
    keyboard: false // ESC 键盘不关闭.
  });

  $('#userModal').modal('show');
};

function submit() {
  var id          = $("input[name=id]").val();
  var name          = $("input[name=name]").val();
  var openid          = $("input[name=openid]").val();

  if (name == '' || openid == '') {
    toastr.error("缺少必要参数.");
    return false;
  }
  var url = '';
  if (id != '') {
    url = "<?php echo base_url('CompanyUser/update') ?>";
  } else {
    url = "<?php echo base_url('CompanyUser/create') ?>";
  }

  var formData = $('#user_form').serialize();
  $.ajax({
    type: 'POST',
    url: url,
    data: formData,
    dataType: 'json',
    async:false,//同步请求
    success: function(data){
      if(data.code==200){
        toastr.success(data.msg);
        location.reload();
      }else{
        toastr.error(data.msg);
      }
      
    },
    error: function(xhr, type){
       toastr.error(detailLabel+"未知错误");
    }
  });
}

function delUser($id, $name) {
    layer.confirm('确定删除 「'+$name+'」用户吗 ?', {icon: 3, title:'提示'}, function(index){
      var index;
      $.ajax({
        url: "<?php echo base_url('CompanyUser/del') ?>",
        type: 'POST',
        dataType: 'json',
        data: {id:$id},
        beforeSend: function () {
          index_load = layer.load();
        },
        success: function (result) {
          if (result.code == 200) {
            layer.close(index);
            toastr.success(result.msg);
            location.reload();
          } else {
            layer.close(index_load);
            toastr.error(result.msg);
          }
        },
        error: function () {
          toastr.error('删除异常');
        },
        complete: function () {
          layer.close(index_load);
        }
      });
      
      return true;
    });
}


