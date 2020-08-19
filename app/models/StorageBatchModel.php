<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class StorageBatchModel extends MY_Model {

	// 表名
	public static $table_name = 'storage_batch';

	// 包含字段
	public static $columns = 'id,batch_storage_date,batch_storage_no,batch_storage_num,create_time';
	
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
	 * [get_info]
	 *
	 * @DateTime 2019-07-10
	 * @Author   black.zhang
	 */
	public function get_info($where)
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}

}