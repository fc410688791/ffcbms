<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('prt'))
{

	/**
	 * [reservation 打印信息]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    [type]     $reservation [description]
	 * @return   [type]                  [description]
	 */
	function prt($data, $msg = '')
	{
		if ( ! empty($msg))
		{
			echo "[{$msg}]";
		}

		echo "<br><pre>";
		print_r($data);
		return true;
	}
}

if ( ! function_exists('code'))
{
    /**
     * [code 返回客户端密钥键值的状态码]
     *
     * @Author leeprince:2019-01-19T12:39:55+0800
     * @param  integer                            $k    [description]
     * @param  array                              $data [description]
     * @return [type]                                   [description]
     */
    function code($k, $data = [], $keyDefault = 'WXAPPUU'): array
    {
        $CI = &get_instance();
        if (! $keyDefault) {
            $keyDefault = $CI->getK();
        }
        $config = $CI->config->item("code-{$keyDefault}");
        if ( empty($config)) {
            errorLog('CODE KEY 未知'.$keyDefault);
            return ['errMsg' => 'CODE KEY 未知'];
        }
        if ( ! array_key_exists($k, $config)) {
            errorLog('codeNum 未知. codeNum:'.$k);
            return ['errMsg' => 'codeNum 未知'];
        }
        
        if ( (($k == 0) || $k) && $data) {
            $returnData = $config[$k];
            $returnData['data'] = $data;
            return $returnData;
        } else {
            return $config[$k];
        }
    }
}

if ( ! function_exists('prt_exit'))
{

	/**
	 * [prt_exit 打印信息并结束脚本]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @param    [type]     $data [description]
	 * @return   [type]           [description]
	 */
	function prt_exit($data, $msg = '')
	{
		if ( ! empty($msg))
		{
			echo "[{$msg}]";
		}

		echo "<br><pre>";
		print_r($data);
		exit;
	}
}

if ( ! function_exists('check_form_filter'))
{

	/**
	 * [check_form_filter 表单提交后检查过滤]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @param    [type]     $data [description]
	 * @return   [type]           [description]
	 */
	function check_form_filter($data)
	{
		if( empty($data) ) {
			return false;
		}

		if ( is_array($data) ){
			$filter_data = array();
			foreach($data as $k=>$v) {
				$filter_data[$k] = trim(strip_tags($v));
			}

			return $filter_data;
		}

		if ( is_string($data) ) {
			return trim(strip_tags($data));
		}
	}
}

if ( ! function_exists('switch_reservation'))
{

	/**
	 * [reservation reservation的时间间隔的转化: 2017-12-06 - 2017-12-14 转化为: 时间戳array(1512489600, 1513267199)]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    [type]     $reservation [description]
	 * @return   [type]                  [description]
	 */
	function switch_reservation($reservation)
	{
		if ( ! is_string($reservation))
		{
			return FALSE;
		}

		$arr = explode(' - ', $reservation);

		return array(strtotime($arr[0]), strtotime(date('Y-m-d 23:59:59', strtotime($arr[1]))));
	}
}

if ( ! function_exists('default_reservation'))
{

	/**
	 * [default_reservation 默认的 reservation]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    [int]     $past_time [description]
	 * @return   [type]                [description]
	 */
	function default_reservation($past_time)
	{
		if ( empty($past_time))
		{
			return FALSE;
		}

		return date('m/d/Y', strtotime("- {$past_time} day")).' - '.date('m/d/Y');
	}
}

if ( ! function_exists('diff_between_t'))
{

	/**
	 * [diff_between_t 两个时间戳的相隔时间]
	 *
	 * @DateTime 2017-12-25
	 * @Author   leeprince
	 * @param    [type]     $sta_t [description]
	 * @param    [type]     $end_t [description]
	 * @param    string     $type  [相隔时间的类型]
	 * @return   [type]            [description]
	 */
	function diff_between_t($sta_t, $end_t, $type = 'day')
	{
		if ($sta_t > $end_t)
		{
			return NULL;
		}

		switch ($type) {
			case 'day':
				$by_t = 86400;
				break;
			default:
			    $by_t = 86400;
		}

		return floor(($end_t - $sta_t) / $by_t);
	}
}

if ( ! function_exists('get_ip'))
{

	/**
	 * [get_ip 获得客户端 ip]
	 *
	 * @DateTime 2017-11-07
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	function get_ip()
	{
		if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" )) {
			$ip = getenv ( "HTTP_CLIENT_IP" );
		} elseif (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" )) {
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		} elseif (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" )) {
			$ip = getenv ( "REMOTE_ADDR" );
		} elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" )) {
			$ip = $_SERVER ['REMOTE_ADDR'];
		} else {
			$ip = "unknown";
		}
		return $ip;
	}
}

if ( ! function_exists('check_form_filter'))
{

	/**
	 * [get_ip 获得客户端 ip]
	 *
	 * @DateTime 2017-11-07
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	// 表单提交后检查过滤
	function check_form_filter($data) {
		if( empty($data) ) {
			return false;
		}

		if ( is_array($data) ){
			$filter_data = array();
			foreach($data as $k=>$v) {
				$filter_data[$k] = trim(strip_tags($v));
			}
			return $filter_data;
		}

		if ( is_string($data) ) {
			return trim(strip_tags($data));
		}
	}
}

if ( ! function_exists('render_js_confirm'))
{

	/**
	 * [render_js_confirm 点击按钮弹窗 modal]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @param    [type]     $class_name [description]
	 * @param    [type]     $title      [description]
	 * @param    string     $modal_id   [description]
	 * @return   [type]                 [description]
	 */
	function render_js_confirm($class_name, $title, $modal_id = 'warning', $cancel_btn = '取消', $confirm_btn = '确定')
	{
		if (empty($class_name) || empty($title) || empty($modal_id))
		{
			return FALSE;
		}

		$confirm_html = "
			<div class='modal modal-$modal_id fade' id='modal-$modal_id'>
			  <div class='modal-dialog'>
			    <div class='modal-content'>
			      <div class='modal-header'>
			        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
			          <span aria-hidden='true'>&times;</span></button>
			        <h4 class='modal-title' id='title-$modal_id'></h4>
			      </div>
			      <div class='modal-footer'>
			        <button type='button' class='btn btn-outline pull-left' data-dismiss='modal'>$cancel_btn</button>
			        <a type='button' class='btn btn-outline' id='confirm-$modal_id' href=''>$confirm_btn</a>
			      </div>
			    </div>
			    <!-- /.modal-content -->
			  </div>
			  <!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->

			<script type='text/javascript'>

			  $('.$class_name').click(function(){
			    var href = $(this).attr('data-href');
			    var id = $(this).attr('data-id');
			    var title = id+'-'+'$title';

			    $('#confirm-$modal_id').attr('href', href);
			    $('#title-$modal_id').html(title);
			    $('#modal-$modal_id').modal('show');

			  });
			</script>
		";

		return $confirm_html;
	}
}

if ( ! function_exists('del_arr_by_value'))
{

	/**
	 * [del_arr_by_value 通过值删除数组中的对应元素]
	 *
	 * @DateTime 2017-12-12
	 * @Author   leeprince
	 * @param    [type]     $array [description]
	 * @param    [type]     $value [description]
	 * @return   [type]            [description]
	 */
	function del_arr_by_value($array, $value)
	{
		if (empty($array) || empty($value) || ! is_array($array))
		{
			return FALSE;
		}

		foreach ($array as $k => $v)
		{
			if ($v == $value)
			{
				unset($array[$k]);
				break;
			}
		}

		return $array;
	}
}

if ( ! function_exists('create_device_id'))
{
	/**
	 * [create_device_id 生成设备ID 规则

					设备ID生成规则:
					10030105000
					1附加字段
					003批次
					01生产商
					设备ID
					05000
				]
	 *
	 * @DateTime 2017-12-26
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	function create_device_id($batch_id, $prod_id, $i,$num_len = '5',$e_field = 1)
	{
		$batch_id = str_pad($batch_id, '3', '0', STR_PAD_LEFT); // 批次
		$prod_id  = str_pad($prod_id, '2', '0', STR_PAD_LEFT); // 设备生产商
		$i        = str_pad($i, $num_len, '0', STR_PAD_LEFT); // 设备当前数量

		// 设备 ID
		return "{$e_field}{$batch_id}{$prod_id}{$i}";
	}
}

if ( ! function_exists('object2array'))
{
	/**
	 * [object2array 对象转化为数组]
	 *
	 * @DateTime 2018-04-04
	 * @Author   leeprince
	 * @param    [type]     $object [description]
	 * @return   [type]             [description]
	 */
	function object2array($object) {
	  if (is_object($object))
	  {
	    foreach ($object as $key => $value)
	    {
	      $array[$key] = $value;
	    }
	  }
	  else
	  {
	    $array = $object;
	  }

	  return $array;
	}
}
if(!function_exists("dump"))
{
         /**
         * 格式化var_dump函数
         * @param type $data
         */
        function dump($data)
        {
             echo "<pre>";
             var_dump($data);
             echo "</pre>";
        }
}
if(!function_exists("getdehasTaxFee"))
{

      function getdehasTaxFee($fee,$tax)
      {
          return sprintf("%.2f",$fee/$tax);
      }
}
/**
 * 根据指定条件排序
 */
if(!function_exists("arraySortByMapped"))
{
    function arraySortByMapped($sort,$array,$k){
        $sort_key = [];
        foreach($array as $val)
        {
           $sort_key[] = $sort[$val[$k]];
        }
        array_multisort($sort_key,SORT_ASC,$array);
        return $array;
    }
}
if ( ! function_exists('time_diff'))
{
	/**
	 * 计算时间差
	 * @param int $timestamp1 时间戳开始
	 * @param int $timestamp2 时间戳结束
	 * @return array
	 */
	function time_diff($timestamp1, $timestamp2)
	{
	    if ($timestamp2 <= $timestamp1)
	    {
	        return ['day'=>0,'hours'=>0, 'minutes'=>0, 'seconds'=>0];
	    }
	    $timediff = $timestamp2 - $timestamp1;
	    // 天
	    $day = floor($timediff/86400);
	    // 时
	    $remain = $timediff%86400;
	    $hours = intval($remain/3600);
	    // 分
	    $remain = $timediff%3600;
	    $mins = intval($remain/60);
	    // 秒
	    $secs = $remain%60;
	    $time = ['day'=>$day, 'hours'=>$hours, 'minutes'=>$mins, 'seconds'=>$secs];
	    return $time;
	}
}
if(!function_exists("IntToChr"))
{
    /**
     * 数字转字母 （类似于Excel列标）
     * @param Int $index 索引值
     * @param Int $start 字母起始值
     * @return String 返回字母
     */
    function IntToChr($index, $start = 65) {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= IntToChr(floor($index / 26)-1);
        }
        return $str . chr($index % 26 + $start);
    }
}
if(!function_exists("getStationStatusText"))
{
    function getStationStatusText($status){
           $CI = &get_instance();
           $station_status_options = $CI->config->item("station_status_option");
           return $station_status_options[$status];
    }
}
if(!function_exists("getStationTypeText"))
{
    function getStationTypeText($type_id){
           $CI = &get_instance();
           $CI->load->model('Station_type_model');
           $station_type_options = $CI->Station_type_model->get_one_data(["id"=>$type_id]);
           return $station_type_options["type_name"];
    }
}
if(!function_exists("createQrcodeCode"))
{
	/**
	 * [createQrcodeCode 生成二维码活动码]
	 *
	 * @DateTime 2018-05-24
	 * @Author   leeprince
	 * @param    [type]     $prefix [description]
	 * @param    [type]     $i      [description]
	 * @return   [type]             [description]
	 */
    function createQrcodeCode($prefix, $i){
           // return md5(md5($prefix).md5($i).md5(substr(microtime(), 2, 3)));
           return md5(md5($prefix).$i.time());
    }
}
if(!function_exists("getProvinceIdByCityId"))
{
    function getProvinceIdByCityId($cid){
           $CI = &get_instance();
           $CI->load->model('locations_model');
           $city = $CI->locations_model->get_one_data(["id"=>$cid]);
           $province = $CI->locations_model->get_one_data(["id"=>$city['parent_id']]);
           return $province["id"];
    }
}
if(!function_exists("getCityIdBySid"))
{
    function getCityIdBySid($sid){
           $CI = &get_instance();
           $CI->load->model('locations_model');
           $CI->load->model('station_model');
           $station = $CI->station_model->get_one_data(["id"=>$sid]);
           $city = $CI->locations_model->get_one_data(["id"=>$station['station_addr']]);
           return $city["id"];
    }
}
if(!function_exists("getActivityTypeText"))
{
    function getActivityTypeText($type_id){
         $CI = &get_instance();
         $CI->load->model("ActivityType_model");
         $activitytype = $CI->ActivityType_model->get_one_data(["id"=>$type_id]);
         return $activitytype["name"];
    }
}
if(!function_exists("uploadFile"))
{
    function uploadFile($file){
        $CI = &get_instance();
        $CI->load->model("Img_model");
        $md5_file = md5_file($_FILES[$file]['tmp_name']);
        $Img = $CI->Img_model->get_one_data(['img_md5'=>$md5_file]);
        if($Img){
            return $Img;
        }
        else
        {
            $file_name = $file.date("YmdHis",time());
            $upload_config = $CI->config->item('upload');
            $sys_value_option = $CI->admin_process->get_sys_value_option();
			$upload_config['max_size'] = $sys_value_option['file_max_size'];
			$upload_config['upload_path'] = $sys_value_option['file_upload_path'];
            $upload_config['file_name'] =$file_name;
            $CI->load->library('upload');
            $upload = new Upload($upload_config);
            if ( ! $upload->do_upload($file)){
                $error = "图片上传失败, 错误详情: ".$upload->display_errors();
                throw  new Exception($error);
            }
            else
            {
                $ext = $CI->upload->get_extension($_FILES[$file]['name']);
                $insert_data = [
                    "name"          =>$file_name.$ext,
                    "path"          =>"/assets/uploads/",
                    "img_md5"       =>$md5_file,
                    "create_time"   =>time()
                ];
                $CI->Img_model->insertData($insert_data);
                $insert_data['id'] = $CI->db->insert_id();
                return $insert_data;
            }
            unset($upload);
        }
    }
}
if(!function_exists("getPropertyValue")){

    function getPropertyValue($aid,$property){
        $CI = &get_instance();
        $CI->load->model("ActivityTypePropertyName_model");
        $property_value =  $CI->ActivityTypePropertyName_model->getPropertyNameValueByActivityid($aid,[$property]);
        return $property_value[$property];
    }
}
if(!function_exists("getActPartText")){
    function getActPartText($id){
        $text = "";
        switch ($id){
            case 1:$text = "肩部";
            break;
            case 2:$text = "背部";
            break;
            case 3:$text = "腰部";
            break;
        }
        return $text;
    }
}
if(!function_exists("getActSexText")){
    function getActSexText($id){
        $text = "";
        switch ($id){
            case 1:$text = "男";
            break;
            case 2:$text = "女";
            break;
        }
        return $text;
    }
}
if(!function_exists("getActAgeText")){
    function getActAgeText($id){
        $text = "";
        switch ($id){
            case 1:$text = "青年";
            break;
            case 2:$text = "中年";
            break;
            case 3:$text = "老年";
            break;
        }
        return $text;
    }
}
if(!function_exists("getImgUrl")){
    function getImagUrl($id){
        $CI = &get_instance();
        $CI->load->model("img_model");
        $res = $CI->img_model->get_one_data(["id"=>$id]);
        $url = "";
        if($res){
         $url = "http://".$res["url"];
        }
        return $url;
    }
}
if(!function_exists("getOrderUserModelBydeviceid")){
    /**
     * 根据设备ID获取订单设备模型
     * @param type $dev_id
     * @return type
     */
    function getOrderUserModelBydeviceid($dev_id){
        $CI = &get_instance();
        $dev_type = substr($dev_id,0,1);
        if($dev_type == 1){
            $CI->load->model("api/Order_user_model");
            return $CI->Order_user_model;
        } else{
            $CI->load->model("api/BedOrder_user_model");
            return $CI->BedOrder_user_model;
        }
    }
}
if(!function_exists("getOrderUserBigChairModelBydeviceid")){
    /**
     * 根据设备ID获取订单设备模型
     * @param type $dev_id
     * @return type
     */
    function getOrderUserBigChairModelBydeviceid($dev_id){
        $CI = &get_instance();
        $dev_type = substr($dev_id,0,1);
        if($dev_type == 1){
            $CI->load->model("api/Order_user_model");
            return $CI->Order_user_model;
        } else{
            $CI->load->model("api/OrderUserBigChair_model");
            return $CI->OrderUserBigChair_model;
        }
    }
}
if ( ! function_exists("setIndexArray")) {
    /**
     * [setIndexArray 设置索引数组]
     *
     * @author leeprince <[<email address>]>
     * @param [type] $array [description]
     * @param [type] $key   [description]
     */
    function setIndexArray($array, $key = 'id') {
        $option = [];
        foreach ($array as $value) {
        	$option[] = $value[$key];
        }
        return $option;
    }
}

if ( ! function_exists("getsellerOrderStatus")) {
	/**
	 * [sellerOrderStatus description]
	 *
	 * @author leeprince <[<email address>]>
	 * @param  [type] $order_status  [description]
	 * @param  [type] $refund_status [description]
	 * @return [type]                [description]
	 */
	function sellerOrderStatus($order_status, $refund_status)
	{
		// 订单状态:0=>待支付,1=>支付成功(待接单),2=>支付取消，3=>服务中,4=>待确认,5=>已完成
		// 订单退款状态:0=>待退款,1=>退款成功;2=>退款失败;
		
	}
}

if ( ! function_exists("ajaxReturn")) {
	/**
	 * [ajaxReturn ajax 返回]
	 *
	 * @author leeprince <[<email address>]>
	 * @param  integer $code [description]
	 * @param  string  $ret  [description]
	 * @return [type]        [description]
	 */
	function ajaxReturn($code = 0, $ret = '')
    {
        $CI = &get_instance();
        $CI->ajax_return(['code' => $code, 'data' => $ret]);
    }
}

if ( ! function_exists("get2ArrayToValueArray")) {
	/**
	 * [get2ArrayToValueArray 返回二维数组中指定键名与键值组成的数组;使用数组函数array_column替代该方法]
	 *
	 * @Author leeprince:2019-06-05T10:28:46+0800
	 * @param  integer                            $code [description]
	 * @param  string                             $ret  [description]
	 * @return [type]                                   [description]
	 */
	function get2ArrayToValueArray($array, $key)
    {
    	if (! $key) {
    		return FALSE;
    	}
        $newArray = [];
        for ($i = 0; $i < count($array); $i++) {
        	$newArray[] = $array[$i][$key];
        }
        return $newArray;
    }
}

if ( ! function_exists("get2ArrayToKeyValueArray")) {
	/**
	 * [get2ArrayToKeyValueArray 返回二维数组中指定键名与键值组成的数组;当valueKey不需要自定义时，可使用数组函数array_column替代该方法。]
	 *
	 * @Author leeprince:2019-09-05T21:36:45+0800
	 * @param  [type]                             $array           [description]
	 * @param  [type]                             $key             [description]
	 * @param  [type]                             $valueKey        [数组中某一键名，或者数组中多个键名组成的数组]
	 * @param  boolean                            $valueKeyToArray [valueKey是否组成新的数组]
	 * @return [type]                                              [description]
	 */
	function get2ArrayToKeyValueArray($array, $key, $valueKey, $valueKeyToArray = true)
    {
    	if (! $key || ! $valueKey) {
    		return null;
    	}
        $newArray = [];
        for ($i = 0; $i < count($array); $i++) {
        	if (is_array($valueKey)) {
        		$keyData = [];
        		for ($j = 0; $j < count($valueKey); $j++) {
        			$keyData[$valueKey[$j]] = $array[$i][$valueKey[$j]];
        		}
        		if ($valueKeyToArray) {
        			$newArray[$array[$i][$key]][] = $keyData;
        		} else {
        			$newArray[$array[$i][$key]] = $keyData;
        		}
        	} else {
        		$newArray[$array[$i][$key]] = $array[$i][$valueKey];
        	}
        }
        $array = null;
        return $newArray;
    }
}

if ( ! function_exists('getArrayMergeAndUnique')) {
	/**
	 * [getArrayMergeAndUnique 取两个数据并集]
	 *
	 * @Author leeprince:2019-09-05T14:30:25+0800
	 * @param  [type]                             $array1 [description]
	 * @param  [type]                             $array2 [description]
	 * @return [type]                                     [description]
	 */
	function getArrayMergeAndUnique($array1, $array2) {
		$merge = array_merge($array1, $array2);
		$mergeUnique = array_unique($merge);
		return $mergeUnique;
	}
}

if ( ! function_exists('object_to_array')) {
	/**
     * [object_to_array 将对象转成数组]
     *
     * @Author leeprince:2019-11-01T17:19:05+0800
     * @param  [type]                             $stdclassobject [description]
     * @return [type]                                             [description]
     */
	function object_to_array($stdclassobject)
	{
		$_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
		foreach ($_array as $key => $value) 
		{
			$value = (is_array($value) || is_object($value)) ? object_to_array($value) : $value;
			$array[$key] = $value;
		}

		return $array;
	}
}

if ( ! function_exists('isJson')) {
    /**
     * [isJson 判断是否为json]
     *
     * @Author leeprince:2019-11-01T17:19:52+0800
     * @param  [type]                             $string [description]
     * @return boolean                                    [description]
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}









