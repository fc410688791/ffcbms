<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CustomerService extends Admin_Controller
{
    private $status_list = array('1'=>'退款成功','2'=>'待退款','3'=>'拒绝退款');
    public function __construct()
    {
        parent::__construct();
        $this->load->model('RefundModel');
        $this->load->model('OrderModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-01-24
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $this->load->model('LocationModel');
        $this->load->model('FileModel');
        $page = $this->input->get('per_page')??1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $status = $this->input->get('status')??2;
        $province_id = $this->input->get('province_id');
        $city_id     = $this->input->get('city_id');
        $street_id   = $this->input->get('street_id');
        $village_id   = $this->input->get('village_id');
        $key   = $this->input->get('key');
        
        //支付类型列表;
        $pay_type_option = $this->config->item('pay_type');
        //状态列表
        $this->_data['status_list'] = $this->status_list;
        
        $first_list = $this->LocationModel->get_list(array('pid'=>0));
        $this->_data['first_list'] = $first_list;
        
        $where = array();
        $order_list = array();
        if ($key){
            $where = "out_trade_no = '$key'";
            $this->_data['key'] = $key;
            $order_list = $this->OrderModel->get_list($where, $limit, $offset);
            foreach ($order_list as &$order_info){
                $pay_type = $order_info['pay_type'];
                $order_info['pay_type'] = $pay_type_option[$pay_type];
                $order_info ['create_time'] = date("Y-m-d H:i:s", $order_info ['create_time']);
                switch ($order_info['status']) {
                    case '0':    // 待支付
                        $order_info['status'] = '待支付';
                        break;
                    case '1':    // 支付成功
                        $order_info['status'] = '支付成功';
                        break;
                    case '2':    // 支付取消
                        $order_info['status'] = '支付取消';
                        break;
                    default:
                        $order_info['status'] = '状态异常';
                        break;
                }
                $refund = $this->RefundModel->get_info(array('r.order_id'=>$order_info['id']));
                if($refund){
                    switch ($refund['r_status']) {
                        case '1':    // 退款成功
                            $order_info['r_status'] = '退款成功';
                            break;
                        case '2':    // 待退款
                            $order_info['r_status'] = '待退款';
                            break;
                        case '3':    // 拒绝退款
                            $order_info['r_status'] = '拒绝退款';
                            break;
                        default:
                            $order_info['r_status'] = '状态异常';
                            break;
                    }
                }else {
                    $order_info['r_status'] = '无';
                }
                
            }
        }else {
            if ($status){
                $where ['r.status'] = $status;
                $this->_data['status'] = $status;
            }
            if ($province_id){
                $where ['province_id'] = $province_id;
                $this->_data['province_id'] = $province_id;
                //市列表
                $second_list = $this->LocationModel->get_list(array('pid'=>$province_id));
                $this->_data['second_list'] = $second_list;
            }
            
            if ($city_id){
                $where ['city_id'] = $city_id;
                $this->_data['city_id'] = $city_id;
                //区列表
                $third_list = $this->LocationModel->get_list(array('pid'=>$city_id));
                $this->_data['third_list'] = $third_list;
            }
            
            if ($street_id){
                $where ['street_id'] = $street_id;
                $this->_data['street_id'] = $street_id;
                //区列表
                $fourth_list = $this->LocationModel->get_list(array('pid'=>$street_id));
                $this->_data['fourth_list'] = $fourth_list;
            }
            
            if ($village_id){
                $where ['village_id'] = $village_id;
                $this->_data['village_id'] = $village_id;
            }
        }
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->RefundModel->get_count($where);
        $list = $this->RefundModel->get_list($where, $limit, $offset);
        foreach ($list as &$info){
            $pay_type = $info['pay_type'];
            $info['pay_type'] = $pay_type_option[$pay_type];
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
            if ($info['status']==1){
                $info['status_color'] = 'green';
            }elseif ($info['status']==2){
                $info['status_color'] = 'red';
            }elseif ($info['status']==3){
                $info['status_color'] = 'yellow';
            }
            $info ['status'] = $this->status_list[$info['status']];
            if ($info['file_ids']){
                $where_in = explode(",", $info['file_ids']);
                $file_list = $this->FileModel->get_list($where_in);
                $info['file_list'] = $file_list;
            }
        }
        $this->_data['order_list'] = $order_list;
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('customer_service/index', $this->_data);
    }
    
    public function refund()
    {
        $now = time();
        $id  = $this->input->get('id');
        if (!$id){
            $out_trade_no = $this->input->post('out_trade_no');
            $mobile = $this->input->post('mobile');
            $refund_text_id = $this->input->post('refund_text_id');
            $reason = $this->input->post('reason');
            $remark = $this->input->post('remark');
            $source = $this->input->post('source');
            if (!$out_trade_no||!$mobile||!$refund_text_id||!$source){
                $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
            }
            $order_info = $this->OrderModel->get_info(array('out_trade_no'=>$out_trade_no));//查询订单
            if ($order_info['status']==1){//已支付订单
                $where = array('r.order_id'=>$order_info['id']);
                $refund_info = $this->RefundModel->get_info($where);//查询退款订单
                if ($refund_info){//存在
                    $this->ajax_return(array('code'=>400, 'msg'=>'存在退款订单,请在下面退款订单列表处理.'));
                }else {//不存在，退款->创建退款订单
                    $fee = $order_info['cash_fee']*100;
                    $out_refund_no = $this->generateOrderNumber('userRefund');
                    if ($order_info['pay_type']==1){//微信小程序支付
                        $ref_order = $this->wxRefund($fee,$order_info['transaction_id'],$out_refund_no);
                    }elseif ($order_info['pay_type']==2){//支付宝小程序支付
                        $ref_order = $this->zfbRefund($order_info);
                    }elseif ($order_info['pay_type']==3){//充币支付
                        $order_info['now'] = $now;
                        $this->cbRefund($order_info);
                    }else {
                        exit();
                    }
                    $this->db->trans_begin(); // 事务开始
                    //改变订单状态
                    $save_order['complete_status'] = 3;
                    $save_order['complete_time'] = $now;
                    $save_order['update_time'] = $now;
                    $this->OrderModel->update($save_order, $where=array('id'=>$order_info['id']));
                    //添加退款记录
                    $add_data = [
                        'order_id'       => $order_info['id'],
                        'out_refund_no'  => $out_refund_no,
                        'refund_text_id' => $refund_text_id,
                        'reason'         => $reason,
                        'remark'        => $remark,
                        'mobile'         => $mobile,
                        'file_ids'       => '',
                        'create_time'    => $now,
                        'refund_time'    => $now,
                        'source'         => $source,
                        'status'         => 1,
                        'refund_id'      => $ref_order['refund_id']??'',
                        'update_time'    => $now
                    ];
                    $this->RefundModel->add_data($add_data);
                    if ($order_info['at_receive_id']){
                        //修改优惠券状态
                        $this->load->model('MemberActivityReceiveModel');
                        $save_receive = array();
                        $save_receive['receive_status'] = 0;
                        $save_receive['update_time'] = $now;
                        $this->MemberActivityReceiveModel->update($save_receive, array('id'=>$order_info['at_receive_id']));
                    }
                    // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                    $this->add_sys_log('refund', $add_data);
                    // 事务提交
                    $this->db->trans_complete();
                    if ($this->db->trans_status() == FALSE)
                    {
                        $this->db->trans_rollback();
                        $this->ajax_return(array('code'=>400, 'msg'=>'退款失败.'));
                    }
                    else
                    {
                        $this->db->trans_commit();
                        $this->ajax_return(array('code'=>200, 'msg'=>'退款成功.'));
                    }
                }
            }else {
                $this->ajax_return(array('code'=>400, 'msg'=>'订单状态错误.'));
            }
        }else {
            $operation    = $this->input->post('operation');
            $remark       = $this->input->post('remark');
            $re = false;
            if ($operation == 'refund'){//退款
                $where = array('r.id'=>$id,'r.status'=>2,'o.status'=>1);
                $info = $this->RefundModel->get_info($where);
                if ($info){//订单状态正常&退款申请状态正常
                    if ($info['pay_type']==1){//微信支付
                        $fee = $info['cash_fee']*100;
                        $ref_order = $this->wxRefund($fee,$info['transaction_id'],$info['out_refund_no']);
                    }elseif ($info['pay_type']==2){//支付宝支付
                        $ref_order = $this->zfbRefund($info);
                    }elseif ($info['pay_type']==3){//充币支付
                        $info['now'] = $now;
                        $this->cbRefund($info);
                        exit();
                    }else {
                        exit();
                    }
                    $this->db->trans_begin(); // 事务开始
                    //改变订单状态
                    $save_order = array();
                    $save_order['complete_status'] = 3;
                    $save_order['complete_time'] = $now;
                    $save_order['update_time'] = $now;
                    $this->OrderModel->update($save_order, $where=array('id'=>$info['order_id']));
                    
                    //改变退款申请状态
                    $save_refund = array();
                    $save_refund ['status'] = 1;
                    $save_refund ['remark'] = $remark;
                    $save_refund ['refund_id'] = $ref_order['refund_id']??'';
                    $save_refund ['refund_time'] = $now;
                    $save_refund ['update_time'] = $now;
                    $this->RefundModel->update($save_refund, $where=array('id'=>$id));
                    
                    if ($info['at_receive_id']){
                        //修改优惠券状态
                        $this->load->model('MemberActivityReceiveModel');
                        $save_receive = array();
                        $save_receive['receive_status'] = 0;
                        $save_receive['update_time'] = $now;
                        $this->MemberActivityReceiveModel->update($save_receive, array('id'=>$info['at_receive_id']));
                    }
                    // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                    $this->add_sys_log($id, $save_refund);
                    // 事务提交
                    $this->db->trans_complete();
                    if ($this->db->trans_status() == FALSE)
                    {
                        $this->db->trans_rollback();
                        $this->ajax_return(array('code'=>400, 'msg'=>'退款失败.'));
                    }
                    else
                    {
                        $this->db->trans_commit();
                        $this->ajax_return(array('code'=>200, 'msg'=>'退款成功.'));
                    }
                } 
            } elseif ($operation == 'refuse'){//拒绝
                $save_refund ['status'] = 3;
                $save_refund ['remark'] = $remark;
                $save_refund ['update_time'] = $now;
                $where = array('id'=>$id);
                $re = $this->RefundModel->update($save_refund, $where);
                if ($re){
                    $this->add_sys_log($id, $save_refund);
                    $this->ajax_return(array('code'=>200, 'msg'=>'操作成功.'));
                }else {
                    $this->ajax_return(array('code'=>400, 'msg'=>'操作失败.'));
                }
            }elseif ($operation == 'remark'){//备注
                $save_refund ['remark'] = $remark;
                $save_refund ['update_time'] = $now;
                $where = array('id'=>$id);
                $re = $this->RefundModel->update($save_refund, $where);
                if ($re){
                    $this->add_sys_log($id, $save_refund);
                    $this->ajax_return(array('code'=>200, 'msg'=>'操作成功.'));
                }else {
                    $this->ajax_return(array('code'=>400, 'msg'=>'操作失败.'));
                }
            }
        }
    }
    
    private function wxRefund($fee,$transaction_id,$out_refund_no)
    {
        // 微信小程序支付退款业务处理
        $this->load->library('/weixin/Weixin_pay', array('type' => 'WXAPP'));
        $input = new WxPayRefund();
        $input->SetTransaction_id($transaction_id);
        $input->SetOut_refund_no($out_refund_no);
        $input->SetTotal_fee($fee);
        $input->SetRefund_fee($fee);
        $input->SetOp_user_id($this->config->item('MCHID'));
        // 申请退款接口
        try
        {
            $ref_order = WxPayApi::refund($input);
        }
        catch (Exception $e)
        {
            return ['code' => -103, 'msg' => '退款出错，请求微信服务器失败。'.$e->getMessage()];
        }
        if ($ref_order['result_code']!='SUCCESS'&&$ref_order['return_code']!='SUCCESS'){
            log_msg('WXAPP', 'DEBUG', '申请退款接口返回：'.json_encode($ref_order));
            $this->jump_error_page($ref_order['err_code_des']);
        }
        return $ref_order;
    }
    
    private function zfbRefund($info)
    {
        // 支付宝小程序支付退款业务处理
        $this->load->library('/alipay/Alipay_lib', array('type' => 'ALIAPP'));
        $order_info = array();
        $order_info['out_trade_no']  = $info['out_trade_no'];
        $order_info['trade_no']      = $info['transaction_id'];
        $order_info['refund_amount'] = $info['cash_fee'];
        $order_info['operator_id']   = $this->get_user_id();
        $order_info['goods_id']      = $info['product_id'];
        $order_info['quantity']      = 1;
        $order_info['price']         = $info['cash_fee'];
        $order_info['goods_name']    = $info['product_name'];
        $order_info['body']          = $info['describe'];
        // 统一收单交易退款接口
        $ref_order = $this->alipay_lib->alipayTradeRefund($order_info);
        if ($ref_order['code'] != 10000)
        {
            log_msg('ZFBAPP', 'DEBUG', '申请退款接口返回：'.$ref_order['sub_msg']);
            $this->jump_error_page($ref_order['sub_msg']);
        }
        return $ref_order;
    }
    
    private function cbRefund($info)
    {
        // 充币支付退款业务处理
        $this->load->model('MemberCurrencyRecordModel');
        $id  = $this->input->get('id');
        if ($id){
            $where = array('id'=>$info['order_id']);
            $order_info = $this->OrderModel->get_info($where);
            $this->load->model('MemberModel');
            $where = array('uuid'=>$order_info['uuid']);
            $member_info = $this->MemberModel->get_info($where);
            
            $this->load->model('MemberBalanceModel');
            $where = array('id'=>$member_info['balance_id']);
            $balance_info = $this->MemberBalanceModel->get_info($where);
            
            $this->db->trans_begin(); // 事务开始
            //充币交易记录
            $data = array();
            $data['uuid'] = $member_info['uuid'];
            $data['mobile'] = $member_info['mobile'];
            $data['trade_type'] = 3;
            $data['out_trade_no'] = $order_info['out_trade_no'];
            
            $data['c_currency_balance'] = $balance_info['currency_balance'];
            $data['c_currency_act_balance'] = $balance_info['currency_act_balance'];
            $data['c_currency_act_count'] = $balance_info['currency_act_balance'];
            $data['c_currency_act_gift_count'] = $balance_info['currency_act_gift_count'];
            $data['record_currency'] = $order_info['cash_fee']*100;
            $data['record_gift_currency'] = 0;
            $data['create_time'] = $info['now'];
            $this->MemberCurrencyRecordModel->add_data($data);
            $ref_order['refund_id'] = $this->db->insert_id();
            
            //更新用户充币记录
            $where = array('id'=>$member_info['balance_id']);
            $save = array();
            $save ['currency_balance'] = $balance_info['currency_balance']+($order_info['cash_fee']*100);
            $save ['currency_act_balance'] = $balance_info['currency_balance']+($order_info['cash_fee']*100);
            $save ['update_time'] = $info['now'];
            $this->MemberBalanceModel->update($save, $where);
            
            //改变订单状态
            $save_order = array();
            $save_order['complete_status'] = 3;
            $save_order['complete_time'] = $info['now'];
            $save_order['update_time'] = $info['now'];
            $this->OrderModel->update($save_order, $where=array('id'=>$info['order_id']));
            
            //改变退款申请状态
            $remark = $this->input->post('remark');
            $save_refund = array();
            $save_refund ['status'] = 1;
            $save_refund ['remark'] = $remark;
            $save_refund ['refund_id'] = $ref_order['refund_id']??'';
            $save_refund ['refund_time'] = $info['now'];
            $save_refund ['update_time'] = $info['now'];
            $this->RefundModel->update($save_refund, $where=array('id'=>$info['id']));
            
            if ($info['at_receive_id']){
                //修改优惠券状态
                $this->load->model('MemberActivityReceiveModel');
                $save_receive = array();
                $save_receive['receive_status'] = 0;
                $save_receive['update_time'] = $info['now'];
                $this->MemberActivityReceiveModel->update($save_receive, array('id'=>$info['at_receive_id']));
            }
            
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($info['id'], $data);
            
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE)
            {
                $this->db->trans_rollback();
                $this->ajax_return(array('code'=>200, 'msg'=>'退款失败.'));
            }
            else
            {
                $this->db->trans_commit();
                $this->ajax_return(array('code'=>200, 'msg'=>'退款成功.'));
            }
        }else {
            $order_info = $info;
            
            $this->load->model('MemberModel');
            $where = array('uuid'=>$order_info['uuid']);
            $member_info = $this->MemberModel->get_info($where);
            
            $this->load->model('MemberBalanceModel');
            $where = array('id'=>$member_info['balance_id']);
            $balance_info = $this->MemberBalanceModel->get_info($where);
            
            $this->db->trans_begin(); // 事务开始
            //充币交易记录
            $data = array();
            $data['uuid'] = $member_info['uuid'];
            $data['mobile'] = $member_info['mobile'];
            $data['trade_type'] = 3;
            $data['out_trade_no'] = $order_info['out_trade_no'];
            $data['c_currency_balance'] = $balance_info['currency_balance'];
            $data['c_currency_act_balance'] = $balance_info['currency_act_balance'];
            $data['c_currency_act_count'] = $balance_info['currency_act_count'];
            $data['c_currency_act_gift_count'] = $balance_info['currency_act_gift_count'];
            $data['record_currency'] = $order_info['cash_fee']*100;
            $data['record_gift_currency'] = 0;
            $data['create_time'] = $info['now'];
            $this->MemberCurrencyRecordModel->add_data($data);
            $ref_order['refund_id'] = $this->db->insert_id();
            
            //更新用户充币记录
            $where = array('id'=>$member_info['balance_id']);
            $save = array();
            $save ['currency_balance'] = $balance_info['currency_balance']+($order_info['cash_fee']*100);
            $save ['currency_act_balance'] = $balance_info['currency_act_balance']+($order_info['cash_fee']*100);
            $save ['update_time'] = $info['now'];
            $this->MemberBalanceModel->update($save, $where);
            
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE)
            {
                $this->db->trans_rollback();
            }
            else
            {
                $this->db->trans_commit();
            }
        }
    }
    
    
    /**
     * [generate_order_number 生成商户订单号: 订单前缀（1位数字）+3位毫秒+14位时间+9位随机数]
     *
     * @Author leeprince:2019-01-22T12:03:20+0800
     * @param  [type]                             $prefix [description]
     * @return [type]                                     [description]
     */
    function generateOrderNumber($k = 'WXAPPUU'): string
    {
        $prefix = $this->getOrderNumberPrefix($k);
        if ( ! $prefix) {
            return null;
        }
        
        if ($this->env_isnot_production()) {
            $prefix = 'test'.$prefix;
        }
        
        $time = [];
        $time[] = $prefix;
        $time[] = substr(microtime(), 2, 3);
        $time[] = date('YmdHis');
        $time[] = random_int(100000000, 999999999);
        return join('', $time);
    }
    
    /**
     * [getOrderNumberPrefix 生成密码的前缀]
     *
     * @Author leeprince:2019-01-22T14:16:14+0800
     * @param  [type]                             $k [description]
     * @return [type]                                [description]
     */
    function getOrderNumberPrefix($k): string
    {
        switch ($k) {
            case 'WXAPPUU':
                return 1;
                break;
            case 'WXPARTNER':
                return 2;
                break;
            case 'userRefund':
                return 3;
                break;
                
            default:
                return null;
                break;
        }
    }
}
