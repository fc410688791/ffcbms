<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentReadMsgModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_read_msg';

	// 包含字段
	public static $columns = 'id,agent_id,msg_id,status,crate_time,update_time';
	
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
	    $res = $this->db->select(self::$columns)
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	
	/**
	 * [get_user 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_user($where = array())
	{
	    $res = $this->db->select('a_r_m.agent_id,a.user_name')
	    ->from('agent_read_msg a_r_m')
	    ->where($where)
	    ->join('ffc_agent a', 'a_r_m.agent_id=a.id')
	    ->order_by('a_r_m.id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
}