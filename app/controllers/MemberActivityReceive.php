<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberActivityReceive extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberActivityReceiveModel');
    }
    

    /**
     * [index 卡劵使用列表]
     *
     * @DateTime 2019-09-25
     * @Author   black.zhang
     */
    public function index()
    {
        $key = $this->input->get('key');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $receive_status = $this->input->get('receive_status');
     
        $now = time();
        //卡券状态
        $receive_status_option = array('0'=>'可用','1'=>'已用','2'=>'失效');
        $this->_data['receive_status_option'] = $receive_status_option;
        //卡券类型
        $card_type_option = array('1'=>'打折劵','2'=>'满减劵','3'=>'抵用券');
        $this->_data['card_type_option'] = $card_type_option;
        
        $where = array();
        if ($key){
            $where = "a.uuid = '$key' or a.activity_id='$key'";
            $this->_data['key'] = $key;
        }
        if ($receive_status!=''){
            $where ['receive_status'] = $receive_status;
            $this->_data['receive_status'] = $receive_status;
        }
        
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MemberActivityReceiveModel->get_count($where);
        $list = $this->MemberActivityReceiveModel->get_list($where, $limit, $offset);
        foreach($list as &$info){
            $info ['receive_status'] = $receive_status_option[$info ['receive_status']];
            $info ['card_type'] = $card_type_option[$info ['card_type']];
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
            if (!$info['out_trade_no']){
                $info['out_trade_no'] = '-';
                $info['product'] = '-';
                $info ['use_time'] = '-';
            }else {
                $info['product'] = $info['product_name'].'/'.$info['product_price'].'元';
                $info ['use_time'] = date("Y-m-d H:i:s", $info ['use_time']);
            }
            if ($info['trigger_type']==1){
                $info['time_frame'] = '-';
            }elseif ($info['trigger_type']==2){
                $info ['time_frame'] = date('Y-m-d H:i:s', $info ['start_time']).' 至 '.date('Y-m-d H:i:s', $info ['end_time']);
            }
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('member_activity_receive/index', $this->_data);
    }
}
