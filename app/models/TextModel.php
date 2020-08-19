<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class TextModel extends MY_Model {

	// 表名
	public static $table_name = 'text';

	// 包含字段
	public static $columns = 'id,type,text_id,text,status,text_ext,create_time,update_time';

	/**
	 * [tableName 表名]
	 *
	 * @Author leeprince:2019-11-01T14:43:46+0800
	 * @return [type]                             [description]
	 */
	public function tableName()
	{
	    return self::$table_name;
	}

	/**
	 * [columns 字段名]
	 *
	 * @Author leeprince:2019-11-01T14:43:56+0800
	 * @return [type]                             [description]
	 */
	public function columns()
	{
	    return self::$columns;
	}

	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_count($where = array())
	{
	    $res = $this->db->select("count(*) as total_rows")
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row()->total_rows;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_list($where = array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [add_data 添加]
	 *
	 * @DateTime 2019-01-16
	 * @Author   black.zhang
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
	 * [update 更新]
	 *
	 * @DateTime 2019-01-23
	 * @Author   black.zhang
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
	
	/**
	 * [get_option 下拉列表]
	 *
	 * @DateTime 2019-08-21
	 * @Author   black.zhang
	 */
	public function get_option($where = array())
	{
	    $this->db
	    ->select('text_id,text')
	    ->where($where);
	    $list = $this->db->order_by("id ASC")
	    ->get(self::$table_name)
	    ->result_array();
	    $re = array();
	    foreach ($list as &$v){
	        $text_id = $v['text_id'];
	        $text = $v['text'];
	        $re[$text_id] = $text;
	    }
	    return $re;
	}
}