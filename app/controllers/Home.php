<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Admin_Controller {
      
    public function __construct() {
        parent::__construct();
        $this->load->model('OrderModel');
        $this->load->model('MemberChargeOrderModel');
        // 加载 Redis 类库
        $this->load->library('RedisDB');
    }
    
    /**
     * [index description]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index() 
    {
        $now = time();
        if (IS_AJAX)
        {
            $method = $this->input->get('method');
            $reservation = $this->input->get('reservation');
            $start_time = strtotime($reservation);
            $end_time = strtotime("next month", $start_time);
            if ($end_time>$now){
                $end_time = $now;
            }
            
            // 连接 Redis
            $redis = $this->redisdb->connect();
            $redis->select(1);
            $order_data = array();
            for($i=$start_time;$i<$end_time;){
                $d = date("Y-m-d", $i);
                $j = $i + 86400;
                $data = $redis->get($d);
                if ($data){
                    $data = json_decode($data, true);
                }else {
                    //充电
                    $data = $this->get_count($i, $j);
                    if ($i<($now-86400)){
                        $redis->set($d,json_encode($data));
                    }
                    //充值
                }
                $order_data[$d] = $data;
                $i = $j;
            }
            if ($order_data){      
                $re = array();
                foreach ($order_data as $k=>$v){
                    $date_data = array(); 
                    $date_data ['x_k'] = $k;
                    $date_data ['y_k'] = round($v['order_count']['order_sum']-$v['refund_count']['order_sum'],2);
                    $re [] = $date_data;
                }
                $this->ajax_return(array('code'=>200, 'data'=>$re));
            }else {
                $this->ajax_return(array('code'=>201, 'msg'=>'获取图表信息失败'));
            }
            
        }
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $beginYesterday = $beginToday - 86400;
        $this->load->model('MachineModel');
        $dev_count_data = $this->MachineModel->get_count_data();
        $dev_count = array('1'=>0,'2'=>0,'3'=>0,);
        $dev_sum = 0;
        foreach ($dev_count_data as $v){
            $key = $v['status'];
            $dev_count [$key] = $v['dev_count'];
            $dev_sum += $v['dev_count'];
        }
        $dev_count ['dev_sum'] = $dev_sum;
        $this->_data['dev_count'] = $dev_count;
        
        //总订单统计（充电+充值）
        /* 总收入：               订单列表中-不包括退款状态下已支付-所有金额数量
                            总订单数：           订单列表中-不包括退款状态下已支付-所有订单数量 
        */
        //总订单
        $where = array('status ='=>1,'complete_status !='=>3);
        $all_order_count_data = $this->OrderModel->get_count_data($where);
        $all_order_count = array('order_sum'=>0, 'order_count'=>0,);
        foreach ($all_order_count_data as $v){
            $all_order_count ['order_sum'] += $v['order_sum'];
            $all_order_count ['order_count'] += $v['order_count'];
        }
        
        /* $charge_count_data = $this->MemberChargeOrderModel->get_count_data(array('order_status'=>1));
        if ($charge_count_data){
            $all_order_count ['order_count'] += $charge_count_data[0]['order_count'];
            $all_order_count ['order_sum'] += $charge_count_data[0]['order_sum'];
        } */
        
        $this->_data['all_order_count'] = $all_order_count;
        
        //今日订单统计
        $where = array('create_time >='=>$beginToday);
        $today_order_count_data = $this->OrderModel->get_count_data($where);
        $today_order_count = $yesterday_order_count = array(
            '0'=>array('order_sum'=>0, 'order_count'=>0),
            '1'=>array('order_sum'=>0, 'order_count'=>0),
            '2'=>array('order_sum'=>0, 'order_count'=>0)
        );
        $today_order_count ['order_count'] = 0;
        foreach ($today_order_count_data as $v){
            $key = $v['status'];
            $today_order_count [$key]['order_sum'] = $v['order_sum'];
            $today_order_count [$key]['order_count'] = $v['order_count'];
            $today_order_count ['order_count'] += $v['order_count'];
        }
        $where = array('complete_time >='=>$beginToday,'complete_status'=>3);
        $today_refund_count_data = $this->OrderModel->get_count_data($where);
        $today_order_count ['refund'] = $yesterday_order_count ['refund'] = array('order_sum'=>0, 'order_count'=>0);
        if ($today_refund_count_data){
            $today_order_count[1]['order_sum'] -= $today_refund_count_data[0]['order_sum'];
            $today_order_count[1]['order_count'] -= $today_refund_count_data[0]['order_count'];
            $today_order_count ['refund']['order_sum'] = $today_refund_count_data[0]['order_sum'];
            $today_order_count ['refund']['order_count'] = $today_refund_count_data[0]['order_count'];
        }
        
        /* $today_charge_count_data = $this->MemberChargeOrderModel->get_count_data(array('pay_time >='=>$beginToday,'order_status'=>1));
        if ($today_charge_count_data){
            $today_order_count[1]['order_sum'] += $today_charge_count_data[0]['order_sum'];
            $today_order_count[1]['order_count'] += $today_charge_count_data[0]['order_count'];
        } */
        
        $this->_data['today_order_count'] = $today_order_count;
        
        //昨日订单统计
        $where = array('pay_time >='=>$beginYesterday,'pay_time <'=>$beginToday);
        $yesterday_order_count_data = $this->OrderModel->get_count_data($where);
        $yesterday_order_count ['order_count'] = 0;
        foreach ($yesterday_order_count_data as $v){
            $key = $v['status'];
            $yesterday_order_count [$key]['order_sum'] = $v['order_sum'];
            $yesterday_order_count [$key]['order_count'] = $v['order_count'];
            $yesterday_order_count ['order_count'] += $v['order_count'];
        }
        $where = array('complete_time >='=>$beginYesterday,'complete_time <'=>$beginToday,'complete_status'=>3);
        $yesterday_refund_count_data = $this->OrderModel->get_count_data($where);
        if ($yesterday_refund_count_data){
            $yesterday_order_count[1]['order_sum'] -= $yesterday_refund_count_data[0]['order_sum'];
            $yesterday_order_count[1]['order_count'] -= $yesterday_refund_count_data[0]['order_count'];
            $yesterday_order_count ['refund']['order_sum'] = $yesterday_refund_count_data[0]['order_sum'];
            $yesterday_order_count ['refund']['order_count'] = $yesterday_refund_count_data[0]['order_count'];
        }
        
        /* $yesterday_charge_count_data = $this->MemberChargeOrderModel->get_count_data(array('pay_time >='=>$beginYesterday,'pay_time <'=>$beginToday,'order_status'=>1));
        if ($yesterday_charge_count_data){
            $yesterday_order_count[1]['order_sum'] += $yesterday_charge_count_data[0]['order_sum'];
            $yesterday_order_count[1]['order_count'] += $yesterday_charge_count_data[0]['order_count'];
        } */
        
        $this->_data['yesterday_order_count'] = $yesterday_order_count;
        
        //待处理
        //采购待确认订单
        $this->load->model('AgentOrderModel');
        $c1 = $this->AgentOrderModel->get_list_count(array('a_o.status'=>1,'a_o.is_confirm'=>0));
        $this->_data['c1'] = $c1;
        //设备老化完成故障
        $this->load->model('MachineIotTriadModel');
        // 获得 搜索/筛选 数据的记录数
        $c2 = 0;
        $whereLike = '';
        $where = array('aging_status<>'=>0,'storage_status'=>0);
        $total_rows = $this->MachineIotTriadModel->getLikeCount($where, $whereLike);
        $triad_list = $this->MachineIotTriadModel->getLikeData($where, $whereLike, $total_rows, 0);
        // 连接 Redis
        $redis = $this->redisdb->connect();
        foreach ($triad_list as &$info){
            $re = $redis->get('aging-'.$info['device_name']);
            $data = json_decode($re, true);
            switch ($info['aging_status']){
                case 1:
                    if ($data['faultMachines']){
                        if (count($data['faultMachines'])>0){
                            $c2 ++;
                        }
                    }
                    break;
                case 2:
                    $c2 ++;
                    break;
                default:
                    break;
            }
        }
        // 关闭 Redis
        $redis->close();
        $this->_data['c2'] = $c2;
        //分佣待确定
        $this->load->model('AgentCommissionModel');
        $c3 = $this->AgentCommissionModel->get_count(array('commission_status'=>0));
        $this->_data['c3'] = $c3;
        //提现待审核订单
        $this->load->model('AgentWithdrawModel');
        $c4 = $this->AgentWithdrawModel->get_count(array('a_w.status'=>0));
        $this->_data['c4'] = $c4;
        //提现待确定订单
        $c5 = $this->AgentWithdrawModel->get_count(array('a_w.status'=>5));
        $this->_data['c5'] = $c5;
        //待退款订单
        $this->load->model('RefundModel');
        $c6 = $this->RefundModel->get_count(array('r.status'=>2));
        $this->_data['c6'] = $c6;
        
        // 连接 Redis
        $redis = $this->redisdb->connect();
        $dev_list = json_decode($redis->get('homeMachineOffline'), true);
        if (!$dev_list){
            $off_line_count = 0;
        }else {
            $off_line_count = count($dev_list);
        }
        /* $ctime = time();
        foreach ($dev_list as &$dev){
            $lastOnlieTime = "【未知】";
            $offlineTimeNum = "【上一次上线时间未知，无法估计时间】";
            if ( ! empty($dev['lastOnlieTime'])) {
                $lastOnlieTime = date("Y-m-d H:i:s", $dev['lastOnlieTime']);
                $timeDiff = time_diff($dev['lastOnlieTime'], $ctime);
                $offlineTimeNum = "{$timeDiff['day']}天{$timeDiff['hours']}小时{$timeDiff['minutes']}分";
            }
            $dev ['off_line_time_num'] = $offlineTimeNum;
        } */
        $this->_data['off_line_count'] = $off_line_count; 
        
        $this->_data['restype'] = 'by_res_tp'; 
        $reservation = date("Y-m",$beginToday);
        $this->_data['reservation'] = $reservation;
        $this->template->admin_render('home/index', $this->_data);
    }
    
    private function get_count($start_time,$end_time)
    {
        //订单统计
        $where = array('status'=>1,'pay_time >='=>$start_time,'pay_time <'=>$end_time);
        $order_count_data = $this->OrderModel->get_count_data($where);;
        $order_count = $refund_count = array('order_sum'=>0, 'order_count'=>0);
        foreach ($order_count_data as $v){
            $order_count ['order_sum'] += $v['order_sum'];
            $order_count ['order_count'] += $v['order_count'];
        }
        $where = array('complete_status'=>3,'complete_time >='=>$start_time,'complete_time <'=>$end_time);
        $refund_count_data = $this->OrderModel->get_count_data($where);
        foreach ($refund_count_data as $v){
            $refund_count ['order_sum'] += $v['order_sum'];
            $refund_count ['order_count'] += $v['order_count'];
        }
        return array('order_count'=>$order_count,'refund_count'=>$refund_count);
    }
}