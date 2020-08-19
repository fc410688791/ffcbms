<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MachineOffLine extends Admin_Controller
{    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * [index 设备离线列表]
     *
     * @DateTime 2019-11-06
     * @Author   black.zhang
     */
    public function index()
    {
        // 加载 Redis 类库
        $this->load->library('RedisDB');
        // 连接 Redis
        $redis = $this->redisdb->connect();
        $off_line_list = json_decode($redis->get('homeMachineOffline'), true);
        if (!$off_line_list){
            $off_line_list = array();
        }
        $ctime = time();
        foreach ($off_line_list as &$dev){
            $lastOnlieTime = "【未知】";
            $offlineTimeNum = "【上一次上线时间未知，无法估计时间】";
            if ( ! empty($dev['lastOnlieTime'])) {
            $lastOnlieTime = date("Y-m-d H:i:s", $dev['lastOnlieTime']);
            $timeDiff = time_diff($dev['lastOnlieTime'], $ctime);
            $offlineTimeNum = "{$timeDiff['day']}天{$timeDiff['hours']}小时{$timeDiff['minutes']}分";
            }
            $dev ['off_line_time_num'] = $offlineTimeNum;
        }
        $this->_data['list'] = $off_line_list;
        $this->template->admin_render('machine_off_line/index', $this->_data);
    }
}
