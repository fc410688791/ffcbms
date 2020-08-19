<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class StorageOperateModel extends MY_Model {

	// 表名
	public static $table_name = 'storage_operate';

	// 包含字段
	public static $columns = 'id,storage_machine_type_record_id,triad_id,create_time';

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
	    $res = $this->db->select('count(id) as count')
	    ->from(self::$table_name)
	    ->where($where)
	    ->get()
	    ->row_array();
	    return $res['count'];
	}
	
	/**
	 * [get_storage 获得记录数]
	 *
	 * @DateTime 2019-12-20
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_storage($where)
	{
	    $res = $this->db->select('bind_triad_mark')
	    ->from(self::$table_name)
	    ->where($where)
	    ->group_by('bind_triad_mark')
	    ->get()
	    ->result_array();
	    return count($res);
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
	    $res = $this->db->select('id,bind_triad_mark,triad_id')
	    ->from('storage_operate')
	    ->where($where)
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [get_storage_list 获取多条数据]
	 *
	 * @DateTime 2019-12-20
	 * @Author   black.zhang
	 */
	public function get_storage_list($where = array(), $limit = 20, $offset = 0)
	{
	    $res = $this->db->select('bind_triad_mark')
	    ->from('storage_operate as a')
	    ->where($where)
	    ->group_by('bind_triad_mark')
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-23
	 * @Author   black.zhang
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
}