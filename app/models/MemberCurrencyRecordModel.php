<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberCurrencyRecordModel extends MY_Model {

	// 表名
	public static $table_name = 'member_currency_record';

	// 包含字段
	public static $columns = 'id,uuid,trade_type,out_trade_no,c_currency_balance,c_currency_act_balance,c_currency_act_count,c_currency_act_gift_count,record_currency,record_gift_currency,create_time';
	
	/**
	 * [get_count 获取数据条数]
	 *
	 * @DateTime 2019-07-30
	 * @Author   black.zhang
	 */
	public function get_count($where, $or_where)
	{
	    $select = 'count(*) count';
	    $res = $this->db->select($select)
	    ->where($where)
	    ->or_where($or_where)
	    ->get(self::$table_name);
	    return $res->row()->count;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-07-30
	 * @Author   black.zhang
	 */
	public function get_list($where, $or_where, $limit, $offset)
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->or_where($or_where)
	    ->order_by('id', 'DESC')
	    ->limit($limit, $offset)
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [add_data 添加]
	 *
	 * @DateTime 2019-01-16
	 * @Author   black.zhang
	 * @param    [type]     $add_array [description]
	 */
	public function add_data($add_array)
	{
	    if ( ! is_array($add_array))
	    {
	        return FALSE;
	    }
	    
	    return $this->db->insert(self::$table_name, $add_array);
	}
}