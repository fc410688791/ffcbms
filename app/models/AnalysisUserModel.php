<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AnalysisUserModel extends MY_Model {

	// 表名
	public static $table_name = 'analysis_user';

	// 包含字段
	public static $columns = '';

	/**
	 * [get_count 获取统计结果]
	 *
	 * @DateTime 2019-01-21
	 * @Author   black.zhang
	 */
	public function get_count($where = array())
	{
		$this->db->select('count(id) as count')
		->where($where);
		$res = $this->db->get(self::$table_name)
		->row_array();
		return $res['count'];
	}
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-28
	 * @Author   black.zhang
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
	
	/**
	 * [get_group_count 获取分组统计结果]
	 *
	 * @DateTime 2019-01-21
	 * @Author   black.zhang
	 */
	public function get_group_count($where = array(),$group_by)
	{
	    $res = $this->db->select('count(id) as count,'.$group_by)
	    ->from(self::$table_name)
	    ->where($where)
	    ->group_by($group_by)
	    ->get()
	    ->result_array();
	    return $res;
	}
	
	/**
	 * [statistics_page 统计页面]
	 * @Author black.zhang
	 */
	public function statistics_page($select, $where)
	{
	    $res = $this->db->select($select)
	    ->where($where)
	    ->group_by('page_type_id')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}