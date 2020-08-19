<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class SceneModel extends MY_Model {

	// 表名
	public static $table_name = 'scene';

	// 包含字段
	public static $columns = 'id,name,pid,status,create_time';
	
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where = array())
	{
	    $res = $this->db->select("id,name")
	    ->where($where)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}