<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentAddressModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_address';

	// 包含字段
	public static $columns = 'id,agent_id,name,mobile,position_id,position,status,create_time,update_time';
	
	/**
	 * [get_count 获得表的总行数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $res = $this->db->select("count(id) as total_rows")
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row()->total_rows;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where = array(), $limit = 20, $offset = 0)
	{
	    $res = $this->db->select("a_a.*,p.name as p_name")
	    ->from('agent_address a_a')
	    ->where($where)
	    ->join('ffc_position p', "a_a.position_id=p.id", "left")
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get();
	    return $res->result_array();
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