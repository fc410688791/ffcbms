/*// Generated by CoffeeScript 1.6.3
(function() {
  $(function() {
    $(document).on("click", "#del ", function(event) {
      var $this;
      event.preventDefault();
      event.stopPropagation();
      $this = $(this);
      $this.closest('.brick').remove();
      return $('.placeholder').gridly('layout');
    });
    $(document).on("click", ".add", function(event) {

      event.preventDefault();
      event.stopPropagation();
        var x=$("#x").val();
        var y=$("#y").val();
        var width =$("#width").val();
        var height=$("#height").val();
        var brick;
        brick = '<div class="brick small" style="width:'+width+'px;height:'+height+'px"><div class=" action" style="width:'+width+'px;"><a class="sta_ft_a_l" class="del" href="javascript:;">删除</a>|<a class="sta_ft_a_r" id="content-config" href="javascript:void(0);">内容配置</a></div></div>';
      $('.placeholder').append(brick);
      return $('.placeholder').gridly();

    });
    return $('.placeholder').gridly();
  });

}).call(this);*/