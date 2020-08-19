<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentSettlementModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_settlement';

	// 包含字段
	public static $columns = 'id,agent_id,settlement_amount,curr_withdra_amount,create_time';
	
	/**
	 * [tableName 表名]
	 *
	 * @DateTime 2019-01-07
	 * @Author   breite
	 * @return   [type]     [description]
	 */
	public function tableName()
	{
	    // return 'activity_content';
	    return self::$table_name;
	}
	
	/**
	 * [columns 字段名]
	 *
	 * @DateTime 2019-01-07
	 * @Author   breite
	 * @return   [type]     [description]
	 */
	public function columns()
	{
	    return self::$columns;
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