<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MachineBatchModel extends MY_Model {

	// 表名
	public static $table_name = 'machine_batch';

	// 包含字段
	public static $columns = 'id,book_id,copywriting_id,name,number,batch_no,create_time';
	
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
	
	/**
	 * [add_data 添加]
	 *
	 * @DateTime 2019-01-16
	 * @Author   black.zhang
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

}