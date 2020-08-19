<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script src="<?php echo $assets_dir; ?>/js/echarts.min.js"></script>
<style type="text/css">
.sort-field{
	float:left;
	height:40px;
	line-height:40px;
}
.sort-button{
	float:left;
	width:15px;
}
.sort{
	cursor:pointer;
	width:15px;
}
#title-page_name{
	cursor:pointer;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-title">
                    <div class='with-border'>
                        <div class="btn-group menuBar">
                            <span class="btn btn-default chartbtn" data-viem='behavior'>行为分析</span>
                            <span class="btn btn-default chartbtn" data-viem='source'>来源分析</span>
                            <span class="btn btn-primary chartbtn" data-viem='page'>页面分析</span>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <h3>页面分析</h3>
                    <div style="margin:25px 0;" class="col-xs-12">
                        <select name="time_type" id="time_type" style="height:30px;">
                            <option value="1" <?php echo $time_type == 1?'selected':''; ?>>最近7天</option>
                            <option value="2" <?php echo $time_type == 2?'selected':''; ?>>最近30天</option>
                            <option value="3" <?php echo $time_type == 3?'selected':''; ?>>自定义</option>
                        </select>
                        <input type="text" name="reservation" id="reservation" value="<?php echo isset($reservation)?$reservation:''; ?>" style="margin-left:25px;height:30px;border:0px;"/>
                        <span style="float:right;">
                            <a id="download" href="javascript:void(0);">下载</a>
                        </span>
                    </div>
                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr style="background-color:#c2c2c2;">
                                <td>
                                    <div id='title-page_name'  class="sort-field">页面名称</div>
                                </td>
                                <td>
                                    <div class="sort-field">访问次数</div>
                                    <div class="sort-button">
                                        <span id="a_u" class="sort" style="<?php if ($o=='a_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="a_d" class="sort" style="<?php if ($o=='a_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field">访问人数</div>
                                    <div class="sort-button">
                                        <span id="b_u" class="sort" style="<?php if ($o=='b_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="b_d" class="sort" style="<?php if ($o=='b_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field">次均时长(s)</div>
                                    <div class="sort-button">
                                        <span id="c_u" class="sort" style="<?php if ($o=='c_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="c_d" class="sort" style="<?php if ($o=='c_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field">入口页次数</div>
                                    <div class="sort-button">
                                        <span id="d_u" class="sort" style="<?php if ($o=='d_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="d_d" class="sort" style="<?php if ($o=='d_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field">退出页次数</div>
                                    <div class="sort-button">
                                        <span id="e_u" class="sort" style="<?php if ($o=='e_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="e_d" class="sort" style="<?php if ($o=='e_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field">退出率</div>
                                    <div class="sort-button">
                                        <span id="f_u" class="sort" style="<?php if ($o=='f_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="f_d" class="sort" style="<?php if ($o=='f_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sort-field">分享次数</div>
                                    <div class="sort-button">
                                        <span id="g_u" class="sort" style="<?php if ($o=='g_u'){echo 'color:red;';} ?>">▲</span>
                                        <span id="g_d" class="sort" style="<?php if ($o=='g_d'){echo 'color:red;';} ?>">▼</span>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($series as $info): ?>
                            <tr>
                                <td><?php $page_type_id = $info['page_type_id']; echo $page_option[$page_type_id]; ?></td>
                                <td><?php echo $info['access_count']; ?></td>
                                <td><?php echo $info['access_user_count']; ?></td>
                                <td><?php echo $info['avg_time']; ?></td>
                                <td><?php echo $info['entry_count']; ?></td>
                                <td><?php echo $info['exit_count']; ?></td>
                                <td><?php echo $info['exit_rate']; ?></td>
                                <td><?php echo $info['share_count']; ?></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(function(){
    $('#reservation').daterangepicker({
      locale: {
          format: 'YYYY-MM-DD',
          applyLabel: '确认',
          cancelLabel: '取消',
          daysOfWeek: ['日', '一', '二', '三', '四', '五','六'],
          monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
          firstDay: 1
      }
    }).on('apply.daterangepicker', function (ev, picker) {
    	get_series();
    });
    var time_type = $('#time_type').val();
	if(time_type==3){
		$("#reservation").show();
	}else{
		$("#reservation").hide();
	}
});

$("#time_type").on('change',function(){
	var time_type = $(this).val();
	if(time_type==3){
		$("#reservation").show();
	}else{
		$("#reservation").hide();
		get_series();
	}
});
function get_series(){
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
	var to_url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem=page';
    location.href = to_url+'&time_type='+time_type+'&reservation='+reservation;
}

$('#download').on('click', function(){
	var time_type = $("#time_type").val();
	var reservation = $("#reservation").val();
    var to_url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem=page&operate=download';
    location.href = to_url+'&time_type='+time_type+'&reservation='+reservation;
});
$('.chartbtn').on('click', function(){
    var viem = $(this).data('viem');
    var to_url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem='+viem;
    location.href = to_url;
});
$("#title-page_name").on("click",function(e){
	var url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem=page';
    location.href = url;
});
$(".sort").on("click",function(e){
	var id = e.target.id;
	var url = '<?php echo base_url('UsageAnalysis/index');?>'+'?viem=page&o='+id;
    location.href = url;
});
</script>