<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberCurrencyRecord extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberCurrencyRecordModel');
    }

    /**
     * [index 列表]
     *
     * @DateTime 2019-07-30
     * @Author   black.zhang
     */
    public function index()
    {
        $now = time();
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $key = $this->input->get('key');
        $direction = $this->input->get('direction');
        $trade_type = $this->input->get('trade_type');
        $pay_type = $this->input->get('pay_type');
        $reservation = $this->input->get('reservation');

        //交易类型列表;
        $trade_type_option = [
            1=>'购买充币',
            2=>'充电消费',
            3=>'用户退款'
        ];
        $this->_data['trade_type_option'] = $trade_type_option;
        
        $or_where = array();
        if ($key){
            $or_where ['uuid'] = $key;
            $or_where ['out_trade_no'] = $key;
            $this->_data['key'] = $key;
        }
        
        $where = array();
        if ($direction){
            if ($direction==1){//转入
                $where ['trade_type !='] = 2;
            }elseif ($direction==2){//转出
                $where ['trade_type'] = 2;
            }
            $this->_data['direction'] = $direction;
        }
        
        if ($trade_type){
            $where ['trade_type'] = $trade_type;
            $this->_data['trade_type'] = $trade_type;
        }
        
        if ($pay_type){
            $where ['pay_type'] = $pay_type;
            $this->_data['pay_type'] = $pay_type;
        }
        
        if ($reservation){
            list($start_time, $end_time) = switch_reservation($reservation);
            $where['create_time >='] = $start_time;
            $where['create_time <='] = $end_time;
            $this->_data['reservation'] = $reservation;
        }
        
        $total_rows = $this->MemberCurrencyRecordModel->get_count($where,$or_where);
        $list = $this->MemberCurrencyRecordModel->get_list($where, $or_where, $limit, $offset); 
        foreach ($list as &$info){
            if ($info['trade_type'] == 2){
                $info['direction'] = '转出';
            }elseif (in_array($info['trade_type'], array(1,3))){
                $info['direction'] = '转入';
            }
            $trade_type = $info['trade_type'];
            $info['trade_type_name'] = $trade_type_option[$trade_type];
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->_data['reservation'] = $reservation;
        $this->template->admin_render('member_currency_record/index', $this->_data);
    }
}
