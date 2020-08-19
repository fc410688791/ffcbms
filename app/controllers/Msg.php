<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Msg extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MsgModel');
        $this->load->model('AgentReadMsgModel');
        $this->load->model('AgentModel');
    }
    
    /**
     * [index  列表]
     *
     * @DateTime 2019-06-24
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $where = array();
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MsgModel->get_count($where);
        $list = $this->MsgModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
            if ($info['type']==1&&$info['user_type']==2){
                $user_list = $this->AgentReadMsgModel->get_user(array('msg_id'=>$info['id']));
                $user = array();
                foreach ($user_list as $v){
                    $user [] = $v['user_name'].'<'.$v['agent_id'].'>';
                }
                $info['user'] = join(':', $user);
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('msg/index', $this->_data);
    }
    
    /**
     * [send 发送消息]
     *
     * @DateTime 2019-06-24
     * @Author   black.zhang
     */
    public function send()
    {
        $user_type = $this->input->post('user_type');
        $user = $this->input->post('user');
        $title = $this->input->post('title');
        $content = $this->input->post('content');
        if (($user_type==2&&!$user)||!$title||!$content){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        if ($user_type!=1&&$user_type!=2){
            $this->ajax_return(array('code'=>400, 'msg'=>'参数错误.'));
        }
        $now = time();
        
        $msg_data = array();
        $msg_data ['type'] = 1;  //代理商
        $msg_data ['user_type'] = $user_type;  //1:所有用户；2:指定用户
        $msg_data ['title'] = $title;
        $msg_data ['content'] = $content;
        $msg_data ['create_time'] = $now;
        // 事务开始
        $this->db->trans_start();
        $this->db->insert('msg', $msg_data);
        $re = $this->db->insert_id();
        if ($re) {
            $agent_read_msg_data = array();
            $user_list = array();
            if ($user_type==1){
                $list = $this->AgentModel->get_user();
                foreach ($list as $v){
                    $user_list [] = $v['id'];
                }
            }elseif ($user_type==2){
                $user_list = explode(';', $user);
            }
            foreach($user_list as $v){
                $info = array();
                $info ['agent_id'] = $v;
                $info ['msg_id'] = $re;
                $info ['status'] = 0;
                $info ['create_time'] = $now;
                $info ['update_time'] = 0;
                $agent_read_msg_data [] = $info;
            }
            $this->db->insert_batch('agent_read_msg', $agent_read_msg_data);
        }
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'发送消息失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log('msg', $msg_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'发送消息成功.'));
        }
    }
}
