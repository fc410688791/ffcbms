<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberActivityCharge extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberActivityChargeModel');
    }
    

    /**
     * [index 充币活动列表]
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
        $is_charge_status = $this->input->get('is_charge_status');
        $is_gift_status = $this->input->get('is_gift_status');
        $status = $this->input->get('status');
        
        $where = array();
        if ($key){
            $where ['charge_name'] = $key;
            $this->_data['key'] = $key;
        }
        
        if (isset($is_charge_status)&&$is_charge_status!=99){
            $where ['is_charge_status'] = $is_charge_status;
            $this->_data['is_charge_status'] = $is_charge_status;
        }
        
        if (isset($is_gift_status)&&$is_gift_status!=99){
            $where ['is_gift_status'] = $is_gift_status;
            $this->_data['is_gift_status'] = $is_gift_status;
        }

        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MemberActivityChargeModel->get_count($where);
        $list = $this->MemberActivityChargeModel->get_list($where, $limit, $offset);
        foreach($list as &$info){
            if ($info['is_charge_status']==1){
                $info['is_charge_status'] = '展示';
            }else {
                $info['is_charge_status'] = '不展示';
            }
            if ($info['is_gift_status']==1){
                $info['is_gift_status'] = '参与';
            }else {
                $info['is_gift_status'] = '不参与';
            }
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
            $info ['gift_start_time'] = $info ['gift_start_time']?date("Y-m-d H:i:s", $info ['gift_start_time']):'-';
            $info ['gift_end_time'] = $info ['gift_end_time']?date("Y-m-d H:i:s", $info ['gift_end_time']):'-';
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该记录吗 ?', 'danger');
        $this->template->admin_render('member_activity_charge/index', $this->_data);
    }
    
    /**
     * [add 添加功能]
     *
     * @DateTime 2019-07-31
     * @Author   black.zhang
     */
    public function add()
    {
        $now = time();
        $charge_name = $this->input->post('charge_name');
        $charge_amount = $this->input->post('charge_amount');
        $is_charge_status = $this->input->post('is_charge_status');
        $is_gift_status = $this->input->post('is_gift_status');
        $gift_currency = $this->input->post('gift_currency');
        $type = $this->input->post('type');
        $gift_start_time = $this->input->post('gift_start_time');
        $gift_end_time = $this->input->post('gift_end_time');
        $data = array();
        $data ['charge_name'] = $charge_name;
        $data ['charge_amount'] = $charge_amount;
        $data ['is_charge_status'] = $is_charge_status;
        $data ['is_gift_status'] = $is_gift_status;
        if ($type==1){
            $data ['gift_start_time'] = 0;
            $data ['gift_end_time'] = 0;
        }elseif ($type==2){
            $data ['gift_start_time'] = 0;
        }
        if ($is_gift_status == 1){
            $data ['gift_currency'] = (int)$gift_currency;
            $data ['gift_start_time'] = strtotime($gift_start_time);
            $data ['gift_end_time'] = strtotime($gift_end_time);
        }else {
            $data ['gift_currency'] = 0;
            $data ['gift_start_time'] = 0;
            $data ['gift_end_time'] = 0;
        }
        $data ['create_time'] = $now;
        $data ['update_time'] = $now;
        $re = $this->MemberActivityChargeModel->add_data($data);
        if ($re) {
            $this->add_sys_log('member_activity_charge', $data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'创建失败.'));
        }
    }
    
    public function info()
    {
        $id = $this->input->get('id');
        if (!$id){
            exit();
        }
        $where = array();
        $where ['id'] = $id;
        $info = $this->MemberActivityChargeModel->get_info($where);
        if ($info['gift_start_time']&&$info['gift_end_time']){
            $info['type'] = 3;
            $info['gift_start_time'] = date('Y-m-d', $info['gift_start_time']);
            $info['gift_end_time'] = date('Y-m-d', $info['gift_end_time']);
        }elseif ($info['gift_end_time']){
            $info['type'] = 2;
            $info['gift_end_time'] = date('Y-m-d', $info['gift_end_time']);
        }else {
            $info['type'] = 1;
        }
        
        $this->ajax_return(array('code'=>200, 'data'=>$info));
    }
    
    public function update()
    {
        $now = time();
        $id = $this->input->get('id');
        $charge_name = $this->input->post('charge_name');
        $charge_amount = $this->input->post('charge_amount');
        $is_charge_status = $this->input->post('is_charge_status');
        $is_gift_status = $this->input->post('is_gift_status');
        $gift_currency = $this->input->post('gift_currency');
        $type = $this->input->post('type');
        $gift_start_time = $this->input->post('gift_start_time');
        $gift_end_time = $this->input->post('gift_end_time');
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->MemberActivityChargeModel->get_info($where);
        $save = array();
        if ($charge_name!=$info['charge_name']){
            $save ['charge_name'] = $charge_name;
        }
        
        if ($charge_amount!=$info['charge_amount']){
            $save ['charge_amount'] = $charge_amount;
        }
        
        if ($is_charge_status!=$info['is_charge_status']){
            $save ['is_charge_status'] = $is_charge_status;
        }
        
        if ($is_gift_status!=$info['is_gift_status']){
            $save ['is_gift_status'] = $is_gift_status;
        }

        if ($is_gift_status==1){
            $save ['gift_currency'] = (int)$gift_currency;
            $save ['gift_start_time'] = strtotime($gift_start_time);
            $save ['gift_end_time'] = strtotime($gift_end_time);
            if ($type==1){
                $save ['gift_start_time'] = 0;
                $save ['gift_end_time'] = 0;
            }elseif ($type==2){
                $save ['gift_start_time'] = 0;
            }
        }else {
            $save ['gift_currency'] = 0;
            $save ['gift_start_time'] = 0;
            $save ['gift_end_time'] = 0;
        }

        if ($save){
            $save ['update_time'] = $now;
            $re = $this->MemberActivityChargeModel->update($save, $where);
            if ($re){
                // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                $this->add_sys_log($id, $save);
                $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
            }else {
                $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
            }
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'没有任何修改！'));
        }
    }
}
