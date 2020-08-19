<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class FileModel extends MY_Model {

	// 表名
	public static $table_name = 'file';

	// 包含字段
	public static $columns = 'id,bucket,type,platform_type,request_id,source,hash,object,url,create_time';
	
	/**
	 * [tableName 表名]
	 *
	 * @DateTime 2019-01-07
	 * @Author   breite
	 * @return   [type]     [description]
	 */
	public function tableName()
	{
	    return self::$table_name;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where_in = array())
	{
	    $res = $this->db->select("url")
	    ->where_in('id',$where_in)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [get_url 获取url]
	 *
	 * @DateTime 2019-07-10
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_url($where)
	{
	    $res = $this->db->select("url")
	    ->where($where)
	    ->get(self::$table_name)
	    ->row_array();
	    return $res['url'];
	}

}