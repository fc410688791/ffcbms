<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberBalanceModel extends MY_Model {

	// 表名
	public static $table_name = 'member_balance';

	// 包含字段
	public static $columns = 'id,currency_balance,currency_act_balance,currency_act_count,currency_act_gift_count,create_time,update_time';
	
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
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-28
	 * @Author   black.zhang
	 * @return   [type]               [description]
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
}