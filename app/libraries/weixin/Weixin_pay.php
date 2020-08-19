<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'lib/WxPay.Api.php';
require_once 'lib/WxPay.Data.php';
require_once 'lib/WxPay.Exception.php';
require_once 'lib/WxPay.Notify.php';
require_once 'PayNotifyCallBack.php';

// 微信支付接口
class Weixin_pay {
	/**
	 *
	 * @DateTime 2017-10-15
	 * @Author   leeprince
	 * 微信支付配置数组
	 * APPID        	小程序 APPID
	 * MCHID       		商户号 MCHID
	 * KEY       		加密 KEY
	 * APPSECRET   		小程序 APPSECRET
	 * SSLCERT_PATH 	证书路径(apiclient_cert.pem)
	 * SSLKEY_PATH   	密钥路径(apiclient_key.pem)
	 * CURL_PROXY_HOST	curl 代理 HOST
	 * CURL_PROXY_PORT	curl代理 PORT
	 * CURL_PROXY_PORT	上报信息配置
	 * NOTIFY_URL		异步通知 URL
	 */
	protected static $_config;

	// 将 CodeIgniter 对象赋值给一个变量
	protected $_CI;

	// 调用接口的类型: WXAPP/WXH5
	public static $_type;

	// 微信支付写入日志的文件名
	protected static $_file_name;

	/**
	 * [__construct description]
	 *
	 * @DateTime 2018-03-13
	 * @Author   leeprince
	 * @param    string     $req [// 判断请求是否为微信客户端 小程序(WXAPP) 还是 H5(WXH5); 默认小程序(WXAPP)]
	 */
	public function __construct($req = array())
	{
		$this->_CI = &get_instance();

		if ( empty(self::$_type))
		{
		    switch ($req['type']) {
		    	case 'WXAPP_PARTNER':
		    	    $this->_CI->config->load('weixin_partner_config');
		    	    break;
		    	default:
		    		$this->_CI->config->load('weixin_config');
		    		
		    		break;
		    }
			self::$_type      = $req['type'];
			// 微信支付的日志文件名
			self::$_file_name = self::$_type;

	        $wx_config['APPID']           = $this->_CI->config->item('APPID');
	        $wx_config['MCHID']           = $this->_CI->config->item('MCHID');
	        $wx_config['KEY']             = $this->_CI->config->item('KEY');
	        $wx_config['APPSECRET']       = $this->_CI->config->item('APPSECRET');
	        $wx_config['SSLCERT_PATH']    = $this->_CI->config->item('SSLCERT_PATH');
	        $wx_config['SSLKEY_PATH']     = $this->_CI->config->item('SSLKEY_PATH');
	        $wx_config['PUBLIC_PATH']     = $this->_CI->config->item('PUBLIC_PATH');
	        $wx_config['CURL_PROXY_HOST'] = $this->_CI->config->item('CURL_PROXY_HOST');
	        $wx_config['CURL_PROXY_PORT'] = $this->_CI->config->item('CURL_PROXY_PORT');
	        $wx_config['REPORT_LEVENL']   = $this->_CI->config->item('REPORT_LEVENL');
	        $wx_config['NOTIFY_URL']      = $this->_CI->config->item('NOTIFY_URL');

	    	self::$_config = $wx_config;
		}


		log_msg(self::$_file_name, 'debug', '[Weixin_pay 请求的微信客户端类型]' . self::$_type);
	}

	/**
	 * [get_openid
	 * 	通过跳转获取用户的 openid，跳转流程如下：
	 *  1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 *  2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * ]
	 *
	 * @DateTime 2017-10-16
	 * @Author   leeprince
	 * @param    [type]     $code [description]
	 * @return   [type]           [用户的 openid]
	 */
	public function get_openid($code)
	{
		//通过code获得openid
		if ( ! isset($_GET['code']))
		{
			//触发微信返回code码
			// 返回当前访问页面的链接, 包含参数
			// $baseUrl = urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']); //
			// $baseUrl = urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']); //
			$baseUrl = urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

			$url = $this->_create_oauth_url_for_code($baseUrl);
			Header("Location: $url");
			exit();
		}
		else
		{
			//获取code码，以获取openid
			$openid = $this->get_openid_from_mp($_GET['code']);
			return $openid;
		}
	}

	/**
	 * [get_openid_from_mp 通过code从工作平台获取openid机器access_token]
	 *
	 * @DateTime 2017-10-16
	 * @Author   leeprince
	 * @param    [type]     $code [微信跳转回来带上的code]
	 * @return   [type]           [openid]
	 */
	public function get_openid_from_mp($code)
	{
		$url = $this->_creat_oauth_url_for_openid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(self::$_config['CURL_PROXY_HOST'] != "0.0.0.0"
			&& self::$_config['CURL_PROXY_PORT'] != 0)
		{
			curl_setopt($ch,CURLOPT_PROXY, self::$_config['CURL_PROXY_HOST']);
			curl_setopt($ch,CURLOPT_PROXYPORT, self::$_config['CURL_PROXY_PORT']);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);

		$openid = $data['openid'];
		if ( empty($openid))
		{
			log_msg(self::$_file_name, 'error', '[获取 openid 发生错误, 错误详情: ]'. json_encode($data));
		}
		return $openid;
	}

	/**
	 * [_creat_oauth_url_for_openid 构造获取open和access_toke的url地址]
	 *
	 * @DateTime 2017-10-16
	 * @Author   leeprince
	 * @param    [type]     $code [微信跳转带回的code]
	 * @return   [type]           [请求的url]
	 */
	private function _creat_oauth_url_for_openid($code)
	{
		$urlObj["appid"] = self::$_config['APPID'];
		$urlObj["secret"] =self::$_config['APPSECRET'];
		$urlObj["grant_type"] = "authorization_code";

		if (self::$_type == 'WXH5')
		{
			// 微信 h5使用
			$urlObj["code"] = $code;
			$bizString = $this->_to_url_params($urlObj);
			return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
		}
		else
		{
			// 小程序可以使用
			$urlObj["js_code"] = $code;
			$bizString = $this->_to_url_params($urlObj);
			return "https://api.weixin.qq.com/sns/jscode2session?".$bizString;
		}
	}
	/**
	 * [_to_url_params 拼接签名字符串]
	 *
	 * @DateTime 2017-10-16
	 * @Author   leeprince
	 * @param    [array]     $urlObj [urlObj]
	 * @return   [type]             [返回已经拼接好的字符串]
	 */
	private function _to_url_params($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}


	/**
	 * [_create_oauth_url_for_code 构造获取code的url连接]
	 *
	 * @DateTime 2017-10-17
	 * @Author   leeprince
	 * @param  [string] $redirectUrl [微信服务器回跳的url，需要url编码]
	 * @return [type]              [返回构造好的url]
	 */
	private function _create_oauth_url_for_code($redirectUrl)
	{
		$urlObj["appid"] = self::$_config['APPID'];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->_to_url_params($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}


	/**
	 * [get_jsapi_parameters 获取jsapi支付的参数]
	 *
	 * @DateTime 2017-10-17
	 * @Author   leeprince
	 * @param  [array] $UnifiedOrderResult [统一支付接口返回的数据]
	 * @return [json]                     [json数据，可直接填入js函数作为参数]
	 */
	public function get_jsapi_parameters($UnifiedOrderResult)
	{

		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误");
		}

		$jsapi = new WxPayJsApiPay();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();

		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		$parameters = json_encode($jsapi->GetValues());
		return $parameters;
	}

	/**
	 * [weixin_notify 微信异步回调]
	 *
	 * @DateTime 2017-10-18
	 * @Author   leeprince
	 * @return   [bool]     [description]
	 */
	public function weixin_notify()
	{
//	    log_msg('Weixin_pay_weixin_notify','debug', '[wx_log]getcallback: '. var_export(file_get_contents('php://input'),true));//日志文件
		$notify = new PayNotifyCallBack();

		$rs = $notify->Handle(false);

		return $rs;
	}

	/**
	 * [xml_to_array xml 格式转换为 array]
	 *
	 * @DateTime 2017-10-18
	 * @Author   leeprince
	 * @param    [xml]     $xml [description]
	 * @return   [array]          [description]
	 */
	public function xml_to_array($xml)
	{
		$array = array();
		$tmp = null;
		try{
		    $tmp = (array) simplexml_load_string($xml);
		}catch(Exception $e){}
		if($tmp && is_array($tmp)){
		    foreach ( $tmp as $k => $v) {
		        $array[$k] = (string) $v;
		    }
		}
		return $array;
	}

	/**
	 * [orderQueryByoid 查询订单]
	 *
	 * @DateTime 2018-05-12
	 * @Author   leeprince
	 * @param    [type]     $out_trade_no [description]
	 * @return   [type]                   [description]
	 */
	public function orderQueryByoid($out_trade_no)
	{
		$input = new WxPayOrderQuery();
		$input->SetOut_trade_no($out_trade_no);

		return WxPayApi::orderQuery($input);
	}


	/**
	 * 企业付款到零钱
	 * @param type $PartnerTradeNo
	 * @param type $ip
	 * @param type $amount
	 * @param type $desc
	 * @param type $checkname
	 * @param type $openid
	 * @return type
	 */
	public function actMchPay($PartnerTradeNo, $amount, $desc, $openid, $checkname = "NO_CHECK", $re_user_name = '')
	{
	    $inputObj = new WxPayMchPay();
	    $inputObj->SetPartnerTradeNo($PartnerTradeNo);
	    $inputObj->SetIP(get_ip());
	    $inputObj->SetAmount($amount);
	    $inputObj->SetDesc($desc);
	    $inputObj->SetCheckName($checkname);
	    if ($checkname == 'FORCE_CHECK') {
	    	$inputObj->SetReUserName($re_user_name);
	    }
	    $inputObj->SetOpenId($openid);
	    $result = WxPayApi::MchPay($inputObj);
	    return $inputObj->xml_to_array($result);
	}
}
