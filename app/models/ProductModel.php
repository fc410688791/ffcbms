<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductModel extends MY_Model {

	// 表名
	public static $table_name = 'product';

	// 包含字段
	public static $columns = 'id,type,name,price,incentive_price,open_time,status,describe,user_id,create_time,update_time,is_default';
	
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
	    ->where($where)
	    ->get(self::$table_name);
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
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
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
	
	/**
	 * [del_data 删除]
	 *
	 * @DateTime 2019-01-17
	 * @Author   black.zhang
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function del_data($wh_array = array())
	{
	    return $this->db->delete(self::$table_name, $wh_array);
	}
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-23
	 * @Author   black.zhang
	 * @return   [type]               [description]
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
	
	/**
	 * [get_prod_option 查询商品下拉列表]
	 *
	 * @DateTime 2019-05-23
	 * @Author   black.zhang
	 * @return   [type]     [description]
	 */
	public function get_prod_option($where = array(), $where_in = array())
	{
		$this->db
			->select('id,name,price,incentive_price,type')
			->where($where);
		if ($where_in){
		    $this->db->where_in('id',$where_in);
		}
		$res = $this->db->order_by("type ASC,id DESC")
			->get(self::$table_name);

		return $res->result_array();
	}

}