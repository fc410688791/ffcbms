<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class PasswordBookModel extends MY_Model {

	// 表名
	public static $table_name = 'password_book';

	// 包含字段
	public static $columns = 'id,name,group_num,password_num,create_time,user_id';

	/**
	 * [get_count_all 获得表的总行数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count_all()
	{
	    $res = $this->db->count_all(self::$table_name);
	    return $res;
	}
	
	/**
	 * [get_count 获得 搜索/筛选 数据的记录数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count()
	{
	    $res = $this->db->count_all(self::$table_name);
	    return $res;
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
	 * [get_info 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where)
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [del_data 删除]
	 *
	 * @DateTime 2019-01-17
	 * @Author   black.zhang
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function del_data($wh_array = array())
	{
	    return $this->db->delete(self::$table_name, $wh_array);
	}
}