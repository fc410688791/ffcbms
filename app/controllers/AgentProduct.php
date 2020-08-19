<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentProduct extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentProductModel');
        $this->load->model('FileModel');
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
        $id = $this->input->get('id');
        $name = $this->input->get('name');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        //采购商品类型列表
        $this->load->model('AgentProductTypeModel');
        $where = array();
        $total_rows = $this->AgentProductTypeModel->get_all_data_count($where);
        $agent_product_type_list = $this->AgentProductTypeModel->get_list($where, $total_rows, 0);
        $agent_product_type_option = array();
        foreach ($agent_product_type_list as $agent_product_type_info){
            $agent_product_type_option[$agent_product_type_info['id']] = $agent_product_type_info['type_name'];
        }
        $this->_data['agent_product_type_option'] = $agent_product_type_option;
        
        $where = array();
        if ($id){
            $where ['a.id'] = $id;
            $this->_data['id'] = $id;
        }
        if ($name){
            $where ['a.name'] = $name;
            $this->_data['name'] = $name;
        }

        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->AgentProductModel->get_count($where);
        $list = $this->AgentProductModel->get_list($where, $limit, $offset);
        foreach($list as &$info){
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
            if ($info['status']==1){
                $info['status'] = '是';
            }else {
                $info['status'] = '否';
            }
            $info['thumbnail_file'] = $this->FileModel->get_url(array('id'=>$info['thumbnail_file_id']));
            if ($info['scene_file_id']){
                $scene_list = json_decode($info['scene_file_id'],true);
                $scene = array();
                foreach ($scene_list as $v){
                    $scene [] = $v;
                }
                $info['scene'] = join('、', $scene);
            }else {
                $info['scene'] = '';
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;4
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该记录吗 ?', 'danger');
        $this->template->admin_render('agent_product/index', $this->_data);
    }
    
    /**
     * [get_info ajax请求记录详情功能]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function info()
    {
        $id = $this->input->get('id');
        if (!$id){
            exit();
        }
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentProductModel->get_info($where);
        $this->load->model('user_model');
        $where = array('user_id'=>$info['user_id']);
        $user_info = $this->user_model->get_one_data($where);
        $info ['user_name'] = $user_info['user_name'];
        
        $info['product_op_file_url'] = $this->FileModel->get_url(array('id'=>$info['product_op_file_id']));
        $info['thumbnail_file_url'] = $this->FileModel->get_url(array('id'=>$info['thumbnail_file_id']));
        $info['detail_file_url'] = $this->FileModel->get_url(array('id'=>$info['detail_file_id']));
        $scene_list = array();
        if ($info['scene_file_id']){
            $scene_file = json_decode($info['scene_file_id'], true);
            foreach ($scene_file as $file_id=>$scene_name){
                $scene = array();
                $scene ['id'] = $file_id;
                $scene ['name'] = $scene_name;
                $scene ['url'] = $this->FileModel->get_url(array('id'=>$file_id));
                $scene_list [] = $scene;
            }
        }
        $info ['scene_list'] = $scene_list;
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
        $type = $this->input->post('type');
        $min_num = $this->input->post('min_num');
        $max_num = $this->input->post('max_num');
        $price = $this->input->post('price');
        $p_img_id = $this->input->post('p_img_id');
        $s_img_id = $this->input->post('s_img_id');
        $b_img_id = $this->input->post('b_img_id');
        $sfile = $this->input->post('sfile');
        $sort = $this->input->post('sort');
        $status = $this->input->post('status');
        if ($status == 1){
            $where1 = array();
            $where1 ['min_num <='] = $min_num;
            $where1 ['max_num >='] = $min_num;
            $where1 ['status'] = 1;
            $where1 ['type'] = $type;
            $info1 = $this->AgentProductModel->get_info($where1);
            $where2 = array();
            $where2 ['min_num <='] = $max_num;
            $where2 ['max_num >='] = $max_num;
            $where2 ['status'] = 1;
            $where2 ['type'] = $type;
            $info2 = $this->AgentProductModel->get_info($where2);
            $where3 = array();
            $where3 ['min_num >='] = $min_num;
            $where3 ['max_num <='] = $max_num;
            $where3 ['status'] = 1;
            $where3 ['type'] = $type;
            $info3 = $this->AgentProductModel->get_info($where3);
            if ($info1||$info2||$info3){
                $this->ajax_return(array('code'=>400, 'msg'=>'范围重叠.'));
            }
        }
        $product_data = array();
        $product_data ['name'] = $name;
        $product_data ['type'] = $type;
        $product_data ['min_num'] = $min_num;
        $product_data ['max_num'] = $max_num;
        $product_data ['price'] = $price;
        $product_data ['product_op_file_id'] = $p_img_id;
        $product_data ['thumbnail_file_id'] = $s_img_id;
        $product_data ['detail_file_id'] = $b_img_id;
        if ($sfile){
            $product_data ['scene_file_id'] = json_encode($sfile);
        }else {
            $product_data ['scene_file_id'] = '';
        }
        $product_data ['sort'] = $sort;
        $product_data ['status'] = $status;
        $product_data ['create_time'] = time();
        $product_data ['user_id'] = $this->get_user_id();
        $re = $this->AgentProductModel->add_data($product_data);
        
        if ($re) {
            $this->add_sys_log('agent_product', $product_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建商品成功.'));
        } else {
            $this->ajax_return(array('code'=>400, 'msg'=>'创建商品失败.'));
        }
    }
    
    /**
     * [update 编辑功能]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function update()
    {
        $id = $this->input->get('id');
        $name = $this->input->post('name');
        $type = $this->input->post('type');
        $min_num = $this->input->post('min_num');
        $max_num = $this->input->post('max_num');
        $price = $this->input->post('price');
        $p_img_id = $this->input->post('p_img_id');
        $s_img_id = $this->input->post('s_img_id');
        $b_img_id = $this->input->post('b_img_id');
        $sfile = $this->input->post('sfile');
        $sort = $this->input->post('sort');
        $status = $this->input->post('status');
                     
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentProductModel->get_info($where);
        $save = array();
        if ($name!=$info['name']){
            $save ['name'] = $name;
        }
        
        if ($type!=$info['type']){
            $save ['type'] = $type;
        }
        
        if ($min_num!=$info['min_num']){
            $save ['min_num'] = $min_num;
        }
        
        if ($max_num!=$info['max_num']){
            $save ['max_num'] = $max_num;
        }
        
        if ($price!=$info['price']){
            $save ['price'] = $price;
        }
        
        if ($p_img_id!=$info['product_op_file_id']){
            $save ['product_op_file_id'] = $p_img_id;
        }
        
        if ($s_img_id!=$info['thumbnail_file_id']){
            $save ['thumbnail_file_id'] = $s_img_id;
        }
        
        if ($b_img_id!=$info['detail_file_id']){
            $save ['detail_file_id'] = $b_img_id;
        }
        
        if ($sort!=$info['sort']){
            $save ['sort'] = $sort;
        }
        
        if ($status!=$info['status']){
            $save ['status'] = $status;
        }
        
        if ($sfile){
            $save ['scene_file_id'] = json_encode($sfile);
        }else {
            $save ['scene_file_id'] = '';
        }
        if ($status==1){
            if ($min_num!=$info['min_num']){
                $where1 = array();
                $where1 ['min_num <='] = $min_num;
                $where1 ['max_num >='] = $min_num;
                $where1 ['type'] = $info['type'];
                $where1 ['status'] = 1;
                $where1 ['id <>'] = $id;
                $info1 = $this->AgentProductModel->get_info($where1);
                $info2 = null;
                $info3 = null;
                if ($info1||$info2||$info3){
                    $this->ajax_return(array('code'=>400, 'msg'=>'范围重叠.'));
                }
            }
            if ($max_num!=$info['max_num']){
                $info1 = null;
                $where2 = array();
                $where2 ['min_num <='] = $max_num;
                $where2 ['max_num >='] = $max_num;
                $where2 ['type'] = $info['type'];
                $where2 ['status'] = 1;
                $where2 ['id <>'] = $id;
                $info2 = $this->AgentProductModel->get_info($where2);
                $info3 = null;
                if ($info1||$info2||$info3){
                    $this->ajax_return(array('code'=>400, 'msg'=>'范围重叠.'));
                }
            }
        }
        if ($save){
            $save ['update_time'] = time();
            $re = $this->AgentProductModel->update($save, $where);
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
