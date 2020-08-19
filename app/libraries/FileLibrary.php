<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 反馈
 */
class FileLibrary
{
	protected $_CI;
	const K = 'WXAPPUU';

	/* 初始化 */
	const BUCKET_PRIVATE           = '';
	const BUCKET_PUBLIC_WRITE_READ = '';
	const BUCKET_PUBLIC_READ      = 'back-pub-read';
	public static $bucket= '';

	/* 上传文件 */
	const UPLOAD_PATH                  = APPPATH.'/tmp/';
	const ALLOWED_TYPES_IMG            = 'gif|jpg|png|jpeg';
	const MAX_SIZE                     = 20480; // 单位kb: 20 M
	const SOURCE_PREFIX_FEEDBACK       = 'feedback';
	const SOURCE_PREFIX_REFUND         = 'refund'; 
	const SOURCE_PREFIX_MERCHANT_LOGO  = 'merchantlogo';
	const SOURCE_PREFIX_AGENT_CARD     = 'agentcard';
	const SOURCE_PREFIX_AGENT_DELIVERY = 'delivery';
	const SOURCE_PREFIX_BANK_CARD      = 'bankcard';
	const SOURCE_PREFIX_INVOICE        = 'invoice';
	const SOURCE_PREFIX_SUB_AGENT      = 'subagent';
	const SOURCE_PREFIX_MACHINE_TYPE   = 'machinetype';
	const SOURCE_PREFIX_BANNER         = 'banner';
	const SOURCE_PREFIX_UI             = 'ui';


	/** 身份证识别允许最大 */
	const CARD_MAX_SIZE = 3 * 1024; #  单位kb

	/* 压缩 */
	const ZOOM = 0.5; // 缩放比例
	const PUALITY = 90; // 质量值：范围从 0（最差质量，文件更小）到 100

	/**
	 * [__construct 初始化]
	 *
	 * @Author leeprince:2019-02-20T11:23:12+0800
	 */
	public function __construct()
	{
		$this->_CI = &get_instance();

		$this->_CI->load->model([
			'FileModel'
		]);

		self::$bucket = self::BUCKET_PUBLIC_READ;
		try {
			$this->_CI->load->library('aliyunOss/AliyunOss', ['bucket' => self::$bucket]);
		} catch (Exception $e) {
			errorLog($e->getMessage());
			return code(10400, [], self::K);
		}
	}

	/**
	 * [upload description]
	 *
	 * @Author leeprince:2019-03-13T22:03:55+0800
	 * @param  [type]                             $type             [文件类型 1:图片]
	 * @param  [type]                             $source           [来源：1:意见反馈；2:退款; 3:商家logo; 4:身份证;5: 发货凭证;6:银行卡；7:发票; 8: 发展下级代理商]
	 * @param  [type]                             $file             [description]
	 * @param  boolean                            $isReturnImageUrl [是否返回图片地址]
	 * @param  boolean                            $isCompress       [是否压缩]
	 * @return [type]                                               [description]
	 */
	public function upload($type, $source, $file, $isReturnImageUrl = false, $isCompress = false)
	{
		if (! $type || ! $source || ! $file) {
			return code(10406, [], self::K);
		}
		$hash       = md5_file($file['tmp_name']);
		$hashFiel = $this->_CI->FileModel->findOne(['hash' => $hash]);
		if ($hashFiel) {
			if ($isReturnImageUrl) {
				return code(0, ['id' => $hashFiel['id'], 'url' => $hashFiel['url']], self::K);
			}
			return code(0, ['id' => $hashFiel['id']], self::K);
		}
		$ttime = time();
		$tdata = date('YmdHis', $ttime).explode('.', microtime(true))[1];
		
		$allowedTypes = $this->getAllowedTypes($type);
		if ( ! $allowedTypes) {
			return code(10401, [], self::K);
		}
		$filePrefix   = $this->getFilePrefix($source);
		if ( ! $filePrefix) {
			return code(10402, [], self::K);
		}
		$fileName     = $filePrefix.'-'.$type.'-'.$tdata;

		$config['allowed_types'] = $allowedTypes;
		$config['upload_path']   = $this->_CI->config->item('upload')['upload_path'];
		$config['max_size']      = self::MAX_SIZE;
		$config['file_name']     = $fileName;

		$this->_CI->load->library('upload', $config);
		$this->isDetectMime($source);
		if ( ! $this->_CI->upload->do_upload('file')) {
		    $error = $this->_CI->upload->display_errors();
		    errorLog('上传错误:'.$error.'-上传路径:'.$config['upload_path'].'-文件名:'.$config['file_name']);
		    return code(10403, [], self::K);
		}

		$upFileName = $this->_CI->upload->data('file_name');
		$fileSize = $this->_CI->upload->data('file_size'); // kb
		$filePath = $config['upload_path'].$upFileName;
		if ($isCompress || ($source == 4 && $fileSize > self::CARD_MAX_SIZE)) {
			$this->compressedImage($filePath, $filePath);
		}

		$res = $this->_CI->aliyunoss->uploadFile($upFileName, $filePath);
		if ( ! $res) {
			return code(10404, [], self::K);
		}
		unlink($filePath);
		$request_id = $res['x-oss-request-id'];
		$url        = $res['oss-request-url'];
		$resInsert = $this->_CI->FileModel->insert([
			'bucket'        => self::$bucket, 
			'type'          => $type, 
			'platform_type' => 1, 
			'request_id'    => $request_id, 
			'source'        => $source, 
			'hash'          => $hash, 
			'object'        => $upFileName, 
			'url'           => $url, 
			'create_time'   => $ttime
		]);
		if ( ! $resInsert) {
			return code(10405, [], self::K);
		}

		$fileId = $this->_CI->db->insert_id();
		if ($isReturnImageUrl) {
			return code(0, ['id' => $fileId, 'url' => $url], self::K);
		}
		return code(0, ['id' => $fileId], self::K);
	}

	/**
	 * [getAllowedTypes 允许类型]
	 *
	 * @Author leeprince:2019-02-22T17:00:14+0800
	 * @param  [type]                             $type [description]
	 * @return [type]                                   [description]
	 */
	private function getAllowedTypes(int $type)
	{
		switch ($type) {
			case 1:
				return $allowed_types = self::ALLOWED_TYPES_IMG;
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * [getAllowedTypes 文件前缀]
	 *
	 * @Author leeprince:2019-02-22T17:00:14+0800
	 * @param  [type]                             $type [description]
	 * @return [type]                                   [description]
	 */
	private function getFilePrefix(int $source)
	{
		switch ($source) {
			case 1:
				return self::SOURCE_PREFIX_FEEDBACK;
				break;
			case 2:
				return self::SOURCE_PREFIX_REFUND;
				break;
			case 3:
				return self::SOURCE_PREFIX_MERCHANT_LOGO;
				break;
			case 4:
				return self::SOURCE_PREFIX_AGENT_CARD;
				break;
			case 5:
				return self::SOURCE_PREFIX_AGENT_DELIVERY;
				break;
			case 6:
				return self::SOURCE_PREFIX_BANK_CARD;
				break;
			case 7:
				return self::SOURCE_PREFIX_INVOICE;
				break;
			case 8:
				return self::SOURCE_PREFIX_SUB_AGENT;
				break;
			case 9:
			    return self::SOURCE_PREFIX_MACHINE_TYPE;
			    break;
			case 10:
			    return self::SOURCE_PREFIX_BANNER;
			    break;
			case 11:
			    return self::SOURCE_PREFIX_UI;
			    break;
			default:
				return false;
				break;
		}
	}

	/**
	 * [isDetectMime 检查文件]
	 *
	 * @Author leeprince:2019-03-29T10:38:53+0800
	 * @param  int                                $source [description]
	 * @return boolean                                    [description]
	 */
	private function isDetectMime(int $source)
	{
		switch ($source) {
			case 4:
				$this->_CI->upload->detect_mime = FALSE; // 是否检查文件类型, 非后缀
				$this->_CI->upload->ignore_mime = TRUE; // is_allowed_filetype 是否忽略检查 mime 类型
				break;
			case 8:
				$this->_CI->upload->detect_mime = FALSE; // 是否检查文件类型, 非后缀
				$this->_CI->upload->ignore_mime = TRUE; // is_allowed_filetype 是否忽略检查 mime 类型
				break;
			default:
				$this->_CI->upload->detect_mime = TRUE;
				$this->_CI->upload->ignore_mime = FALSE; // is_allowed_filetype 是否忽略检查 mime 类型
				break;
		}
	}

	/**
	 * [compressedImage 压缩图片]
	 *
	 * @Author leeprince:2019-03-13T21:22:58+0800
	 * @param  [type]                             $imgsrc [图片路径，包含文件名]
	 * @param  [type]                             $imgdst [压缩后保存路径,包含文件名]
	 * @return [type]                                     [description]
	 */
	function compressedImage($imgsrc, $imgdst) {
		list($width, $height, $type) = getimagesize($imgsrc);

		$new_width = $width * self::ZOOM;//压缩后的图片宽
		$new_height = $height * self::ZOOM;//压缩后的图片高
		 
		switch ($type) {
		  	case 1:
			  	return false;
			    $giftype = check_gifcartoon($imgsrc);
			    if ($giftype) {
			      header('Content-Type:image/gif');
			      $image_wp = imagecreatetruecolor($new_width, $new_height);
			      $image = imagecreatefromgif($imgsrc);
			      imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			      //90代表的是质量、压缩图片容量大小
			      imagejpeg($image_wp, $imgdst, 90);
			      imagedestroy($image_wp);
			      imagedestroy($image);
			    }
			    break;
		  	case 2:
			    header('Content-Type:image/jpeg');
			    $image_wp = imagecreatetruecolor($new_width, $new_height);
			    $image = imagecreatefromjpeg($imgsrc);
			    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			    //90代表的是质量、压缩图片容量大小
			    imagejpeg($image_wp, $imgdst, self::PUALITY);
			    imagedestroy($image_wp);
			    imagedestroy($image);
			    break;
		  	case 3:
			    header('Content-Type:image/png');
			    $image_wp = imagecreatetruecolor($new_width, $new_height);
			    $image = imagecreatefrompng($imgsrc);
			    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			    //90代表的是质量、压缩图片容量大小
			    imagejpeg($image_wp, $imgdst, self::PUALITY);
			    // imagepng($image_wp, $imgdst); # 推荐使用：imagejpeg;保存后的图片大小更小，拥有第三个参数可控
			    imagedestroy($image_wp);
			    imagedestroy($image);
			    break;
		}
	}

}






































