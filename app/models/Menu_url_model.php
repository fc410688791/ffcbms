<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_url_model extends MY_Model {

	// 表名
	public static $table_name = 'menu_url';

	// 包含字段
	public static $columns = 'menu_id, menu_name, menu_url, module_id, is_show, online, menu_desc, father_menu';

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
			->order_by('menu_id', 'DESC')
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
	 * [get_search_count 获得 搜索/筛选 数据的记录数]
	 *
	 * @DateTime 2017-11-13
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

		$res = $this->db->select('menu_id')
			->or_like($like_array)
			->where($wh_array)
			->get(self::$table_name);

		return $res->num_rows();
	}

	/**
	 * [get_search_data 获得 搜索/筛选 数据]
	 *
	 * @DateTime 2017-11-13
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
			->order_by('module_id DESC, father_menu DESC, menu_id DESC')
			->limit($limit, $offset)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_module_menu_name_option 菜单模块与功能列表联查, 查询所有在线的功能列表与菜单模块的数据]
	 *
	 * @DateTime 2017-11-13
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_module_menu_name_option()
	{
		$this->load->model('module_model');

		$m = module_model::$table_name;
		$u = self::$table_name;

		$select = "
		$m.module_name, $m.module_id, 
		$u.menu_id, $u.menu_name, $u.father_menu, $u.is_show
		";

		$wh_array = array(
			"$m.online"     => 1,
			"$u.online"     => 1,
			// "$u.is_show"    => 1,
			// "$m.module_url" => '#/#'
		);

		$order_by = "{$m}.module_sort ASC, {$u}.module_id ASC, {$u}.menu_id ASC";

		$res = $this->db->select($select)
			->join($m, "$u.module_id = $m.module_id", 'left')
			->where($wh_array)
			->order_by($order_by)
			->get($u);

		return $res->result_array();
	}

	/**
	 * [get_menu_name_option 获得菜单下拉列表 menu_id => menu_name]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_menu_name_option($wh_array)
	{	
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select('menu_id, menu_name')
			->where($wh_array)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [batch_up_user_group 批量更新菜单模块的功能列表]
	 *
	 * @DateTime 2017-11-10
	 * @Author   leeprince
	 * @param    [type]     $up_array    [description]
	 * @param    [type]     $wh_in_array [description]
	 * @param    [type]     $field       [where_in 的字段]
	 * @return   [type]                  [description]
	 */
	public function batch_up_data($up_array, $wh_in_array, $field)
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

}