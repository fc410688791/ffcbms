<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agent extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentModel');
        $this->load->model('AgentUserModel');
        $this->load->model('AgentMerchantModel');
        $this->load->model('MachineModel');
        $this->load->model('AgentCommissionModel');
        $this->load->model('PositionModel');
        $this->load->model('FileModel');
        $this->load->model('AgentCardModel');
    }

    /**
     * [index 代理商]
     *
     * @DateTime 2019-03-04
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $key = $this->input->get('key');
        $proxy_pattern = $this->input->get('proxy_pattern');
        $is_verification = $this->input->get('is_verification');
        $commission_status = $this->input->get('commission_status');
        $reservation = $this->input->get('reservation');
        $now = time();
        if (!$reservation){
            $reservation = "2019-01-01 - ".date('Y-m-d', $now);
        }
        $where = array();
        if ($proxy_pattern!=''){
            $where ['a.proxy_pattern'] = $proxy_pattern;
            $this->_data['proxy_pattern'] = $proxy_pattern;
        }
        if ($commission_status!=''){
            $where ['a_c.commission_status'] = $commission_status;
            $this->_data['commission_status'] = $commission_status;
        }
        if ($is_verification!=''){
            $where ['a.is_verification'] = $is_verification;
            list($start_time, $end_time) = switch_reservation($reservation);
            if ($is_verification==1){
                $where ['a.verify_time >='] = $start_time;
                $where ['a.verify_time <='] = $end_time;
            }else {
                $where ['a.create_time >='] = $start_time;
                $where ['a.create_time <='] = $end_time;
            }
            $this->_data['is_verification'] = $is_verification;
        }
        if ($key){
            $where = "a.id = '$key' or a.user_name = '$key' or a.card_name = '$key'";
            $this->_data['key'] = $key;
        }
        $total_rows = $this->AgentModel->get_count($where);
        $list = $this->AgentModel->get_list($where, $limit, $offset); 
        foreach ($list as &$info){
            //子商户
            $where = array();
            $where ['a_u.agent_id'] = $info['id'];
            //$where ['group_id'] = 2;  //商户
            $where_in = array('field'=>'a_u.group_id','list'=>array(2,3));
            $info['store_count'] = $this->AgentUserModel->get_count($where, $where_in);
            //投放点
            $where = array();
            $where ['a_m.agent_id'] = $info['id'];
            $where ['a_m.status'] = 1;
            $info['merchant_count'] = $this->AgentMerchantModel->get_count($where);
            //设备
            $where = array();
            $where ['m.agent_id'] = $info['id'];
            $info['machine_count'] = $this->MachineModel->get_count($where);
            
            $info['commission_proportion'] = $info['commission_proportion']?$info['commission_proportion'].'%':'-';
            if ($info['commission_status']==1){
                $info['commission_status_name'] = '正常分佣';
            }elseif ($info['commission_status']==2){
                $info['commission_status_name'] = '暂停分佣';
            }else {
                $info['commission_status_name'] = '不可分佣';
            }
            if ($info['is_verification']==1){
                $info['is_agreement'] = "已同意";
                $info['is_verification'] = '已认证';
                $info['verify_time'] = date('Y-m-d H:i:s', $info['verify_time']);
            }else {
                $info['is_agreement'] = "未同意";
                $info['is_verification'] = '未认证';
                $info['verify_time'] = '-';
            }
            if (!$info['card_name']){
                $info['card_name'] = '-';
            }
            if (!$info['user_name']){
                $info['user_name'] = '-';
            }
            if ($info['onetime_share']==1){
                $info['onetime_share'] = '已审批';
            }else{
                $info['onetime_share'] = '-';
            }
            
            //上级代理
            if ($info['rel_agent_id']){
                $agent_info = $this->AgentModel->get_info(array('id'=>$info['rel_agent_id']));
                $info['rel_agent_card_name'] = $agent_info['card_name']?$agent_info['card_name']:$agent_info['user_name'];
            }else {
                $info['rel_agent_card_name'] = '-';
            }
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->_data['reservation'] = $reservation;
        $this->template->admin_render('agent/index', $this->_data);
    }
    
    /**
     * [info 查看功能]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     */
    public function info()
    {
        $now = time();
        $id = $this->input->get('id');
        $block = $this->input->get('block');
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentModel->get_info($where);
        if ($block=='update'){
            $agent_commission_info = $this->AgentCommissionModel->get_info(array('agent_id'=>$id,'c_commission_type'=>1));
            if ($agent_commission_info){
                $info ['commission_type'] = $agent_commission_info['commission_type'];
                $info ['commission_proportion'] = $agent_commission_info['commission_proportion'];
                $info ['commission_withdrawal_amount'] = $agent_commission_info['commission_withdrawal_amount'];
                $info ['commission_withdrawal_time'] = $agent_commission_info['commission_withdrawal_time']?$agent_commission_info['commission_withdrawal_time']:10;
                if ($agent_commission_info ['commission_contract_start_time']&&$agent_commission_info ['commission_contract_end_time']){
                    $info ['contract_time'] = date('Y-m-d', $agent_commission_info['commission_contract_start_time']).' - '.date('Y-m-d', $agent_commission_info['commission_contract_end_time']);
                }else {
                    $info ['contract_time'] = date('Y-m-d', $now).' - '.date('Y-m-d', $now);
                }
                $info ['commission_time'] = $agent_commission_info['commission_time']?date('Y-m-d H:i:s', $agent_commission_info['commission_time']):'无';
                if ($agent_commission_info ['commission_status']==0){
                    $info ['recovery'] = 1;
                }else {
                    $info ['recovery'] = 0;
                }
            }else {
                $info ['commission_type'] = 1;
                $info ['commission_proportion'] = 0;
                $info ['commission_withdrawal_amount'] = 1000;
                $info ['commission_withdrawal_time'] = 10;
                $info ['commission_time'] = '无';
                $info ['recovery'] = 0;
            }
        }elseif ($block=='info'){
            if ($info['sex']==1){
                $info['sex'] = '男';
            }elseif ($info['sex']==2){
                $info['sex'] = '女性';
            }else {
                $info['sex'] = '未知';
            }
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
            $info['login_time'] = date('Y-m-d H:i:s', $info['login_time']);
            if ($info['is_verification']){
                $info['is_verification'] = '已认证';
                $info['verify_time'] = date('Y-m-d H:i:s', $info['verify_time']);
                $info['f_url'] = $info['front_file_id']?$this->FileModel->get_url(array('id'=>$info['front_file_id'])):'';
                $info['b_url'] = $info['back_file_id']?$this->FileModel->get_url(array('id'=>$info['back_file_id'])):'';
                $info['h_url'] = $info['hold_file_id']?$this->FileModel->get_url(array('id'=>$info['hold_file_id'])):'';
            }else {
                $info['is_verification'] = '未认证';
            }
            if ($info['is_agreement']){
                $info['is_agreement'] = '同意';
            }else {
                $info['is_agreement'] = '拒绝';
            }
            //银行卡
            $card_list = $this->AgentCardModel->get_list(array('agent_id'=>$id));
            foreach ($card_list as &$card_info){
                $card_info ['create_time'] = date('Y-m-d H:i:s', $card_info ['create_time']);
            }
            $info['card_list'] = $card_list;
        }
        $this->ajax_return(array('code'=>200, 'data'=>$info));
    }
    
    /**
     * [update 编辑功能]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function update()
    {
        $id = $this->input->get('id');
        $proxy_pattern = $this->input->post('proxy_pattern');
        $onetime_discount_rate = $this->input->post('onetime_discount_rate');
        $onetime_num = $this->input->post('onetime_num');
        $onetime_share = $this->input->post('onetime_share');
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentModel->get_info($where);
        $save = array();
        
        if (isset($proxy_pattern)&&$proxy_pattern!=$info['proxy_pattern']){
            $save ['proxy_pattern'] = $proxy_pattern;
        }
        if (isset($onetime_discount_rate)&&$onetime_discount_rate!=$info['onetime_discount_rate']){
            $save ['onetime_discount_rate'] = $onetime_discount_rate;
        }
        if (isset($onetime_num)&&$onetime_num!=$info['onetime_num']){
            $save ['onetime_num'] = (int)$onetime_num;
        }
        if (isset($onetime_share)&&$onetime_share!=$info['onetime_share']&&$info['proxy_pattern']==2){
            $save ['onetime_share'] = $onetime_share;
        }
        
        if ($save){
            $save ['update_time'] = time();
            $re = $this->AgentModel->update($save, $where);
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
    
    /**
     * [address 收货地址]
     *
     * @DateTime 2019-03-04
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function address()
    {
        $this->load->model('AgentAddressModel');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $agent_id = $this->input->get('agent_id');
        $default_address_id = $this->input->get('default_address_id');
        
        $where = array();
        if ($agent_id){
            $where ['agent_id'] = $agent_id;
        }
        $total_rows = $this->AgentAddressModel->get_count($where);
        $list = $this->AgentAddressModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            if ($info['id'] == $default_address_id){
                $info['is_default'] = '是';
            }else {
                $info['is_default'] = '否';
            }
            $info ['position'] = $info ['p_name'].$info ['position'];
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent/address', $this->_data);
    }
    
    /**
     * [merchant 投放地址]
     *
     * @DateTime 2019-03-04
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function merchant()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $agent_id = $this->input->get('agent_id');
        
        $where = array();
        if ($agent_id){
            $where ['a_m.agent_id'] = $agent_id;
        }
        $where ['a_m.status'] = 1;
        $total_rows = $this->AgentMerchantModel->get_count($where);
        $list = $this->AgentMerchantModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent/merchant', $this->_data);
    }
    
    /**
     * [user 员工]
     *
     * @DateTime 2019-03-04
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function user()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $agent_id = $this->input->get('agent_id');
        $agent_user_id = $this->input->get('agent_user_id');
        $agent_user_group = array('1'=>'运维人员','2'=>'商户');
        $where = array();
        if ($agent_id){
            $where ['a_u.agent_id'] = $agent_id;
            $where ['a_u.parent_type'] = 0;
        }
        if ($agent_user_id){
            $where ['a_u.parent_id'] = $agent_user_id;
            $where ['a_u.parent_type'] = 1;
        }
        $where ['a_u.status <>'] = 0;
        $where ['a_u.group_id'] = 1;  //运维人员
        $total_rows = $this->AgentUserModel->get_count($where);
        $list = $this->AgentUserModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
            $group_id = $info['group_id'];
            $info['group_name'] = $agent_user_group[$group_id];
            if ($info['position_id']){
                $position_info = $this->PositionModel->get_info(array('id'=>$info['position_id']));
                $info['p_name'] =  str_replace(",", "", $position_info['name']);
            }else {
                $info['p_name'] = '';
            }
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent/user', $this->_data);
    }

    
    /**
     * [store 商户]
     *
     * @DateTime 2019-06-13
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function store()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $agent_id = $this->input->get('agent_id');
        
        $where = array();
        if ($agent_id){
            $where ['agent_id'] = $agent_id;
        }
        $where ['group_id'] = 2;  //商户
        $total_rows = $this->AgentUserModel->get_count($where);
        $list = $this->AgentUserModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            if ($info['role_merchant_id']){
                $where_in = explode(',', $info['role_merchant_id']);
                $name_list = $this->AgentMerchantModel->get_name($where_in);
                $name = array();
                foreach ($name_list as $v){
                    $name [] = $v['name'];
                }
                $info['role_merchant_id'] = join(', ', $name);
            }
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent/store', $this->_data);
    }
    
    /**
     * [commission 分佣设置]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function commission()
    {
        $now = time();
        $id = $this->input->get('id');
        $commission_proportion = (int)$this->input->post('commission_proportion');
        $commission_type = $this->input->post('commission_type');
        $commission_withdrawal_amount = (float)$this->input->post('commission_withdrawal_amount');
        $commission_withdrawal_time = (int)$this->input->post('commission_withdrawal_time');
        $contract_time = $this->input->post('contract_time');
        $commission_time = strtotime($this->input->post('commission_time'));
        $where = array();
        $where ['agent_id'] = $id;
        $where ['c_commission_type'] = 1;  //代理商
        $info = $this->AgentCommissionModel->get_info($where);
        $agent_info = $this->AgentModel->get_info(array('id'=>$id));
        if ($agent_info['proxy_pattern']==2){
            $this->ajax_return(array('code'=>400, 'msg'=>'内部自营不能设置分佣！'));
        }
        $data = array();
        if ($info){//修改
            list($commission_contract_start_time, $commission_contract_end_time) = switch_reservation($contract_time);
            if (isset($commission_proportion)&&$commission_proportion!=$info['commission_proportion']){
                $data ['commission_proportion'] = $commission_proportion;
            }
            if (isset($commission_type)&&$commission_type!=$info['commission_type']){
                $data ['commission_type'] = $commission_type;
            }
            if (isset($commission_withdrawal_amount)&&$commission_withdrawal_amount!=$info['commission_withdrawal_amount']){
                $data ['commission_withdrawal_amount'] = $commission_withdrawal_amount;
            }
            if ($commission_withdrawal_time&&$commission_withdrawal_time!=$info['commission_withdrawal_time']&&$commission_type==2){
                $data ['commission_withdrawal_time'] = $commission_withdrawal_time;
            }
            if ($commission_contract_start_time!=$info['commission_contract_start_time']){
                $data ['commission_contract_start_time'] = $commission_contract_start_time;
            }
            if ($commission_contract_end_time!=$info['commission_contract_end_time']){
                $data ['commission_contract_end_time'] = $commission_contract_end_time;
            }
            if ($commission_time&&$commission_time!=$info['commission_time']){
                $data ['commission_time'] = $commission_time;
            }
            if ($data){
                $data ['commission_status'] = 0;
                $data ['update_time'] = $now;
                $re = $this->AgentCommissionModel->update($data, $where);
            }else {
                $this->ajax_return(array('code'=>400, 'msg'=>'无修改！'));
            }
        }else {//新增
            $data['agent_id'] = $id;
            $data['c_commission_type'] = 1;
            $data['commission_type'] = $commission_type;
            $data['commission_withdrawal_amount'] = $commission_withdrawal_amount;
            $data['commission_withdrawal_time'] = $commission_withdrawal_time;
            $data['commission_proportion'] = $commission_proportion;
            $data['commission_time'] = $commission_time;
            $data['commission_status'] = 0;
            $data['create_time'] = $now;
            $re = $this->AgentCommissionModel->add_data($data);
        }
        
        if ($re){
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $data);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
        }
    }
    
    /**
     * [confirm 开始/结束分佣]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function confirm()
    {
        $now = time();
        $id = $this->input->get('id');
        $commission_status = $this->input->post('commission_status');
        $content = $this->input->post('content');
        $where = array();
        $where ['agent_id'] = $id;
        $where ['c_commission_type'] = 1;
        $agent_commission_info = $this->AgentCommissionModel->get_info($where);
        if (!$agent_commission_info){
            $this->ajax_return(array('code'=>400, 'msg'=>'无分佣设置！'));
        }
        $save = array();
        if (isset($commission_status)&&$commission_status!=$agent_commission_info['commission_status']){
            $save ['commission_status'] = $commission_status;
        }
        if ($save){
            $save ['update_time'] = $now;
            $re = $this->AgentCommissionModel->update($save, $where);
            if ($re){
                if ($commission_status==2&&$content){
                    $this->load->model('ReasonModel');
                    $data = array();
                    $data ['type'] = 1;
                    $data ['uid'] = $id;
                    $data ['content'] = $content;
                    $data ['create_time'] = time();
                    $this->ReasonModel->add_data($data);
                }
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
    
    /**
     * [recovery 取消分佣设置]
     *
     * @DateTime 2019-12-11
     * @Author   black.zhang
     */
    public function recovery()
    {
        $id = $this->input->get('id');
        $c_commission_type = $this->input->get('c_commission_type');
        $where = array();
        if ($c_commission_type==1){
            $where ['agent_id'] = $id;
        }elseif ($c_commission_type==2){
            $where ['agent_user_id'] = $id;
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'类型错误！'));
        }
        $where ['c_commission_type'] = $c_commission_type;
        $where ['commission_status'] = 0;
        $agent_commission_info = $this->AgentCommissionModel->get_info($where);
        if (!$agent_commission_info){
            $this->ajax_return(array('code'=>400, 'msg'=>'无分佣设置！'));
        }
        $save = array();
        $save ['commission_status'] = 3;
        $save ['update_time'] = time();
        $re = $this->AgentCommissionModel->update($save, $where);
        if ($re){
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
        }
    }
}
