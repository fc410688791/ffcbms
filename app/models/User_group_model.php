<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group_model extends MY_Model {

	// 表名
	public static $table_name = 'user_group';

	// 包含字段
	public static $columns = 'group_id, group_name, group_role, owner_id, group_desc, def_index_id';

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
	 * @return   [type]     [description]
	 */
	public function get_all_data($wh_array = array())
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select(self::$columns)
			->where($wh_array)
			->order_by('group_id DESC')
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_one_data 根据条件返回该表一行信息]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_one_data($wh_array)
	{
		if ( ! is_array($wh_array))
		{
			return FALSE;
		}

		$res = $this->db->select(self::$columns)
			->where($wh_array)
			->get(self::$table_name);

		return $res->row_array();
	}

	/**
	 * [add_data 添加]
	 *
	 * @DateTime 2017-11-09
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
	 * @DateTime 2017-11-09
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
	 * @DateTime 2017-11-09
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
		$res = $this->db->select('group_id')
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
			->order_by('group_id DESC')
			->limit($limit, $offset)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_user_group_option 获得用户角色下拉列表关联数组]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_user_group_option()
	{
		$res = $this->db->select('group_id, group_name')
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_user_by_group_id 通过 group_id 获得关联表用户表的数据]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @param    [type]     $group_id [description]
	 * @return   [type]               [description]
	 */
	public function get_user_by_group_id($group_id)
	{
		if ( empty($group_id) || ! is_numeric($group_id))
		{
			return FALSE;
		}

		$this->load->model('user_model');
		$user_table = user_model::$table_name;

		$select = self::$columns.", $user_table.user_id, $user_table.user_name, $user_table.real_name, $user_table.mobile, $user_table.email, $user_table.login_time, $user_table.login_ip, $user_table.user_group, $user_table.user_desc";

		$res = $this->db->select($select)
			->join($user_table, self::$table_name.".group_id = $user_table.user_group", 'right')
			->where(self::$table_name.'.group_id', $group_id)
			->order_by('user_group.group_id, user.user_id')
			->get(self::$table_name);

		return $res->result_array();
	}
}