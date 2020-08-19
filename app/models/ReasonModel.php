<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ReasonModel extends MY_Model {

	// 表名
	public static $table_name = 'reason';

	// 包含字段
	public static $columns = 'id,type,uid,content,create_time';
	
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
	
	/**
	 * [get_info]
	 *
	 * @DateTime 2019-01-16
	 * @Author   black.zhang
	 * @param    [type]     $where    [description]
	 */
	public function get_info($where)
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
}