<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentWithdrawModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_withdraw';

	// 包含字段
	public static $columns = 'id,agent_id,agent_user_id,withdraw_type,commission_id,card_id,withdraw_trade_no,withdraw_transaction_id,pre_withdraw_amount,withdraw_amount,withdraw_card_amount,deduction_amount,withdraw_rate_amount,real_withdraw_amount,real_deduction_amount,is_have_invoice,invoice_status,invoice_file_id,invoice_num,invoice_company_name,invoice_amount,reject_reason_id,status,create_time,pay_time,update_time';
	
	/**
	 * [get_count 获得记录数]
	 *
	 * @DateTime 2019-06-21
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $res = $this->db->select('count(a_w.id) count')
	    ->from('agent_withdraw a_w')
	    ->where($where)
	    ->join('ffc_agent_commission a_c', 'a_w.commission_id=a_c.id', 'left')
	    ->join('ffc_agent a', 'a_c.agent_id=a.id', 'left')
	    ->join('ffc_agent_user a_u', 'a_c.agent_user_id=a_u.id', 'left')
	    ->get();
	    return (int)$res->row()->count;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-06-21
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where, $limit, $offset)
	{
	    $res = $this->db->select('a_w.id,a_w.pre_withdraw_amount,a_w.withdraw_amount,a_w.withdraw_card_amount,a_w.deduction_amount,a_w.withdraw_rate_amount,a_w.real_withdraw_amount,a_w.create_time,a_w.pay_time,a_w.status,a_w.invoice_status,a_w.invoice_file_id,a_w.is_have_invoice,a_c.agent_id,a_c.agent_user_id,a_c.c_commission_type,a_c.commission_type,a_c.commission_withdrawal_amount,a_c.commission_withdrawal_time,a.proxy_pattern,a.card_name')
	    ->from('agent_withdraw a_w')
	    ->where($where)
	    ->join('ffc_agent_commission a_c', 'a_w.commission_id=a_c.id', 'left')
	    ->join('ffc_agent a', 'a_c.agent_id=a.id', 'left')
	    ->join('ffc_agent_user a_u', 'a_c.agent_user_id=a_u.id', 'left')
	    ->order_by('a_w.id', 'ASC')
	    ->limit($limit, $offset)
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-06-21
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where)
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [withdrawStatistics 提现统计]
	 *
	 * @Author black.zhang
	 * @return [array]
	 */
	public function withdrawStatistics($where = array())
	{
	    if (!is_array($where)){
	        return;
	    }
	    $res = $this->db->select("sum(withdraw_amount) as withdraw_amount_statistics")
	    ->where($where)
	    ->get(self::$table_name);
	    return  $res->row_array();
	}
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-23
	 * @Author   black.zhang
	 * @return   [type]               [description]
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}

}