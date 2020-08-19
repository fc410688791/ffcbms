<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentProductModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_product';

	// 包含字段
	public static $columns = 'id,name,type,min_num,max_num,price,status,user_id,create_time,update_time,sort,thumbnail_file_id,thumbnail_file_id,detail_file_id,scene_file_id,product_op_file_id';
	
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
	    ->from('agent_product as a')
	    ->where($where)
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
	    $res = $this->db->select('a.*,b.type_name as agent_product_type_name,c.type_name')
	    ->from('agent_product as a')
	    ->join('agent_product_type as b', 'a.type=b.id')
	    ->join('machine_type as c', 'b.machine_type=c.id')
	    ->where($where)
	    ->order_by('a.sort ASC,a.id DESC')
	    ->limit($limit, $offset)
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
	 * [get_prod_option 查询商品名称下拉列表 name => name]
	 *
	 * @DateTime 2019-01-27
	 * @Author   breite
	 * @return   [type]     [description]
	 */
	public function get_prod_option()
	{
		$res = $this->db->distinct()
			->select('name ,id')
			->order_by("id DESC")
			->get(self::$table_name);

		return $res->result_array();
	}

}