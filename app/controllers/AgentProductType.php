<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentProductType extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentProductTypeModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-01-16
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        //设备类型列表
        $this->load->model('MachineTypeModel');
        $where = array();
        $total_rows = $this->MachineTypeModel->get_all_data_count($where);
        $machine_type_list = $this->MachineTypeModel->get_list($where, $total_rows, $offset);
        $machine_type_option = array();
        foreach ($machine_type_list as $machine_type_info){
            $machine_type_option[$machine_type_info['id']] = $machine_type_info['type_name'];
        }
        $this->_data['machine_type_option'] = $machine_type_option;
        
        $where = array();
        $where ['status <>'] = 0;
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->AgentProductTypeModel->get_all_data_count($where);
        $list = $this->AgentProductTypeModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info['machine_type_name'] = $machine_type_option[$info['machine_type']];
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent_product_type/index', $this->_data);
    }
    
    /**
     * [info 详情]
     *
     * @DateTime 2019-01-16
     * @Author   black.zhang
     */
    public function info()
    {
        $id = $this->input->get('id');
        if (!$id){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        $where = array('id'=>$id);
        $select = 'id,type_name,machine_type,status';
        $info = $this->AgentProductTypeModel->findOne($where, $select);
        if ($info) {
            $this->ajax_return(array('code'=>200, 'data'=>$info));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'查询失败.'));
        }
    }
    
    /**
     * [add 添加]
     *
     * @DateTime 2019-01-16
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function add()
    {
        $now = time();
        $type_name = $this->input->post('type_name');
        $machine_type = (int)$this->input->post('machine_type');
        $status = (int)$this->input->post('status');
        if (!$type_name||!$machine_type||!$status){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        
        $add_data = array();
        $add_data ['type_name'] = $type_name;
        $add_data ['machine_type'] = $machine_type;
        $add_data ['status'] = $status;
        $add_data ['create_time'] = $now;
        $add_data ['update_time'] = 0;
        $re = $this->AgentProductTypeModel->add_data($add_data);
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log('text', $add_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'添加成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'添加失败.'));
        }
    }
    
    /**
     * [update 更新]
     *
     * @DateTime 2019-01-16
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function update()
    {
        $id = $this->input->post('id');
        $type_name = $this->input->post('type_name');
        $machine_type = (int)$this->input->post('machine_type');
        $status = (int)$this->input->post('status');
        if (!$id||!$type_name||!$machine_type||!$status){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        
        $where = array('id'=>$id);
        $save = array();
        $save ['type_name'] = $type_name;
        $save ['machine_type'] = $machine_type;
        $save ['status'] = $status;
        $save ['update_time'] = time();
        $re = $this->AgentProductTypeModel->update($save, $where);
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败.'));
        }
    }
    
    /**
     * [del 软删除]
     *
     * @DateTime 2019-01-16
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function del()
    {
        $now = time();
        $id = $this->input->get('id');
        if (!$id){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        
        $where=array('id'=>$id);
        $save = array();
        $save ['status'] = 3;
        $save ['update_time'] = $now;
        $re = $this->AgentProductTypeModel->update($save, $where);
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'删除成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'删除失败.'));
        }
    }
}
