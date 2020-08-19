<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentCardModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_card';

	// 包含字段
	public static $columns = 'id,agent_id,agent_user_id,card_type,name,card_mark,card_name,card_no,status,create_time,update_time';
	
	/**
	 * [get_info 商户详情]
	 *
	 * @DateTime 2019-08-06
	 * @Author   black.zhang
	 */
	public function get_info($where = array())
	{
	    $res = $this->db
            	    ->select(self::$columns)
            	    ->where($where)
            	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_list($where = array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}