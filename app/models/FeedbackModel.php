<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class FeedbackModel extends MY_Model {

	// 表名
	public static $table_name = 'feedback';

	// 包含字段
	public static $columns = 'id,type,machine_id,uuid,content,file_ids,mobile,mobile_model,client_type,status,create_time,process_time';
	
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
	
}