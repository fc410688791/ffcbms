<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Secretary extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentUserModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-05-09
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $agent_id = $this->input->get('agent_id');
        $this->_data['agent_id'] = $agent_id;
        $where = array();
        $where ['a_u.group_id'] = 4;
        $where ['a_u.status<>'] = 0;
        if ($agent_id){
            $where ['a_u.agent_id'] = $agent_id;
        }
        $total_rows = $this->AgentUserModel->get_count($where);
        $list = $this->AgentUserModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
            switch ($info ['status']){
                case 0:
                    $info ['bind_status_name'] = '已删除';
                    break;
                case 1:
                    $info ['bind_status_name'] = '已绑定';
                    break;
                case 2:
                    $info ['bind_status_name'] = '已解绑';
                    break;
                default:
                    break;
            }
        }
        $this->_data['list'] = $list;
        $this->template->admin_render('secretary/index', $this->_data);
    }
    
    /**
     * [update 编辑功能]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     */
    public function update()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if (empty($id)) {
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数！'));
        }
        
        $where = array(
            'id' => $id,
        );
        $info = $this->AgentUserModel->get_info($where);
        $save = array();
        if ($status!=$info['status']){
            $save['status'] = $status;
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
}
