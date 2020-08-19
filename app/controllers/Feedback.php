<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Feedback extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('FeedbackModel');
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
        $this->load->model('FileModel');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
  
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->FeedbackModel->get_count_all();
        $list = $this->FeedbackModel->get_list($where = array(), $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
            if ($info['file_ids']){
                $where_in = explode(",", $info['file_ids']);
                $file_list = $this->FileModel->get_list($where_in);
                $info['file_list'] = $file_list;
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('feedback/index', $this->_data);
    }

}
