<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentTurnRecordModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_turn_record';

	// 包含字段
	public static $columns = 'id,turn_type,is_redirect_trun_agent,current_agent_id,after_agent_id,create_time';
	
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