<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PwdBook extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PasswordBookModel');
    }

    /**
     * [index 密码本列表]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        if(IS_AJAX)
        {
            $id = $this->input->get('id');
            $info = $this->PasswordBookModel->get_info($where = array('id'=>$id));
            $this->load->model('PasswordModel');
            $list = $this->PasswordModel->get_list($where = array('book_id'=>$id),$order_by = 'id', $limit = 10000);
            $data = array();
            $data ['otherData'] = array('passwordGroupNum'=>(int)$info['group_num'],'passwordNum'=>(int)$info['password_num']);
            /* $book = array();
            foreach ($list as $info){
                $group = $info['group_index'];
                $book [$group] [] = (int)$info['password'];
            }
            foreach ($book as $group){
                $data ['passwordBook'] [] = $group;
            } */
            $pwd = array();
            foreach ($list as $v){
                $pwd [] = (int)$v['password'];
            }
            $pwd = join(',', $pwd);
            $content = "PWBook[" . $info['group_num'] . "][" . $info['password_num'] . "] = {". $pwd ."}";
            $this->ajax_return(array('code'=>200, 'msg'=>$content));
        }
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
  
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->PasswordBookModel->get_count_all();
        $list = $this->PasswordBookModel->get_list($where = array(), $limit, $offset);      
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该记录吗 ?', 'danger');
        $this->template->admin_render('password_book/index', $this->_data);
    }

    /**
     * [add 添加功能]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $name = $this->input->get('name');  //密码本名称
        $group_num = $this->input->get('group_num');  //共有几组密码组
        $password_num = $this->input->get('password_num');  //每组密码个数      

        $password_book_data = array();
        $password_book_data ['name'] = $name;
        $password_book_data ['group_num'] = $group_num;
        $password_book_data ['password_num'] = $password_num;
        $password_book_data ['create_time'] = time();
        $password_book_data ['user_id'] = $this->get_user_id();
        // 事务开始
        $this->db->trans_start();
        $this->db->insert('password_book', $password_book_data);
        $re = $this->db->insert_id();
        if ($re) {
            $password_data = array();
            for ($i=0;$i<$group_num;$i++){
                for ($j=0;$j<$password_num;$j++){
                    $info = array();
                    $pwd = '';
                    for ($k=0;$k<5;$k++){
                        $pwd .= rand(1,5);
                    }
                    $info ['book_id'] = $re;
                    $info ['group_index'] = $i;
                    $info ['password_index'] = $j;
                    $info ['password'] = (int)$pwd;
                    $password_data [] = $info;
                }
            }
            $this->db->insert_batch('password', $password_data);
        }
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'创建密码本失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log('password_book', $password_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建密码本成功.'));
        }
    }
    
    /**
     * [del 删除功能]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function del()
    {
        $id = $this->input->get('id');
        
        if (empty($id)) {
            $this->jump_error_page('缺少参数.');
        }
        
        $wh_data = array(
            'id' => $id,
        );
        $info = $this->PasswordBookModel->get_info($wh_data);
        
        if (empty($info)) {
            $this->jump_error_page('密码本不存在.');
        }else {
            $this->load->model('MachineModel');
            $re = $this->MachineModel->get_info($where = array());
            if ($re){
                $this->jump_error_page('存在使用该密码本设备，无法删除.');
            }
        }
        
        $res = $this->PasswordBookModel->del_data($wh_data);
        if (! $res) {
            $this->jump_error_page('服务器异常.');
        } else {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $info);          
            // 操作成功跳转
            $this->jump_success_page('删除成功.');
        }
    }
}
