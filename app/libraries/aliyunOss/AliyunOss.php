<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 阿里云对象存储
 */
class AliyunOss
{
	protected $CI;
	protected static $accessKeyId;
	protected static $accessKeySecret;
	protected static $endpoint;
	protected static $bucket;
	protected $ossClient;

	/**
	 * [__construct 初始化配置]
	 *
	 * @Author leeprince:2019-02-20T10:08:21+0800
	 */
	public function __construct(array $config)
	{
		$this->CI = &get_instance();
		$this->CI->load->config('aliyun_config');

		self::$accessKeyId     = $this->CI->config->item('accessKeyId');
		self::$accessKeySecret = $this->CI->config->item('accessKeySecret');
		self::$endpoint        = $this->CI->config->item('endpoint');

		if ( ! isset($config['bucket']) ||  ! $config['bucket']) {
		    throw new Exception('请选择bucket', 1);
		}
		self::$bucket          = $config['bucket'];

		$isCName       = false;
		$securityToken = NULL;
		$requestProxy  = NULL;
		if ( ! empty($config)) {
			if (array_key_exists('isCName', $config)) {
				$isCName = $config['isCName'];
			}
			if (array_key_exists('securityToken', $config)) {
				$isCName = $config['securityToken'];
			}
			if (array_key_exists('requestProxy', $config)) {
				$isCName = $config['requestProxy'];
			}
		}

		try{
		    $this->ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, self::$endpoint, $isCName, $securityToken, $requestProxy);

		    $res = $this->ossClient->doesBucketExist(self::$bucket);
		} catch(OssException $e) {
		    throw new Exception('阿里云对象存储-初始化失败: funciton:'.__FUNCTION__.';错误内容：'.$e->getMessage(), 1);
		}

		if ( ! $res) {
		    throw new Exception('阿里云对象存储-存储空间不存在', 1);
		}
	}

	/**
	 * [putObject 字符串上传]
	 *
	 * @Author leeprince:2019-02-20T11:50:38+0800
	 * @param  [type]                             $object  [description]
	 * @param  [type]                             $content [description]
	 * @return [type]                                      [description]
	 */
	public function putObject($object, $content)
	{
		try {
			$res = $this->ossClient->putObject(self::$bucket, $object, $content);
		} catch (OssException $e) {
			errorLog("字符串上传失败：".$e->getMessage());
			return false;
		}
		return $res;
	}

	/**
	 * [uploadFile 上传文件]
	 *
	 * @Author leeprince:2019-02-20T10:08:11+0800
	 * @param  [type]                             $object   [description]
	 * @param  [type]                             $filePath [description]
	 * @return [type]                                       [description]
	 */
	/*
		{
		    "server": "AliyunOSS",
		    "date": "Wed, 20 Feb 2019 08:48:07 GMT",
		    "content-length": "0",
		    "connection": "keep-alive",
		    "x-oss-request-id": "5C6D144731DE4FBA04E3EE38",
		    "etag": "\"8C6820DF5B712F67F9C01958CD194861\"",
		    "x-oss-hash-crc64ecma": "4826905496313060479",
		    "content-md5": "jGgg31txL2f5wBlYzRlIYQ==",
		    "x-oss-server-time": "29",
		    "info": {
		        "url": "http://bucket-20190218.oss-cn-beijing.aliyuncs.com/upload-fime-2019-02-20%2016%3A48%3A07",
		        "content_type": null,
		        "http_code": 200,
		        "header_size": 334,
		        "request_size": 452,
		        "filetime": -1,
		        "ssl_verify_result": 0,
		        "redirect_count": 0,
		        "total_time": 41122,
		        "namelookup_time": 1143,
		        "connect_time": 6885,
		        "pretransfer_time": 6928,
		        "size_upload": 21,
		        "size_download": 0,
		        "speed_download": 0,
		        "speed_upload": 512,
		        "download_content_length": 0,
		        "upload_content_length": 21,
		        "starttransfer_time": 12076,
		        "redirect_time": 0,
		        "redirect_url": "",
		        "primary_ip": "59.110.190.32",
		        "certinfo": [],
		        "primary_port": 80,
		        "local_ip": "192.168.3.246",
		        "local_port": 53080,
		        "http_version": 2,
		        "protocol": 1,
		        "ssl_verifyresult": 0,
		        "scheme": "HTTP",
		        "content_length_download": 0,
		        "content_length_upload": 21,
		        "appconnect_time": 0,
		        "method": "PUT"
		    },
		    "oss-request-url": "http://bucket-20190218.oss-cn-beijing.aliyuncs.com/upload-fime-2019-02-20%2016%3A48%3A07",
		    "oss-redirects": 0,
		    "oss-stringtosign": "PUT\n\ntext/plain\nWed, 20 Feb 2019 08:48:07 GMT\n/bucket-20190218/upload-fime-2019-02-20 16:48:07",
		    "oss-requestheaders": {
		        "Accept-Encoding": "",
		        "Content-Type": "text/plain",
		        "Date": "Wed, 20 Feb 2019 08:48:07 GMT",
		        "Host": "bucket-20190218.oss-cn-beijing.aliyuncs.com",
		        "Authorization": "OSS LTAIAHfFbIjSMpQm:nl8JhHKG7aKNJjyyUIiTnFDQDUk="
		    },
		    "body": ""
		}

	 */
	public function uploadFile($object, $filePath)
	{
		try {
			$res = $this->ossClient->uploadFile(self::$bucket, $object, $filePath);
		} catch (OssException $e) {
			errorLog("上传文件失败：".$e->getMessage());
			return false;
		}
		return $res;
	}

	/**
	 * [signUrl 授权访问]
	 *
	 * @Author leeprince:2019-02-20T20:57:00+0800
	 * @return [type]                             [description]
	 */
	public function signUrl($object, $timeout = 3600)
	{
		try {
			$signUrl = $this->ossClient->signUrl(self::$bucket, $object, $timeout);
		} catch (OssException $e) {
			errorLog("授权访问失败：".$e->getMessage());
			return false;
		}
		return $signUrl;
	}

	/**
	 * [appendFile 追加上传]
	 *
	 * @Author leeprince:2019-02-20T21:44:57+0800
	 * @param  string                             $object          [description]
	 * @param  array                              $appendFileArray [description]
	 * @return [type]                                              [description]
	 */
	public function appendFile(string $object, array $appendFileArray)
	{
		$position = 0;
		try {
			for ($i = 0; $i < count($appendFileArray); $i++) { 
				$position = $this->ossClient->appendFile(self::$bucket, $object, $appendFileArray[$i], $position);
			}
		} catch (OssException $e) {
			errorLog("追加上传失败：".$e->getMessage());
			return false;
		}
		return $position;
	}

	/**
	 * [getObjectAcl 获取文件访问权限]
	 *
	 * @Author leeprince:2019-02-23T16:44:38+0800
	 * @param  [type]                             $object [description]
	 * @return [type]                                     [description]
	 */
	public function getObjectAcl($object)
	{
		try {
			$objectAcl = $this->ossClient->getObjectAcl(self::$bucket, $object);
		} catch (OssException $e) {
			errorLog("获取文件访问权限失败:".$e->getMessage());
			return false;
		}
		return $objectAcl;
	}

	/**
	 * [getObject 下载到本地文件]
	 *
	 * @Author leeprince:2019-02-23T17:13:49+0800
	 * @param  [type]                             $object    [description]
	 * @param  [type]                             $localfile [description]
	 * @return [type]                                        [description]
	 */
	public function getObject($object, $localfile)
	{

		$options = [
			OssClient::OSS_FILE_DOWNLOAD => $localfile
		];
		try {
			$objectAcl = $this->ossClient->getObject(self::$bucket, $object, $options);
		} catch (OssException $e) {
			errorLog("下载到本地文件失败:".$e->getMessage());
			return false;
		}
		return $objectAcl;
	}

}

























