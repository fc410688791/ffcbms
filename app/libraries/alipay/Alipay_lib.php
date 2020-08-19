<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/alipay/AopSdk.php';

/**
 * 支付宝接口
 */
class Alipay_lib {

	// 将 CodeIgniter 对象赋值给一个变量
	protected $_CI;

	// 请求的类型
	public static $_type;

	// 日志文件名
	public static $_file_name;

	// 实例 AopClient
	protected $_aop;

	/**
	 * [__construct 构造函数]
	 *
	 * @DateTime 2018-03-24
	 * @Author   leeprince
	 * @param    array      $req [description]
	 */
	public function __construct($req = array())
	{
		$this->_CI = &get_instance();

		if ( empty(self::$_type))
		{
			switch ($req['type']) {
				case 'value':
					# code...
					break;
				
				default:
			    	$this->_CI->config->load('alipay_app_config');
					break;
			}
			self::$_type      = $req['type'];
			// 日志文件名与支付宝客户端类型一致
			self::$_file_name = self::$_type;
		}

		// 实例 AopClient
		$this->_aop = new AopClient();
		$this->_aop->getwayurl          = $this->_CI->config->item('getwayurl');
		$this->_aop->appId              = $this->_CI->config->item('appId');
		$this->_aop->rsaPrivateKey      = $this->_CI->config->item('rsaPrivateKey');
		$this->_aop->alipayrsaPublicKey = $this->_CI->config->item('alipayrsaPublicKey');
		$this->_aop->signType           = $this->_CI->config->item('signType');

		

		log_msg(self::$_file_name, 'debug', '[Alipay_lib 请求的支付宝客户端类型]' . self::$_type);
	}


	/**
	 * [alipaySystemOauthToken alipay.system.oauth.token(换取授权访问令牌)]
	 *
	 * @DateTime 2018-03-24
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function alipaySystemOauthToken($opt)
	{
		$request = new AlipaySystemOauthTokenRequest();
		$request->setGrantType('authorization_code');
		if (isset($opt['code']))
		{
			$request->setCode($opt['code']);
		}
		else
		{
			$request->setRefreshToken($opt['refresh_token']);
		}
		$result = $this->_aop->execute($request);

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultUser_id = $result->$responseNode->user_id;
		$resultAccess_token = $result->$responseNode->access_token;
		if(!empty($resultUser_id)&&!empty($resultAccess_token))
		{
			return $result->$responseNode;
		} 
		else 
		{
			log_msg(self::$_file_name, 'ERROR', '换取授权访问令牌发生错误: '. json_encode($result->$responseNode));
			return FALSE;
		}
	}

	/**
	 * [alipayTradeAppPay 统一收单交易支付接口]
	 *
	 * @DateTime 2018-03-28
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function alipayTradeAppPay($ali_params)
	{
		//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
		$request = new AlipayTradeAppPayRequest();
		//SDK已经封装掉了公共参数，这里只需要传入业务参数
		$bizcontent = "{\"body\":\"{$ali_params['body']}\"," 
        . "\"subject\": \"{$ali_params['subject']}\","
        . "\"out_trade_no\": \"{$ali_params['out_trade_no']}\","
        . "\"timeout_express\": \"{$ali_params['timeout_express']}\"," 
        . "\"total_amount\": \"{$ali_params['total_amount']}\","
        . "\"product_code\":\"QUICK_MSECURITY_PAY\""
        . "}";

		$request->setNotifyUrl($ali_params['setNotifyUrl']);
		$request->setBizContent($bizcontent);
		//这里和普通的接口调用不同，使用的是sdkExecute
		$response = $this->_aop->sdkExecute($request);

		//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
		// return htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
		return $response;//就是orderString 可以直接给客户端请求，无需再做处理。
	}

	/**
	 * [rsaCheckV1 验证签名]
	 *
	 * @DateTime 2018-03-28
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function rsaCheckV1()
	{
		return $this->_aop->rsaCheckV1($_POST, NULL, "RSA2");
	}

	/**
	 * [alipayTradeQuery 统一收单线下交易查询
            订单支付时传入的商户订单号,和支付宝交易号不能同时为空。 
            trade_no,out_trade_no如果同时存在优先取trade_no]
	 *
	 * @DateTime 2018-03-28
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function alipayTradeQuery($query_condition)
	{
		$request = new AlipayTradeQueryRequest();
		if ( ! empty($query_condition['trade_no']))
		{
			$request->setBizContent("{
				'trade_no':{$query_condition['trade_no']}
			}");
		}
		elseif ( ! empty($query_condition['out_trade_no']))
		{
			$request->setBizContent("{
				'out_trade_no':{$query_condition['out_trade_no']}
			}");
		}
		else
		{
			throw new Exception("统一收单线下交易查询传入参数发生异常!!!!", 1);
			
		}
		
		$result = $this->_aop->execute($request); 

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;

		log_msg(self::$_file_name, 'DEBUG', '统一收单线下交易查询: '. json_encode($result->$responseNode));
		if(!empty($resultCode)&&$resultCode == 10000){
			return TRUE;
		} 
		else 
		{
			return FALSE;
		}
		
	}

	/**
	 * [payBackBusinessProcess 微信/ 支付宝支付后返回的异步通知业务处理]
	 *
	 * @DateTime 2018-03-28
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function payBackBusinessProcess()
	{
		$this->_CI->load->library('csc_process');

		// 微信/ 支付宝支付后返回的异步通知业务处理
		$ali_config['appId'] = $this->_CI->config->item('appId');
		$ali_config['seller_id'] = $this->_CI->config->item('seller_id');
		return $this->_CI->csc_process->payBackBusinessProcess($_POST, self::$_type, $ali_config, self::$_file_name);
	}

	/**
	 * [alipayTradeRefund 统一收单交易退款接口]
	 *
	 * @DateTime 2018-04-03
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function alipayTradeRefund($order_info)
	{
		$request = new AlipayTradeRefundRequest ();
		
		$request->setBizContent("{" .
		"\"out_trade_no\":\"{$order_info['out_trade_no']}\"," .
		"\"trade_no\":\"{$order_info['trade_no']}\"," .
		"\"refund_amount\":\"{$order_info['refund_amount']}\"," .
		"\"refund_reason\":\"正常退款\"," .
		"\"out_request_no\":\"HZ01RF001\"," .
		"\"operator_id\":\"{$order_info['operator_id']}\"," .
		"\"goods_detail\":[{" .
		"\"goods_id\":\"{$order_info['goods_id']}\"," .
		"\"goods_name\":\"{$order_info['goods_name']}\"," .
		"\"quantity\":\"{$order_info['quantity']}\"," .
		"\"price\":{$order_info['price']}," .
		"\"body\":\"{$order_info['body']}\"" .
		"        }]" .
		"  }");
		$result = $this->_aop->execute($request); 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$response = json_encode($result->$responseNode);
		log_msg(self::$_file_name, 'DEBUG', '统一收单交易退款接口,请求数据'. json_encode($order_info));
		log_msg(self::$_file_name, 'DEBUG', '统一收单交易退款接口,响应数据'. $response);
		return json_decode($response, true);
	}

	/**
	 * [aliorderQueryByoid 通过商户订单查询统一收单线下交易查询接口]
	 *
	 * @DateTime 2018-05-14
	 * @Author   leeprince
	 * @param    [type]     $out_trade_no [description]
	 * @return   [type]                   [description]
	 */
	public function aliorderQueryByoid($out_trade_no)
	{
		$request = new AlipayTradeQueryRequest ();
		$request->setBizContent("{" .
		"\"out_trade_no\":\"{$out_trade_no}\"" .
		"  }");
		$result = $this->_aop->execute ( $request); 

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;

		log_msg(self::$_file_name, 'DEBUG', '统一收单线下交易查询接口,响应数据'. json_encode($result->$responseNode));
		if( ! empty($resultCode) && $resultCode == 10000)
		{
			return object2array($result->$responseNode);
		} 
		else 
		{
			log_msg(self::$_file_name, 'ERROR', '统一收单线下交易查询接口错误!!!!!:'. json_encode($result->$responseNode));
			return FALSE;
		}
	}

}