<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberChargeOrder extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberChargeOrderModel');
        $this->load->model('MemberActivityChargeModel');
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
        $pay_type       = $this->input->get('pay_type');
        $reservation    = $this->input->get('reservation');
        $activity_charge_id    = $this->input->get('activity_charge_id');
        $orderSelect    = $this->input->get('orderSelect');
        
        //支付类型列表;
        $pay_type_option = $this->config->item('pay_type');
        unset($pay_type_option['3']);
        $this->_data['pay_type_option'] = $pay_type_option;
        //充币商品列表
        $charge_name_option = $this->MemberActivityChargeModel->get_charge_option();
        $this->_data['charge_name_option'] = $charge_name_option;
        
        $where = array();
        if ($key) {
            $where = "a.out_trade_no = '$key' or b.uuid = '$key' or b.nickname = '$key' or b.mobile = '$key'";
            $this->_data['key'] = $key;
        }else{
            if($pay_type){
                $where['a.pay_type'] = $pay_type;
                $this->_data['pay_type'] = $pay_type;
            }
            if($activity_charge_id){
                $where['c.id'] = $activity_charge_id;
                $this->_data['activity_charge_id'] = $activity_charge_id;
            }
            if ($reservation){
                // 通过下单时间查询
                list($start_time, $end_time) = switch_reservation($reservation);
                $where['a.create_time >='] = $start_time;
                $where['a.create_time <'] = $end_time;
                $this->_data['reservation'] = $reservation;
            }
            if ($orderSelect){
                switch ($orderSelect) {
                    case 'all':       // 全部订单
                        break;
                    case 'prepay':    // 待支付
                        $where['a.order_status'] = 0;
                        break;
                    case 'payed':    // 支付成功
                        $where['a.order_status'] = 1;
                        break;
                    case 'paycancel': // 已取消
                        $where['a.order_status'] = 2;
                        break;
                    default:
                        throw new Exception("订单选择栏出错", 1);
                        break;
                }
            }
        }
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MemberChargeOrderModel->get_count($where);
        $list = $this->MemberChargeOrderModel->get_list($where, $limit, $offset);
        foreach ($list as &$value) {
            $pay_type = $value['pay_type'];
            $value['pay_type'] = $pay_type_option[$pay_type];
            $value['sta_color'] = 'black';
            if($value['order_status']==0){
                $value['order_status']='待支付';
                $value['sta_color']='text-aqua';
            }else if($value['order_status']==1){
                $value['order_status']='已支付';
                $value['sta_color']='text-green';
            }elseif($value['order_status']==2){
                $value['order_status']='已取消';
                $value['sta_color']='text-red';
            }
        }
        $this->_data['list'] = $list;
        // 商品名称下拉列表;
        $product_name_option = $this->admin_process->get_prod_option();
        $this->_data['product_name_option'] = $product_name_option;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->_data['total_rows'] = $total_rows;
        
        $this->template->admin_render('member_charge_order/index', $this->_data);
    }
}
