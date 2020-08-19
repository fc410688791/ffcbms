<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberModel extends MY_Model {

	// 表名
	public static $table_name = 'member';

	// 包含字段
	public static $columns = 'id,uuid,open_id,unit_id,gender,country,province,city,nickname,avatar_url,mobile,position_id,device_type,client_type,create_time,update_time,balance_id';
	
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
	    $res = $this->db->select("m.uuid,m.nickname,m.mobile,m.gender,m.client_type,m.device_type,m.create_time,p.name as p_name,m_b.currency_balance")
	    ->from('ffc_member as m')
	    ->where($where)
	    ->join('ffc_position as p', 'm.position_id = p.id', 'left')
	    ->join('ffc_member_balance as m_b', 'm.balance_id = m_b.id', 'left')
	    ->order_by('m.id', 'DESC')
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
	 * [statistics_group 分组统计]
	 * @Author black.zhang
	 */
	public function statistics_group($where, $group_by, $offset, $limit)
	{
	    $member_count_query = $this->db->select("count(id) as count,".$group_by)
	    ->from(self::$table_name)
	    ->where($where)
	    ->group_by($group_by)
	    ->get_compiled_select();
	    $res = $this->db->select("count,".$group_by)
	    ->from("(".$member_count_query.")a")
	    ->order_by('count DESC')
	    ->limit($limit, $offset)
	    ->get();
	    return $res->result_array();
	}
}