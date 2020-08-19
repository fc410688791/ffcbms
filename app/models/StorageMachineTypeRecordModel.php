<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class StorageMachineTypeRecordModel extends MY_Model {

	// 表名
	public static $table_name = 'storage_machine_type_record';

	// 包含字段
	public static $columns = 'id,storage_record_id,storage_type,curr_type_storage_num,op_type_storage_num,user_id,agent_order_id,agent_id,create_time';

		
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
	    ->from('storage_machine_type_record as a')
	    ->join('agent_product as b', 'a.agent_product_id=b.id')
	    ->join('agent as c', 'a.agent_id = c.id', 'left')
	    ->join('user as d', 'a.user_id = d.user_id', 'left')
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
	    $res = $this->db->select('a.*,c.user_name as agent_name,d.user_name,b.name,e.type_name as agent_product_type_name,f.type_name,g.url as thumbnail_file')
	    ->from('storage_machine_type_record as a')
	    ->join('agent_product as b', 'a.agent_product_id=b.id')
	    ->join('agent as c', 'a.agent_id = c.id', 'left')
	    ->join('user as d', 'a.user_id = d.user_id', 'left')
	    ->join('agent_product_type as e', 'b.type=e.id', 'left')
	    ->join('machine_type as f', 'e.machine_type=f.id', 'left')
	    ->join('file as g', 'b.thumbnail_file_id=g.id', 'left')
	    ->where($where)
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
	 */
	public function get_info($where)
	{
	    $res = $this->db->select('a.*,c.user_name as agent_name,d.user_name,b.name,e.type_name as agent_product_type_name,f.type_name,g.url as thumbnail_file')
	    ->from('storage_machine_type_record as a')
	    ->where($where)
	    ->join('agent_product as b', 'a.agent_product_id=b.id')
	    ->join('agent as c', 'a.agent_id = c.id', 'left')
	    ->join('user as d', 'a.user_id = d.user_id', 'left')
	    ->join('agent_product_type as e', 'b.type=e.id', 'left')
	    ->join('machine_type as f', 'e.machine_type=f.id', 'left')
	    ->join('file as g', 'b.thumbnail_file_id=g.id', 'left')
	    ->get();
	    return $res->row_array();
	}
	
	/**
	 * [get_group_list 获取分组数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_group_list($where)
	{
	    $res = $this->db->select('a.agent_product_id,e.url as thumbnail_file,b.name,c.type_name as agent_product_type_name,d.type_name,sum(op_type_storage_num) as op_type_storage_num')
	    ->from('storage_machine_type_record as a')
	    ->join('agent_product as b', 'a.agent_product_id=b.id')
	    ->join('agent_product_type as c', 'b.type=c.id')
	    ->join('machine_type as d', 'c.machine_type=d.id')
	    ->join('file as e', 'b.thumbnail_file_id=e.id')
	    ->where($where)
	    ->group_by('a.agent_product_id')
	    ->get();
	    return $res->result_array();
	}
}