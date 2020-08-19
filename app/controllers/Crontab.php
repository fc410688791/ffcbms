<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crontab extends MY_Controller{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('OrderModel');
    }

    
    public function index()
    {
        exit();
    }
    
    //更新订单状态定时器,已支付订单3小时后自动完成
    public function completeOrder()
    {
        $now = time();
        $c_time = $now-10800;//3小时
        
        $save = array();
        $save ['complete_status'] = 1;
        $save ['complete_time'] = $now;
        $save ['update_time'] = $now;
        
        $where = array("pay_time <="=>$c_time, "status"=>1, "complete_status"=>0);
        
        $this->OrderModel->update($save, $where);
        exit();
    }
    
    //更新订单状态定时器,待支付订单变更为取消
    public function cancelOrder()
    {
        $now = time();
        $c_time = $now-900;//15分钟
        $save = array();
        $save ['complete_status'] = 1;
        $save ['complete_time'] = $now;
        $save ['update_time'] = $now;
        $save ['status'] = 2;
        
        $where = array("status"=>0, "create_time <="=>$c_time);

        $this->OrderModel->update($save, $where);
        exit();
    }
    
    //代理商收益统计
    public function statisticsAgentIncome()
    {
        exit();
        date_default_timezone_set('PRC');
        $now = time();
        $day = date('Y-m-d', $now);
        $d = date('d', $now);
        $daytime = strtotime($day);
        $start_time = $daytime-86400;
        $end_time = $daytime;
        $this->load->model('AgentModel');
        $this->load->model('RefundModel');
        $this->load->model('AgentSettlementModel');
        
        // 事务开始
        $this->db->trans_start();
        $where = array();
        $where['commission_status'] = 1;
        $agent_list = $this->AgentModel->get_user($where,'id,withdraw_cash_amount,total_income,commission_withdrawal_time,commission_type,commission_proportion,commission_time');
        foreach ($agent_list as $agent_info){
            if ($agent_info['commission_type']==1){//即时
                $u_start_time = $start_time;
                $u_end_time = $end_time;
            }elseif ($agent_info['commission_type']==2){//月结
                if ($d<$agent_info['commission_withdrawal_time']){
                    $start_day = date('Y-m',strtotime("-2 month")).'-'.$agent_info['commission_withdrawal_time'];
                    $end_day = date('Y-m',strtotime("-1 month")).'-'.$agent_info['commission_withdrawal_time'];
                }else {
                    $start_day = date('Y-m',strtotime("-1 month")).'-'.$agent_info['commission_withdrawal_time'];
                    $end_day = date('Y-m').'-'.$agent_info['commission_withdrawal_time'];
                }
                $u_start_time = strtotime($start_day);
                $u_end_time = strtotime($end_day);
            }
            $u_start_time = $agent_info['commission_time'];//开始分佣时间
            //收入
            $where = array();
            $where ['status'] = 1;
            $where ['is_settlement'] = 0;
            $where ['agent_id'] = $agent_info['id'];
            $where ['pay_time >='] = $u_start_time;
            $where ['pay_time <'] = $u_end_time;
            $agentIncomeStatistics = $this->OrderModel->orderStatistics($where);
            $cash_fee_statistics = $agentIncomeStatistics['cash_fee_statistics']+$agentIncomeStatistics['settlement_amount_statistics'];
            
            //退款
            $where = array();
            $where ['o.agent_id'] = $agent_info['id'];
            $where ['r.status'] = 1;
            $where ['r.update_time >='] = $u_start_time;
            $where ['r.update_time <'] = $u_end_time;
            $where ['r.is_settlement'] = 0;
            $agentIncomeStatistics = $this->RefundModel->statistics_agent_income($where);
            $refund_order_sum = $agentIncomeStatistics['refund_order_sum']+$agentIncomeStatistics['refund_settlement_amount'];
            
            if ($cash_fee_statistics){//标记订单
                $where = array();
                $where ['agent_id'] = $agent_info['id'];
                $where ['status'] = 1;
                $where ['pay_time >='] = $u_start_time;
                $where ['pay_time <'] = $u_end_time;
                $where ['is_settlement'] = 0;
                $save = array();
                $save ['is_settlement'] = 1;
                $save ['settlement_time'] = $now;
                $this->OrderModel->update($save, $where);
            }
            
            if ($refund_order_sum){//标记退款订单
                $where = array();
                $where ['ffc_order.agent_id'] = $agent_info['id'];
                $where ['ffc_refund.status'] = 1;
                $where ['ffc_refund.update_time >='] = $u_start_time;
                $where ['ffc_refund.update_time <'] = $u_end_time;
                $where ['ffc_refund.is_settlement'] = 0;
                $save = array();
                $save ['ffc_refund.is_settlement'] = 1;
                $save ['ffc_refund.settlement_time'] = $now;
                $this->RefundModel->settlement($save, $where);
            }

            $fee = round(($cash_fee_statistics-$refund_order_sum)*$agent_info['commission_proportion']*0.01, 2);
            if ($fee){
                $add = array();
                $add ['agent_id'] = $agent_info['id'];
                $add ['settlement_amount'] = $fee;  //结算金额
                $add ['curr_withdra_amount'] = $agent_info['withdraw_cash_amount'];  //结算前可提现金额
                $add ['create_time'] = $now;
                $this->AgentSettlementModel->add_data($add);
                
                $save_agent = array();
                $save_agent['update_time'] = $now;
                $save_agent['withdraw_cash_amount'] = round($agent_info['withdraw_cash_amount']+$fee, 2);
                $save_agent['total_income'] = round($agent_info['total_income']+$fee, 2);
                $this->AgentModel->update($save_agent, array('id'=>$agent_info['id']));
            }
        }
        // 事务提交
        $this->db->trans_complete();
        $this->load->model('sys_log_model');
        if ($this->db->trans_status() == FALSE)
        {
            $this->db->trans_rollback();
            $this->sys_log_model->add_log('crontab', 'statisticsAgentIncome', 'Crontab', $day, 'rollback');
        }
        else
        {
            $this->db->trans_commit();
            $this->sys_log_model->add_log('crontab', 'statisticsAgentIncome', 'Crontab', $day, 'commit');
        }
        echo $day;
        exit();
    }
    
    //收益统计
    public function statisticsIncome()
    {
        $now = time();
        $day = date('Y-m-d', $now);
        $d = date('d', $now);
        $daytime = strtotime($day);
        $start_time = $daytime-86400;
        $end_time = $daytime;
        $this->load->model('AgentCommissionModel');
        $this->load->model('AgentUserModel');
        $this->load->model('RefundModel');
        $this->load->model('AgentSettlementModel');
        $this->load->model('sys_log_model');
        
        $where = array();
        $where['commission_status'] = 1;
        $agent_commission_list = $this->AgentCommissionModel->get_list($where, 0, 0);
        foreach ($agent_commission_list as $info){
            if ($info['commission_type']==1){//即时
                $u_start_time = $start_time;
                $u_end_time = $end_time;
            }elseif ($info['commission_type']==2){//月结
                if ($d<$info['commission_withdrawal_time']){
                    $start_day = date('Y-m',strtotime("-2 month")).'-'.$info['commission_withdrawal_time'];
                    $end_day = date('Y-m',strtotime("-1 month")).'-'.$info['commission_withdrawal_time'];
                }else {
                    $start_day = date('Y-m',strtotime("-1 month")).'-'.$info['commission_withdrawal_time'];
                    $end_day = date('Y-m').'-'.$info['commission_withdrawal_time'];
                }
                $u_start_time = strtotime($start_day);
                $u_end_time = strtotime($end_day);
            }
            if ($info['commission_contract_end_time']>0&&$u_end_time>$info['commission_contract_end_time']){//合同结束
                $u_end_time = $info['commission_contract_end_time'];
            }
            $u_start_time = $info['commission_time'];//开始分佣时间
            
            // 事务开始
            $this->db->trans_start();
            $cash_fee_statistics = 0;
            $refund_order_sum = 0;
            if ($info['c_commission_type']==1){//代理商
                //收入
                $where = array();
                $where ['status'] = 1;
                $where ['is_settlement'] = 0;
                $where ['agent_id'] = $info['agent_id'];
                $where ['pay_time >='] = $u_start_time;
                $where ['pay_time <'] = $u_end_time;
                $agentIncomeStatistics = $this->OrderModel->orderStatistics($where);
                $cash_fee_statistics = $agentIncomeStatistics['cash_fee_statistics']+$agentIncomeStatistics['settlement_amount_statistics'];
                
                if ($cash_fee_statistics){//标记订单
                    $where = array();
                    $where ['agent_id'] = $info['agent_id'];
                    $where ['status'] = 1;
                    $where ['pay_time >='] = $u_start_time;
                    $where ['pay_time <'] = $u_end_time;
                    $where ['is_settlement'] = 0;
                    $save = array();
                    $save ['is_settlement'] = 1;
                    $save ['settlement_time'] = $now;
                    $this->OrderModel->update($save, $where);
                }
                
                //退款
                $where = array();
                $where ['o.agent_id'] = $info['agent_id'];
                $where ['r.status'] = 1;
                $where ['r.refund_time >='] = $u_start_time;
                $where ['r.refund_time <'] = $u_end_time;
                $where ['r.is_settlement'] = 0;
                $agentIncomeStatistics = $this->RefundModel->refundStatistics($where);
                $refund_order_sum = $agentIncomeStatistics['refund_order_sum']+$agentIncomeStatistics['refund_settlement_amount'];
                
                if ($refund_order_sum){//标记退款订单
                    $where = array();
                    $where ['ffc_order.agent_id'] = $info['agent_id'];
                    $where ['ffc_refund.status'] = 1;
                    $where ['ffc_refund.refund_time >='] = $u_start_time;
                    $where ['ffc_refund.refund_time <'] = $u_end_time;
                    $where ['ffc_refund.is_settlement'] = 0;
                    $save = array();
                    $save ['ffc_refund.is_settlement'] = 1;
                    $save ['ffc_refund.settlement_time'] = $now;
                    $this->RefundModel->settlement($save, $where);
                }
            }elseif ($info['c_commission_type']==2){//商户
                $agent_user_info = $this->AgentUserModel->get_info(array('id'=>$info['agent_user_id']));
                if ($agent_user_info['role_merchant_id']){
                    $role_merchant = explode(',', $agent_user_info['role_merchant_id']);
                    //收入
                    $where = array();
                    $where ['status'] = 1;
                    $where ['pay_time >='] = $u_start_time;
                    $where ['pay_time <'] = $u_end_time;
                    $where ['is_settlement'] = 0;
                    $where_in = array('field'=>'merchant_id','list'=>$role_merchant);
                    $agentIncomeStatistics = $this->OrderModel->orderStatistics($where, $where_in);
                    $cash_fee_statistics = $agentIncomeStatistics['cash_fee_statistics']+$agentIncomeStatistics['settlement_amount_statistics'];
                    
                    if ($cash_fee_statistics){//标记订单
                        $where = array();
                        $where ['status'] = 1;
                        $where ['pay_time >='] = $u_start_time;
                        $where ['pay_time <'] = $u_end_time;
                        $where ['is_settlement'] = 0;
                        $save = array();
                        $save ['is_settlement'] = 1;
                        $save ['settlement_time'] = $now;
                        $this->db->where($where);
                        $this->db->where_in('merchant_id', $role_merchant);
                        $this->db->update('order', $save);
                    }
                    
                    //退款
                    $where = array();
                    $where ['r.status'] = 1;
                    $where ['r.refund_time >='] = $u_start_time;
                    $where ['r.refund_time <'] = $u_end_time;
                    $where ['r.is_settlement'] = 0;
                    $where_in = array('field'=>'o.merchant_id','list'=>$role_merchant);
                    $agentIncomeStatistics = $this->RefundModel->refundStatistics($where, $where_in);
                    $refund_order_sum = $agentIncomeStatistics['refund_order_sum']+$agentIncomeStatistics['refund_settlement_amount'];
                    
                    if ($refund_order_sum){//标记退款订单
                        $where = array();
                        $where ['ffc_refund.status'] = 1;
                        $where ['ffc_refund.refund_time >='] = $u_start_time;
                        $where ['ffc_refund.refund_time <'] = $u_end_time;
                        $where ['ffc_refund.is_settlement'] = 0;
                        $save = array();
                        $save ['ffc_refund.is_settlement'] = 1;
                        $save ['ffc_refund.settlement_time'] = $now;
                        $this->db->where($where);
                        $this->db->where_in('ffc_order.merchant_id', $role_merchant);
                        $this->db->update('(ffc_refund join ffc_order on ffc_refund.order_id = ffc_order.id)', $save);
                    }
                }
            }
            
            $fee = round(($cash_fee_statistics-$refund_order_sum)*$info['commission_proportion']*0.01, 2);
            if ($fee){
                $add = array();
                $add ['agent_id'] = $info['agent_id'];
                if($info['c_commission_type']==1){//代理商
                    $add ['agent_user_id'] = 0;
                }elseif ($info['c_commission_type']==2){//商户
                    $add ['agent_user_id'] = $info['agent_user_id'];
                }
                $add ['settlement_type'] = $info['c_commission_type'];
                $add ['settlement_amount'] = $fee;  //结算金额
                $add ['curr_withdra_amount'] = $info['withdraw_cash_amount'];  //结算前可提现金额
                $add ['create_time'] = $now;
                $this->AgentSettlementModel->add_data($add);
                
                $save = array();
                $save['update_time'] = $now;
                $save['withdraw_cash_amount'] = round($info['withdraw_cash_amount']+$fee, 2);
                $save['total_income'] = round($info['total_income']+$fee, 2);
                $this->AgentCommissionModel->update($save, array('id'=>$info['id']));
            }
            
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE)
            {
                $this->sys_log_model->add_log('crontab', __FUNCTION__, 'Crontab', $day, json_encode(array('status'=>'rollback','info'=>$info)));
            }
            else
            {
                $this->sys_log_model->add_log('crontab', __FUNCTION__, 'Crontab', $day, json_encode(array('status'=>'commit','info'=>$info)));
            }
        }
        echo $day;
        exit();
    }
    
    //运营分析-页面统计
    public function statisticsPage()
    {
        $this->load->model('AnalysisUserModel');
        $this->load->model('AnalysisStatisticalPageModel');
        $this->load->model('sys_log_model');
        $now = time();
        $day = date('Y-m-d', $now);
        $daytime = strtotime($day);
        $end_time = $daytime;

        $last = $this->AnalysisStatisticalPageModel->get_info();
        if ($last){
            $start_time = $last['statistical_time']+86400;
        }else {
            $start_time = $daytime-86400;
        }
        
        for ($i=$start_time;$i<$end_time;){
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $where ['is_page_type_mark'] = 0;
            $select = 'page_type_id,count(id) as access_count,sum(stay_time_count) as stay_count_time';
            $list = $this->AnalysisUserModel->statistics_page($select, $where);
            foreach ($list as &$info){
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['page_type_id'] = $info['page_type_id'];
                $info['access_user_count'] = count($this->AnalysisUserModel->get_group_count($where, 'uuid'));
                
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['page_type_id'] = $info['page_type_id'];
                $where ['access_type'] = 1;
                $info['entry_count'] = $this->AnalysisUserModel->get_count($where);
                
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['page_type_id'] = $info['page_type_id'];
                $where ['is_exit'] = 1;
                $info['exit_count'] = $this->AnalysisUserModel->get_count($where);
                
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['page_type_id'] = $info['page_type_id'];
                $where ['is_share'] = 1;
                $info['share_count'] = $this->AnalysisUserModel->get_count($where);
                
                $info['statistical_time'] = $i;
                
                $info['create_time'] = $now;
            }
            
            // 事务开始
            $this->db->trans_start();
            
            if ($list) {
                //写入统计表
                $this->db->insert_batch('analysis_statistical_page', $list);
                //改变统计状态
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['is_page_type_mark'] = 0;
                $this->AnalysisUserModel->update(array('is_page_type_mark'=>1), $where);
            }
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE)
            {
                $this->sys_log_model->add_log('crontab', __FUNCTION__, 'Crontab', $day, 'rollback');
            }
            else
            {
                $this->sys_log_model->add_log('crontab', __FUNCTION__, 'Crontab', $day, 'commit');
            }
            $i += 86400;
        }
        echo $day;
        exit();
    }
    
    //更新优惠券状态定时器
    public function cancelActivityCard()
    {
        $this->load->model('MemberActivityCardModel');
        $this->load->model('MemberActivityReceiveModel');
        $now = time();
        //更新活动卡券表
        $save = array();
        $save ['update_time'] = $now;
        $save ['is_show'] = 0;
        $where = array();
        $where['is_show'] = 1;
        $where['end_time !='] = 0;
        $where['end_time <'] = $now;
        $this->MemberActivityCardModel->update($save, $where);
        //更新活动卡券领取表
        $save = array();
        $save ['update_time'] = $now;
        $save ['receive_status'] = 2;
        $where = array();
        $where['receive_status'] = 0;
        $where['end_time !='] = 0;
        $where['end_time <'] = $now;
        $this->MemberActivityReceiveModel->update($save, $where);
        exit();
    }
}