<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

define('ERRORLOGFILENAME', 'errorLog');
define('DEBUGLOGFILENAME', 'debugLog');
define('TIMELOGFILENAME', 'timeLog');

if ( ! function_exists('errorLog'))
{
	/**
	 * [errorLog 模型中记录业务错误请使用此方法, 其他请使用: $this->errorLog()]
	 *
	 * @DateTime 2018-06-05
	 * @Author   leeprince
	 * @param    [type]     $message [description]
	 * @return   [type]              [description]
	 */
	function errorLog($message, $ex = '', $filename = '')
	{
		if (is_array($message) || is_object($message))
		{
			$message = json_encode($message);
		}
		$e_message = $message;
		if ( ! empty($ex)) {
			$e_message .=  PHP_EOL .'[抛出异常内容]'. $ex->getMessage(). PHP_EOL .
				   		'[发生异常的文件]'. $ex->getFile() . PHP_EOL .
				   		'[发生异常的文件行号]'. $ex->getLine();
		}
		$thisCI = &get_instance();
		$logMessage = $e_message . PHP_EOL .
        '[请求URL]:'.current_url() . PHP_EOL .
		'[请求IP]:'.get_ip();

        if ( empty($filename)) {
        	$filename = ERRORLOGFILENAME;
        }
		log_msg($filename, 'error', $logMessage);
	}
}

if ( ! function_exists('debugLog'))
{
	/**
	 * [f_debugLog 测试日志]
	 *
	 * @DateTime 2018-06-05
	 * @Author   leeprince
	 * @param    [type]     $message [description]
	 * @return   [type]              [description]
	 */
	function debugLog($message, $filename = '')
	{
		if (empty($filename)) {
			$filename = DEBUGLOGFILENAME;
		}
		log_msg($filename, 'debug', $message);
	}
}

if ( ! function_exists('recordTimeLog')) {
    /**
     * [recordTimeLog 记录时间日志]
     *
     * @DateTime 2018-07-26
     * @Author   leeprince
     * @param    [type]     $start_mark [description]
     * @param    [type]     $end_mark   [description]
     * @return   [type]                 [description]
     */
    function recordTimeLog($start_mark, $end_mark, $functionName, $fileName = '')
    {
        if ( empty($start_mark) && empty($end_mark) && empty($functionName)) {
            return NULL;
        }
        if (!$fileName) {
            $fileName = TIMELOGFILENAME;
        }
        $thisCI = &get_instance();
        $timeInterval = $thisCI->benchmark->elapsed_time($start_mark, $end_mark);
        $timeMsg = '[基准测试]';
        $timeMsg .= '[方法名m]:'. $functionName;
        $timeMsg .= $msg;

        $timeMsg .= $timeInterval;
        log_msg($fileName, 'error', $timeMsg);
    }
}

if ( ! function_exists('perrorLog'))
{
	/**
	 * [errorLog 指定用户写入日志;模型中记录业务错误请使用此方法, 其他请使用: $this->errorLog()]
	 *
	 * @DateTime 2018-06-05
	 * @Author   leeprince
	 * @param    [type]     $message [description]
	 * @return   [type]              [description]
	 */
	function perrorLog($message)
	{
		log_msg(PERRORLOGFILENAME, 'error', $message);
	}
}

