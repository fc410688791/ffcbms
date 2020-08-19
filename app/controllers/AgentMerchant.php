<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentMerchant extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("AgentMerchantModel");
        $this->load->model("MachineModel");
        $this->load->model("OrderModel");
        $this->load->model("ProductModel");
        $this->load->model("AgentTurnRecordModel");
        $this->load->model("AgentTurnDataRecordModel");
        $this->load->model("AgentUserModel");
    }

    /**
     * [merchant 投放地址]
     *
     * @DateTime 2019-03-04
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $key = $this->input->get('key');
        $sort = $this->input->get('sort')??1;
        $reservation = $this->input->get('reservation')??date("Y-m-d").' - '.date("Y-m-d");
        $o = $this->input->get('o');
        $agent_user_id = $this->input->get('agent_user_id');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $date = explode(' - ', $reservation);
        list($start_time , $end_time) = $date;
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time)+86399;
        $day = ($end_time-$start_time+1)/86400;
        $this->_data['day'] = $day;
        $this->_data['reservation'] = $reservation;
        $this->_data['o'] = $o;
        
        //设备类型列表
        $this->load->model('TextModel');
        $type_option = $this->TextModel->get_option(array('type'=>2));
        $this->_data['type_option'] = $type_option;
        
        //代理商列表
        $this->load->model('AgentModel');
        $agent_list = $this->AgentModel->get_user($where = array(), $field = 'id,user_name');
        $this->_data['agent_list'] = $agent_list;
        
        $where = "a_m.create_time < '$end_time'";
        $where = "a_m.status = 1";
        if ($key){
            $where .= " and (a_m.name like '%$key%' or a_m.agent_id like '%$key%' or a.card_name like '%$key%')";
            $this->_data['key'] = $key;
        }
        switch ($sort){
            case -1://全部
                break;
            case 1://有设备
                $where .= " and merchant_count>0";
                break;
            case 2://无设备
                $where .= " and merchant_count IS NULL";
                break;
            default:
                break;
        }
          
        //Machine Count Query
        $machine_where = array();
        $machine_where ['activity_time <='] = $end_time;
        $merchant_count_query = $this->db->select('merchant_id,count(merchant_id) as merchant_count')
                                ->from('ffc_machine')
                                ->where($machine_where)
                                ->group_by('merchant_id')
                                ->get_compiled_select();
        
        //Order Count Query
        $order_where = array();
        $order_where ['create_time >='] = $start_time;
        $order_where ['create_time <='] = $end_time;
        $order_where ['status'] = 1;  //支付成功
        $order_where ['complete_status !='] = 3;  //不包含退款
        $order_count_query = $this->db->select("merchant_id,count(id) as pay_count,sum(cash_fee) as cash_fee_statistics")
                            ->from('ffc_order')
                            ->where($order_where)
                            ->group_by('merchant_id')
                            ->get_compiled_select();
        
        switch ($o){
            case 'm_u':
                $order_by = 'merchant_count desc';
                break;
            case 'm_d':
                $order_by = 'merchant_count asc';
                break;
            case 'o_u':
                $order_by = 'pay_count desc';
                break;
            case 'o_d':
                $order_by = 'pay_count asc';
                break;
            case 'c_u':
                $order_by = 'ifnull(pay_count,0)/ifnull(merchant_count,0) desc';
                break;
            case 'c_d':
                $order_by = 'ifnull(pay_count,0)/ifnull(merchant_count,0) asc';
                break;
            case 'f_u':
                $order_by = 'cash_fee_statistics desc';
                break;
            case 'f_d':
                $order_by = 'cash_fee_statistics asc';
                break;
            default:
                $order_by = 'a_m.id DESC';
                break;
        }
        $where_in = array();
        if ($agent_user_id){
            $this->load->model('AgentUserModel');
            $info = $this->AgentUserModel->get_info($where = array('id'=>$agent_user_id));
            if ($info['role_merchant_id']){
                $merchant_list = explode(',', $info['role_merchant_id']);
                $info['merchant_count'] = count($merchant_list);
                $where_in = array('field'=>'a_m.id','list'=>$merchant_list);
            }
        }
        
        $this->db->from("agent_merchant a_m")
             ->join('ffc_agent a', "a_m.agent_id=a.id", "left")
             ->join('ffc_agent_user a_u', "a_m.create_id=a_u.id", "left")
             ->join("($merchant_count_query) as m_c_q", "a_m.id=m_c_q.merchant_id", "left")
             ->join("($order_count_query) as o_c_q", "a_m.id=o_c_q.merchant_id", "left");
        if ($where_in){
            $this->db->where_in($where_in['field'], $where_in['list']);
        }else{
            $this->db->where($where);
        }
        $total_rows =  $this->db->select("count(a_m.id) as total_rows")
                            ->get()
                            ->row()
                            ->total_rows;
        
        $this->db->from("agent_merchant a_m")
                 ->join('ffc_agent a', "a_m.agent_id=a.id", "left")
                 ->join('ffc_agent_user a_u', "a_m.create_id=a_u.id", "left")
                 ->join("($merchant_count_query) as m_c_q", "a_m.id=m_c_q.merchant_id", "left")
                 ->join("($order_count_query) as o_c_q", "a_m.id=o_c_q.merchant_id", "left");
        if ($where_in){
            $this->db->where_in($where_in['field'], $where_in['list']);
        }else{
            $this->db->where($where);
        }
        $list = $this->db->select("a_m.id,a_m.name,a_m.create_time,a_m.status,a_m.agent_id,a.card_name,a_u.name as a_u_name,ifnull(merchant_count,0) as merchant_count,ifnull(pay_count,0) as pay_count,ifnull(cash_fee_statistics,0) as cash_fee_statistics")
                         ->limit($limit, $offset)
                         ->order_by($order_by)
                         ->get()
                         ->result_array();
        
        foreach ($list as &$info){
            if ($info['status']==1){
                $info['status_name'] = '可用';
            }else {
                $info['status_name'] = '不可用';
            }
            if (!$info['card_name']){
                $info['card_name'] = '-';
            }
        }
        $this->_data['sort'] = $sort;
        $this->_data['list'] = $list;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('del', '你确认删除该记录吗 ?', 'danger');
        $this->template->admin_render('agent_merchant/index', $this->_data);
    }
    
    /**
     * [setProduct 设置设备绑定商品]
     *
     * @DateTime 2019-05-06
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function setProduct()
    {
        $merchant_id           = $this->input->get('merchant_id');
        $product_type          = $this->input->post('product_type');
        $product_id            = $this->input->post('product_id');
        $default_product_id    = $this->input->post('default_product_id');
        if (!$merchant_id||!$product_id){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数！'));
        }
        $where = array();
        $where ['merchant_id'] = $merchant_id;
        if ($product_type==1){
            $where ['type'] = 1;
        }else {
            $where ['type >'] = 1;
        }
        $save = array('product_id'=>join(',',$product_id), 'default_product_id'=>$default_product_id, 'update_time'=>time());
        $re = $this->MachineModel->update($save, $where);
        if ($re){
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($merchant_id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
        }
    }
    
    /**
     * [monthlyBill 投放点月账单]
     *
     * @DateTime 2019-05-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function monthlyBill()
    {
        $merchant_id = $this->input->get('merchant_id');
        $start = 1546272000;//2019-01-01 00:00:00
        $end = strtotime(date('Y-m-d'))+86400-1;//today end
        $where = "a_m.id = '$merchant_id'";
        $merchant_info = $this->db->select("a_m.id,a_m.name,a_m.create_time,a.card_name,a_u.name as a_u_name")
        ->from("agent_merchant a_m")
        ->where($where)
        ->join('ffc_agent a', "a_m.agent_id=a.id", "left")
        ->join('ffc_agent_user a_u', "a_m.create_id=a_u.id", "left")
        ->get()
        ->row_array();
        $info ['create_time'] = date('Y-m-d', $merchant_info ['create_time']);
        $this->_data['merchant_info'] = $merchant_info;
        
        $list = array();
        $machine_where = array();
        $order_where = array();
        $order_where ['status'] = 1;  //支付成功
        $order_where ['complete_status !='] = 3;  //不包含退款
        for ($end;$end>$start;){
            $date = date('Y-m', $end);
            $row['start_time'] = $start_time = strtotime($date);
            $row['end_time'] =  $end_time = $end;
            $row['reservation'] = date('Y-m-1', $end).' - '.date('Y-m-d', $end);
            $order_where ['merchant_id'] = $machine_where ['merchant_id'] = $merchant_id;
            //设备数量
            $machine_where ['activity_time <='] = $end_time;
            $row['merchant_count'] = $this->MachineModel->get_count($machine_where);
            //订单数量(已支付订单，不包含已退款)
            $order_where ['create_time >='] = $start_time;
            $order_where ['create_time <='] = $end_time;
            $order_data = $this->OrderModel->orderStatistics($order_where);
            $row['pay_count'] = $order_data['pay_count'];
            $row['cash_fee_statistics'] = $order_data['cash_fee_statistics'];
            $list [$date] = $row;
            $end = strtotime($date)-1;
        }
        $this->_data['list'] = $list;
        $this->template->admin_render('agent_merchant/monthly_bill', $this->_data);
    }
    
    /**
     * [dailyBill 投放点日账单]
     *
     * @DateTime 2019-05-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function dailyBill()
    {
        $merchant_id = $this->input->get('merchant_id');
        $start_time = $this->input->get('start_time');
        $end_time = $this->input->get('end_time');
        $where = "a_m.id = '$merchant_id'";
        $merchant_info = $this->db->select("a_m.id,a_m.name,a_m.create_time,a.card_name,a_u.name as a_u_name")
        ->from("agent_merchant a_m")
        ->where($where)
        ->join('ffc_agent a', "a_m.agent_id=a.id", "left")
        ->join('ffc_agent_user a_u', "a_m.create_id=a_u.id", "left")
        ->get()
        ->row_array();
        $info ['create_time'] = date('Y-m-d', $merchant_info ['create_time']);
        $this->_data['merchant_info'] = $merchant_info;
        
        $list = array();
        $machine_where = array();
        $machine_where ['merchant_id'] = $merchant_id;
        $order_where = array();
        $order_where ['status'] = 1;  //支付成功
        $order_where ['complete_status !='] = 3;  //不包含退款
        $order_where ['merchant_id'] = $merchant_id;
        for ($end_time;$end_time>$start_time;){
            $date = date('Y-m-d', $end_time);
            $row['reservation'] = $date.' - '.$date;
            $order_where ['merchant_id'] = $machine_where ['merchant_id'] = $merchant_id;
            //设备数量
            $machine_where ['activity_time <='] = $end_time;
            $row['merchant_count'] = $this->MachineModel->get_count($machine_where);
            //订单数量(已支付订单，不包含已退款)
            $order_where ['create_time >='] = $end_time+1-86400;
            $order_where ['create_time <='] = $end_time;
            $order_data = $this->OrderModel->orderStatistics($order_where);
            $row['pay_count'] = $order_data['pay_count'];
            $row['cash_fee_statistics'] = $order_data['cash_fee_statistics'];
            $list [$date] = $row;
            $end_time = $end_time-86400;
        }
        $this->_data['list'] = $list;
        $this->template->admin_render('agent_merchant/daily_bill', $this->_data);
    }
    
    /**
     * [del 删除功能]
     *
     * @DateTime 2019-06-11
     * @Author   black.zhang
     * @return   [type]
     */
    public function del()
    {
        $id = $this->input->get('id');
        
        if (empty($id)) {
            $this->jump_error_page('缺少参数.');
        }
        $dev_count = $this->MachineModel->get_count(array('merchant_id'=>$id));
        if ($dev_count>0){
            $this->jump_error_page('投放点存在设备.');
        }
        $wh_data = array(
            'id' => $id,
            'status' => 1,
        );
        $info = $this->AgentMerchantModel->get_info($wh_data);
        if (empty($info)) {
            $this->jump_error_page('记录不存在.');
        }
        
        //$res = $this->AgentMerchantModel->del_data($wh_data);
        $save = array('status'=>0,'update_time'=>time());
        $res = $this->AgentMerchantModel->update($save, $wh_data);
        if (! $res) {
            $this->jump_error_page('服务器异常.');
        } else {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $info);
            // 操作成功跳转
            $this->jump_success_page('删除成功.');
        }
    }
    
    /**
     * [get_merchant_list 获取投放点列表]
     * @DateTime 2019-09-19
     * @Author   black.zhang
     */
    public function get_merchant_list()
    {
        $agent_id = $this->input->get('agent_id');
        $where = array();
        $where ['status'] = 1;
        if ($agent_id){
            $where ['agent_id'] = $agent_id;
        }
        $list = $this->AgentMerchantModel->get_field_list($where, 'id,name');
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
        $merchant_list = $this->input->post('merchant_list');
        $is_redirect_trun_agent = $this->input->post('is_redirect_trun_agent');
        $agent_id = $this->input->post('agent_id');
        $merchant_id = $this->input->post('merchant_id');
        if (!$merchant_list||!$agent_id||(!$is_redirect_trun_agent&&!$merchant_id)){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数！'));
        }
        // 事务开始
        $this->db->trans_start();
        $now = time();
        $user_id = $this->top_get_session('user_id');
        foreach ($merchant_list as $merchant){
            $merchant_info = $this->AgentMerchantModel->get_field_list(array('id'=>$merchant), 'agent_id')[0]; 
            if ($merchant_info['agent_id']!=$agent_id||($merchant_info['agent_id']==$agent_id&&!$is_redirect_trun_agent&&$merchant!=$merchant_id)){
                $add = array();
                $add['turn_type'] = 2;  //投放点
                $add['is_redirect_trun_agent'] = $is_redirect_trun_agent;
                $add['current_agent_id'] = $merchant_info['agent_id'];
                $add['after_agent_id'] = $agent_id;
                $add['create_time'] = $now;
                $this->AgentTurnRecordModel->add_data($add);
                $turn_record_id = $this->db->insert_id();
                
                $add_data = array();
                $add_data ['turn_record_id'] = $turn_record_id;
                $add_data ['object_turn_type'] = 2;
                $add_data ['object_id'] = $merchant;
                $add_data ['current_agent_id'] = $merchant_info['agent_id'];
                $add_data ['after_agent_id'] = $agent_id;
                $add_data ['current_merchant_id'] = $merchant;
                $add_data ['after_merchant_id'] = $merchant;
                $add_data ['op_user_id'] = $user_id;
                $add_data ['create_time'] = $now;
                $this->AgentTurnDataRecordModel->add_data($add_data);
                
                if ($merchant_info['agent_id']!=$agent_id){//迁移到新的代理商
                    $save_merchant = array();
                    $save_merchant ['agent_id'] = $agent_id;
                    $save_merchant ['update_time'] = $now;
                    $this->AgentMerchantModel->update($save_merchant, array('id'=>$merchant));
                    //移除子账号权限
                    $agent_user_list = $this->AgentUserModel->get_field_list("role_merchant_id like '%".$merchant."%'", $field = 'id,role_merchant_id');
                    foreach ($agent_user_list as $agent_user){
                        $arr = explode(',', $agent_user['role_merchant_id']);
                        $role_merchant = array();
                        foreach ($arr as $v){
                            if ($v!=$merchant){
                                $role_merchant [] = $v;
                            }
                        }
                        $role_merchant_id = join(',', $role_merchant);
                        $this->AgentUserModel->update(array('role_merchant_id'=>$role_merchant_id), array('id'=>$agent_user['id']));
                    }
                }
                
                //迁移设备
                $save_machine = array();
                if ($merchant_info['agent_id']!=$agent_id){
                    $save_machine ['agent_id'] = $agent_id;
                }
                if (!$is_redirect_trun_agent&&$merchant!=$merchant_id){
                    $save_machine ['merchant_id'] = $merchant_id;
                }
                if ($save_machine){
                    $where = array();
                    $where ['merchant_id'] = $merchant;
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
                            $add_data ['current_merchant_id'] = $merchant;
                            $add_data ['after_merchant_id'] = $merchant_id;
                            $add_data ['op_user_id'] = $user_id;
                            $add_data ['create_time'] = $now;
                            $add_bash [] = $add_data;
                        }
                        $this->AgentTurnDataRecordModel->add_batch($add_bash);
                        
                        $this->MachineModel->update($save_machine, $where);
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
        $where ['object_turn_type'] = 2;
        $where ['object_id'] = $id;
        $total_rows = $this->AgentTurnDataRecordModel->get_count($where);
        $list = $this->AgentTurnDataRecordModel->get_list($where, $total_rows, 0);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
        }
        $this->ajax_return(array('code'=>200, 'list'=>$list));
    }
}
