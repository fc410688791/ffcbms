<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentTurnDataRecordModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_turn_data_record';

	// 包含字段
	public static $columns = '';
	
	/**
	 * [get_count 获得记录数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_count($where)
	{
	    $res = $this->db->select('count(a.id) count')
	    ->from('agent_turn_data_record as a')
	    ->where($where)
	    ->join('agent as b','a.current_agent_id = b.id')
	    ->join('agent as c','a.after_agent_id = c.id')
	    ->get();
	    return (int)$res->row()->count;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_list($where = array(), $limit = 20, $offset = 0)
	{
	    $res = $this->db->select('a.*,b.user_name as current_agent_name,c.user_name as after_agent_name,d.name as current_merchant_name')
	    ->from('agent_turn_data_record as a')
	    ->where($where)
	    ->join('agent as b','a.current_agent_id = b.id')
	    ->join('agent as c','a.after_agent_id = c.id')
	    ->join('agent_merchant as d','a.current_merchant_id = d.id','left')
	    ->limit($limit, $offset)
	    ->order_by('a.id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [add_data 添加]
	 *
	 * @DateTime 2019-01-16
	 * @Author   black.zhang
	 */
	public function add_data($add_array)
	{
	    if ( ! is_array($add_array))
	    {
	        return FALSE;
	    }
	    
	    return $this->db->insert(self::$table_name, $add_array);
	}
	
	/**
	 * [add_batch 批量添加]
	 *
	 * @DateTime 2019-01-16
	 * @Author   black.zhang
	 */
	public function add_batch($add_array = array())
	{
	    if ( ! is_array($add_array))
	    {
	        return FALSE;
	    }
	    
	    return $this->db->insert_batch(self::$table_name, $add_array);
	}
}