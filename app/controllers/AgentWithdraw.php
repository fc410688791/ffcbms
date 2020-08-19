<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentWithdraw extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgentWithdrawModel');
        $this->load->model('FileModel');
        $this->load->model('AgentCommissionModel');
        $this->load->model('AgentModel');
        $this->load->model('AgentUserModel');
        $this->load->model('AgentCardModel');
    }

    /**
     * [index 代理商提现列表]
     *
     * @DateTime 2019-06-21
     * @Author   breite
     * @return   [type]     [description]
     */
    public function index()
    {
        $page           = $this->input->get('per_page')?:1;
        $limit          = $this->config->item('per_page');
        $offset         = ($page-1)*$limit;
        
        $key              = $this->input->get('key');
        $reservation      = $this->input->get('reservation');
        $status           = $this->input->get('status')??99;
        $invoice_status   = $this->input->get('invoice_status')??99;
        $proxy_pattern    = $this->input->get('proxy_pattern');
        $commission_type  = $this->input->get('commission_type');
        $where          = array();
        if ($key){
            $where = "a.user_name = '$key' or a.card_name = '$key' or a.mobile = '$key' or a_w.agent_id = '$key'";
            $this->_data['key'] = $key;
        }else {
            if ($reservation){
                list($start_time, $end_time) = switch_reservation($reservation);
                $where['a_w.create_time >='] = $start_time;
                $where['a_w.create_time <'] = $end_time;
                $this->_data['reservation'] = $reservation;
            }
            if ($status!=99){
                $where ['a_w.status'] = $status;
                $this->_data['status'] = $status;
            }
            if ($invoice_status!=99){
                $where ['a_w.invoice_status'] = $invoice_status;
                $this->_data['invoice_status'] = $invoice_status;
            }
            if ($proxy_pattern){
                if ($proxy_pattern==4){
                    $where ['a_c.c_commission_type'] = 2;  //子商户
                }else {
                    $where ['a.proxy_pattern'] = $proxy_pattern;
                    $where ['a_c.c_commission_type'] = 1;  //子商户
                }
                $this->_data['proxy_pattern'] = $proxy_pattern;
            }
            if ($commission_type){
                $where ['a.commission_type'] = $commission_type;
                $this->_data['commission_type'] = $commission_type;
            }
        }
        $total_rows = $this->AgentWithdrawModel->get_count($where);
        $list = $this->AgentWithdrawModel->get_list($where, $limit, $offset);
        foreach($list as &$info){
            if ($info['c_commission_type']==1){
                if ($info['proxy_pattern']==1){
                    $info['proxy_pattern'] = '普通代理';
                }elseif ($info['proxy_pattern']==2){
                    $info['proxy_pattern'] = '内部自营';
                }elseif ($info['proxy_pattern']==3){
                    $info['proxy_pattern'] = '0元代理';
                }
            }elseif ($info['c_commission_type']==2){
                $info['proxy_pattern'] = '商户';
            }
            if ($info['commission_type']==1){
                $info['commission_type'] = '即时';
            }elseif ($info['commission_type']==2){
                $info['commission_type'] = '月结';
            }
            if ($info['status']==0){
                $info['status_name'] = '待提现';
                $info['status_name_color'] = 'black';
            }elseif ($info['status']==1){
                $info['status_name'] = '提现中';
                $info['status_name_color'] = 'yellow';
            }elseif ($info['status']==2){
                $info['status_name'] = '提现成功';
                $info['status_name_color'] = 'orange';
            }elseif ($info['status']==3){
                $info['status_name'] = '提现失败';
                $info['status_name_color'] = 'red';
            }elseif ($info['status']==4){
                $info['status_name'] = '驳回申请';
                $info['status_name_color'] = 'red';
            }elseif ($info['status']==5){
                $info['status_name'] = '审核通过';
                $info['status_name_color'] = 'green';
            }else {
                $info['status_name'] = $info['status'];
                $info['status_name_color'] = 'blue';
            }
            if ($info['invoice_status']==0){
                $info['invoice_status_name'] = '无发票';
                $info['invoice_status_name_color'] = 'black';
            }elseif ($info['invoice_status']==1){
                $info['invoice_status_name'] = '待确认';
                $info['invoice_status_name_color'] = 'black';
            }elseif ($info['invoice_status']==2){
                $info['invoice_status_name'] = '确认正确';
                $info['invoice_status_name_color'] = 'green';
            }elseif ($info['invoice_status']==3){
                $info['invoice_status_name'] = '确认错误';
                $info['invoice_status_name_color'] = 'red';
            }
            $info ['create_time'] = date("Y-m-d H:i:s", $info ['create_time']);
            $info ['pay_time'] = $info ['pay_time']?date("Y-m-d H:i:s", $info ['pay_time']):'-';
            if ($info ['invoice_file_id']){
                $info ['invoice_img_url'] = $this->FileModel->get_url(array('id'=>$info ['invoice_file_id']));
            }
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('agent_withdraw/index', $this->_data);
    }
    
    /**
     * [withdraw 确认提现到银行卡]
     *
     * @Author zm:2019-07-05T14:36:13+0800
     * @return [type]                             [description]
     */
    public function withdraw()
    {
        $now = time();
        $id = $this->input->get('id');
        if (!$id){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        //提现信息
        $withdraw_info = $this->AgentWithdrawModel->get_info(array('id'=>$id));
        if (!$withdraw_info){
            $this->ajax_return(array('code'=>400, 'msg'=>'未找到提现信息.'));
        }
        //分佣信息
        $commission_info = $this->AgentCommissionModel->get_info(array('id'=>$withdraw_info['commission_id']));
        if (!$commission_info){
            $this->ajax_return(array('code'=>400, 'msg'=>'未找到分佣信息.'));
        }
        //账户信息
        if ($commission_info['c_commission_type']==1){//代理商
            $info = $this->AgentModel->get_info(array('id'=>$commission_info['agent_id']));
        }elseif ($commission_info['c_commission_type']==2){//商户
            $info = $this->AgentUserModel->get_info(array('id'=>$commission_info['agent_user_id']));
        }
        if (!$info){
            $this->ajax_return(array('code'=>400, 'msg'=>'未找到账户信息.'));
        }
        //银行卡信息
        $card_info = $this->AgentCardModel->get_info(array('id'=>$withdraw_info['card_id']));
        if (!$card_info){
            $this->ajax_return(array('code'=>400, 'msg'=>'未找到银行卡信息.'));
        }
        
        if ($withdraw_info['status']==2){
            $this->ajax_return(array('code'=>400, 'msg'=>'提现订单状态错误.'));
        }
        /* if ($withdraw_info['withdraw_amount']<$commission_info['commission_withdrawal_amount']){
            $this->ajax_return(array('code'=>400, 'msg'=>'提现金额小于提现阈值.'));
        } */
        if ($withdraw_info['withdraw_amount']>$commission_info['withdraw_cash_amount']){
            $this->ajax_return(array('code'=>400, 'msg'=>'提现金额超出可提现金额.'));
        }
        /* 
        //微信手续费
        $withdraw_card_amount = round($withdraw_info['withdraw_amount']*0.006, 2);
        //税
        if ($info['is_have_invoice']==1){
            $deduction_amount = 0;
        }else {
            $deduction_amount = round(($withdraw_info['withdraw_amount']-$withdraw_card_amount)/1.06*0.06*1.12, 2);
        }
        //提现手续费
        $withdraw_rate_amount = round(($withdraw_info['withdraw_amount']-$withdraw_card_amount-$deduction_amount)*0.001/1.001, 2);
        if ($withdraw_rate_amount<1){
            $withdraw_rate_amount = 1;
        }elseif ($withdraw_rate_amount>25){
            $withdraw_rate_amount = 25;
        }
        //实际到账
        $real_withdraw_amount = round($withdraw_info['withdraw_amount']-$withdraw_card_amount-$deduction_amount-$withdraw_rate_amount, 2);
         */
        //微信转账到银行卡
        $this->load->library('/weixin/Weixin_pay', array('type' => 'WXAPP_PARTNER'));
        $input = new WxPayBank();
        $input->SetPartnerTradeNo($withdraw_info['withdraw_trade_no']);
        $input->SetEncBankNo($card_info['card_no']);
        $input->SetEncTrueName($card_info['name']);
        $input->SetBankCode($card_info['card_mark']);
        $input->SetAmount($withdraw_info['real_withdraw_amount']*100);
        try
        {
            $re = WxPayApi::payBank($input);
        }
        catch (Exception $e)
        {
            return ['code' => -103, 'msg' => '退款出错，请求微信服务器失败。'.$e->getMessage()];
        }
        if ($re['return_code']=='SUCCESS'&&$re['result_code']=='SUCCESS'){
            //事务开始
            $this->load->model('AgentModel');
            $this->db->trans_start();
            
            //修改提现表
            $where = array();
            $where ['id'] = $id;
            $where ['status <>'] = 2;
            $save = array();
            $save ['status'] = 2;
            $save ['withdraw_transaction_id'] = $re['payment_no'];//微信返回：微信企业付款单号
            $save ['real_deduction_amount']   = $re['cmms_amt']*0.01;//微信返回：手续费金额RMB(分)
            $save ['pay_time']                = $now;
            $save ['update_time']             = $now;
            $this->AgentWithdrawModel->update($save, $where);
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($id, $save);
            
            //修改分佣表
            $where = array();
            $where ['id'] = $withdraw_info['commission_id'];
            $withdraw_cash_amount = $commission_info['withdraw_cash_amount'] - $withdraw_info['withdraw_amount'];
            $save = array();
            $save ['withdraw_cash_amount'] = $withdraw_cash_amount;
            $save ['update_time'] = $now;
            $this->AgentCommissionModel->update($save, $where);
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($info['agent_id'], $save);
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE)
            {
                $this->db->trans_rollback();
                $this->ajax_return(array('code'=>400, 'msg'=>'处理失败.'));
            }
            else
            {
                $this->db->trans_commit();
                $this->ajax_return(array('code'=>200, 'msg'=>'处理成功.'));
            }
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>$re['err_code_des']));
        }
    }
    
    /**
     * [refuseWithdraw 拒绝提现]
     *
     * @DateTime 2019-07-09
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function refuseWithdraw()
    {
        $id = $this->input->get('id');
        $content = $this->input->post('content');
        $now = time();
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentWithdrawModel->get_info($where);
        $this->load->model('ReasonModel');
        $data = array();
        $data ['type'] = 2;
        if ($info['withdraw_type']==1){
            $data ['uid'] = $info['agent_id'];
        }elseif ($info['withdraw_type']==2){
            $data ['uid'] = $info['agent_user_id'];
        }
        $data ['content'] = $content;
        $data ['create_time'] = $now;
        // 事务开始
        $this->db->trans_start();
        
        $this->ReasonModel->add_data($data);
        
        $reject_reason_id = $this->db->insert_id();
        $save = array();
        $save ['status'] = 4;//驳回申请
        $save ['reject_reason_id'] = $reject_reason_id;
        $save ['update_time'] = $now;
        $where = array();
        $where ['id'] = $id;
        $this->AgentWithdrawModel->update($save, $where);
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'失败,写入数据错误.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'操作成功.'));
        }
    }
    
    /**
     * [examine 账单过审]
     *
     * @DateTime 2019-10-15
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function examine()
    {
        $id = $this->input->get('id');
        $now = time();
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentWithdrawModel->get_info($where);
        
        // 事务开始
        $this->db->trans_start();
        
        $save = array();
        $save ['status'] = 5;//审核通过待提现
        $save ['update_time'] = $now;
        $where = array();
        $where ['id'] = $id;
        $this->AgentWithdrawModel->update($save, $where);
        
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->ajax_return(array('code'=>400, 'msg'=>'失败,写入数据错误.'));
        }
        else
        {
            $this->db->trans_commit();
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'操作成功.'));
        }
    }
    
    /**
     * [info 详情]
     *
     * @DateTime 2019-07-10
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function info()
    {
        $this->load->model('ReasonModel');
        $id = $this->input->get('id');
        $where = array();
        $where ['id'] = $id;
        $info = $this->AgentWithdrawModel->get_info($where);
        if ($info['reject_reason_id']){
            $reason_info = $this->ReasonModel->get_info(array('id'=>$info['reject_reason_id']));
            $info['reason_content'] = $reason_info['content'];
        }
        $card_info = $this->AgentCardModel->get_info(array('id'=>$info['card_id']));
        $info['card_name'] = $card_info['card_name'];
        $info['card_no'] = $card_info['card_no'];
        $this->ajax_return(array('code'=>200, 'data'=>$info));
    }
    
    /**
     * [invoice 发票确认]
     *
     * @DateTime 2019-07-10
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function invoice()
    {
        $id = $this->input->get('id');
        $invoice_status = $this->input->post('invoice_status');
        $now = time();
        $where = array();
        $where ['a_w.id'] = $id;
        $info = $this->AgentWithdrawModel->get_info($where);
        $save ['invoice_status'] = $invoice_status;
        $save ['update_time'] = $now;
        $where = array();
        $where ['id'] = $id;
        $re = $this->AgentWithdrawModel->update($save, $where);
        if ($re)
        {
            $this->add_sys_log($id, $save);
            $this->ajax_return(array('code'=>200, 'msg'=>'操作成功.'));
        }
        else
        {
            $this->ajax_return(array('code'=>400, 'msg'=>'写入失败'));
        }
    }
    
    /**
     * [monthBill  月账单]
     *
     * @DateTime 2019-10-14
     * @Author   black.zhang
     */
    public function monthBill()
    {
        $operation = $this->input->get('operation');
        $agent_id = $this->input->get('agent_id');
        if (!$agent_id){
            exit();
        }
        $where = array();
        $where ['id'] = $agent_id;
        $agent_info = $this->AgentModel->get_info($where);
        $commission_info = $this->AgentCommissionModel->get_info(array('agent_id'=>$agent_id));
        $agent_info ['commission_time'] = $commission_info['commission_time'];
        $agent_info ['commission_proportion'] = $commission_info['commission_proportion'];
        
        $this->load->model('OrderModel');
        $this->load->model('RefundModel');
        $start_time = $agent_info['commission_time'];
        $end_time = time();
        $data = array();
        for ($i=$end_time;$i>$start_time;){
            $info = array();
            $day = date('Y-m', $i);
            $s = strtotime($day);
            $e = $i+1;
            $info ['day'] = $day;
            $info ['start_time'] = $s;
            $info ['end_time'] = $e;
            //收入
            $where = array();
            $where ['status'] = 1;
            $where ['agent_id'] = $agent_id;
            $where ['pay_time >='] = $s;
            $where ['pay_time <'] = $e;
            $agentIncomeStatistics1 = $this->OrderModel->orderStatistics($where);
            $order_statistics = $agentIncomeStatistics1['cash_fee_statistics']+$agentIncomeStatistics1['settlement_amount_statistics'];
            
            //退款
            $where = array();
            $where ['o.agent_id'] = $agent_id;
            $where ['r.status'] = 1;
            $where ['r.refund_time >='] = $s;
            $where ['r.refund_time <'] = $e;
            $agentIncomeStatistics2 = $this->RefundModel->refundStatistics($where);
            $refund_statistics = $agentIncomeStatistics2['refund_order_sum']+$agentIncomeStatistics2['refund_settlement_amount'];
            
            $info ['income'] = round($order_statistics-$refund_statistics, 2);
            $info ['a'] = round($info ['income']*$agent_info['commission_proportion']/100, 2);
            $info ['b'] = round($info ['a']*0.006, 2);
            $info ['c'] = round(($info ['a']-$info ['b'])/1.06*0.06, 2);
            $info ['d'] = round($info ['c']*0.07, 2);
            $info ['e'] = round($info ['c']*0.03, 2);
            $info ['f'] = round($info ['c']*0.02, 2);
            $info ['g'] = round(($info ['a']-$info ['b']-$info ['c']-$info ['d']-$info ['e']-$info ['f'])/1.001*0.001, 2);
            $info ['h'] = round(($info ['a']-$info ['b']-$info ['c']-$info ['d']-$info ['e']-$info ['f']-$info ['g']), 2);
            $data [] = $info;
            $i = $s-1;
        }
        if ($operation=='download'){
            $this->load->library('phpexcel/PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','月份')
            ->setCellValue('B1','月流水')
            ->setCellValue('C1','可提现金额'.$agent_info['commission_proportion'].'%')
            ->setCellValue('D1','手续费0.6%')
            ->setCellValue('E1','增值税6%')
            ->setCellValue('F1','城建税7%')
            ->setCellValue('G1','教育税附加3%')
            ->setCellValue('H1','地方教育税附加2%')
            ->setCellValue('I1','提现手续费0.1%')
            ->setCellValue('J1','实际提现金额')
            ->setCellValue('K1','代理商姓名');
            $pCoordinate = 2;
            foreach ($data as $key=>$value){
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+$pCoordinate), $value['day'])
                ->setCellValue('B'.($key+$pCoordinate), $value['income'])
                ->setCellValue('C'.($key+$pCoordinate), $value['a'])
                ->setCellValue('D'.($key+$pCoordinate), $value['b'])
                ->setCellValue('E'.($key+$pCoordinate), $value['c'])
                ->setCellValue('F'.($key+$pCoordinate), $value['d'])
                ->setCellValue('G'.($key+$pCoordinate), $value['e'])
                ->setCellValue('H'.($key+$pCoordinate), $value['f'])
                ->setCellValue('I'.($key+$pCoordinate), $value['g'])
                ->setCellValue('J'.($key+$pCoordinate), $value['h'])
                ->setCellValue('K'.($key+$pCoordinate), $agent_info['card_name']);
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$agent_info['card_name'].'月账单.xlsx');
            header('Cache-Control: max-age=0');
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }else {
            $this->_data['agent_info'] = $agent_info;
            $this->_data['commission_info'] = $commission_info;
            $this->_data['list'] = $data;
            $this->template->admin_render('agent_withdraw/monthly_bill.php', $this->_data);
        }
    }
    
    /**
     * [dayBill  日账单]
     *
     * @DateTime 2019-10-14
     * @Author   black.zhang
     */
    public function dayBill ()
    {
        $operation = $this->input->get('operation');
        $agent_id = $this->input->get('agent_id');
        $start_time = $this->input->get('start_time');
        $end_time = $this->input->get('end_time');
        if (!$agent_id||!$start_time||!$end_time){
            exit();
        }
        $where = array();
        $where ['id'] = $agent_id;
        $agent_info = $this->AgentModel->get_info($where);
        $commission_info = $this->AgentCommissionModel->get_info(array('agent_id'=>$agent_id));
        $agent_info ['commission_time'] = $commission_info['commission_time'];
        $agent_info ['commission_proportion'] = $commission_info['commission_proportion'];
        
        $this->load->model('OrderModel');
        $this->load->model('RefundModel');
        $data = array();
        for ($i=$end_time;$i>$start_time;){
            $info = array();
            $day = date('Y-m-d', $i-1);
            $s = strtotime($day);
            $e = $i;
            $info ['day'] = $day;
            $info ['start_time'] = $s;
            $info ['end_time'] = $e;
            //收入
            $where = array();
            $where ['status'] = 1;
            $where ['agent_id'] = $agent_id;
            $where ['pay_time >='] = $s;
            $where ['pay_time <'] = $e;
            $agentIncomeStatistics1 = $this->OrderModel->orderStatistics($where);
            $order_statistics = $agentIncomeStatistics1['cash_fee_statistics']+$agentIncomeStatistics1['settlement_amount_statistics'];
            
            //退款
            $where = array();
            $where ['o.agent_id'] = $agent_id;
            $where ['r.status'] = 1;
            $where ['r.refund_time >='] = $s;
            $where ['r.refund_time <'] = $e;
            $agentIncomeStatistics2 = $this->RefundModel->refundStatistics($where);
            $refund_statistics = $agentIncomeStatistics2['refund_order_sum']+$agentIncomeStatistics2['refund_settlement_amount'];
            
            $info ['income'] = round($order_statistics-$refund_statistics, 2);
            $info ['settlement'] = round($info ['income']*$agent_info['commission_proportion']/100, 2);
            $data [] = $info;
            $i = $s;
        }
        
        if ($operation=='download'){
            $this->load->library('phpexcel/PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','日期')
            ->setCellValue('B1','流水')
            ->setCellValue('C1','可提现金额')
            ->setCellValue('D1','代理商姓名');
            $pCoordinate = 2;
            foreach ($data as $key=>$value){
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+$pCoordinate), $value['day'])
                ->setCellValue('B'.($key+$pCoordinate), $value['income'])
                ->setCellValue('C'.($key+$pCoordinate), $value['settlement'])
                ->setCellValue('D'.($key+$pCoordinate), $agent_info['card_name']);
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$agent_info['card_name'].'日账单.xlsx');
            header('Cache-Control: max-age=0');
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }else {
            $this->_data['start_time'] = $start_time;
            $this->_data['end_time'] = $end_time;
            $this->_data['agent_info'] = $agent_info;
            $this->_data['list'] = $data;
            $this->template->admin_render('agent_withdraw/daily_bill.php', $this->_data);
        }
    }
    
}
