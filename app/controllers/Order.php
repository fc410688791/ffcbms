<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OrderModel');
        $this->load->model('ProductModel');
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
        $page           = $this->input->get('per_page')?:1;
        $limit          = $this->config->item('per_page');
        $offset         = ($page-1)*$limit;
        
        $key            = $this->input->get('key');
        $merchant_id    = $this->input->get('merchant_id');
        $province_id    = $this->input->get('province_id');
        $pay_type       = $this->input->get('pay_type');
        $type           = $this->input->get('type');
        $city_id        = $this->input->get('city_id');
        $street_id      = $this->input->get('street_id');
        $village_id     = $this->input->get('village_id');        
        $product_id     = $this->input->get('product_id');
        $reservation    = $this->input->get('reservation');
        $selectTime     = $this->input->get('selectTime');
        $orderSelect    = $this->input->get('orderSelect');
        
        $first_list     = $this->LocationModel->get_list(array('pid'=>0));
        $this->_data['first_list'] = $first_list;
        
        //支付类型列表;
        $pay_type_option = $this->config->item('pay_type');
        $this->_data['pay_type_option'] = $pay_type_option;

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
        if ($key) {
            $where = "a.out_trade_no = '$key' or a.uuid = '$key' or a.machine_id = '$key'";
            $this->_data['key'] = $key;
        }else{
            if ($merchant_id){
                $where['a.merchant_id'] = $merchant_id;
                $where ['a.status'] = 1;  //支付成功
                $where ['a.complete_status !='] = 3;  //不包含退款
                $this->_data['merchant_id'] = $merchant_id;
            } 
            if ($village_id) {
                $where['c.village_id'] = $village_id;
                $this->_data['village_id'] = $village_id;
            }
            if ($street_id) {
                $where['c.street_id'] = $street_id;
                $this->_data['street_id'] = $street_id;
                //区列表
                $fourth_list = $this->LocationModel->get_list(array('pid'=>$street_id));
                $this->_data['fourth_list'] = $fourth_list;
            }
            if($city_id){
                $where['c.city_id'] = $city_id;
                $this->_data['city_id'] = $city_id;
                //区列表
                $third_list = $this->LocationModel->get_list(array('pid'=>$city_id));
                $this->_data['third_list'] = $third_list;
            }
            if($pay_type){
                $where['a.pay_type'] = $pay_type;
                $this->_data['pay_type'] = $pay_type;
                //市列表
                $second_list = $this->LocationModel->get_list(array('pid'=>$province_id));
                $this->_data['second_list'] = $second_list;
            }
            if($province_id){
                $where['c.province_id'] = $province_id;
                $this->_data['province_id'] = $province_id;
                //市列表
                $second_list = $this->LocationModel->get_list(array('pid'=>$province_id));
                $this->_data['second_list'] = $second_list;
            }
            if($type){
                $where['e.type'] = $type;
                $this->_data['type'] = $type;
            }
            if($product_id){
                $where['a.product_id'] = $product_id;
                $this->_data['product_id'] = $product_id;
            }
            if ( ! empty($reservation) && $selectTime){
                // 通过下单时间查询
                list($start_time, $end_time) = switch_reservation($reservation);
                $where['a.create_time >='] = $start_time;
                $where['a.create_time <'] = $end_time;
                $this->_data['selectTime'] = $selectTime;
                $this->_data['reservation'] = $reservation;
            }
            if ($orderSelect){
                switch ($orderSelect) {
                    case 'all':       // 全部订单
                        break;
                    case 'prepay':    // 待支付
                        $where['a.status'] = 0;
                        break;
                    case 'payed':    // 支付成功
                        $where['a.status'] = 1;
                        $where['a.complete_status'] = 0;
                        break;
                    case 'complete':  // 已完成
                        $where['a.status'] = 1;
                        $where['a.complete_status !='] = 0;
                        break;
                    case 'paycancel': // 已取消
                        $where['a.status'] = 2;
                        break;
                    default:
                        throw new Exception("订单选择栏出错", 1);
                        break;
                }
            }
        }
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->OrderModel->get_list_count($where);
        $list = $this->OrderModel->get_list($where, $limit, $offset);
        foreach ($list as &$value) {
            $pay_type = $value['pay_type'];
            $value['pay_type'] = $pay_type_option[$pay_type];
            $value['sta_color'] = 'black';
            if($value['status']==0){
                $value['status']='待支付';
                $value['sta_color']='text-aqua';
            }else if($value['status']==1){
                if ($value['complete_status']==0){
                    $value['status']='已支付';
                    $value['sta_color']='text-green';
                }else{
                    $value['status']='已完成';
                    $value['sta_color']='text-blue';
                }
            }elseif($value['status']==2){
                $value['status']='已取消';
                $value['sta_color']='text-red';
            }
            if ($value['agent_product_type']){
                $value['agent_product_type_name'] = $agent_product_type_option[$value['agent_product_type']];
            }else {
                $value['agent_product_type_name'] = '';
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);     
        $this->template->admin_render('order/index', $this->_data);
    }
    
    public function get_order_info()
    {
        $order_id = $this->input->get('order_id');
        if (!$order_id){
            exit();
        }
        $order_info = $this->OrderModel->get_info(array('id'=>$order_id));
        $this->load->model('RefundModel');
        $refund_info = $this->RefundModel->get_info(array('r.order_id'=>$order_id));
        if ($order_info){
            /* $this->load->model('TextModel');
            $type_option = $this->TextModel->get_option(array('type'=>2,'status'=>1));
            $order_info['type'] = $type_option[$order_info['type']]; */
            $order_info ['create_time'] = date('Y-m-d H:i:s', $order_info ['create_time']);
            $order_info ['pay_time'] = $order_info ['pay_time']?date('Y-m-d H:i:s', $order_info ['pay_time']):'-';
            $order_info ['open_time'] = $order_info ['open_time']?date('Y-m-d H:i:s', $order_info ['open_time']):'-';
            $order_info ['charge_time'] = $order_info ['charge_time']?date('Y-m-d H:i:s', $order_info ['charge_time']):'-';
            if ($order_info ['status']==1){
                $order_info ['o_status'] = '支付成功';
            }else {
                $order_info ['o_status'] = '未支付';
            }
            if ($order_info ['pay_type']==1){
                $order_info ['pay_type'] = '微信小程序';
            }elseif ($order_info ['pay_type']==2){
                $order_info ['pay_type'] = '支付宝小程序';
            }elseif ($order_info ['pay_type']==3){
                $order_info ['pay_type'] = '充币';
            }else {
                $order_info ['pay_type'] = '其他';
            }
            if($order_info ['complete_status']==1){
                $order_info ['complete_status'] = '自动完成';
            }elseif ($order_info ['complete_status']==2){
                $order_info ['complete_status'] = '用户确认完成';
            }elseif ($order_info ['complete_status']==3){
                $order_info ['complete_status'] = '已退款';
            }elseif ($order_info ['complete_status']==4){
                $order_info ['complete_status'] = '拒绝退款';
            }else {
                $order_info ['complete_status'] = '待完成';
            }
            if ($refund_info){
                switch ($refund_info ['r_status']){
                    case 1:
                        $order_info ['r_status'] = '退款成功';
                        break;
                    case 2:
                        $order_info ['r_status'] = '待退款';
                        break;
                    case 3:
                        $order_info ['r_status'] = '拒绝退款';
                        break;
                    default:
                        $order_info ['r_status'] = '未知';
                        break;
                }
            }else {
                $order_info ['r_status'] = '无';
            }
            $data = array('order_info'=>$order_info, 'refund_info'=>$refund_info);
            $this->ajax_return(array('code'=>200, 'data'=>$data));
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'订单不存在！'));
        }
    }
}
