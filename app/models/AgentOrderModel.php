<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentOrderModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_order';

	// 包含字段
	public static $columns = 'id,purchase_trade_no,purchase_transaction_id,agent_id,product_id,type,purchase_num,cash_fee,price,address_id,logistics_id,pay_client_type,pay_type,status,is_confirm,create_time,pay_time,receive_time,update_time';

	/**
	 * [get_list_count 获取数据条数]
	 *
	 * @DateTime 2019-03-22
	 * @Author   black
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list_count($where = array())
	{
		$res = $this->db->select('count(a_o.id) count')
		->from("ffc_agent_order as a_o")
		->join("ffc_agent_product as a_p", "a_o.product_id = a_p.id", 'LEFT')
		->join("ffc_agent_address as a_a", "a_o.address_id = a_a.id", 'LEFT')
		->join("ffc_agent as a", "a_o.agent_id = a.id")
		->join("ffc_position as p", "a_a.position_id = p.id", 'LEFT')
		->join("ffc_logistics as l", "a_o.logistics_id = l.id", 'LEFT')
		->where($where)
		->get();

		return $res->row()->count;
	}
	
	/**
	 * [get_fee_sum]
	 *
	 * @DateTime 2019-03-22
	 * @Author   black
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_fee_sum($where = array())
	{
	    $res = $this->db->select('sum(a_o.cash_fee) fee')
	    ->from("ffc_agent_order as a_o")
	    ->join("ffc_agent_product as a_p", "a_o.product_id = a_p.id", 'LEFT')
	    ->join("ffc_agent_address as a_a", "a_o.address_id = a_a.id", 'LEFT')
	    ->join("ffc_agent as a", "a_o.agent_id = a.id")
	    ->join("ffc_position as p", "a_a.position_id = p.id", 'LEFT')
	    ->join("ffc_logistics as l", "a_o.logistics_id = l.id", 'LEFT')
	    ->where($where)
	    ->get();
	    
	    return $res->row()->fee;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   breite
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where = array(), $limit = 20, $offset = 0)
	{
        $select = 'a_o.id,a_o.agent_id,a_o.type,a.card_name a_card_name,a.mobile a_mobile, a.proxy_pattern,a_a.name a_a_name,a_a.mobile a_a_mobile,a_p.name a_p_name,a_o.purchase_trade_no,a_o.purchase_num,a_o.cash_fee,a_o.create_time,a_o.pay_time,a_o.address_id,p.name p_name,a_a.position a_a_position,a_o.status,a_o.is_confirm,a_o.logistics_status,l.logistics_no,l.company_name,f.url,f2.url as confirm_url';
		$res = $this->db->select($select)
		->from("ffc_agent_order as a_o")
		->join("ffc_agent_product as a_p", "a_o.product_id = a_p.id", 'LEFT')
		->join("ffc_agent_address as a_a", "a_o.address_id = a_a.id", 'LEFT')
		->join("ffc_agent as a", "a_o.agent_id = a.id")
		->join("ffc_position as p", "a_a.position_id = p.id", 'LEFT')
		->join("ffc_logistics as l", "a_o.logistics_id = l.id", 'LEFT')
		->join("ffc_file as f", "l.file_id = f.id", 'LEFT')
		->join("ffc_file as f2", "l.confirm_file_id = f2.id", 'LEFT')
		->where($where)
		->order_by("a_o.id desc")
		->limit($limit,$offset)
		->get();
	
		return $res->result_array();
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
	
	/**
	 * [get_statistics 统计数据]
	 *
	 * @DateTime 2019-06-19
	 * @Author   black
	 */
	public function get_statistics($where = array())
	{
	    $res = $this->db->select('count(a_o.id) order_count,sum(a_o.purchase_num) as purchase_num_sum,sum(a_o.cash_fee) as cash_fee_sum')
	    ->from("ffc_agent_order as a_o")
	    ->join("ffc_agent as a", "a_o.agent_id = a.id")
	    ->where($where)
	    ->get();
	    
	    return $res->row_array();
	}
	
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
}