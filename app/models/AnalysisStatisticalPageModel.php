<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AnalysisStatisticalPageModel extends MY_Model {

	// 表名
	public static $table_name = 'analysis_statistical_page';

	// 包含字段
	public static $columns = '';
	
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
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
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-09-04
	 * @Author   black.zhang
	 */
	public function get_info($where=array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->order_by('id', 'DESC')
	    ->limit(1, 0)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [statistics_page 统计页面]
	 * @Author black.zhang
	 */
	public function statistics_page($select, $where, $order)
	{
	    $res = $this->db->select($select)
	    ->where($where)
	    ->group_by('page_type_id')
	    ->order_by($order)
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}