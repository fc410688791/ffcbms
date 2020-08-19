<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgingSetting extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('TextModel');
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
        $where = array();
        $where ['type'] = 6;
        
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->TextModel->get_count($where);
        $list = $this->TextModel->get_list($where, $total_rows, 0);
        foreach ($list as &$info){
            $info['text_ext'] = json_decode($info['text_ext'], true);
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
        }
        $this->_data['list'] = $list;
        
        $where = array();
        $where ['type'] = 7;
        $percent_of_pass_list = $this->TextModel->get_list($where, 1, 0);
        $this->_data['percent_of_pass'] = $percent_of_pass_list?$percent_of_pass_list[0]['text_ext']*100:'';
        $this->template->admin_render('aging_setting/index', $this->_data);
    }
    
    /**
     * [update 修改]
     *
     * @DateTime 2019-05-09
     * @Author   black.zhang
     */
    public function update()
    {
        $id = $this->input->get('id');
        $h = $this->input->post('h');
        $i = $this->input->post('i');
        
        $where = array();
        $where ['id'] = $id;
        $text_ext = [$h,$i];
        $save = array();
        $save['text_ext'] = json_encode($text_ext);
        if ($save){
            $save ['update_time'] = time();
            $re = $this->TextModel->update($save, $where);
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
    
    /**
     * [pop_update 通过率修改]
     *
     * @DateTime 2019-11-01
     * @Author   black.zhang
     */
    public function pop_update()
    {
        $percent_of_pass = $this->input->post('percent_of_pass');
        if ($percent_of_pass>100){
            $percent_of_pass = 100;
        }elseif ($percent_of_pass<0){
            $percent_of_pass = 0;
        }
        $where = array();
        $where ['type'] = 7;
        $percent_of_pass_list = $this->TextModel->get_list($where, 1, 0);
        if ($percent_of_pass_list){
            $save = array();
            $save['text_ext'] = round($percent_of_pass/100, 2);
            $save ['update_time'] = time();
            $id = $percent_of_pass_list[0]['id'];
            $re = $this->TextModel->update($save, array('id'=>$id));
            $this->add_sys_log($id, $save);
        }else {
            $data = array();
            $data ['type'] = 7;
            $data ['text_id'] = 1;
            $data ['text'] = '老化测试通过率';
            $data ['status'] = 1;
            $data ['text_ext'] = round($percent_of_pass/100, 2);
            $data ['create_time'] = time();            
            $re = $this->TextModel->add_data($data);
            $this->add_sys_log('text', $data);
        }
        if ($re){
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
        }
    }
}
