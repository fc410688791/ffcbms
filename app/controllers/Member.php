<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Member extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $key = $this->input->get('key');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $where = array();
        if ($key) {
            //$where ['mobile'] = $mobile;
            $where = "uuid like '%$key%' or mobile = '$key' or nickname = '$key'";
            $this->_data['key'] = $key;
        }
  
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MemberModel->get_count($where);
        $list = $this->MemberModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
            if ($info ['gender'] == 1){
                $info ['gender'] = '男';
            }elseif ($info ['gender'] == 2){
                $info ['gender'] = '女';
            }else {
                $info ['gender'] = '未知';
            }
            if ($info ['client_type'] == 1){
                $info ['client_type'] = '微信小程序';
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        
        $this->template->admin_render('member/index', $this->_data);
    }
}
