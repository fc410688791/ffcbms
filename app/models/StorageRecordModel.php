<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class StorageRecordModel extends MY_Model {

	// 表名
	public static $table_name = 'storage_record';

	// 包含字段
	public static $columns = 'id,storage_type,curr_storage_num,op_storage_num,op_user_id,storage_mark,create_time';
	
	/**
	 * [tableName 表明]
	 *
	 * @Author leeprince:2019-06-04T11:36:22+0800
	 * @return [type]                             [description]
	 */
	public function tableName()
	{
	    return self::$table_name;
	}

	/**
	 * [columns 字段]
	 *
	 * @Author leeprince:2019-06-04T11:36:25+0800
	 * @return [type]                             [description]
	 */
	public function columns()
	{
	    return self::$columns;
	}
	
	/**
	 * [get_count 获得记录数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $res = $this->db->select('count(*) count')
	    ->from('storage_record as a')
	    ->where($where)
	    ->join('user as b','a.op_user_id = b.user_id')
	    ->join('agent as c','a.op_user_id = c.id')
	    ->get();
	    return (int)$res->row()->count;
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
	    $res = $this->db->select('a.*,b.user_name,c.user_name as agent_name')
	    ->from('storage_record as a')
	    ->where($where)
	    ->join('user as b','a.op_user_id = b.user_id','left')
	    ->join('agent as c','a.op_user_id = c.id','left')
	    ->limit($limit, $offset)
	    ->order_by('a.id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
		
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}

}