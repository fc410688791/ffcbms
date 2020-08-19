<?php

/**
 *
 */
class PayNotifyCallBack extends WxPayNotify {
    //查询订单
    public function Queryorder($transaction_id) {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        
        $result = WxPayApi::orderQuery($input);
        
        log_msg('PayNotify_Queryorder', 'debug', '[wx_log]Queryorder: ' . json_encode($result));
        
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return true;
        }
        return false;
    }
    
    //重写回调处理函数
    
    /**
     * [NotifyProcess 支付完成后，微信会把相关支付结果和用户信息发送给商户，商户需要接收处理，并返回应答。
     *
     * 对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，微信会通过一定的策略定期重新发起通知，尽可能提高通知的成功率，但微信不保证通知最终能成功。 （通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒）
     * 注意：同样的通知可能会多次发送给商户系统。商户系统必须能够正确处理重复的通知。
     * 推荐的做法是，当收到通知进行处理时，首先检查对应业务数据的状态，判断该通知是否已经处理过，如果没有处理过再进行处理，如果处理过直接返回结果成功。在对业务数据进行状态检查和处理之前，要采用数据锁进行并发控制，以避免函数重入造成的数据混乱。
     * 特别提醒：商户系统对于支付结果通知的内容一定要做签名验证,并校验返回的订单金额是否与商户侧的订单金额一致，防止数据泄漏导致出现“假通知”，造成资金损失。 ]
     *
     * @DateTime 2017-10-18
     * @Author   leeprince
     * @param    [type]     $data [description]
     * @param    [type]     &$msg [description]
     */
    public function NotifyProcess($data, &$msg) {
        log_msg('PayNotify_NotifyProcess', 'debug', '[wx_log] 回调开始: ' . json_encode($data));
        
        $notfiyOutput = array();
        
        if (!array_key_exists("appid", $data)
            OR !array_key_exists("mch_id", $data)
            OR !array_key_exists("transaction_id", $data)
            OR !array_key_exists("out_trade_no", $data)
            OR !array_key_exists("total_fee", $data)
        ) {$msg = '回调输入参数不正确';log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return false;}
        
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {$msg='回调微信订单查询失败';log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return false;}
        
        $appid = $data['appid'];
        $mch_id = $data['mch_id'];
        $transaction_id = $data['transaction_id'];
        $out_trade_no = $data['out_trade_no'];
        $pay_time = strtotime($data['time_end']);
        $total_fee = $data['total_fee'] / 100;//订单金额
        $cash_fee = $data['cash_fee'] / 100;//现金支付金额
        
        if (($appid != Weixin_pay::$_config['APPID']) OR ($mch_id != Weixin_pay::$_config['MCHID'])) {$msg = '商户信息不匹配'; log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return false;}
        
        // 业务逻辑
        $this->_CI->load->model('api/orders_model');
        $order_info = $this->_CI->orders_model->get_order_pro_id($out_trade_no, 'out_trade_no');
    
        if (empty($order_info)) {$msg = '商户订单信息不匹配'; log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.msg);return false;}
        // 订单处理过的, 直接返回
        if ($order_info['status'] == '支付成功') {$msg='订单已成功处理过';log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return true;}
        // 订单金额
        $pro_price = $order_info['price'];
        $order_id = $order_info['id'];
        // 订单金额不对
        if ($pro_price != $total_fee) {$msg='商品价格不匹配';log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return false;}
        //更新订单
        $update_array = array('transaction_id' => $transaction_id, 'cash_fee' => $cash_fee, 'status' => '支付成功', 'pay_time' => $pay_time,);
        $where_array = array('id' => $order_id);
        $res = $this->_CI->orders_model->updata_order($update_array, $where_array);
        
        if (!$res) {$msg = '商户订单更新失败';return false;}
        
        $msg = '商户订单更新成功';
        log_msg('PayNotify_NotifyProcess', 'debug', '[wx_log] '.$msg);
        
        //启动设备
        $this->_CI->load->library('RedisDB');
        $redis_conn = $this->_CI->redisdb->connect();
        $this->_CI->load->library('UdpFunc');
        $macid = udpid($order_info['device_id']);
        $heart_data = $redis_conn->get($macid);
        if($heart_data){
            $res_order = $this->_CI->orders_model->get_order_by_orderid($order_info['out_trade_no']);
            $this->_CI->load->model('api/products_model');
            $res_produc = $this->_CI->products_model->get_product($res_order['product_id']);
            if (!$res_produc) {$msg = '商品匹配失败';log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return false;}
            //计算时间
            $tnow = time();
            $ptime = $res_produc['pd_time'] * 60;
            $usetime = $res_order['total_time'] + ($res_order['begin_time'] ? $tnow - $res_order['begin_time'] : 0);
            $lefttime = $ptime - $usetime;
            $usblefttime = $res_produc['give_usb_time'] * 60 - $usetime;
            //组合启动指令
            $actCmd = null;
            // 商品是按摩
            if ($res_produc['prop_id'] == 1) {$actCmd = [UDP_CMDID_AMY => $lefttime, UDP_CMDID_USB => $usblefttime];}
            // 商品是充电
            elseif ($res_produc['prop_id'] == 2) {$actCmd = [UDP_CMDID_USB => $lefttime];}
            $res_udp = $this->_CI->udpfunc->active($order_info['out_trade_no'], $actCmd);
            if(!$res_udp){$msg = '通过UDP启动设备失败';log_msg('PayNotify_NotifyProcess', 'ERROR', '[wx_log] '.$msg);return false;}
        }
        
        log_msg('PayNotify_NotifyProcess', 'debug', '[wx_log] 回调结束');
        return true;
    }
}