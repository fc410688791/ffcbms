<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?php echo $assets_dir; ?>/css/monitor.css?v=201912252" >

<!-- 外置数据 -->
<div class='hide'><input id='assetPath' value='<?php echo $assets_dir; ?>'></div>
<div class='hide'><input id='triadBindList' value='<?php echo $triadBindList; ?>'></div>

<section class="content">
<div class="box">
    <div class="box-header with-border">
        <b>充电桩总数：</b><span><?php echo $triadCount ?> &nbsp; &nbsp; |&nbsp; &nbsp;
        <b>用户使用设备总数：</b><span><?php echo $machineCount ?>
    </div>
</div>
<div class="box ">
    <div class="box-header with-border">
        <b><i>查询操作设置</i></b>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class='inlineBlock'>查询设备类型：
            充电桩<input type="radio" name="triggerTypeMachine" checked value='stake' >
            充电桩下所有路口<input type="radio" name="triggerTypeMachine"  value='inter' >
        </div> &nbsp; &nbsp; |&nbsp; &nbsp;
        <div class='inlineBlock'>触发一次查询类型：
            自动<input type="radio" name="triggerTypeFind" value='auto' checked>
            点击<input type="radio" name="triggerTypeFind" value='click' >
        </div> &nbsp; &nbsp; |&nbsp; &nbsp;
        <div class='inlineBlock'>打开即时查询刷新：<input type="checkbox" name="refresh"><input type="number" name="" id='refreshTime' style='width:60px' value=1000>ms</div>
    </div>
    <!-- /.box-body -->
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="form-inline form-group">
            <select name="province" class="form-control select2" id="merchantBtn" style='width:150px;margin-right: 50px;'>
                <option value=0>选择投站点</option> 
                <?php foreach ($merchantList as $key => $value) { ?>
                    <option value=<?php echo $value['id'] ?>><?php echo $value['name'].'('.$value["count"].')' ?></option>
                <?php } ?>
            </select>
            <button class='btn btn-success merchantReload' style="display: none;"><i class='fa fa-spinner'></i>批量刷新</button>
            <div class="form-control visibility"></div>
            <select name="province" class="form-control select2" id="province" style='width:150px;'>
                <!-- 省 -->
                <option value=0>选择地址</option> 
                <?php foreach ($provinceList as $key => $value) { ?>
                    <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php } ?>
            </select>
            <select class="form-control select2" name="city" id='city' style='width:150px;'>
                <!-- 市/区 -->
                <option value=0>选择地址</option>
            </select>
            <select class="form-control select2" id='street' name="street" style='width:150px;'>
                <!-- 区/街道/镇 -->
                <option value=0>选择地址</option> 
            </select>
            <select class="form-control select2" id='village' name="village" style='width:150px;'>
                <!-- 街道/居委会 -->
                <option value=0>选择地址</option>
            </select>
            <button class="btn btn-success find">查询</button>
            <button class="btn btn-danger reset">重置</button>
        </div>
        <div class="btn-group statusGrounBtn">
            <label>
                <button class='btn statusBtn statusBtnAct'><i class='siconAll'></i><span>全部(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconStakeOnline'><img src="<?php echo $assets_dir; ?>/img/online.pic" class='statusBtnImg'></i><span>在线(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconStakeOffline'><img src="<?php echo $assets_dir; ?>/img/offline.pic" class='statusBtnImg'></i><span>不在线(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconPre'></i><span>待用(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconUse'></i><span>使用中(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconOffline'></i><span>掉线(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconError'></i><span>故障(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconTimeout'></i><span>超时(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='sicon4Gofflime'></i><span>4G离线(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconNocommend'></i><span>异常命令字(0)</span></button>
            </label>
            <label>
                <button class='btn statusBtn'><i class='siconAliex'></i><span>阿里云异常(0)</span></button>
            </label>
        </div>
    </div>
    <div class="box-body" id="findMachineInfo"></div>
</div>
</section>
<script src="<?php echo $assets_dir ?>/js/autoselectv1.js?v=adaad"></script>
<script src='<?php echo $assets_dir ?>/bower_components/layer-v3.1.1/layer/layer.js'></script>
<script src="<?php echo $assets_dir ?>/js/monitor/index.js?v=1"></script>
<!-- 查看详情 - detailModal -end -->
<script type="text/javascript">

</script>
























