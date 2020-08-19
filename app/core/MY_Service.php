<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Service
{
    public function __construct()
    {
        log_message('info', "Service Class Initialized");
    }
 
    public function __get($key)
    {
        $CI = & get_instance();
        return $CI->$key;
    }

    /**
     * [getCurrentTime 获取当前时间]
     *
     * @DateTime 2018-07-17
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function getCurrentTime()
    {
    	return time();
    }
    
    /**
     * [env_is_production 判断不是线上环境, 跳过部分步骤方便测试. 跳过部分步骤时, 请谨慎考虑后面流程]
     *
     * @DateTime 2018-03-21
     * @Author   leeprince
     * @return   [type]     [description]
     */
    protected function env_isnot_production()
    {
        if (ENVIRONMENT == 'production')
        {
            return FALSE;
        }

        return TRUE;
    }
}