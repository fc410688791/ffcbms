"use strict";

/**
 * [监控视图]
 *
 * @Author leeprince:2019-06-06T11:16:46+0800
 * @param  {Object}                           ){                 $('.select2').select2()        toastr.options [description]
 * @return {[type]}                               [description]
 */
$(function(){
    $('.select2').select2()

    // 拖动排序
    // Make the dashboard widgets sortable Using jquery UI
    $('.connectedSortable').sortable({
      placeholder         : 'sort-highlight',
      connectWith         : '.connectedSortable',
      handle              : '.headerHander',
      forcePlaceholderSize: true,
      zIndex              : 999999
    });
    $('.connectedSortable .headerHander, .connectedSortable .nav-tabs-custom').css('cursor', 'move');

    // toastr 自定义样式
    toastr.options = {
        "closeButton": true, // 设置显示"X" 关闭按钮
        "preventDuplicates": true, // 重复内容的提示框只出现一次，无论提示框是打开还是关闭
        "debug": false,
        "positionClass": "toast-top-center", // toast-top-left;toast-top-right;toast-top-center;toast-top-full-width;toast-bottom-right;toast-bottom-left;toast-bottom-center;toast-bottom-full-width
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "1000", // 设置toastr过多久关闭
        "extendedTimeOut": "2000", // 该timeout会更新关闭所需的timeout.
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    initView()
})
var assetPath = $('#assetPath').val()
var triadBindList = JSON.parse($('#triadBindList').val()) 


// 地区筛选联动
var selectConfig = {
    requestUrl:"index",
    selectData:[
        {
            elem:"province", // 上级元素ID
            elemFor:"city", // 当前元素ID
            elemForDefaultVal:"选择地址", // 当前元素默认值
            method:"getAddress" // 请求接口的data对象的键固定为:key
        },
        {
            elem:"city", 
            elemFor:"street",
            elemForDefaultVal:"选择地址",
            method:"getAddress"
        },
        {
            elem:"street", 
            elemFor:"village",
            elemForDefaultVal:"选择地址",
            method:"getAddress"
            // extraElem:['city'],// 额外 elem 对应的值;这里是对应市(elem:"city")的.请求接口的data对象的键为该extraElem里的值;
        },
    ]
}
Autoselectv1(selectConfig)
// 地区筛选联动 - end



// 投放点筛选
$('#merchantBtn').on('change', function(){
    var mid = $(this).val()
    if (mid == 0) {
        initView()
        $('.merchantReload').hide()
        return
    }
    $('.merchantReload').show()

    initAddress()

    eAjax('index', {'method': 'byMerchant', 'id':mid}, res => {
        var code = res.code
        if (code != 0) {
            toastr.error(res.msg)
            return
        }
        setMachineList(res.data)
    })
})

// 投放点下的设置桩列表；~~设置设备列表~~
var allMachineCount = 0
function setMachineList(data) {
    allMachineCount = 0
    initView()

    // 投放点
    for (var merchantInfoKey in data) {
        var merchantInfo = data[merchantInfoKey]

        var merchantName = merchantInfo.merchantName
        var positionName = merchantInfo.positionName
        var triadGroup   = merchantInfo.triadGroup

        var merchantInfoHtml = 
            "<div class='merchantInfo'>" +
                "<div class='merchantName'>投放点位置："+merchantName+"</div>"+
                "<div class='merchantPositionName'>（"+positionName+"）</div>"+
            "</div>"

        var setStakeGroupTriadGroupHtml = setStakeGroupTriadGroup(triadGroup)
        var stakeHtml = setStakeGroupTriadGroupHtml['stakeHtml']
        var interHtml = setStakeGroupTriadGroupHtml['interHtml']


        var stakeInfoHtml = 
            "<div class='stakeInfo'>"+
                stakeHtml+
            "</div>"

        var triadInfoHtml = 
            "<div class='triadInfo'>"+
                interHtml+
            "</div>"
        
        var boxHtml = 
            "<div class='MerchantList'>" +
                merchantInfoHtml + stakeInfoHtml + triadInfoHtml + 
            "</div>"

        $('#findMachineInfo').append(boxHtml)

        if (settingTrigger('isStake')) {
            $('.triadInfo').hide()
        } else {
            $('.stakeInfo').hide()
        }
    }

    statusGroupBtnsiconAll()

    timelyFindMachine()
}

/** 设置一个桩及一个桩下包含一个或者多个模组，一个模组包含一个或者多个亚克力板，一个亚克力板啊包含一个或者多个用户二维码 */
function setStakeGroupTriadGroup(traidGroup)
{
    let stakeHtml = ''
    let interHtml = ''
    // 一个桩标记即一个或者多个模组的标记
    for (var i in traidGroup) {
        var bind_triad_mark = i
        let lineData  = traidGroup[i]

        // 一个桩的所有HTML
        let fHtml = ''
        // 模组数
        var triadNum = 1

        var fHtmlF = ''

        // 桩下的模组
        for (var j in lineData) {
            let traid_id  = j
            let triadMachineData     = lineData[j]

            // 模组对应亚克力板总数
            var bind_side_num = triadBindList[traid_id]['bind_side_num']
            // 一个亚克力板对应的用户二维码数
            var bind_plate_code_num = triadBindList[traid_id]['bind_plate_code_num']

            // 模组对应亚克力板初始值
            let sideNum = 1
            // 一个亚克力板对应的用户二维码数初始值
            let sidePlateNum = 1

            // 模组下的设备
            for (var ii in triadMachineData) {
                let machineData = triadMachineData[ii]

                var machine_id      = machineData.machine_id
                var status          = machineData.status
                var product_key     = machineData.product_key
                var device_name     = machineData.device_name
                var device_secret   = machineData.device_secret
                var inter_num       = machineData.inter_num
                var position        = machineData.position

                var addData = "data-machine_id="+machine_id+" data-bind_triad_mark="+bind_triad_mark+" data-product_key="+product_key+" data-device_name="+device_name+" data-device_secret="+device_secret+" data-inter_num="+inter_num+" data-status="+status
                
                if ( ! settingTrigger('isStake')) {
                    allMachineCount++
                }
                let addDataCode = addData + " id=uCode-"+machine_id

                var userNum = inter_num

                fHtml += 
                "<div class='machineBox'>"+
                        "<div class='userNum'>"+
                        "<span class='mNum'>"+userNum+"</span>"+
                        "<div class='mNumDiv' id="+machine_id+"></div>"+
                    "</div>"+
                    "<div class='userCode'>"+
                        "<span class='uCode' "+addDataCode+">"+machine_id+"</span>"+
                        "<div class='uCodeDiv'></div>"+
                    "</div>"+
                "</div>"

                // 一个亚克力板
                if (sidePlateNum%bind_plate_code_num == 0) {
                    fHtmlF += "<div class='machineLine font'>" +
                                "<div class='uPosition'><span class='pText'> 第"+triadNum+"个模组-"+sideNum+"面</span><div class='pDiv'></div></div>"+
                            fHtml + "</div>"
                    ++sideNum
                    fHtml = ''
                }
                sidePlateNum++
            }

            triadNum++
        }

        let triadNumShow = --triadNum

        interHtml += 
            "<div class='interInfo' id=inter-"+bind_triad_mark+">"+
                "<div class='machinePositonName'>"+position+"【此桩共"+triadNumShow+"个模组；一个模组"+bind_side_num+"个亚克力板；一个亚克力板"+bind_plate_code_num+"个二维码】<span class='backStakeInfo' style='display: none'><button class='btn btn-xs btn-primary'>＜返回充电桩</button></span></div>"+
                "<div class='machineLine'>"+
                    fHtmlF + 
                "</div>"+
            "</div>"

        // 桩的展示
        if (settingTrigger('isStake') ) {
            allMachineCount++
            var addDataStake = addData + " id=uStake-"+machine_id
            stakeHtml += 
                "<div class='stakeInfoIterm uStake' "+addDataStake+">"+
                    "<div class='stakeInfoItermImg'><img src="+assetPath+"/img/prestatus.pic  id="+device_name+"></div>"+
                    "<div class='stakeInfoItermPosition'><span>"+position+"</span></div>"+
                "</div>"
        }
    }

    var setStakeGroupTriadGroupHtml = {
        'stakeHtml': stakeHtml,
        'interHtml': interHtml,
    }

    return setStakeGroupTriadGroupHtml

}

/** 这是按钮组的“全部”按钮 */
function statusGroupBtnsiconAll() {
    $('.siconAll').parent().find('span').html("全部("+allMachineCount+")")
    $('.statusBtn').eq(0).addClass('statusBtnAct')
    $('.statusGrounBtn').find('label').first().show()
}


// 是否点击才触发单个设备查询
function timelyFindMachine()
{
    if (settingTrigger('isAuto')) {
        findMachineStatus()
    }
    return
}

// 点击查询设备
$('.find').on('click', function(){
    initMerchant()

    var province = $('#province').val()
    var city     = $('#city').val()
    var street   = $('#street').val()
    var village  = $('#village').val()

    if (province == 0) {
        toastr.error('请选择地址')
        return
    }

    var data ={
        method: 'byPosition',
        province: province,
        city: city,
        street: street,
        village: village,
    }

    $("#findMachineInfo").html('')
    eAjax('index', data, res => {
        var code = res.code
        if (code != 0) {
            toastr.error(res.msg)
            return
        }

        setMachineList(res.data)
    })
})
// 重置
$('.reset').on('click', function(){
    initView()
    initAddress()
    initMerchant()
})

// 初始化-设备列表；状态组
function initView()
{
    $("#findMachineInfo").html('')
    $('.statusGrounBtn').find('label').hide()
    $('.statusBtn').removeClass('statusBtnAct')
    // $('#merchantReload').hide()
}
// 初始化地址选择
function initAddress()
{
    $('#province').val(0)
    $('#province').trigger('change')
}
// 初始化投放点列表
function initMerchant()
{
    $('#merchantBtn').val(0)
    $('#merchantBtn').trigger('change')
}

// 整理实时查询设备状态的信息
var layerIndex = 0 // 加载层

var siconStakeOnlineCount = 0
var siconStakeOfflineCount = 0

var preCount = 0
var useCount = 0
var offCount = 0
var errCount = 0
var outCount = 0
var mofCount = 0
var nocCount = 0
var aliCount = 0

// 查询单个设备ID或者一个模块下所有设备ID的状态或者一个桩下所有模块的桩设备
function findMachineStatus(machine_id = '', device_name = '',  bindDeviceMark = '') 
{
    setDefaultNum()
    
    layerIndex = layer.load(0, {
      shade: [0.1,'#333'] //0.1透明度的白色背景
    });

    var data = []

    var elMark = ''
    if (settingTrigger('isStake')) { // 是否为充电桩
        elMark = 'uStake'
    } else {
        elMark = 'uCode'
    }

    // machine_id 不为空则查询一个设备数据
    if (machine_id != '') {
        var el = $("#"+elMark+'-'+machine_id)

        var itemData = {
            'method': 'findMachineStatus',
            'machine_id': machine_id,
            'machine_status': el.data('status'),
            'product_key': el.data('product_key'),
            'device_name': el.data('device_name'),
            'device_secret': el.data('device_secret'),
            'inter_num':el.data('inter_num'),
        }
        data.push(itemData)
    } else if (device_name != '' || bindDeviceMark != '') {
        let el
        if (device_name != '') {
            el = $('#inter-'+device_name+' .'+elMark)
        } else {
            el = $('#inter-'+bindDeviceMark+' .'+elMark)
        }

        el.each(function() {
            var machine_id     = $(this).data('machine_id')
            var machine_status = $(this).data('status')
            var product_key    = $(this).data('product_key')
            var device_name    = $(this).data('device_name')
            var device_secret  = $(this).data('device_secret')
            var inter_num      = $(this).data('inter_num')
            
            var itemData = {
                'method': 'findMachineStatus',
                'machine_id': machine_id,
                'machine_status': machine_status,
                'product_key': product_key,
                'device_name': device_name,
                'device_secret': device_secret,
                'inter_num': inter_num,
            }
            data.push(itemData)
        }) 
    } else {
        var el = $('.'+elMark)
        el.each(function() {
            var machine_id     = $(this).data('machine_id')
            var machine_status = $(this).data('status')
            var product_key    = $(this).data('product_key')
            var device_name    = $(this).data('device_name')
            var device_secret  = $(this).data('device_secret')
            var inter_num      = $(this).data('inter_num')
            
            var itemData = {
                'method': 'findMachineStatus',
                'machine_id': machine_id,
                'machine_status': machine_status,
                'product_key': product_key,
                'device_name': device_name,
                'device_secret': device_secret,
                'inter_num': inter_num,
            }
            data.push(itemData)
        }) 
    }
    startOperation(data, 0)
}
// 实际操作设备
function startOperation(data, i) {
    setTimeout(function() {
        eAjax('index', data[i], res => {

            if (res.code != 0) {
                toastr.error(res.msg)
            } else {
                var findData = res.data
                var findMachineId = findData.machine_id
                var device_name = findData.device_name
                var findStatus = findData.status

                if (settingTrigger('isStake')) {
                    var el = $('#'+device_name)
                    el.data('return_status', findStatus)
                    if (findStatus == 1 ||
                        findStatus == 2 ||
                        findStatus == 3 ||
                        findStatus == 4 ||
                        findStatus == 5 ||
                        findStatus == 7) {
                        el.prop('src', assetPath+'/img/online.pic')
                        el.parents('.stakeInfoIterm').attr('data-bstatus', 'b-siconStakeOnline')

                        ++siconStakeOnlineCount
                    } else if (findStatus == 6 ||
                            findStatus == 8) {
                        el.prop('src', assetPath+'/img/offline.pic')
                        el.parents('.stakeInfoIterm').attr('data-bstatus', 'b-siconStakeOffline')

                        ++siconStakeOfflineCount
                    } else {
                        toastr.error('返回状态错误-machine_id:'+findMachineId)
                    }
                } else {
                    var el = $('#'+findMachineId)
                    el.data('return_status', findStatus)
                    switch (findStatus) {
                        case 1:
                            el.prop('style', 'background-color:#ffcc02')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconPre')
                            ++preCount
                            break
                        case 2:
                            el.prop('style', 'background-color:#01cc33;')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconUse')
                            ++useCount
                            break
                        case 3:
                            el.prop('style', 'background-color:#797979')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconOffline')
                            ++offCount
                            break
                        case 4:
                            el.prop('style', 'background-color:#c00')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconError')
                            ++errCount
                            break
                        case 5:
                            el.prop('style', 'background-color:#b298f1')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconTimeout')
                            ++outCount
                            break
                        case 6:
                            el.prop('style', 'background-color:#4c09ef')
                            el.parents('.machineBox').attr('data-bstatus', 'b-sicon4Gofflime')
                            ++mofCount
                            break
                        case 7:
                            el.prop('style', 'background-color:#e430bd')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconNocommend')
                            ++nocCount
                            break
                        case 8:
                            el.prop('style', 'background-color:#050010')
                            el.parents('.machineBox').attr('data-bstatus', 'b-siconAliex')
                            ++aliCount
                            break
                        default:
                            toastr.error('返回状态错误-machine_id:'+findMachineId)
                            break
                    }
                }

                
            }

            ++i
            if (i >= data.length) {

                if (settingTrigger('isStake')) {
                    $('.siconStakeOnline').parent().find('span').html("在线("+siconStakeOnlineCount+")")
                    $('.siconStakeOffline').parent().find('span').html("不在线("+siconStakeOfflineCount+")")

                    $('.statusGrounBtn').find('label').eq(3).prevAll().show()
                } else {
                    $('.siconPre').parent().find('span').html("待用("+preCount+")")
                    $('.siconUse').parent().find('span').html("使用中("+useCount+")")
                    $('.siconOffline').parent().find('span').html("掉线("+offCount+")")
                    $('.siconError').parent().find('span').html("故障("+errCount+")")
                    $('.siconTimeout').parent().find('span').html("超时("+outCount+")")
                    $('.sicon4Gofflime').parent().find('span').html("4G离线("+mofCount+")")
                    $('.siconNocommend').parent().find('span').html("异常命令字("+nocCount+")")
                    $('.siconAliex').parent().find('span').html("阿里云异常("+aliCount+")")

                    $('.statusGrounBtn').find('label').eq(2).nextAll().show()
                }

                layer.close(layerIndex)


                if (settingTrigger('refresh')) {
                    setTimeout(function() {
                        setDefaultNum()
                        layerIndex = layer.load(0, {
                          shade: [0.1,'#333'] //0.1透明度的白色背景
                        });
                        startOperation(data, 0)
                    }, $('#refreshTime').val())
                }
                return
            } else {
                startOperation(data, i)
            }
        }, '', 'POST', 'json', true)
    }, 100)
}
// 重置数量数字
function setDefaultNum() {
    siconStakeOnlineCount = 0
    siconStakeOfflineCount = 0

    preCount = 0
    useCount = 0
    offCount = 0
    errCount = 0
    outCount = 0
    mofCount = 0
    nocCount = 0
    aliCount = 0
}

/** 操作设置的事件： 是否为充电桩、 是否自动触发一次查询、 是否自动刷新  */
function settingTrigger(type)
{
    switch(type) {
        case 'isStake': // 是否为充电桩
            var isStake = $(':input[name=triggerTypeMachine]:checked').val()
            if (isStake == 'stake') {
                return true
            }
            return false
            break
        case 'isAuto': // 是否自动触发一次查询
            var isAuto = $(':input[name=triggerTypeFind]:checked').val()
            if (isAuto == 'auto') {
                return true
            }
            return false
            break
        case 'refresh': // 是否自动刷新
            if ($(':input[name=refresh]').is(':checked')) {
                return true
            }
            return false
            break
        default:
            toastr.error('设置类型错误')
            break 
    }
}

// 筛选设备状态
$('.statusBtn').on('click', function(){
    $('.statusBtn').removeClass('statusBtnAct')
    $(this).addClass('statusBtnAct')

    var statusBtnClass = $(this).find('i').prop('class')
    var leftbstatus = 'b-'+statusBtnClass

    $('.machineBox').removeClass('machineBoxVisibilityHidden')
    $('.stakeInfoIterm').removeClass('machineBoxVisibilityHidden')

    var selectMark = '.machineBox'
    if (settingTrigger('isStake')) {
        selectMark = '.stakeInfoIterm'
    }

    if (statusBtnClass != 'siconAll') {
        $(selectMark).each(function(){
            var bstatus = $(this).data('bstatus')
            if (bstatus != leftbstatus) {
                $(this).addClass('machineBoxVisibilityHidden')
            }
        })
    }
})

// 设备订单信息弹窗提示或者点击查询单个设备
$("#findMachineInfo").on("click", '.machineBox', function(){
    var machine_id        = $(this).find('.userCode').find('.uCode').data('machine_id')
    var product_key       = $(this).find('.userCode').find('.uCode').data('product_key')
    var device_name       = $(this).find('.userCode').find('.uCode').data('device_name')
    var inter_num         = $(this).find('.userCode').find('.uCode').data('inter_num')
    var machineRealStatus = $('#'+machine_id).data('return_status')

    // 还没有返回状态时，先查询单个设备状态
    if ( ! settingTrigger('isAuto')) {
        findMachineStatus(machine_id)
        return
    }

    var data = {'method': 'findMachine', 'machine_id':machine_id}

    var tipsButtonHtml = " data-product_key="+product_key+" data-device_name="+device_name+" data-inter_num="+inter_num
    eAjax('index', data, res => {
        var isShowUseClearButton = 'hidden'


        if (res.code != 0) {
            var pay_time = '-'
            var product_time = '-'
            var out_trade_no = '-'

            if (machineRealStatus == 2) {
                isShowUseClearButton = ''
            }
            // toastr.error(res.msg)
        } else {
            var order        = res.data
            var out_trade_no = order.out_trade_no
            var product_time = getSplit(order.product_time, '-')
            var pay_time     = new Date(parseInt(order.pay_time) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ')
            var afterTime    = parseInt((new Date()).getTime()/1000) - order.pay_time
            var useTime      = product_time[0]*3600 + product_time[1]*60

            if ((afterTime > useTime) && (machineRealStatus == 2)) {
                isShowUseClearButton = ''
            } else {
                isShowUseClearButton = 'hidden'
            }

            product_time = product_time[0]+"小时"+product_time[1]+"分钟"
        }
        var tipsHtml = "<ul>" + 
                "<li><div class=tipsLine><div class=title>最后一次支付时间:</div><div>"+pay_time+"</div></div><li>"+
                "<li><div class=tipsLine><div class=title>服务时间:</div><div>"+product_time+"</div></div><li>"+
                "<li><div class=tipsLine><div class=title>订单编号:</div><div>"+out_trade_no+"</div></div><li>"+
            "</ul>"+
            "<button class=tipsButton "+isShowUseClearButton+tipsButtonHtml+">清除异常‘在使用中’状态</button>"
        layer.tips(tipsHtml, this, {
          tips: [1, '#9a7c7c'],
          time: 10000, // 0
          area: '400px',
          closeBtn: [1, true]
        })
        return false

    }, {})
})

/**  点击查询单个充电桩
        1. 查询单个桩状态
        2. 查询桩下的所有模组对应设备状态
 */
$('#findMachineInfo').on('click', '.stakeInfoIterm', function(){
    var machine_id = $(this).data('machine_id')
    if ( ! settingTrigger('isAuto')) {
        findMachineStatus(machine_id)
    } else {
        var bind_triad_mark = $(this).data('bind_triad_mark')

        // 
        $(':input[name=triggerTypeMachine][value=inter]').prop('checked', true)

        // 隐藏其他按钮
        $('.statusGrounBtn').find('label').eq(0).nextAll().hide()
        var triadInfoEl = '#inter-'+bind_triad_mark

        // 所有路口显示
        $('.triadInfo').show() 
        // 桩显示
        $(triadInfoEl).show()
        // 其他桩隐藏
        $(triadInfoEl).prevAll().hide()
        $(triadInfoEl).nextAll().hide()
        // 返回桩显示
        $('.backStakeInfo').show()

        // 充电桩隐藏
        $('.stakeInfo').hide() 
        findMachineStatus('', '', bind_triad_mark)
    }
    return 
})

/** 点击返回桩 */
$('#findMachineInfo').on('click', '.backStakeInfo', function() {
    $(':input[name=triggerTypeMachine][value=stake]').prop('checked', true)
    $('.statusGrounBtn').find('label').eq(0).nextAll().hide()
    $('.statusGrounBtn').find('label').eq(3).prevAll().show()
    $('.triadInfo').hide()
    $('.stakeInfo').show()

})

// 清除异常‘在使用中’状态
$(document).on('click', '.tipsButton', function(){
    var dataEl = $(this)
    var machine_id     = dataEl.data('machine_id')
    var product_key    = dataEl.data('product_key')
    var device_name    = dataEl.data('device_name')
    var inter_num      = dataEl.data('inter_num')
    
    var itemData = {
        'method': 'operateMachine',
        'product_key': product_key,
        'device_name': device_name,
        'inter_num': inter_num,
    }
    dataEl.prop('disabled', true)

    eAjax('index', itemData, res => {
        if (res.code != 0) {
            layer.msg(res.msg, {icon: 2})
            dataEl.prop('disabled', false)
            return
        } 
        $(this).hide()
        layer.msg('清除成功', {icon: 1})
    }, {}, 'POST', 'json', true)
})

// 重新刷新
$('.merchantReload').on('click', function() {
    findMachineStatus()
})


$(':input[name=triggerTypeMachine]').on('change', function() {
    $('.merchantReload').hide()
})











/*// 监听总数量的变化，达到总数各状态的数量
var findCountObj = {
    listenAllCount:0
}
Object.defineProperties(findCountObj, {
    listenAllCount: {
        configurable: true,
        get: function() {
            return allMachineCountFind;
        },
        set: function(newValue) {
            listenAllCount = newValue       
            if (listenAllCount == allMachineCount) {
                $('.siconPre').parent().find('span').html("待用("+preCount+")")
                $('.siconUse').parent().find('span').html("使用中("+useCount+")")
                $('.siconOffline').parent().find('span').html("掉线("+offCount+")")
                $('.siconError').parent().find('span').html("故障("+errCount+")")
                $('.siconNoreturn').parent().find('span').html("超时("+mofCount+")")
                $('.sicon4Gofflime').parent().find('span').html("4G离线("+mofCount+")")
                $('.siconNocommend').parent().find('span').html("异常命令字("+nocCount+")")
                $('.siconAliex').parent().find('span').html("阿里云异常("+aliCount+")")

                layer.close(layerIndex)

                $('.statusGrounBtn').find('label').eq(0).nextAll().slideToggle()
            }
        }
    }
})*/





