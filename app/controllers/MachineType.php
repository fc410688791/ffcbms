<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MachineType extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineTypeModel');
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
        
        $where = array();
        $where ['status <>'] = 3;
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineTypeModel->get_all_data_count($where);
        $list = $this->MachineTypeModel->get_list($where, $limit, $offset);
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('machine_type/index', $this->_data);
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
        $select = 'id,type_name,module_num,module_plate_num,module_plate_code_num,status';
        $info = $this->MachineTypeModel->findOne($where, $select);
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
        $module_num = (int)$this->input->post('module_num');
        $module_plate_num = (int)$this->input->post('module_plate_num');
        $module_plate_code_num = (int)$this->input->post('module_plate_code_num');
        $status = (int)$this->input->post('status');
        if (!$type_name||!$module_num||!$module_plate_num||!$module_plate_code_num||!$status){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        
        $add_data = array();
        $add_data ['type_name'] = $type_name;
        $add_data ['module_num'] = $module_num;
        $add_data ['module_plate_num'] = $module_plate_num;
        $add_data ['module_plate_code_num'] = $module_plate_code_num;
        $add_data ['status'] = $status;
        $add_data ['create_time'] = $now;
        $add_data ['update_time'] = 0;
        $re = $this->MachineTypeModel->add_data($add_data);
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
        $module_num = (int)$this->input->post('module_num');
        $module_plate_num = (int)$this->input->post('module_plate_num');
        $module_plate_code_num = (int)$this->input->post('module_plate_code_num');
        $status = (int)$this->input->post('status');
        if (!$id||!$type_name||!$module_num||!$module_plate_num||!$module_plate_code_num||!$status){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        
        $where = array('id'=>$id);
        $save = array();
        $save ['type_name'] = $type_name;
        $save ['module_num'] = $module_num;
        $save ['module_plate_num'] = $module_plate_num;
        $save ['module_plate_code_num'] = $module_plate_code_num;
        $save ['status'] = $status;
        $save ['update_time'] = time();
        $re = $this->MachineTypeModel->update($save, $where);
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
        $re = $this->MachineTypeModel->update($save, $where);
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'删除成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'删除失败.'));
        }
    }
}
