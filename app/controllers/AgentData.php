<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentData extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentModel');
        $this->load->model('AgentMerchantModel');
        $this->load->model('AgentOrderModel');
    }

    /**
     * [index 代理商]
     *
     * @DateTime 2019-06-05
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $reservation = $this->input->get('reservation');
        if ($reservation){
            $date = explode(' - ', $reservation);
            list($start_time , $end_time) = $date;
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time)+86400;
            $this->_data['reservation'] = $reservation;
        }else {
            $data = date('Y-m-d');
            $start_time = strtotime($data);
            $end_time = strtotime($data)+86400;
            $this->_data['reservation'] = $data.' - '.$data;
        }
        
        //有设备的投放点
        $merchant_list = $this->db->select('merchant_id,count(merchant_id) as merchant_count')
        ->from('ffc_machine')
        ->where('merchant_id <> 0')
        ->group_by('merchant_id')
        ->get()
        ->result_array();
        $this->_data['merchant_count'] = count($merchant_list);
        
        //代理商总数
        $agent_count = $this->AgentModel->get_count(array(),array());
        $this->_data['agent_count'] = $agent_count;
        
        //已认证代理数
        $is_verification_agent_count = $this->AgentModel->get_count(array('is_verification'=>1),array());
        $this->_data['is_verification_agent_count'] = $is_verification_agent_count;
        
        //0元代理数
        $proxy_pattern_agent_count = $this->AgentModel->get_count(array('proxy_pattern'=>3),array());
        $this->_data['proxy_pattern_agent_count'] = $proxy_pattern_agent_count;
        
        //总采购订单数
        $order_count = $this->AgentOrderModel->get_list_count(array('a_o.status'=>1));
        $this->_data['order_count'] = $order_count;
        
        //总采购收入
        $fee = $this->AgentOrderModel->get_fee_sum(array('a_o.status'=>1));
        $this->_data['fee'] = $fee;
        
        //显示时间搜索内投放点流水排名（前10名），可选择具体天数，时间范围内
        $where = "a_m.create_time < '$end_time'";   
        //Order Count Query
        $order_where = array();
        $order_where ['create_time >='] = $start_time;
        $order_where ['create_time <'] = $end_time;
        $order_where ['status'] = 1;  //支付成功
        $order_where ['complete_status !='] = 3;  //不包含退款
        $order_count_query = $this->db->select("merchant_id,sum(cash_fee) as cash_fee_statistics")
            ->from('ffc_order')
            ->where($order_where)
            ->group_by('merchant_id')
            ->get_compiled_select();
        $merchant_list = $this->db->select("a_m.name,ifnull(cash_fee_statistics,0) as cash_fee_statistics")
            ->from("agent_merchant a_m")
            ->where($where)
            ->join("($order_count_query) as o_c_q", "a_m.id=o_c_q.merchant_id", "left")
            ->limit(10, 0)
            ->order_by('cash_fee_statistics desc,a_m.id asc')
            ->get()
            ->result_array();
        $this->_data['merchant_list'] = $merchant_list;
        
        //展示当前设备下的场景流水占比（单个场景流水/总场景的流水）
        $order_where = array();
        $order_where ['o.create_time >='] = $start_time;
        $order_where ['o.create_time <'] = $end_time;
        $order_where ['o.status'] = 1;  //支付成功
        $order_where ['o.complete_status !='] = 3;  //不包含退款
        $scene_list = $this->db->select("s.name,sum(o.cash_fee) as cash_fee_statistics")
        ->from('ffc_order o')
        ->where($order_where)
        ->join("ffc_scene s", "o.scene_id=s.id", 'left')
        ->group_by('scene_id')
        ->get()
        ->result_array();
        $this->_data['scene_list'] = $scene_list;

        $this->_data['reservation'] = $reservation;
        $this->template->admin_render('agent_data/index', $this->_data);
    }
}
