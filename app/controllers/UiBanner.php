<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UiBanner extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UiBannerModel');
        $this->load->model('FileModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-05-09
     * @Author   black.zhang
     */
    public function index()
    {
        $where = array();
        $where ['is_show'] = 1;
        $ui_banner_count = $this->UiBannerModel->get_count($where);
        $ui_banner_list = $this->UiBannerModel->get_list($where, $ui_banner_count, 0);
        $list = array('1'=>array(),'2'=>array(),'3'=>array(),'4'=>array());
        foreach ($ui_banner_list as &$info){
            $info['img_url'] = $this->FileModel->get_url(array('id'=>$info['file_ids']));
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
            $list [$info['banner_page_type']] [] = $info;
        }
        $this->_data['list'] = $list;
        $this->template->admin_render('ui_banner/index', $this->_data);
    }
    
    /**
     * [add 添加]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $banner_page_type = $this->input->post('banner_page_type');
        $banner_desc = $this->input->post('banner_desc');
        $banner_url = $this->input->post('banner_url');
        $img = $this->input->post('img');
        if (!$banner_page_type||!$banner_desc||!$img){
            $this->ajax_return(array('code'=>400, 'msg'=>'请将数据填充完整.'));
        }
        
        $where = array();
        $where ['is_show'] = 1;
        $where ['banner_page_type'] = $banner_page_type;
        $ui_banner_count = $this->UiBannerModel->get_count($where);
        if ($ui_banner_count>7){
            $this->ajax_return(array('code'=>400, 'msg'=>'banner最多存在8个.'));
        }
        $now = time();
        $data = array();
        $data ['banner_page_type'] = $banner_page_type;
        $data ['banner_desc'] = $banner_desc;
        $data ['banner_sort'] = $ui_banner_count+1;
        $data ['banner_url'] = $banner_url;
        $data ['file_ids'] = $img;
        $data ['create_time'] = $now;
        
        $re = $this->UiBannerModel->add_data($data);
        
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log('ui_banner', $data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'创建失败.'));
        }
    }
    
    /**
     * [update 修改]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function update()
    {
        $id = $this->input->get('id');
        if (!$id){
            $this->ajax_return(array('code'=>400, 'msg'=>'请求错误.'));
        }
        $banner_page_type = $this->input->post('banner_page_type');
        $banner_desc = $this->input->post('banner_desc');
        $banner_url = $this->input->post('banner_url');
        $img = $this->input->post('img');
        if (!$banner_page_type||!$banner_desc||!$img){
            $this->ajax_return(array('code'=>400, 'msg'=>'请将数据填充完整.'));
        }
        
        $now = time();
        $where = array();
        $where ['id'] = $id;
        $save = array();
        $save ['banner_page_type'] = $banner_page_type;
        $save ['banner_desc'] = $banner_desc;
        $save ['banner_url'] = $banner_url;
        $save ['file_ids'] = $img;
        $save ['update_time'] = $now;
        $re = $this->UiBannerModel->update($save, $where);
        
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败.'));
        }
    }
    
    /**
     * [delete 删除]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function delete()
    {
        $id = $this->input->get('id');
        if (!$id){
            $this->ajax_return(array('code'=>400, 'msg'=>'请求错误.'));
        }
        
        $now = time();
        // 事务开始
        $this->db->trans_start();
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->UiBannerModel->get_info($where);
        
        $save = array();
        $save ['is_show'] = 0;
        $save ['update_time'] = $now;
        $this->UiBannerModel->update($save, $where);
        $this->add_sys_log($id, $save);
        
        $save = array();
        $save ['banner_sort'] = "banner_sort'-1";
        $save ['update_time'] = $now;
        $where = array();
        $where ['banner_sort >'] = $info['banner_sort'];
        $where ['is_show'] = 1;
        //$this->UiBannerModel->update($save, $where);
        $this->db->set('banner_sort', "banner_sort-1", false)
        ->set('update_time', $now)
        ->where($where)
        ->update('ui_banner');
        
        $this->add_sys_log($id, $save);
        
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'删除失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->ajax_return(array('code'=>200, 'msg'=>'删除成功.'));
        }
    }
    
    /**
     * [sort 排序]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function sort()
    {
        $banner_page_type = $this->input->post('banner_page_type');
        $arr = $this->input->post('arr');
        if (!$banner_page_type||!$arr){
            $this->ajax_return(array('code'=>400, 'msg'=>'请求错误.'));
        }
        
        $now = time();
        // 事务开始
        $this->db->trans_start();
        foreach ($arr as $key=>$id){
            $save = array();
            $save ['banner_sort'] = $key+1;
            $save ['update_time'] = $now;
            $where = array();
            $where ['id'] = $id;
            $this->UiBannerModel->update($save, $where);
        }
        $this->add_sys_log($banner_page_type, $arr);
        
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'排序失败.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->ajax_return(array('code'=>200, 'msg'=>'排序成功.'));
        }
    }
}
