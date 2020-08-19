<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MsgModel extends MY_Model {

	// 表名
	public static $table_name = 'msg';

	// 包含字段
	public static $columns = 'id,type,user_type,title,content,create_time';
	
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
	
}