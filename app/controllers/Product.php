<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProductModel');
    }
    

    /**
     * [index 商品列表]
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
        if ($key){
            $where = "id = '$key' or name = '$key'";
            $this->_data['key'] = $key;
        }

        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->ProductModel->get_count($where);
        $list = $this->ProductModel->get_list($where, $limit, $offset);
        foreach($list as &$info){
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
            if ($info ['open_time']){
                list($info ['open_time_h'], $info ['open_time_m']) = explode('-', $info ['open_time']);
            }
            if ($info['type']==1){
                $info['type_name'] = '密码器';
            }else{
                $info['type_name'] = '物联网';
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该记录吗 ?', 'danger');
        $this->template->admin_render('product/index', $this->_data);
    }
    
    public function get_info()
    {
        $id = $this->input->get('id');
        if (!$id){
            exit();
        }
        $where = array();
        $where ['id'] = $id;
        $info = $this->ProductModel->get_info($where);
        //$info ['price'] = floatval($info ['price']);
        $this->load->model('user_model');
        $where = array('user_id'=>$info['user_id']);
        $user_info = $this->user_model->get_one_data($where);
        $info ['user_name'] = $user_info['user_name'];
        if ($info ['open_time']){
            list($info ['open_time_h'], $info ['open_time_m']) = explode('-', $info ['open_time']);
        }
        $this->ajax_return(array('code'=>200, 'data'=>$info));
    }
    
    /**
     * [add 添加功能]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $name = $this->input->post('name');
        $type = $this->input->post('type')??1;
        $price = $this->input->post('price');
        $incentive_price = $this->input->post('incentive_price');
        $open_time_h = (int)$this->input->post('open_time_h');
        $open_time_m = (int)$this->input->post('open_time_m');
        $describe = $this->input->post('describe');
        $status = $this->input->post('status');
        $is_default = $this->input->post('is_default');
        
        $product_data = array();
        $product_data ['name'] = $name;
        $product_data ['type'] = $type;
        $product_data ['open_time'] = $open_time_h . '-' . $open_time_m;
        $product_data ['price'] = $price;
        $product_data ['incentive_price'] = $incentive_price;
        $product_data ['describe'] = $describe;
        $product_data ['status'] = $status;
        $product_data ['is_default'] = $is_default;
        $product_data ['create_time'] = time();
        $product_data ['user_id'] = $this->get_user_id();
        
        $re = $this->ProductModel->add_data($product_data);
        
        if ($re) {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log('product', $product_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建商品成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'创建商品失败.'));
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
        $info = $this->ProductModel->get_info($wh_data);
        
        if (empty($info)) {
            $this->jump_error_page('记录不存在.');
        }
        
        $res = $this->ProductModel->del_data($wh_data);
        if (! $res) {
            $this->jump_error_page('服务器异常.');
        } else {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $info);
            // 操作成功跳转
            $this->jump_success_page('删除成功.');
        }
    }
    
    public function update()
    {
        $id = $this->input->get('id');
        $name = $this->input->post('name');
        $type = $this->input->post('type')??1;
        $price = $this->input->post('price');
        $incentive_price = $this->input->post('incentive_price');
        $open_time_h = (int)$this->input->post('open_time_h');
        $open_time_m = (int)$this->input->post('open_time_m');
        $describe = $this->input->post('describe');
        $status = $this->input->post('status');
        $is_default = $this->input->post('is_default');
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->ProductModel->get_info($where);
        $save = array();
        if ($name!=$info['name']){
            $save ['name'] = $name;
        }
        
        if ($type!=$info['type']){
            $save ['type'] = $type;
        }
        
        if ($price!=$info['price']){
            $save ['price'] = $price;
        }
        
        if ($incentive_price!=$info['incentive_price']){
            $save ['incentive_price'] = $incentive_price;
        }
        
        $open_time = $open_time_h.'-'.$open_time_m;
        if ($open_time!=$info['open_time']){
            $save ['open_time'] = $open_time;
        }
        
        if ($describe!=$info['describe']){
            $save ['describe'] = $describe;
        }
        
        if ($status!=$info['status']){
            $save ['status'] = $status;
        }
        
        if ($is_default!=$info['is_default']){
            $save ['is_default'] = $is_default;
        }

        if ($save){
            $save ['update_time'] = time();
            $re = $this->ProductModel->update($save, $where);
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
