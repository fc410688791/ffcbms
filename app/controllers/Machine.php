<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Machine extends Admin_Controller
{
    private $status_list = array('1'=>'正常','2'=>'待绑定','3'=>'待激活','4'=>'故障');
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineModel');
        $this->load->model('OrderModel');
        $this->load->model("AgentTurnRecordModel");
        $this->load->model("AgentTurnDataRecordModel");
    }
    

    /**
     * [index 设备列表]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $key = $this->input->get('key');
        $merchant_id = $this->input->get('merchant_id');
        $merchant_name = $this->input->get('merchant_name');
        $machine_type = $this->input->get('machine_type');
        $agent_product_type = $this->input->get('agent_product_type');
        $status = $this->input->get('status');
        $first_id = $this->input->get('first_id');
        $second_id = $this->input->get('second_id');
        $third_id = $this->input->get('third_id');
        $bind_triad_mark = $this->input->get('bind_triad_mark');
        $fourth_id = $this->input->get('fourth_id');
        $triad_id = $this->input->get('triad_id');
        $batch_id = $this->input->get('batch_id');
        $agent_user_id = $this->input->get('agent_user_id');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $where_in = array();
        if ($agent_user_id){
            $this->load->model('AgentUserModel');
            $info = $this->AgentUserModel->get_info($where = array('id'=>$agent_user_id));
            if ($info['role_merchant_id']){
                $merchant_list = explode(',', $info['role_merchant_id']);
                $info['merchant_count'] = count($merchant_list);
                $where_in = array('field'=>'merchant_id','list'=>$merchant_list);
            }
        }
        
        //设备类型列表
        $this->load->model('MachineTypeModel');
        $where = array();
        $total_rows = $this->MachineTypeModel->get_all_data_count($where);
        $machine_type_list = $this->MachineTypeModel->get_list($where, $total_rows, 0);
        $machine_type_option = array();
        foreach ($machine_type_list as $machine_type_info){
            $machine_type_option[$machine_type_info['id']] = $machine_type_info['type_name'];
        }
        $this->_data['machine_type_option'] = $machine_type_option;
        
        //采购商品类型列表
        $this->load->model('AgentProductTypeModel');
        $where = array();
        $total_rows = $this->AgentProductTypeModel->get_all_data_count($where);
        $agent_product_type_list = $this->AgentProductTypeModel->get_list($where, $total_rows, 0);
        $agent_product_type_option = array();
        foreach ($agent_product_type_list as $agent_product_type_info){
            $agent_product_type_option[$agent_product_type_info['id']] = $agent_product_type_info['type_name'];
        }
        $this->_data['agent_product_type_option'] = $agent_product_type_option;
        
        //代理商列表
        $this->load->model('AgentModel');
        $agent_list = $this->AgentModel->get_user($where = array(), $field = 'id,user_name');
        $this->_data['agent_list'] = $agent_list;
               
        //状态列表
        $this->_data['status_list'] = $this->status_list;
        
        //密码本列表
        $this->load->model('PasswordBookModel');
        $pb_list = $this->PasswordBookModel->get_list($where = array(), 100, 0);
        $this->_data['pb_list'] = $pb_list;
        
        //文案列表
        $this->load->model('PasswordCopywritingModel');
        $pc_list = $this->PasswordCopywritingModel->get_list($where = array(), 100, 0);
        $this->_data['pc_list'] = $pc_list;
                
        //省列表
        $this->load->model('LocationModel');
        $first_list = $this->LocationModel->get_list(array('pid'=>0));
        $this->_data['first_list'] = $first_list;

        $where = array();
        
        if ($key){
            $where = "machine_id = '$key' or a_m.name = '$key'";
            $this->_data['key'] = $key;
        }
        
        if ($merchant_id){
            $where ['merchant_id'] = $merchant_id;
            $this->_data['key'] = $merchant_name;
        }
        
        if ($machine_type){
            $where ['m.type'] = $machine_type;
            $this->_data['machine_type'] = $machine_type;
        }
        
        if ($agent_product_type){
            $where ['a_p.type'] = $agent_product_type;
            $this->_data['agent_product_type'] = $agent_product_type;
        }
        
        if ($status){
            $where ['m.status'] = $status;
            $this->_data['status'] = $status;
        }
        
        if ($first_id){
            $where ['p.province_id'] = $first_id;
            $this->_data['first_id'] = $first_id;
            //市列表
            $second_list = $this->LocationModel->get_list(array('pid'=>$first_id));
            $this->_data['second_list'] = $second_list;
        }
        
        if ($second_id){
            $where ['p.city_id'] = $second_id;
            $this->_data['second_id'] = $second_id;
            //区列表
            $third_list = $this->LocationModel->get_list(array('pid'=>$second_id));
            $this->_data['third_list'] = $third_list;
        }
        
        if ($third_id){
            $where ['p.street_id'] = $third_id;
            $this->_data['third_id'] = $third_id;
            //街道列表
            $fourth_list = $this->LocationModel->get_list(array('pid'=>$third_id));
            $this->_data['fourth_list'] = $fourth_list;
        }
        
        if ($fourth_id){
            $where ['p.village_id'] = $fourth_id;
            $this->_data['fourth_id'] = $fourth_id;
        }
        
        if ($triad_id){
            $where ['m.triad_id'] = $triad_id;
            $this->_data['triad_id'] = $triad_id;
        }
        
        if ($bind_triad_mark){
            $where ['m.bind_triad_mark'] = $bind_triad_mark;
            $this->_data['bind_triad_mark'] = $bind_triad_mark;
        }
        
        if ($batch_id){
            $where ['m.batch_id'] = $batch_id;
            $this->_data['batch_id'] = $batch_id;
        }
        
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineModel->get_count($where, $where_in);
        $list = $this->MachineModel->get_list($where, $limit, $offset, $where_in);
        if ($list){
            $order_where = array();
            if (isset($start_time)){
                $order_where ['create_time >='] = strtotime($start_time);
            }else {
                $order_where ['create_time >='] = mktime(0,0,0,date('m'),date('d'),date('Y'));
            }
            if (isset($end_time)){
                $order_where ['create_time <='] = strtotime($end_time);
            }else {
                $order_where ['create_time <='] = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            }
            $order_where ['status'] = 1;  //支付成功
            $order_where ['complete_status !='] = 3;  //不包含退款
            foreach ($list as &$info){
                $order_where ['machine_id'] = $info['machine_id'];
                $order_data = $this->OrderModel->orderStatistics($order_where);
                $info['pay_count'] = $order_data['pay_count'];
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        
        $this->template->admin_render('machine/index', $this->_data);
    }
    
    public function get_info()
    {
        $machine_id = $this->input->get('machine_id');
        if (!$machine_id){
            exit();
        }
        $where = array();
        $where ['machine_id'] = $machine_id;
        $info = $this->MachineModel->get_info($where);
        
        if ($info['position_id']){
            $this->load->model('LocationModel');
            $where = array();
            $where ['pid'] = $info['province_id'];
            $info['city_list'] = $this->LocationModel->get_list($where);
            $where = array();
            $where ['pid'] = $info['city_id'];
            $info['street_list'] = $this->LocationModel->get_list($where);
            $where = array();
            $where ['pid'] = $info['street_id'];
            $info['village_list'] = $this->LocationModel->get_list($where);
        }   
        $info['product_id'] = explode(',', $info['product_id']);
        $this->ajax_return(array('code'=>200, 'data'=>$info));
    }
    
    public function update()
    {
        $machine_id = $this->input->get('machine_id');
        $status = $this->input->post('status');
        $province_id = $this->input->post('province_id');
        $city_id = $this->input->post('city_id');
        $street_id = $this->input->post('street_id');
        $position = $this->input->post('position');
        $mac = $this->input->post('mac');
        $book_id = $this->input->post('book_id');
        $copywriting_id = $this->input->post('copywriting_id');
        $module_plate_num = (int)$this->input->post('module_plate_num');
        $module_plate_code_num = (int)$this->input->post('module_plate_code_num');
        $product_id = $this->input->post('product_id');
        $default_product_id = $this->input->post('default_product_id');
        
        
        $product_id = join(',', $product_id);
        $where = array();
        $where ['machine_id'] = $machine_id;
        $info = $this->MachineModel->get_info($where);
        $save = array();
        if ($status!=$info['status']){
            $save ['status'] = $status;
        }
        
        if ($mac!=$info['mac']){
            $save ['mac'] = $mac;
        }
        
        if ($book_id!=$info['book_id']){
            $save ['book_id'] = $book_id;
        }
        
        if ($copywriting_id!=$info['copywriting_id']){
            $save ['copywriting_id'] = $copywriting_id;
        }
        
        if ($module_plate_num!=$info['module_plate_num']){
            $save ['module_plate_num'] = $module_plate_num;
        }
        
        if ($module_plate_code_num!=$info['module_plate_code_num']){
            $save ['module_plate_code_num'] = $module_plate_code_num;
        }
        
        if ($product_id!=$info['product_id']){
            $save ['product_id'] = $product_id;
        }
        
        if ($default_product_id!=$info['default_product_id']){
            $save ['default_product_id'] = $default_product_id;
        }
        
        if ($save){
            $save ['update_time'] = time();
            $re = $this->MachineModel->update($save, $where);
            if ($re){
                // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                $this->add_sys_log($machine_id, $save);
                $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
            }else {
                $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
            }
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'没有任何修改！'));
        }
    }
    
    /**
     * [turn 转移]
     * @DateTime 2019-09-19
     * @Author   black.zhang
     */
    public function turn()
    {
        $machine_list = $this->input->post('machine_list');
        $agent_id = $this->input->post('agent_id');
        $merchant_id = $this->input->post('merchant_id');
        if (!$machine_list||!$agent_id||!$merchant_id){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数！'));
        }
        // 事务开始
        //$this->db->trans_start();
        $now = time();
        $user_id = $this->top_get_session('user_id');
        foreach ($machine_list as $machine){
            $where = array();
            $where ['id'] = $machine;
            $field = 'id,agent_id,merchant_id';
            $machine_info = $this->MachineModel->get_field_list($where, $field)[0];
            //迁移设备
            $save_machine = array();
            if ($machine_info['agent_id']!=$agent_id){
                $save_machine ['agent_id'] = $agent_id;
            }
            if ($machine_info['merchant_id']!=$merchant_id){
                $save_machine ['merchant_id'] = $merchant_id;
            }
            if ($save_machine){
                $add_data = array();
                $add_data ['turn_record_id'] = 0;
                $add_data ['object_turn_type'] = 1;
                $add_data ['object_id'] = $machine;
                $add_data ['current_agent_id'] = $machine_info['agent_id'];
                $add_data ['after_agent_id'] = $agent_id;
                $add_data ['current_merchant_id'] = $machine_info['merchant_id'];
                $add_data ['after_merchant_id'] = $merchant_id;
                $add_data ['op_user_id'] = $user_id;
                $add_data ['create_time'] = $now;
                $this->AgentTurnDataRecordModel->add_data($add_data);
                $this->MachineModel->update($save_machine, $where);
            }
        }
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'转移失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log('设备迁移', $this->input->post());
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
        $where ['object_turn_type'] = 1;
        $where ['object_id'] = $id;
        $total_rows = $this->AgentTurnDataRecordModel->get_count($where);
        $list = $this->AgentTurnDataRecordModel->get_list($where, $total_rows, 0);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
        }
        $this->ajax_return(array('code'=>200, 'list'=>$list));
    }
}
