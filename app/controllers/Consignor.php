<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Consignor extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentOrderModel');
        $this->load->model('AgentProductModel');
        $this->load->model('LocationModel');
    }

    /**
     * [index 订单列表]
     *
     * @DateTime 2019-04-0
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page           = $this->input->get('per_page')?:1;
        $limit          = $this->config->item('per_page');
        $offset         = ($page-1)*$limit;
        $province_id    = $this->input->get('province_id');
        $city_id        = $this->input->get('city_id');
        $street_id      = $this->input->get('street_id');
        $village_id     = $this->input->get('village_id');
        $key            = $this->input->get('key');
        $product_id     = $this->input->get('product_id');
        $reservation    = $this->input->get('reservation');
        $selectTime     = $this->input->get('selectTime');
        $orderSelect    = $this->input->get('orderSelect')??0;
        
        $first_list     = $this->LocationModel->get_list(array('pid'=>0));
        $this->_data['first_list'] = $first_list;
        
        //设备类型列表
        /* $this->load->model('TextModel');
        $type_option = $this->TextModel->get_option(array('type'=>2));
        $this->_data['type_option'] = $type_option; */

        if ($key){
            $this->_data['key'] = $key;
            $where = "(a_o.purchase_trade_no='{$key}' or l.logistics_no='{$key}' or a.mobile='{$key}')";
            $where .= ' and a_o.status=1';
            switch ($orderSelect) {
                case '0':     // 待发货
                    $where .= ' and a_o.logistics_status=0';
                    break;
                case '1':      // 已发货
                    $where .= ' and a_o.logistics_status=1';
                    break;
                case '2':  // 已完成
                    $where .= ' and a_o.logistics_status=2';
                    break;
                default:
                    throw new Exception("订单选择栏出错", 1);
                    break;
            }
        }else {
            $where = array();
            if ($product_id){
                $this->_data['product_id'] = $product_id;
                $where['a_o.product_id'] = $product_id;
            }
            
            if ($village_id) {
                $where['p.village_id'] = $village_id;
                $this->_data['village_id'] = $village_id;
            }
            if ($street_id) {
                $where['p.street_id'] = $street_id;
                $this->_data['street_id'] = $street_id;
                //区列表
                $fourth_list = $this->LocationModel->get_list(array('pid'=>$street_id));
                $this->_data['fourth_list'] = $fourth_list;
            }
            if($city_id){
                $where['p.city_id'] = $city_id;
                $this->_data['city_id'] = $city_id;
                //区列表
                $third_list = $this->LocationModel->get_list(array('pid'=>$city_id));
                $this->_data['third_list'] = $third_list;
            }
            if($province_id){
                $where['p.province_id'] = $province_id;
                $this->_data['province_id'] = $province_id;
                //市列表
                $second_list = $this->LocationModel->get_list(array('pid'=>$province_id));
                $this->_data['second_list'] = $second_list;
            }
            if ( ! empty($reservation) && $selectTime){
                // 通过下单时间查询
                list($star_time, $end_time) = switch_reservation($reservation);
                $where['a.create_time>='] = $star_time;
                $where['a.create_time<='] = $end_time;
                $this->_data['selectTime'] = $selectTime;
                $this->_data['reservation'] = $reservation;
            }
            switch ($orderSelect) {
                case '0':     // 待发货
                    $where['a_o.status'] = 1;
                    $where['a_o.logistics_status'] = 0;
                    break;
                case '1':      // 已发货
                    $where['a_o.status'] = 1;
                    $where['a_o.logistics_status'] = 1;
                    break;
                case '2':  // 已完成
                    $where['a_o.status'] = 1;
                    $where['a_o.logistics_status'] = 2;
                    break;
                default:
                    throw new Exception("订单选择栏出错", 1);
                    break;
            }
        }
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->AgentOrderModel->get_list_count($where);
        $list = $this->AgentOrderModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $type = $info['type'];
            //$info['type_name'] = $type_option[$type];
            $info['create_time'] = $info['create_time']?date('Y-m-d H:i', $info['create_time']):'-';
            $info['pay_time'] = $info['pay_time']?date('Y-m-d H:i', $info['pay_time']):'-';
            if ($info['p_name']){
                $info['p_name'] = strtr($info['p_name'], ',', '');
            }
            $info['address_info'] = $info['a_a_name'].', '.$info['a_a_mobile'].', '.$info['p_name'].$info['a_a_position'];
        }
        $this->_data['list'] = $list;
        // 商品名称下拉列表; id => name       
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
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        
        $this->template->admin_render('consignor/index', $this->_data);
    }
}
