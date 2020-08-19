<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberActivityCard extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberActivityCardModel');
    }
    

    /**
     * [index 优惠券列表]
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
        $card_type = $this->input->get('card_type');
        $trigger_type = $this->input->get('trigger_type');
        $is_show = $this->input->get('is_show')??99;
        $status = $this->input->get('status')??99;
        $receive_id = $this->input->get('receive_id');
             
        $now = time();
        //卡券类型
        $card_type_option = array('1'=>'打折劵','2'=>'满减劵','3'=>'抵用券');
        $this->_data['card_type_option'] = $card_type_option;
        
        //卡券触发类型
        $trigger_type_option = array('1'=>'注册触发','2'=>'手动触发');
        $this->_data['trigger_type_option'] = $trigger_type_option;
        
        //展示状态
        $is_show_option = array('99'=>'展示状态','0'=>'不展示','1'=>'展示');
        $this->_data['is_show_option'] = $is_show_option;
        
        //卡券状态
        $status_option = array('99'=>'卡券状态','0'=>'无效','1'=>'有效');
        $this->_data['status_option'] = $status_option;
        
        //设备类型列表
        $this->load->model('TextModel');
        $type_option = $this->TextModel->get_option(array('type'=>2));
        $this->_data['type_option'] = $type_option;
        //商品列表
        $this->load->model('ProductModel');
        $product_option = $this->ProductModel->get_prod_option();
        $this->_data['product_option'] = $product_option;
        
        $where = array();
        if ($key){
            $where = "card_name = '$key'";
            $this->_data['key'] = $key;
        }
        if ($card_type){
            $where ['card_type'] = $card_type;
            $this->_data['card_type'] = $card_type;
        }
        if ($trigger_type){
            $where ['trigger_type'] = $trigger_type;
            $this->_data['trigger_type'] = $trigger_type;
        }
        if ($is_show!=99){
            $where ['is_show'] = $is_show;
            $this->_data['is_show'] = $is_show;
        }
        if ($status!=99){
            $where ['is_show'] = $status;
            $this->_data['status'] = $status;
        }
        if ($receive_id){
            $this->load->model('MemberActivityReceiveModel');
            $receive_info = $this->MemberActivityReceiveModel->get_list(array('a.id'=>$receive_id), 1, 0)[0];
            $where ['id'] = $receive_info['activity_id'];
        }
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MemberActivityCardModel->get_count($where);
        $list = $this->MemberActivityCardModel->get_list($where, $limit, $offset);
        foreach($list as &$info){
            $info ['time_frame'] = date('Y-m-d H:i:s', $info ['start_time']).' 至 '.date('Y-m-d H:i:s', $info ['end_time']);
            $info ['card_type'] = $card_type_option[$info ['card_type']];
            $info ['trigger_type'] = $trigger_type_option[$info ['trigger_type']];
            $info ['status'] = $status_option[$info ['is_show']];
            $info ['is_show'] = $is_show_option[$info ['is_show']];
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('member_activity_card/index', $this->_data);
    }
    
    public function info()
    {
        $id = $this->input->get('id');
        if (!$id){
            exit();
        }
        $this->load->model('MemberActivityScopeDetailModel');
        $where = array();
        $where ['id'] = $id;
        $info = $this->MemberActivityCardModel->get_info($where);
        $where = array();
        $where ['scope_id'] = $info['scope_id'];
        $where ['activity_id'] = $info['id'];
        $product_list =  $this->MemberActivityScopeDetailModel->get_product_list($where);
        //设备类型列表
        $this->load->model('TextModel');
        $type_option = $this->TextModel->get_option(array('type'=>2));
        $type = array();
        foreach ($product_list as $v){
            $type[$v['type']]['name'] = $type_option[$v['type']];
            $type[$v['type']]['list'][] = $v;
        }
        $scope = array();
        foreach ($type as $k=>$v){
            $scope [] = array('type'=>$k,'type_name'=>$v['name'],'list'=>$v['list']);
        }
        $info ['scope'] = $scope;
        $info ['time_frame'] = date('Y-m-d', $info['start_time']).' - '.date('Y-m-d', $info['end_time']);
        $this->ajax_return(array('code'=>200, 'data'=>$info));
    }
    
    /**
     * [add 添加功能]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $now = time();
        $card_name = $this->input->post('card_name');
        $card_type = $this->input->post('card_type');
        $limit_count = $this->input->post('limit_count');
        $quote = $this->input->post('quote');
        $product_list = $this->input->post('product_list');
        $trigger_type = $this->input->post('trigger_type');
        $time_frame = $this->input->post('time_frame');
        $card_total = $this->input->post('card_total');
        $card_describe = $this->input->post('card_describe');
        $is_show = $this->input->post('is_show');
        if (!$card_name||!$quote||!$product_list){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数'));
        }
        
        // 事务开始
        $this->db->trans_start();
        if ($card_type==1){
            $quote = round($quote/10,2);
        }
        list($start_time, $end_time) = switch_reservation($time_frame);
        $data = array();
        $data ['card_name'] = $card_name;
        $data ['card_type'] = (int)$card_type;
        $data ['trigger_type'] = (int)$trigger_type;
        $data ['scope_id'] = 0;
        $data ['limit_count'] = $limit_count;
        $data ['quote'] = $quote;
        $data ['start_time'] = $start_time;
        $data ['end_time'] = $end_time;
        $data ['card_total'] = (int)$card_total;
        $data ['card_remain'] = (int)$card_total;
        $data ['card_describe'] = $card_describe;
        $data ['is_show'] = (int)$is_show;
        $data ['create_time'] = $now;
        $data ['update_time'] = 0;
        $this->MemberActivityCardModel->add_data($data);
        $activity_id = $this->db->insert_id();
        
        $scope_data = array();
        $scope_data ['scope_activity_type'] = 1;
        $scope_data ['scope_manage_type'] = 1;
        $scope_data ['activity_id'] = $activity_id;
        $scope_data ['create_time'] = $now;
        $this->db->insert('member_activity_scope', $scope_data);
        $scope_id = $this->db->insert_id();
        
        $scope_detail = array();
        foreach ($product_list as $product){
            $detail = array();
            $detail ['scope_id'] = $scope_id;
            $detail ['activity_id'] = $activity_id;
            $detail ['manage_id'] = $product;
            $detail ['create_time'] = $now;
            $scope_detail [] = $detail;
        }
        $this->db->insert_batch('member_activity_scope_detail', $scope_detail);
        
        $save = array();
        $save ['scope_id'] = $scope_id;
        $where = array();
        $where ['id'] = $activity_id;
        $this->MemberActivityCardModel->update($save, $where);
        
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'创建失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log('member_activity_card', $data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建成功.'));
        }
    }
    
    /**
     * [update 编辑功能]
     *
     * @DateTime 2019-10-08
     * @Author   black.zhang
     */
    public function update()
    {
        $now = time();
        $id = $this->input->get('id');
        $card_name = $this->input->post('card_name');
        $card_type = $this->input->post('card_type');
        $limit_count = $this->input->post('limit_count');
        $quote = $this->input->post('quote');
        $product_list = $this->input->post('product_list');
        $trigger_type = $this->input->post('trigger_type');
        $time_frame = $this->input->post('time_frame');
        $card_total = $this->input->post('card_total');
        $card_describe = $this->input->post('card_describe');
        $is_show = $this->input->post('is_show');
        if (!$id||!$card_name||!$quote||!$product_list){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数'));
        }
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->MemberActivityCardModel->get_info($where);
        $where = array();
        $where ['scope_id'] = $info['scope_id'];
        $where ['activity_id'] = $id;
        $this->load->model('MemberActivityScopeDetailModel');
        $scope_detail =  $this->MemberActivityScopeDetailModel->get_product_list($where);
        $s_d = array();
        foreach ($scope_detail as $v){
            $s_d [] = $v['manage_id'];
        }
        
        // 事务开始
        $this->db->trans_start();
        $save = array();
        if ($product_list!=$s_d){//添加新的适用范围
            $scope_data = array();
            $scope_data ['scope_activity_type'] = 1;
            $scope_data ['scope_manage_type'] = 1;
            $scope_data ['activity_id'] = $id;
            $scope_data ['create_time'] = $now;
            $this->db->insert('member_activity_scope', $scope_data);
            $scope_id = $this->db->insert_id();
            
            $scope_detail = array();
            foreach ($product_list as $product){
                $detail = array();
                $detail ['scope_id'] = $scope_id;
                $detail ['activity_id'] = $id;
                $detail ['manage_id'] = $product;
                $detail ['create_time'] = $now;
                $scope_detail [] = $detail;
            }
            $this->db->insert_batch('member_activity_scope_detail', $scope_detail);
            $save ['scope_id'] = $scope_id;
        }
        
        if ($card_type==1){
            $quote = round($quote/10,2);
        }
        list($start_time, $end_time) = switch_reservation($time_frame);

        $save ['card_name'] = $card_name;
        $save ['card_type'] = (int)$card_type;
        $save ['trigger_type'] = (int)$trigger_type;
        $save ['limit_count'] = $limit_count;
        $save ['quote'] = $quote;
        $save ['start_time'] = $start_time;
        $save ['end_time'] = $end_time;
        $save ['card_total'] = (int)$card_total;
        $save ['card_remain'] = (int)$card_total;
        $save ['card_describe'] = $card_describe;
        $save ['is_show'] = (int)$is_show;
        $save ['update_time'] = $now;
        $where = array();
        $where ['id'] = $id;
        $this->MemberActivityCardModel->update($save, $where);
        
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功.'));
        }
    }
}
