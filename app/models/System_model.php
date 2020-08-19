<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class System_model extends MY_Model {

	// 表名
	public static $table_name = 'system';

	// 包含字段
	public static $columns = 'key_name, key_value';

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
	 * [get_all_data 根据条件返回该表所有行信息]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_all_data($wh_array = array())
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select(self::$columns)
			->where($wh_array)
			->order_by('key_name')
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_all_data 根据条件返回该表一行信息]
	 *
	 * @DateTime 2017-11-12
	 * @Author   leeprince
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_one_data($wh_array)
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res  = $res = $this->db->select(self::$columns)
			->where($wh_array)
			->get(self::$table_name);

		return $res->row_array();
	}

	/**
	 * [add_data 添加]
	 *
	 * @DateTime 2017-11-13
	 * @Author   leeprince
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
	 * [del_data 删除]
	 *
	 * @DateTime 2017-11-13
	 * @Author   leeprince
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function del_data($wh_array)
	{
		if ( ! is_array($wh_array))
		{
			return FALSE;
		}

		return $this->db->delete(self::$table_name, $wh_array);
	}

	/**
	 * [update_data 更新]
	 *
	 * @DateTime 2017-11-13
	 * @Author   leeprince
	 * @param    [type]     $up_array [description]
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function update_data($up_array, $wh_array)
	{
		if ( ! is_array($up_array) || ! is_array($wh_array))
		{
			return FALSE;
		}

		return $this->db->update(self::$table_name, $up_array, $wh_array);
	}

	/**
	 * [get_all_count 获得表数据的记录数]
	 *
	 * @DateTime 2017-11-13
	 * @Author   leeprince
	 * @param    [type]     $like_array [description]
	 * @param    [type]     $wh_array   [description]
	 * @return   [type]                 [description]
	 */
	public function get_all_count()
	{
		$res = $this->db->select('key_name')
			->get(self::$table_name);

		return $res->num_rows();
	}

	/**
	 * [get_limit_data 获得表数据的分页数据]
	 *
	 * @DateTime 2017-11-30
	 * @Author   leeprince
	 * @param    [type]     $limit  [description]
	 * @param    [type]     $offset [description]
	 * @return   [type]             [description]
	 */
	public function get_limit_data($limit, $offset)
	{
		if ( empty($limit))
		{
			return array();
		}
		
		$res = $this->db->select(self::$columns)
			->order_by('key_name')
			->limit($limit, $offset)
			->get(self::$table_name);

		return $res->result_array();
	}
}