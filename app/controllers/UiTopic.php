<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UiTopic extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UiTopicVersionModel');
        $this->load->model('UiTopicInfoModel');
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
        $topic_type_option = array();
        $topic_type_option [1] = '整体主题';
        $topic_type_option [2] = '底部tabbar';
        $topic_type_option [3] = '扫码页面';
        $topic_type_option [4] = '商品页面';
        $topic_type_option [5] = '充币优惠';
        $topic_type_option [6] = '我的';
        $topic_type_option [7] = '关于全快充';
        $topic_type_option [8] = '订单';
        $topic_type_option [9] = '申请退款';
        $topic_type_option [10] = '倒计时';
        $element_type_option = array();
        $element_type_option [1] = '颜色';
        $element_type_option [2] = '图标';
        $ui_topic = $this->UiTopicVersionModel->get_info();
        if ($ui_topic&&!$ui_topic['is_default']){
            $this->_data['is_default'] = 1;
        }else {
            $this->_data['is_default'] = 0;
        }
        $where = array();
        $ui_topic_info_count = $this->UiTopicInfoModel->get_count($where);
        $ui_topic_info_list = $this->UiTopicInfoModel->get_list($where, $ui_topic_info_count, 0);
        $list = array();
        foreach ($ui_topic_info_list as &$info){
            if ($info['element_type']==2){
                $info['element_url'] = $this->FileModel->get_url(array('id'=>$info['element_value']));
            }
            if ($info['is_show']){
                $info['is_show'] = '展示';
            }else {
                $info['is_show'] = '不展示';
            }
        }
        $this->_data['topic_type_option'] = $topic_type_option;
        $this->_data['element_type_option'] = $element_type_option;
        $this->_data['list'] = $ui_topic_info_list;
        $this->template->admin_render('ui_topic/index', $this->_data);
    }
    
    /**
     * [add 添加]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $topic_type = $this->input->post('topic_type');
        $function_name = $this->input->post('function_name');
        $function_locator = $this->input->post('function_locator');
        $element_type = $this->input->post('element_type');
        $col = $this->input->post('col');
        $img = $this->input->post('img');
        $is_show = $this->input->post('is_show');
        
        $now = time();
        $ui_topic = $this->UiTopicVersionModel->get_info(array());
        if ($ui_topic){
            $version_id = $ui_topic['id'];
        }else {
            $ui_topic_version_data = array();
            $ui_topic_version_data ['is_default'] = 1;
            $ui_topic_version_data ['version_no'] = $now;
            $ui_topic_version_data ['create_time'] = $now;
            $this->UiTopicVersionModel->add_data($ui_topic_version_data);
            $version_id = $this->db->insert_id();
        }
        if ($version_id){
            $ui_topic_data = array();
            $ui_topic_data ['version_id'] = $version_id;
            $ui_topic_data ['topic_type'] = $topic_type;
            $ui_topic_data ['function_name'] = $function_name;
            $ui_topic_data ['function_locator'] = $function_locator;
            $ui_topic_data ['element_type'] = $element_type;
            if ($element_type==1){
                $ui_topic_data ['element_value'] = $col;
            }elseif ($element_type==2){
                $ui_topic_data ['element_value'] = $img;
            }
            $ui_topic_data ['is_show'] = $is_show;
            $ui_topic_data ['create_time'] = $now;
            
            $re = $this->UiTopicInfoModel->add_data($ui_topic_data);
            
            if ($re) {
                // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                $this->add_sys_log('ui_topic_info', $ui_topic_data);
                $this->ajax_return(array('code'=>200, 'msg'=>'创建成功.'));
            } else {
                $this->ajax_return(array('code'=>400, 'msg'=>'创建失败.'));
            }
        }else{
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
        $topic_type = $this->input->post('topic_type');
        $function_name = $this->input->post('function_name');
        $function_locator = $this->input->post('function_locator');
        $element_type = $this->input->post('element_type');
        $col = $this->input->post('col');
        $img = $this->input->post('img');
        $is_show = $this->input->post('is_show');
        
        $now = time();
        $ui_topic = $this->UiTopicVersionModel->get_info(array());
        $ui_topic_data = array();
        $ui_topic_data ['version_id'] = $ui_topic['id'];
        $ui_topic_data ['topic_type'] = $topic_type;
        $ui_topic_data ['function_name'] = $function_name;
        $ui_topic_data ['function_locator'] = $function_locator;
        $ui_topic_data ['element_type'] = $element_type;
        if ($element_type==1){
            $ui_topic_data ['element_value'] = $col;
        }elseif ($element_type==2){
            $ui_topic_data ['element_value'] = $img;
        }
        $ui_topic_data ['is_show'] = $is_show;
        $ui_topic_data ['update_time'] = $now;
        $where = array();
        $where ['id'] = $id;
        $re = $this->UiTopicInfoModel->update($ui_topic_data, $where);
        
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $ui_topic_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'修改成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'修改失败.'));
        }
    }
    
    /**
     * [release 发布]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function release()
    {
        $is_default = $this->input->post('is_default');        
        $now = time();
        $ui_topic = $this->UiTopicVersionModel->get_info(array());
        if (!$ui_topic){
            $this->ajax_return(array('code'=>400, 'msg'=>'请先添加自定义样式.'));
        }
        $where = array();
        $where ['id'] = $ui_topic['id'];
        $save = array();
        $save ['is_default'] = $is_default;
        $save ['version_no'] = $now;
        $save ['update_time'] = $now;
        $re = $this->UiTopicVersionModel->update($save, $where);
        
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($ui_topic['id'], $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'发布成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'发布失败.'));
        }
    }
}
