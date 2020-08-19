<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_log_model extends MY_Model {

	// 表名
	public static $table_name = 'sys_log';

	// 包含字段
	public static $columns = 'op_id, user_name, action, class_name, class_obj, result, op_time';

	/**
	 * [tableName 表名]
	 *
	 * @author leeprince <[<email address>]>
	 * @return [type] [description]
	 */
	public function tableName()
	{
	    return self::$table_name;
	}

	/**
	 * [columns 字段]
	 *
	 * @author leeprince <[<email address>]>
	 * @return [type] [description]
	 */
	public function columns()
	{
	    return self::$columns;
	}

	/**
	 * [add_log 添加系统数据库日志]
	 *
	 * @DateTime 2017-11-02
	 * @Author   leeprince
	 * @param    [type]     $user_name  [description]
	 * @param    [type]     $action     [description]
	 * @param    [type]     $class_name [description]
	 * @param    [type]     $class_obj  [description]
	 * @param    string     $result     [description]
	 */
	public function add_log($user_name, $action, $class_name, $class_obj, $result = "")
	{
		$op_time = time();

		$add_data = array(
			'user_name'  => $user_name,
			'action'     => $action,
			'class_name' => $class_name,
			'class_obj'  => $class_obj,
			'result'     => $result,
			'op_time'    => $op_time,
		);
		return $this->db->insert(self::$table_name, $add_data);
	}

	/**
	 * [get_all_count 获得总记录数]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    array      $where [description]
	 * @return   [type]            [description]
	 */
	public function get_all_count($where = array())
	{
		if ( ! is_array($where))
		{
			return FALSE;
		}

		$res = $this->db->select('op_id')
			->where($where)
			->get(self::$table_name);

		return $res->num_rows();
	}

	/**
	 * [get_page_log 根据偏移, 获得每页数据]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    [type]     $limit  [description]
	 * @param    [type]     $offset [description]
	 * @param    array      $where  [description]
	 * @return   [type]             [description]
	 */
	public function get_page_log($limit, $offset, $where = array())
	{
		if ( empty($limit) || ! is_array($where))
		{
			return FALSE;
		}
		
		$res = $this->db->select(self::$columns)
			->where($where)
			->order_by('op_id', 'DESC')
			->limit($limit, $offset)
			->get(self::$table_name);

		return $res->result_array();
	}
}