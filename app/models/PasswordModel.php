<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class PasswordModel extends MY_Model {

	// 表名
	public static $table_name = 'password';

	// 包含字段
	public static $columns = 'id,book_id,group_index,password_index,password';
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where = array(), $order_by = 'id desc', $limit = 20, $offset = 0)
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->limit($limit, $offset)
	    ->order_by($order_by)
	    ->get(self::$table_name);
	    return $res->result_array();
	}

}