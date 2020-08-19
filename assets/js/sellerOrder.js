	$('.chartbtn').on('click', function(){
		$("input[name=by_menubar]").val($(this).data('bm'));
		
		activityMenuBar();
		$('#selectForm').submit();
	})

	/**
	 * [activityMenuBar 激活状态的状态菜单栏]
	 *
	 * @author leeprince
	 * @param  {[type]} $elem [description]
	 * @return {[type]}       [description]
	 */
	function activityMenuBar()
	{
		var actElem = $("input[name=by_menubar]").val();
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

	/**
	 * [setOrderStatusLabel 设置订单状态与操作这两列数据
	 	// 订单状态:0=>待支付,1=>支付成功(待接单),2=>支付取消，3=>服务中,4=>待确认,5=>已完成
		// 订单退款状态:0=>待退款,1=>退款成功;2=>退款失败;
	 *
	 *
	 * @author leepirnce
	 */	
	function setOrderStatusLabel()
	{

		var oLabelElem   = $('.orderStatusLabel');

		oLabelElem.each(function(){
			var selfThis = $(this);
			var opElem = selfThis.parent().next();

			var optHtml = '无';
			var oStatus      = selfThis.data('ordersta'); // 订单状态
			var rStatus      = selfThis.data('refundsta'); // 退款状态
			var raccept      = selfThis.data('accept'); // 受理状态
			var out_trade_no = selfThis.data('oid'); // 受理状态
			var refundReason = selfThis.data('refundreason'); // 受理状态

			// <span data-toggle="tooltip" title="3 New Messages" class="badge bg-light-blue">3</span>
			var optHtmlPrerefund = '<button type="button"  data-toggle="tooltip" title="'+refundReason+'" class="btn btn-sm btn-info" data-opt="comfirmRefund" data-optid='+out_trade_no+' style="margin-right:20px">确认退款</button>'+
					'<button type="button" class="btn btn-sm btn-default" data-opt="cancelRefund" data-optid='+out_trade_no+'>取消退款</button>';
			var optHtmlPretask = '<button type="button" class="btn btn-sm btn-info" data-opt="confirmReceipt" data-optid='+out_trade_no+' style="margin-right:20px">确认接单</button>'+
								'<button type="button" class="btn btn-sm btn-default" data-opt="cancelReceipt" data-optid='+out_trade_no+' >取消接单</button>';
			var optHtmlComplete = '<button type="button" class="btn btn-sm btn-success" data-opt="completeService" data-optid='+out_trade_no+' style="margin-right:20px">完成服务</button>';
			var optHtmlpreconf = '若用户不确认, 订单将于24个小时后自动确认'
			if (raccept == 2) {
				selfThis.addClass('text-yellow').html('不受理此退款');

			} else if (rStatus != "" || (rStatus == '0')) {
				switch (rStatus) {
					case 0:
						selfThis.addClass('text-yellow').html('待退款');
						optHtml = optHtmlPrerefund;
						break;
					case 1:
						selfThis.addClass('text-green').html('退款成功');
						break;
					case 2:
						selfThis.addClass('text-yellow').html('待退款');
						optHtml = optHtmlPrerefund;
						break;
					default:
						alert('退款状态发生错误');
						break;
				}
			} else {
				switch (oStatus) {
					case 0:
						selfThis.addClass('text-aqua').html('待支付');
						break;
					case 1:
						selfThis.addClass('text-green').html('待接单'); // 支付成功
						optHtml = optHtmlPretask;
						break;
					case 3:
						selfThis.addClass('text-green').html('服务中');
						optHtml = optHtmlComplete;
						break;
					case 4:
						selfThis.addClass('text-yellow').html('待确认');
						optHtml = optHtmlpreconf
						break;
					case 5:
						selfThis.addClass('text-green').html('已完成');
						break;
					default:
				   		alert('订单状态发生错误');
						break;
				}
			}
			opElem.html(optHtml);

		});
	}

	$('.operate').on('click', 'button', function(){
		var selfThis = $(this);
		
		var opt = selfThis.data('opt'); // comfirmRefund=>确认退款, cancelRefund=>取消退款; confirmReceipt=>确认接单; cancelReceipt=>取消接单; completeService=>完成服务
		var out_trade_no = selfThis.data('optid');

		var isContinue = statusOptButton(opt, out_trade_no);
		if (isContinue) {
			var postData = {opt:opt, out_trade_no:out_trade_no};
			pajax(
                statusOprateUrl,
                postData, 
				function(res) {
					if (res.code == 0) {
						alert(res.data);
						selfThis.parent().text(res.data);
						selfThis.parent().prev('.orderStatusLabel').text(res.data)
					} else {
						alert(res.data);
					}
				}
			);
		}
	});

	/**
	 * [pajax 按钮发出请求]
	 *
	 * @author leeprince
	 * @param  {[type]}   selfThis [description]
	 * @param  {[type]}   data     [description]
	 * @param  {Function} callback [description]
	 * @param  {[type]}   dateType [description]
	 * @return {[type]}            [description]
	 */
	function pajax(url, postData, callback, dateType='json')
	{
		$.post(url,
			postData,
			function(res){
				callback(res);
			},
			dateType
		);
	}

	/**
	 * [statusOptButton 根据操作按钮的操作类型判断是否直接请求 ajax]
	 *
	 * @author leeprince
	 * @param  {[type]} opt [description]
	 * @return {[type]}     [description]
	 */
	function statusOptButton(opt, out_trade_no)
	{
		console.log(opt);
		switch (opt) {
			case 'cancelReceipt':
                $('#cOutTradeNo').val(out_trade_no);
                $('#cancelModal-label').html("订单编号 #"+out_trade_no);
                $('#cancelSmt').prop('data-opt', opt);

                $('#cTitle').html('取消接单原因');
				$('#cReason').val('');
		        $("#cancelModal").modal('show');
				return false;
				break;
			case 'cancelRefund':
                $('#cOutTradeNo').val(out_trade_no);
                $('#cancelModal-label').html("订单编号 #"+out_trade_no);
                $('#cancelSmt').prop('data-opt', opt);

				$('#cTitle').html('取消退款原因');
                $('#cReason').val('');
		        $("#cancelModal").modal('show');
				return false;
				break;
			default:
				return true;
				break;
		}
	}

	/**
	 * [cancelFunc 取消接单/取消退款]
	 *
	 * @author leeprince
	 * @param  {[type]} selfThis [description]
	 * @return {[type]}          [description]
	 */
    function cancelFunc(selfThis)
    {
		var opt              = $(selfThis).prop('data-opt')
		var out_trade_no     = $('#cOutTradeNo').val()
		var no_accept_reason = $('#cReason').val()

		if (opt == '' || out_trade_no == '' || no_accept_reason == '') {
			alert('参数错误');
			console.log('opt:'+opt+';out_trade_no:'+out_trade_no+';no_accept_reason:'+no_accept_reason)
			return false;
		}

		postData = {opt:opt, out_trade_no:out_trade_no, no_accept_reason:no_accept_reason};
		pajax(statusOprateUrl, postData, function(res) {
			var optButtonElem = $('#optid-'+out_trade_no)
			if (res.code == 0) {
				$("#cancelModal").modal('hide');
				alert(res.data);
				optButtonElem.text(res.data)
				optButtonElem.prev('.orderStatusLabel').text(res.data)
			} else {
				alert(res.data)
			}
		})
    }

    /**
     * [showProduct 显示商品信息]
     *
     * @author leeprince
     * @param  {[type]} out_trade_no [description]
     * @return {[type]}              [description]
     */
    function showProduct(selfThis, out_trade_no)
    {
        var showProductElem = $('#pdn-'+out_trade_no);
        if (showProductElem.html()) {
            showProductElem.show();
            return true;
        }
        var postData = {out_trade_no:out_trade_no}
        pajax(
            showProductDetailsUrl,
            postData, 
            function(res) {
                if (res.code == 0) {
                    var data = res.data
                    var productHtmlHead = "<div class='sellerOrderProductTip' id='pdn-"+out_trade_no+"'><table class='sellerOrderProductTipTable'>"+
                                            "<tr><th>商品名称</th><th>商品价格(元)</th><th>商品数量</th></tr>";
                    var productHtmlBody = '';
                    for (var f in data) {
                        productHtmlBody = productHtmlBody+ "<tr><td>"+data[f].name+"</td><td>"+data[f].price+"</td><td>"+data[f].num+"</td></tr>";
                    }
                } else {
                    alert(res.data);
                }

                var productHtmlFooter = "</table></div>";
                var productHtml =  productHtmlHead + productHtmlBody + productHtmlFooter;
                  
                $(selfThis).append(productHtml); 
            }
        );
    }

    /**
     * [hideProduct 隐藏弹窗]
     * @param  {[type]} selfThis     [description]
     * @param  {[type]} out_trade_no [description]
     * @return {[type]}              [description]
     */
    function hideProduct(selfThis, out_trade_no)
    {
        hideProductElem = $('#pdn-'+out_trade_no).hide();
        return true;
    } 
