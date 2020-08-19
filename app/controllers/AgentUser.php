<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentUser extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentModel');
        $this->load->model('AgentUserModel');
        $this->load->model('AgentMerchantModel');
        $this->load->model('MachineModel');
        $this->load->model('AgentCommissionModel');
        $this->load->model("AgentTurnRecordModel");
        $this->load->model("AgentTurnDataRecordModel");
    }

    /**
     * [index 商户]
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
        $is_verification = $this->input->get('is_verification');
        $commission_status = $this->input->get('commission_status');
        $reservation = $this->input->get('reservation');
        $agent_id = $this->input->get('agent_id');
        
        //代理商列表
        $this->load->model('AgentModel');
        $agent_list = $this->AgentModel->get_user($where = array(), $field = 'id,user_name');
        $this->_data['agent_list'] = $agent_list;
        
        $where = array();
        if ($key){
            $where = "a_u.id = '$key' or a_u.user_name = '$key' or a_u.name = '$key'";
            $this->_data['key'] = $key;
        }else {
            if (isset($is_verification)&&$is_verification!=''){
                $where ['a_u.is_verification'] = $is_verification;
                $this->_data['is_verification'] = $is_verification;
            }
            if ($commission_status!=''){
                $where ['a_c.commission_status'] = $commission_status;
                $this->_data['commission_status'] = $commission_status;
            }
            if ($reservation){
                list($start_time, $end_time) = switch_reservation($reservation);
                $where['a_u.create_time >='] = $start_time;//00:00:00
                $where['a_u.create_time <='] = $end_time;//23:59:59
                $this->_data['reservation'] = $reservation;
            }
            if ($agent_id){
                $where['a_u.agent_id'] = $agent_id;
            }
            $where['a_u.status<>'] = 0;
            $where['a_u.parent_type'] = 0;
        }
        $where_in = array('field'=>'group_id','list'=>array(2,3));
        $total_rows = $this->AgentUserModel->get_count($where, $where_in);
        $list = $this->AgentUserModel->get_list($where, $where_in, $limit, $offset);
        foreach ($list as &$info){
            if ($info['role_merchant_id']){
                $merchant_list = explode(',', $info['role_merchant_id']);
                $info['merchant_count'] = count($merchant_list);
                $where_in = array('field'=>'merchant_id','list'=>$merchant_list);
                $info['machine_count'] = $this->MachineModel->get_count(array(), $where_in);
            }else {
                $info['merchant_count'] = 0;
                $info['machine_count'] = 0;
            }
            $info['commission_proportion'] = $info['commission_proportion']?$info['commission_proportion'].'%':'-';
            $info['withdraw_cash_amount'] = $info['withdraw_cash_amount']?$info['withdraw_cash_amount']:'-';
            if ($info['commission_type']==1){
                $info['commission_type'] = '即时';
            }elseif ($info['commission_type']==2){
                $info['commission_type'] = '月结';
            }else{
                $info['commission_type'] = '-';
            }
            if ($info['is_verification']==1){
                $info['is_verification'] = '已认证';
            }else{
                $info['is_verification'] = '未认证';
            }
            if ($info['commission_status']==1){
                $info['commission_status_name'] = '正常分佣';
            }elseif ($info['commission_status']==2){
                $info['commission_status_name'] = '暂停分佣';
            }else {
                $info['commission_status_name'] = '不可分佣';
            }
            $info['verify_time'] = $info['verify_time']?date('Y-m-d H:i:s', $info['verify_time']):'-';
            if ($info['group_id']==2){
                $info['group_id'] = '商户';
            }elseif ($info['group_id']==3){
                $info['group_id'] = '运营商户';
            }
            $info['staff'] = $this->AgentUserModel->get_count(array('a_u.parent_id'=>$info['id'],'a_u.parent_type'=>1,'a_u.status'=>1));
        }
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent_user/index', $this->_data);
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
        $info = $this->AgentUserModel->get_info($where);
        if ($block=='update'){
            $agent_commission_info = $this->AgentCommissionModel->get_info(array('agent_user_id'=>$id,'c_commission_type'=>2));
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
        }
        $this->ajax_return(array('code'=>200, 'data'=>$info));
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
        $where ['id'] = $id;
        $agent_id = $this->AgentUserModel->get_info($where)['agent_id'];
        $where = array();
        $where ['agent_user_id'] = $id;
        $where ['c_commission_type'] = 2;  //代理商
        $info = $this->AgentCommissionModel->get_info($where);
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
                $data['commission_status'] = 0;
                $data ['update_time'] = $now;
                $re = $this->AgentCommissionModel->update($data, $where);
            }else {
                $this->ajax_return(array('code'=>400, 'msg'=>'无修改！'));
            }
        }else {//新增
            $data['agent_id'] = $agent_id;
            $data['agent_user_id'] = $id;
            $data['c_commission_type'] = 2;
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
        $where ['agent_user_id'] = $id;
        $where ['c_commission_type'] = 2;
        $commission_info = $this->AgentCommissionModel->get_info($where);
        if (!$commission_info){
            $this->ajax_return(array('code'=>400, 'msg'=>'无分佣设置！'));
        }
        $save = array();
        if (isset($commission_status)&&$commission_status!=$commission_info['commission_status']){
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
    
    public function update()
    {
        $id = $this->input->post('id');
        $password = $this->input->post('password');
        $status = $this->input->post('status');
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentUserModel->get_info($where);
        $save = array();
        if ($password){
            $pwd_prefix = 'ffc-';
            $password = md5(md5($pwd_prefix.$password));
            if ($password!=$info['password']){
                $save ['password'] = $password;
            }
        }
        
        if ($status!==null){
            if ($status!=$info['status']){
                $save ['status'] = $status;
            }
        }
        
        if ($save){
            $save ['update_time'] = time();
            $re = $this->AgentUserModel->update($save, $where);
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
     * [get_merchant_list 获取子商户列表]
     * @DateTime 2019-09-19
     * @Author   black.zhang
     */
    public function get_merchant_list()
    {
        $agent_id = $this->input->get('agent_id');
        $where = array();
        $where ['status'] = 1;
        $where ['group_id <>'] = 1;
        if ($agent_id){
            $where ['agent_id'] = $agent_id;
        }
        $list = $this->AgentUserModel->get_field_list($where, 'id,user_name as name');
        $re = array('code'=>200, 'data'=>$list);
        exit(json_encode($re));
    }
    
    /**
     * [turn 转移]
     * @DateTime 2019-09-19
     * @Author   black.zhang
     */
    public function turn()
    {
        $merchant_list = $this->input->post('merchant_list');//选择的商户列表
        $is_redirect_trun_agent = $this->input->post('is_redirect_trun_agent');//0：重新绑定商户、1：迁移商户
        $agent_id = $this->input->post('agent_id');//待绑定代理商
        $merchant_id = $this->input->post('merchant_id');//待绑定商户
        if (!$merchant_list||!$agent_id||(!$is_redirect_trun_agent&&!$merchant_id)){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数！'));
        }
        // 事务开始
        $this->db->trans_start();
        $now = time();
        $user_id = $this->top_get_session('user_id');
        foreach ($merchant_list as $merchant){
            $agent_user_info = $this->AgentUserModel->get_field_list(array('id'=>$merchant), 'agent_id,role_merchant_id')[0];
            if (($agent_user_info['agent_id']!=$agent_id)||($agent_user_info['agent_id']==$agent_id&&!$is_redirect_trun_agent&&$merchant!=$merchant_id)){
                $add = array();
                $add['turn_type'] = 3;  //商户
                $add['is_redirect_trun_agent'] = $is_redirect_trun_agent;
                $add['current_agent_id'] = $agent_user_info['agent_id'];
                $add['after_agent_id'] = $agent_id;
                $add['create_time'] = $now;
                $this->AgentTurnRecordModel->add_data($add);
                $turn_record_id = $this->db->insert_id();
                
                $add_data = array();
                $add_data ['turn_record_id'] = $turn_record_id;
                $add_data ['object_turn_type'] = 3;  //商户
                $add_data ['object_id'] = $merchant;
                $add_data ['current_agent_id'] = $agent_user_info['agent_id'];
                $add_data ['after_agent_id'] = $agent_id;
                $add_data ['current_agent_user_merchant_id'] = $merchant;
                $add_data ['after_agent_user_merchant_id'] = $merchant;
                $add_data ['op_user_id'] = $user_id;
                $add_data ['create_time'] = $now;
                $this->AgentTurnDataRecordModel->add_data($add_data);
                
                if ($agent_user_info['role_merchant_id']){//商户拥有投放点权限
                    $role_merchant_id = explode(',', $agent_user_info['role_merchant_id']);
                    if ($agent_user_info['agent_id']!=$agent_id){//绑定到新的代理商
                        $save_merchant = array();
                        $save_merchant ['agent_id'] = $agent_id;
                        $save_merchant ['update_time'] = $now;
                        if ($is_redirect_trun_agent){
                            $this->AgentUserModel->update($save_merchant, array('id'=>$merchant));
                        }else {
                            $to_agent_user_info = $this->AgentUserModel->get_field_list(array('id'=>$merchant_id), 'agent_id,role_merchant_id')[0];
                            if ($to_agent_user_info['role_merchant_id']){
                                $save_merchant ['role_merchant_id'] = $to_agent_user_info['role_merchant_id'].','.$agent_user_info['role_merchant_id'];
                            }else {
                                $save_merchant ['role_merchant_id'] = $agent_user_info['role_merchant_id'];
                            }
                            $this->AgentUserModel->update($save_merchant, array('id'=>$merchant_id));  //目标商户
                            
                            $save_merchant ['role_merchant_id'] = '';
                            $this->AgentUserModel->update($save_merchant, array('id'=>$merchant));  //来源商户
                        }
                        $save_role_merchant = array();
                        $save_role_merchant ['agent_id'] = $agent_id;
                        $save_role_merchant ['update_time'] = $now;
                        //投放点迁移
                        foreach ($role_merchant_id as $role_merchant){
                            $add = array();
                            $add['turn_type'] = 2;  //投放点
                            $add['is_redirect_trun_agent'] = 1;
                            $add['current_agent_id'] = $agent_user_info['agent_id'];
                            $add['after_agent_id'] = $agent_id;
                            $add['create_time'] = $now;
                            $this->AgentTurnRecordModel->add_data($add);
                            $turn_record_id = $this->db->insert_id();
                            
                            $add_data = array();
                            $add_data ['turn_record_id'] = $turn_record_id;
                            $add_data ['object_turn_type'] = 2;
                            $add_data ['object_id'] = $role_merchant;
                            $add_data ['current_agent_id'] = $agent_user_info['agent_id'];
                            $add_data ['after_agent_id'] = $agent_id;
                            $add_data ['current_merchant_id'] = $role_merchant;
                            $add_data ['after_merchant_id'] = $role_merchant;
                            $add_data ['op_user_id'] = $user_id;
                            $add_data ['create_time'] = $now;
                            $this->AgentTurnDataRecordModel->add_data($add_data);
                            //投放点绑定代理商修改
                            $this->AgentMerchantModel->update($save_role_merchant, array('id'=>$role_merchant));
                            //移除子账号权限
                            $agent_user_list = $this->AgentUserModel->get_field_list("group_id in (1,4) and role_merchant_id like '%".$role_merchant."%'", $field = 'id,role_merchant_id');
                            foreach ($agent_user_list as $agent_user){
                                $arr = explode(',', $agent_user['role_merchant_id']);
                                $mer_list = array();
                                foreach ($arr as $v){
                                    if ($v!=$role_merchant){
                                        $mer_list [] = $v;
                                    }
                                }
                                $this->AgentUserModel->update(array('role_merchant_id'=>join(',', $mer_list)), array('id'=>$agent_user['id']));
                            }
                            
                            //迁移设备
                            $save_machine = array();
                            $save_machine ['agent_id'] = $agent_id;
                            $where = array();
                            $where ['merchant_id'] = $role_merchant;
                            $field = 'id,agent_id';
                            $machine_list = $this->MachineModel->get_field_list($where, $field);
                            if ($machine_list){
                                $add_bash = array();
                                foreach ($machine_list as $machine){
                                    $add_data = array();
                                    $add_data ['turn_record_id'] = $turn_record_id;
                                    $add_data ['object_turn_type'] = 1;
                                    $add_data ['object_id'] = $machine['id'];
                                    $add_data ['current_agent_id'] = $machine['agent_id'];
                                    $add_data ['after_agent_id'] = $agent_id;
                                    $add_data ['current_merchant_id'] = $role_merchant;
                                    $add_data ['after_merchant_id'] = $role_merchant;
                                    $add_data ['current_agent_user_merchant_id'] = $merchant;
                                    $add_data ['after_agent_user_merchant_id'] = $merchant;
                                    $add_data ['op_user_id'] = $user_id;
                                    $add_data ['create_time'] = $now;
                                    $add_bash [] = $add_data;
                                }
                                $this->AgentTurnDataRecordModel->add_batch($add_bash);
                                
                                $this->MachineModel->update($save_machine, $where);
                            }
                        }
                    }else {
                        $save_merchant = array();
                        $save_merchant ['agent_id'] = $agent_id;
                        $save_merchant ['update_time'] = $now;
                        if (!$is_redirect_trun_agent){//重新绑定商户
                            $to_agent_user_info = $this->AgentUserModel->get_field_list(array('id'=>$merchant_id), 'agent_id,role_merchant_id')[0];
                            if ($to_agent_user_info['role_merchant_id']){
                                $save_merchant ['role_merchant_id'] = $to_agent_user_info['role_merchant_id'].','.$agent_user_info['role_merchant_id'];
                            }else {
                                $save_merchant ['role_merchant_id'] = $agent_user_info['role_merchant_id'];
                            }
                            $this->AgentUserModel->update($save_merchant, array('id'=>$merchant_id));  //目标商户
                            
                            $save_merchant ['role_merchant_id'] = '';
                            $this->AgentUserModel->update($save_merchant, array('id'=>$merchant));  //来源商户
                        }
                    }
                }
            }
        }
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->ajax_return(array('code'=>400, 'msg'=>'转移失败.'));
        }
        else
        {
            $this->add_sys_log('投放点迁移', $this->input->post());
            $this->ajax_return(array('code'=>200, 'msg'=>'转移成功.'));
        }
    }
    
    /**
     * [turn_record 转移记录]
     * @DateTime 2019-09-19
     * @Author   black.zhang
     */
    public function turn_record()
    {
        $id = $this->input->get('id');
        $where = array();
        $where ['object_turn_type'] = 3;
        $where ['object_id'] = $id;
        $total_rows = $this->AgentTurnDataRecordModel->get_count($where);
        $list = $this->AgentTurnDataRecordModel->get_list($where, $total_rows, 0);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
        }
        $this->ajax_return(array('code'=>200, 'list'=>$list));
    }
}
