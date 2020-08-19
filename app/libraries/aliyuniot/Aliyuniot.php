<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'aliyun-php-sdk-core/Config.php';

use \Iot\Request\V20180120 as Iot;

/**
 * 阿里云物联网
 */
class Aliyuniot
{
	protected $CI;
	protected static $accessKeyId;
	protected static $accessKeySecret;
	protected static $regionId;
	protected static $client = null;

	const CMD = [
		'request' => [
			'openLock'     => ['cmdId' => 0x10, 'cmdLen' => 4, 'name' => '解锁设备'], 
			'instantQuery' => ['cmdId' => 0x11, 'cmdLen' => 2, 'name' => '即时查询设备'],
		],
		'response' => [
			40 => ['cmdId' => 0x40, 'cmdLen' => 2, 'name' => '解锁设备应答'],
			41 => ['cmdId' => 0x41, 'cmdLen' => 2, 'name' => '即时查询设备应答'],
		],
		'parameQueryOrUpdate' => ['cmdId' => 0xff, 'cmdLen' => 0, 'name' => '参数设置和查询'] // 保留
	];

	const IS_SEND_HEX = true;

	/**
	 * [__construct 初始化]
	 *
	 * @Author leeprince:2019-05-08T11:07:11+0800
	 */
	public function __construct()
	{
		if (self::$client == null) {
			$this->CI = &get_instance();
			$this->CI->load->config('aliyun_config');

			self::$accessKeyId     = $this->CI->config->item('accessKeyId');
			self::$accessKeySecret = $this->CI->config->item('accessKeySecret');
			self::$regionId        = $this->CI->config->item('regionId');

			$iClientProfile = DefaultProfile::getProfile(self::$regionId, self::$accessKeyId, self::$accessKeySecret);
			self::$client = new DefaultAcsClient($iClientProfile);
		}
	}

	/**
	 * [sendRequet 发送请求：RRPC消息通信]
	 *
	 * @Author leeprince:2019-05-09T11:46:34+0800
	 * @param  [type]                             $deviceName       [description]
	 * @param  [type]                             $productKey       [description]
	 * @param  [type]                             $deviceId         [description]
	 * @param  [type]                             $cmdKey           [description]
	 * @param  [type]                             $cmdCtxArrayIndex [命令内容：索引数组的格式
	 *                                                              	解锁:[路口，小时，分钟]；
	 *                                                              	查询状态:[路口]
	 * 
	 * 
	 * ]
	 * @param  integer                            $timeOut          [description]
	 * @return [type]                                               [description]
	 */
	public function sendRequet($productKey, $deviceName, $cmdKey, $cmdCtxArrayIndex, $timeOut = 5000)
	{
		if ( ! $productKey || ! $deviceName || ! $cmdKey || ! $cmdCtxArrayIndex) {
			errorLog("发送请求：RRPC消息通信-参数错误-productKey：{$productKey};deviceName:{$deviceName};cmdKey:{$cmdKey};cmdCtxArrayIndex:".json_encode($cmdCtxArrayIndex).";timeOut:{$timeOut}");
			throw new Exception("发送请求：RRPC消息通信-参数错误", 1);
		}
		debugLog("发送请求：RRPC消息通信-debug-productKey：{$productKey};deviceName:{$deviceName};cmdKey:{$cmdKey};cmdCtxArrayIndex:".json_encode($cmdCtxArrayIndex).";timeOut:{$timeOut}");
		$request = new Iot\RRpcRequest();
		$request->setProductKey($productKey);
		$request->setDeviceName($deviceName);
		$request->setTimeout($timeOut);
		$request->setRequestBase64Byte($this->dataParsingRequest($cmdKey, $cmdCtxArrayIndex));
		$response = self::$client->getAcsResponse($request);

		return $response;
	}

	/**
	 * [sendRequetTest 发送十六进制测试]
	 *
	 * @Author leeprince:2019-05-29T09:40:42+0800
	 * @param  [type]                             $productKey [description]
	 * @param  [type]                             $deviceName [description]
	 * @param  [type]                             $testHex    [description]
	 * @param  integer                            $timeOut    [description]
	 * @return [type]                                         [description]
	 */
	public function sendRequetHexTest($productKey, $deviceName, $hexContent, $timeOut = 5000)
	{
		$request = new Iot\RRpcRequest();
		$request->setProductKey($productKey);
		$request->setDeviceName($deviceName);
		$request->setTimeout($timeOut);
		// $request->setRequestBase64Byte(base64_encode(bin2hex($hexContent)));
		$request->setRequestBase64Byte(base64_encode(hex2bin($hexContent)));
		$response = self::$client->getAcsResponse($request);
		return $response;
	}

	/**
	 * [encodeCmd 数据解析:请求]
	 *
	 * @Author leeprince:2019-05-08T11:54:42+0800
	 * @return [type]                             [description]
	 */
	public function dataParsingRequest($cmdKey, $cmdCtxArrayIndex)
	{
		if ( ! array_key_exists($cmdKey, $requestCmd = self::CMD['request'])) {
			errorLog('数据解析:请求-命令字不存在'.$cmdKey);
			throw new Exception("数据解析:请求-命令字不存在", 1);
		}
		$cmdId  = $requestCmd[$cmdKey]['cmdId'];
		$cmdLen = $requestCmd[$cmdKey]['cmdLen'];
		switch ($cmdKey) {
			case 'openLock':
				$deviceNum = $cmdCtxArrayIndex[0];
				$hour      = $cmdCtxArrayIndex[1];
				$minute    = $cmdCtxArrayIndex[2];

				$bodyContent = sprintf("%02X%02X%02X%02X%02X%02X", $cmdId, $cmdLen, $deviceNum, $hour, $minute, '00');
				break;
			case 'instantQuery':
				$deviceNum = $cmdCtxArrayIndex[0];
				$bodyContent = sprintf("%02X%02X%02X%02X", $cmdId, $cmdLen, $deviceNum, '00');
				break;
			
			default:
				errorLog('数据解析:请求-命令字不存在-default');
				throw new Exception("数据解析:请求-命令字不存在-default", 1);
				break;
		}
		
		$sendData = $this->sendData($bodyContent);
		return $sendData;
	}

	/**
	 * [dataParsingSendAck 数据解析:响应]
	 * 
	 * 测试数据
		 		$PayloadBase64Byte = base64_encode(hex2bin('40020100B70D')); // 设备解锁测试应答数据
				$PayloadBase64Byte = base64_encode(hex2bin('41020100C1B90d0a')); // 查询设备状态测试应答数据
	 *
	 * @Author leeprince:2019-05-09T10:35:27+0800
	 * @param  [type]                             $data [description]
	 * @return [type]                                   [description]
	 */
	public function dataParsingSendAck($data)
	{
		$bodyContent = $this->ackData($data);
		$cmdId = substr($bodyContent, 0, 2);
		if ( ! array_key_exists($cmdId, $responseCmd = self::CMD['response'])) {
			errorLog('数据解析:响应-命令字不存在-bodyContent:'.$bodyContent.';responseCmd:'.json_encode($responseCmd));
			throw new Exception("命令字不存在", 1);
		}
		$data = [];
		switch ($cmdId) {
			case 40:
				$data[] = substr($bodyContent, 4, 2);
				$data[] = substr($bodyContent, 6, 2);
				break;
			case 41:
				$data[] = substr($bodyContent, 4, 2);
				$data[] = substr($bodyContent, 6, 2);
				break;
			default:
				errorLog('数据解析:响应-命令字不存在-default:'.$bodyContent);
				throw new Exception("数据解析:响应-命令字不存在-default", 1);
				break;
		}
		return $data;
	}

	/**
	 * [dataSign 数据签名]
	 *
	 * @Author leeprince:2019-05-09T10:38:39+0800
	 * @param  [type]                             $data [description]
	 * @return [type]                                   [description]
	 */
	private function dataSign($bodyContent)
	{
		$bodyContentBin = hex2bin($bodyContent); // 十六进制值校验数据
		$crc16 = $this->crc16($bodyContentBin);
		$crc16 = sprintf('%04X', $crc16);
		return $crc16;
	}

	/**
	 * [crc16 crc16-CCITT-FALSE]
	 *
	 * @Author leeprince:2019-05-25T03:25:22+0800
	 * @param  [type]                             $string [description]
	 * @return [type]                                     [description]
	 */
	private function crc16($ptr)
	{
	    $crc = 0xFFFF; // 初始值
	    $crc_table = [
	        0x0000, 0x1021, 0x2042, 0x3063, 0x4084, 0x50a5, 0x60c6, 0x70e7,
	        0x8108, 0x9129, 0xa14a, 0xb16b, 0xc18c, 0xd1ad, 0xe1ce, 0xf1ef,
	        0x1231, 0x210, 0x3273, 0x2252, 0x52b5, 0x4294, 0x72f7, 0x62d6,
	        0x9339, 0x8318, 0xb37b, 0xa35a, 0xd3bd, 0xc39c, 0xf3ff, 0xe3de,
	        0x2462, 0x3443, 0x420, 0x1401, 0x64e6, 0x74c7, 0x44a4, 0x5485,
	        0xa56a, 0xb54b, 0x8528, 0x9509, 0xe5ee, 0xf5cf, 0xc5ac, 0xd58d,
	        0x3653, 0x2672, 0x1611, 0x630, 0x76d7, 0x66f6, 0x5695, 0x46b4,
	        0xb75b, 0xa77a, 0x9719, 0x8738, 0xf7df, 0xe7fe, 0xd79d, 0xc7bc,
	        0x48c4, 0x58e5, 0x6886, 0x78a7, 0x840, 0x1861, 0x2802, 0x3823,
	        0xc9cc, 0xd9ed, 0xe98e, 0xf9af, 0x8948, 0x9969, 0xa90a, 0xb92b,
	        0x5af5, 0x4ad4, 0x7ab7, 0x6a96, 0x1a71, 0xa50, 0x3a33, 0x2a12,
	        0xdbfd, 0xcbdc, 0xfbbf, 0xeb9e, 0x9b79, 0x8b58, 0xbb3b, 0xab1a,
	        0x6ca6, 0x7c87, 0x4ce4, 0x5cc5, 0x2c22, 0x3c03, 0xc60, 0x1c41,
	        0xedae, 0xfd8f, 0xcdec, 0xddcd, 0xad2a, 0xbd0b, 0x8d68, 0x9d49,
	        0x7e97, 0x6eb6, 0x5ed5, 0x4ef4, 0x3e13, 0x2e32, 0x1e51, 0xe70,
	        0xff9f, 0xefbe, 0xdfdd, 0xcffc, 0xbf1b, 0xaf3a, 0x9f59, 0x8f78,
	        0x9188, 0x81a9, 0xb1ca, 0xa1eb, 0xd10c, 0xc12d, 0xf14e, 0xe16f,
	        0x1080, 0xa1, 0x30c2, 0x20e3, 0x5004, 0x4025, 0x7046, 0x6067,
	        0x83b9, 0x9398, 0xa3fb, 0xb3da, 0xc33d, 0xd31c, 0xe37f, 0xf35e,
	        0x2b1, 0x1290, 0x22f3, 0x32d2, 0x4235, 0x5214, 0x6277, 0x7256,
	        0xb5ea, 0xa5cb, 0x95a8, 0x8589, 0xf56e, 0xe54f, 0xd52c, 0xc50d,
	        0x34e2, 0x24c3, 0x14a0, 0x481, 0x7466, 0x6447, 0x5424, 0x4405,
	        0xa7db, 0xb7fa, 0x8799, 0x97b8, 0xe75f, 0xf77e, 0xc71d, 0xd73c,
	        0x26d3, 0x36f2, 0x691, 0x16b0, 0x6657, 0x7676, 0x4615, 0x5634,
	        0xd94c, 0xc96d, 0xf90e, 0xe92f, 0x99c8, 0x89e9, 0xb98a, 0xa9ab,
	        0x5844, 0x4865, 0x7806, 0x6827, 0x18c0, 0x8e1, 0x3882, 0x28a3,
	        0xcb7d, 0xdb5c, 0xeb3f, 0xfb1e, 0x8bf9, 0x9bd8, 0xabbb, 0xbb9a,
	        0x4a75, 0x5a54, 0x6a37, 0x7a16, 0xaf1, 0x1ad0, 0x2ab3, 0x3a92,
	        0xfd2e, 0xed0f, 0xdd6c, 0xcd4d, 0xbdaa, 0xad8b, 0x9de8, 0x8dc9,
	        0x7c26, 0x6c07, 0x5c64, 0x4c45, 0x3ca2, 0x2c83, 0x1ce0, 0xcc1,
	        0xef1f, 0xff3e, 0xcf5d, 0xdf7c, 0xaf9b, 0xbfba, 0x8fd9, 0x9ff8,
	        0x6e17, 0x7e36, 0x4e55, 0x5e74, 0x2e93, 0x3eb2, 0xed1, 0x1ef0
	    ];
	    for ($i = 0; $i < strlen($ptr); $i++) {
	        $crc =  $crc_table[(($crc>>8) ^ ord($ptr[$i]))] ^ (($crc<<8) & 0x00FFFF);
	    }
	    return $crc;
	}

	/**
	 * [checkSig 检查签名]
	 *
	 * @Author leeprince:2019-05-09T11:14:00+0800
	 * @param  [type]                             $data [description]
	 * @return [type]                                   [description]
	 */
	private function checkSig($bodyContent, $dataSig)
	{
		$trueDataSig = $this->dataSign($bodyContent);
		if ($trueDataSig != $dataSig) {
			errorLog('检查签名失败-bodyContent:'.$bodyContent.';dataSig:'.$dataSig.';trueDataSig:'.$trueDataSig);
			throw new Exception("ffc检查签名失败", 1);
		}
	}

	/**
	 * [sendData 封装发送数据]
	 *
	 * @Author leeprince:2019-05-13T10:11:32+0800
	 * @param  [type]                             $bodyContent [description]
	 * @param  [type]                             $sig         [description]
	 * @return [type]                                          [description]
	 */
	private function sendData($bodyContent)
	{
		$sig = $this->dataSign($bodyContent);

		// return base64_encode(pack('H*N', $bodyContent, $sig));
		$bodyContentSig = $bodyContent.$sig;
		debugLog("数据解析:请求-封装发送数据-待base64编码的字符串-bodyContent：{$bodyContent};sig:{$sig}-bodyContentSig:{$bodyContentSig}");

		if (self::IS_SEND_HEX) {
			$bodyContentSig = hex2bin($bodyContentSig); // 十六进制字节发送；pack("H*", $bodyContentSig);
		}
		return base64_encode($bodyContentSig);
	}

	/**
	 * [ackData 解封接收的数据;
	 * 		
	 * ]
	 *
	 * @Author leeprince:2019-05-13T10:13:54+0800
	 * @param  [type]                             $data [description]
	 * @return [type]                                   [description]
	 */
	private function ackData($data)
	{
		// debugLog('数据解析:响应-解封接收的数据-待base64编码的字符串-data:'.$data);
		$decodeData = base64_decode($data);

		$hexData = $decodeData;
		if (self::IS_SEND_HEX) {
			$hexData = bin2hex($decodeData); // 十六进制字节接收; unpack("H*", $decodeData);
		}
		$allData = $this->formatFilter($hexData);
		
		$contentLen  = strlen($allData);
		$bodyContent = substr($allData, 0, $contentLen - 4);
		$dataSig     = substr($allData, -4);
		debugLog('数据解析:响应-解封接收的数据-base64_decode后的数据-hexData:'.$hexData.';allData:'.$allData.';contentLen:'.$contentLen.';bodyContent:'.$bodyContent.';dataSig:'.$dataSig);

		$this->checkSig($bodyContent, $dataSig);
		return $bodyContent;
	}

	/**
	 * [sendDviceProperty 设备设置属性值。]
	 *
	 * @Author leeprince:2019-06-25T21:43:01+0800
	 * @return [type]                             [description]
	 */
	public function sendDviceProperty(string $productKey, string $deviceName, string $data, $timeOut = 5000)
	{
		$request = new Iot\SetDevicePropertyRequest();
		$request->setProductKey($productKey);
		$request->setDeviceName($deviceName);
		$request->setTimeout($timeOut);
		$request->setItems($data);
		$response = self::$client->getAcsResponse($request);

		return $response;
	}

	/**
	 * [FormatFilter 返回数据格式过滤]
	 *
	 * @Author leeprince:2019-07-15T16:22:49+0800
	 * @param  [type]                             $daata [description]
	 */
	private function formatFilter($data)
	{
		$fData = strtoupper($data); // 大写
		$fData = str_replace('0D0A', '', $fData); // 0x0D 0x0A == \r\n

		return $fData;
	}

}









