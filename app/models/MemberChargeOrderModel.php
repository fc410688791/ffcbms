<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberChargeOrderModel extends MY_Model {

	// 表名
	public static $table_name = 'member_charge_order';

	// 包含字段
	public static $columns = 'id,uuid,out_trade_no,transaction_id,charge_type,activity_id,product_price,cash_fee,cash_fee_currency,gift_currency,pay_client_type,pay_type,order_status,create_time,update_time,pay_time';
	
	/**
	 * [get_count 获取数据条数]
	 *
	 * @DateTime 2019-07-30
	 * @Author   black.zhang
	 */
	public function get_count($where)
	{
	    $select = 'count(a.id) count';
	    $res = $this->db->select($select)
	    ->from("ffc_member_charge_order as a")
	    ->where($where)
	    ->join("ffc_member as b", "a.uuid=b.uuid")
	    ->join("ffc_member_activity_charge as c", "a.activity_id=c.id")
	    ->get();
	    return $res->row()->count;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-07-30
	 * @Author   black.zhang
	 */
	public function get_list($where, $limit, $offset)
	{
	    $res = $this->db->select("a.*,c.charge_name")
	    ->from("ffc_member_charge_order as a")
	    ->where($where)
	    ->join("ffc_member as b", "a.uuid=b.uuid")
	    ->join("ffc_member_activity_charge as c", "a.activity_id=c.id")
	    ->order_by('a.id', 'DESC')
	    ->limit($limit, $offset)
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [get_count_data 统计订单]
	 *
	 * @DateTime 2019-01-19
	 * @Author   black.zhang
	 */
	public function get_count_data($where = array(),$group_by = 'order_status')
	{
	    $res = $this->db->select("count(id) as order_count,sum(product_price) as order_sum,".$group_by)
	    ->where($where)
	    ->group_by($group_by)
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}