<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentOrder extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentOrderModel');
        $this->load->model('LocationModel');
    }

    /**
     * [index 订单列表]
     *
     * @DateTime 2019-01-21
     * @Author   breite
     * @return   [type]     [description]
     */
    public function index()
    {
        $where          = array();
        $page           = $this->input->get('per_page')?:1;
        $limit          = $this->config->item('per_page');
        $offset         = ($page-1)*$limit;

        // 设备类型下拉列表;
        /* $this->load->model('TextModel');
        $type_option = $this->TextModel->get_option(array('type'=>2));
        $this->_data['type_option'] = $type_option; */
        
        $agent_proxy_pattern = $this->config->item('agent_proxy_pattern');
        
        $where          = $this->orderWhere();
        
        $province_id    = $this->input->get('province_id');
        $city_id        = $this->input->get('city_id');
        $street_id      = $this->input->get('street_id');
        $village_id     = $this->input->get('village_id');
        $key            = $this->input->get('key');
        $product_id     = $this->input->get('product_id');
        $reservation    = $this->input->get('reservation');
        $selectTime     = $this->input->get('selectTime');

        $first_list     = $this->LocationModel->get_list(array('pid'=>0));
        $this->_data['first_list'] = $first_list;

        if ($key){
            $this->_data['key'] = $key;
        }
        if ($product_id){
            $this->_data['product_id'] = $product_id;
        }
        if ($selectTime){
            $this->_data['selectTime'] = $selectTime;
        }
        if ($reservation){
            $this->_data['reservation'] = $reservation;
        }

        if ($province_id){
            $this->_data['province_id'] = $province_id;
            //市列表
            $second_list = $this->LocationModel->get_list(array('pid'=>$province_id));
            $this->_data['second_list'] = $second_list;
        }
        
        if ($city_id){
            $this->_data['city_id'] = $city_id;
            //区列表
            $third_list = $this->LocationModel->get_list(array('pid'=>$city_id));
            $this->_data['third_list'] = $third_list;
        }
        
        if ($street_id){
            $this->_data['street_id'] = $street_id;
            //区列表
            $fourth_list = $this->LocationModel->get_list(array('pid'=>$street_id));
            $this->_data['fourth_list'] = $fourth_list;
        }
        
        if ($village_id){
            $this->_data['village_id'] = $village_id;
        }

        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->AgentOrderModel->get_list_count($where);
        $list = $this->AgentOrderModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info['create_time'] = date('m-d H:i', $info['create_time']);
            if ($info['status']==0){
                $info['status'] = '待付款';
                $info['status_color'] = '#FFCC33';
            }elseif ($info['status']==1&$info['logistics_status']==0){
                $info['status'] = '待发货';
                $info['status_color'] = '#FF3300';
            }elseif ($info['status']==1&$info['logistics_status']==1){
                $info['status'] = '已发货';
                $info['status_color'] = '#669900';
            }elseif ($info['status']==1&$info['logistics_status']==2){
                $info['status'] = '已完成';
                $info['status_color'] = '#367FA9';
            }elseif ($info['status']==2){
                $info['status'] = '已取消';
                $info['status_color'] = '#99999';
            }else {
                $info['status'] = '未知';
            }
            if($info['is_confirm']==2){
                $info['status'] = '已拒绝';
                $info['status_color'] = '#993300';
            }
            //$info['type_name'] = $type_option[$info['type']];
            $info['agent_proxy_pattern'] = $agent_proxy_pattern[$info['proxy_pattern']];
        }
        $this->_data['list'] = $list;
        // 商品名称下拉列表; id => name
        $this->load->model('AgentProductModel');
        $product_name_option = array();
        $res= $this->AgentProductModel->get_prod_option();
        if ($res)
        {
            foreach ($res as $re)
            {
                $product_name_option[$re['id']] = $re['name'];
            }
            unset($res);
        }
        $this->_data['product_name_option'] = $product_name_option;
        // var_dump($product_name_option);
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->_data['total_rows'] = $total_rows;
        //弹窗
        $this->_data['confirm'] = render_js_confirm('confirm', '确认后订单对发货员可见，将进入发货流程。', 'danger');
        $this->template->admin_render('agent_order/index', $this->_data);
    }
    
    /**
     * [orderWhere 查询条件]
     *
     * @DateTime 2019-01-21
     * @Author   breite
     * @return   [type]     [description]
     */
    private function orderWhere()
    {
        $key              = $this->input->get('key');
        $product_id       = $this->input->get('product_id');
        $reservation      = $this->input->get('reservation');
        $selectTime       = $this->input->get('selectTime');
        $province_id      = $this->input->get('province_id');
        $city_id          = $this->input->get('city_id');
        $street_id        = $this->input->get('street_id');
        $village_id       = $this->input->get('village_id');
        $orderSelect      = $this->input->get('orderSelect')??'all';

        $where   = [];
        if ($key) {           
            $where = "a_o.purchase_trade_no = '{$key}' or a_a.mobile = '{$key}'";
            return $where;
        }
        
        if ($village_id) {
            $where['p.village_id'] = $village_id;
        }elseif ($street_id) {
            $where['p.street_id'] = $street_id;
        }elseif($city_id){
            $where['p.city_id'] = $city_id;
        }elseif($province_id){
            $where['p.province_id'] = $province_id;
        }
        switch ($orderSelect) {
            case 'all':       // 全部订单
                break;
            case 'prepay':    // 待付款
                $where['a_o.status'] = 0;
                break;
            case 'payed':     // 待发货
                $where['a_o.status'] = 1;
                $where['a_o.logistics_status'] = 0;
                $where['a_o.is_confirm!='] = 2;
                break;
            case 'send':      // 已发货
                $where['a_o.status'] = 1;
                $where['a_o.logistics_status'] = 1;
                break;
            case 'complete':  // 已完成
                $where['a_o.status'] = 1;
                $where['a_o.logistics_status'] = 2;
                break;
            case 'paycancel': // 已取消
                $where['a_o.status'] = 2;
                break;
            case 'refuse': // 已拒绝
                $where['a_o.is_confirm'] = 2;
                break;
            default:
                throw new Exception("订单选择栏出错", 1);
                break;
        }
        if($product_id){
            $where['a_o.product_id'] = $product_id;
        }
        if ( ! empty($reservation) && $selectTime){
            // 通过下单时间查询
            list($star_time, $end_time) = switch_reservation($reservation);
            $where['a_o.create_time>='] = $star_time;
            $where['a_o.create_time<='] = $end_time;
        }
        return $where;
    }
    
    
    
    /**
     * [confirm 代理商订单确认]
     *
     * @DateTime 2019-04-16
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function update()
    {
        $id = $this->input->get('id');
        $is_confirm = $this->input->get('is_confirm');
        $address_id = $this->input->get('address_id');
        if (empty($id)||(empty($is_confirm)&&empty($address_id))) {
            $this->jump_error_page('缺少参数');
        }
        if($is_confirm != 2){
            //如果不是拒绝发货走之前的流程
            $where = array(
            'id' => $id,
            );
            $save = array();
            if ($is_confirm){
                $save ['is_confirm'] = $is_confirm;
            }
            if ($address_id){
                $save ['address_id'] = $address_id;
            }
            $save ['update_time'] = time();
            $re = $this->AgentOrderModel->update($save, $where);
            if ($re){
                // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                $this->add_sys_log($id, $save);
                $this->jump_success_page('操作成功.');
            }else {
                $this->jump_error_page('操作失败！');
            }
        }else{
             //拒绝发货的情况
            $purchase_trade_no = $this->input->post('purchase_trade_no');
            $content           = $this->input->post('content');
            $now               = time();
            if (empty($purchase_trade_no)||empty($content)) {
                $this->jump_error_page('缺少参数');
            }
            $this->load->model('ReasonModel');
            $reason_data = array(
                'type'        => 3,
                'uid'         => $purchase_trade_no,
                'content'     => $content,
                'create_time' => $now,
            );
            // 事务开始
            $this->db->trans_start();
            
            $this->ReasonModel->add_data($reason_data);
            
            $reject_reason_id = $this->db->insert_id();
            $update_data = array(
                'is_confirm'                 => 2,
                'reject_logistics_reason_id' =>$reject_reason_id,
                'update_time'                =>$now
            );
            $where_order = array('id' => $id);
            $this->AgentOrderModel->update($update_data, $where_order);
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE){
                $this->db->trans_rollback();
                $this->ajax_return(array('code'=>400, 'msg'=>'失败,写入数据错误.'));
            } else {
                $this->db->trans_commit();
                $this->add_sys_log($id, $update_data);
                $this->ajax_return(array('code'=>200, 'msg'=>'拒绝发货成功'));
            }
        }
        
    }
}
