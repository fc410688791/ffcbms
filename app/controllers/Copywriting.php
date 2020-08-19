<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Copywriting extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PasswordCopywritingModel');
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
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
  
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->PasswordCopywritingModel->get_count_all();
        $list = $this->PasswordCopywritingModel->get_list($where = array(), $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('del', '你确认删除该记录吗 ?', 'danger');
        $this->template->admin_render('password_copywriting/index', $this->_data);
    }

    /**
     * [add 添加功能]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $button_text = $this->input->get('button_text');  //密码按钮对应文案

        $info = $this->PasswordCopywritingModel->get_info($where = array('button_text'=>$button_text)); //查询是否存在相同文案
        if ($info){
            $this->ajax_return(array('code'=>400, 'msg'=>'相同文案已创建。'));
        }
        $password_copywriting_data = array();
        $password_copywriting_data ['button_text'] = $button_text;
        $password_copywriting_data ['create_time'] = time();
        $password_copywriting_data ['user_id'] = $this->get_user_id();
        
        $re = $this->PasswordCopywritingModel->add_data($password_copywriting_data);
        
        if ($re) {
            $this->add_sys_log('password_copywriting', $password_copywriting_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建密码文案成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'创建密码文案失败.'));
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
        $info = $this->PasswordCopywritingModel->get_info($wh_data);
        
        if (empty($info)) {
            $this->jump_error_page('密码本不存在.');
        }
        
        $res = $this->PasswordCopywritingModel->del_data($wh_data);
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
