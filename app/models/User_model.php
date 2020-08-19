<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

	// 表名
	public static $table_name = 'user';

	// 包含字段
	public static $columns = 'user_id, user_name, password, real_name, mobile, email, user_desc, login_time, status, login_ip, user_group, shortcuts, show_quicknote, create_time, address, addr_detail, card_id, sex';

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
	 * @DateTime 2017-11-10
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
			->order_by('user_id', 'DESC')
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_one_data 根据条件返回该表一行信息]
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
	 * @DateTime 2017-11-08
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
	 * [update_area 更新]
	 *
	 * @DateTime 2017-11-10
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
	 * [get_search_count 统计搜索,分页的用户信息数量]
	 *
	 * @DateTime 2017-11-08
	 * @Author   leeprince
	 * @param    [type]     $like_array [description]
	 * @param    [type]     $wh_array   [description]
	 * @return   [type]                 [description]
	 */
	public function get_search_count($like_array, $wh_array)
	{
		if ( ! is_array($like_array) || ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select('user_id')
			->or_like($like_array)
			->where($wh_array)
			->get(self::$table_name);

		return $res->num_rows();
	}

	/**
	 * [get_search_data 搜索,分页获得的用户信息]
	 *
	 * @DateTime 2017-11-08
	 * @Author   leeprince
	 * @param    [type]     $limit      [description]
	 * @param    [type]     $offset     [description]
	 * @param    [type]     $like_array [description]
	 * @param    [type]     $wh_array   [description]
	 * @return   [type]                 [description]
	 */
	public function get_search_data($limit, $offset, $like_array, $wh_array)
	{
		if ( empty($limit) || ! is_array($like_array) || ! is_array($wh_array))
		{
			return array();
		}
		
		$res = $this->db->select(self::$columns)
			->or_like($like_array)
			->where($wh_array)
			->order_by('user_id', 'DESC')
			->limit($limit, $offset)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_user_id 表单验证器回调的方法: 检车用户名和密码是否正确;   获得用户 id]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @param    [type]     $where [description]
	 * @return   [type]            [description]
	 */
	public function get_user_id(string $where)
	{
		if ( ! is_string($where)) {
			return null;
		}
		$res = $this->db->select(self::$table_name.'.user_id')
			->where($where)
			->get(self::$table_name);

		$data = (array)$res->row_array();
		if ( count($data) == 0)
		{
			return FALSE;
		}

		return $data['user_id'];
	}
	
	/**
	 * [get_user_option 获得用户的下拉列表关联数组 user_id => user_name]
	 *
	 * @DateTime 2017-11-10
	 * @Author   leeprince
	 * @param    array      $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_user_option($wh_array = array())
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select('user_id, user_name')
			->where($wh_array)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_user_real_name_option 获得用户的下拉列表关联数组 user_id => real_name]
	 *
	 * @DateTime 2017-11-10
	 * @Author   leeprince
	 * @param    array      $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_user_real_name_option($wh_array = array())
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select('user_id, real_name')
			->where($wh_array)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [batch_up_user_group 批量更新用户所属用户组]
	 *
	 * @DateTime 2017-11-10
	 * @Author   leeprince
	 * @param    [type]     $up_array    [description]
	 * @param    [type]     $wh_in_array [description]
	 * @param    [type]     $field       [where_in 的字段]
	 * @return   [type]                  [description]
	 */
	public function batch_up_user_group($up_array, $wh_in_array, $field)
	{
		if ( ! is_array($up_array) || ! is_array($wh_in_array))
		{
			return FALSE;
		}

		$res = $this->db->set($up_array)
			->where_in($field, $wh_in_array)
			->update(self::$table_name);

		return $res;
	}

	/**
	 * [get_user_option_by_userids 根据 user_id 数组, 查询 user_id => group_name-real_name 的下拉列表]]
	 *
	 * @DateTime 2017-12-12
	 * @Author   leeprince
	 * @param    [type]     $array_ids [description]
	 * @return   [type]                [description]
	 */
	public function get_user_option_by_userids($array_ids)
	{
		$this->load->model('user_group_model');
		$a = $this->db->dbprefix(user_group_model::$table_name);
		$b = $this->db->dbprefix(self::$table_name);

		$select = "{$a}.group_name, {$b}.user_id, {$b}.real_name";

		$res = $this->db->select("{$select}")
			->join("{$a}", "{$a}.group_id = {$b}.user_group")
			->where_in("{$b}.user_id", $array_ids)
			->get("{$b}");

		return $res->result_array();
	}

}