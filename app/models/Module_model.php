<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Module_model extends MY_Model {

	// 表名
	public static $table_name = 'module';

	// 包含字段
	public static $columns = 'module_id, module_name, module_url, module_sort, module_desc, module_icon, online';
	
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
	 * [get_all_data 根据条件获得所有商品]
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
			->order_by('module_sort')
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_one_data 条件返回一条模块信息]
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
	 * [add_data 添加模块]
	 *
	 * @DateTime 2017-11-12
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
	 * [del_module 删除模块]
	 *
	 * @DateTime 2017-11-12
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
		$res = $this->db->select('module_id')
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
			->order_by('module_sort')
			->limit($limit, $offset)
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [update_module 更新模块]
	 *
	 * @DateTime 2017-11-12
	 * @Author   leeprince
	 * @param    [type]     $up_array [description]
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function update_module($up_array, $wh_array)
	{
		if ( ! is_array($up_array) || ! is_array($wh_array))
		{
			return FALSE;
		}

		return $this->db->update(self::$table_name, $up_array, $wh_array);
	}

	/**
	 * [get_module_name_option 获得模块下拉列表  module_id => module_name]
	 *
	 * @DateTime 2017-11-13
	 * @Author   leeprince
	 * @param    array      $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_module_name_option($wh_array = array())
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select('module_id, module_name')
			->where($wh_array)
			->order_by('module_sort')
			->get(self::$table_name);

		return $res->result_array();
	}

	/**
	 * [get_menu_list_by_module_id 获得菜单模块对应的功能列表]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @param    [type]     $module_id [description]
	 * @return   [type]                [description]
	 */
	public function get_menu_list_by_module_id($module_id)
	{
		if ( ! is_numeric($module_id))
		{
			return array();
		}

		$this->load->model('menu_url_model');

		$u = self::$table_name;
		$m = menu_url_model::$table_name;

		$select = "{$m}.menu_id, {$m}.menu_name, {$m}.menu_url, {$m}.module_id, {$m}.is_show, {$m}.online, {$m}.menu_desc, {$m}.father_menu, {$u}.module_name";

		$res = $this->db->select($select)
			->join($m, "{$m}.module_id = {$u}.module_id", 'left')
			->where("{$m}.module_id", $module_id)
			->get($u);

		return $res->result_array();
	}

	/**
	 * [get_module_menu_tree 获得在线菜单模块下面的菜单列表]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_module_menu_tree()
	{
		$this->load->model('menu_url_model');

		$u = self::$table_name;
		$m = menu_url_model::$table_name;

		$select = "{$u}.module_id, {$m}.menu_id, {$m}.father_menu, {$u}.module_name, {$u}.module_url, {$u}.module_sort, {$u}.module_icon, {$m}.menu_name, {$m}.menu_url, {$m}.is_show, {$m}.online, {$m}.menu_desc";

		$wh_array = array(
			"{$u}.online"  => 1,
			"{$m}.online"  => 1,
			// "{$m}.is_show" => 1,
		);

		$order_by = "{$u}.module_sort ASC, {$m}.module_id ASC, {$m}.menu_sort DESC, {$m}.menu_id ASC";

		$res = $this->db->select($select)
			->join($m, "{$m}.module_id = {$u}.module_id", 'right')
			->where($wh_array)
			->order_by($order_by)
			->get($u);

		return $res->result_array();
	}

	public function getModuleOfMenu() 
	{
		$this->load->model('menu_url_model');

		$t1 = $this->tableName();
		$t2 = $this->menu_url_model->tableName();

		return $this->db->select('t1.module_name, t2.menu_url')
			->from("{$t1} as t1")
			->join("{$t2} as t2", 't1.module_id = t2.module_id', 'left')
			->get()->result_array();
	}
}