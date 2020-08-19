<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentUserModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_user';

	// 包含字段
	public static $columns = 'id,open_id,agent_id,group_id,user_name,password,name,role_merchant_id,card,email,is_verification,front_file_id,back_file_id,hold_file_id,verify_time,mobile,position_id,position,status,describe,create_time,update_time';
	
	/**
	 * [get_count 获得表的总行数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where, $where_in = array())
	{
	    $this->db->select("count(a_u.id) as total_rows")
	    ->from('agent_user a_u')
	    ->where($where)
	    ->join('ffc_agent a', "a_u.agent_id=a.id")
	    ->join('ffc_agent_commission a_c', "a_u.id=a_c.agent_user_id", 'left');
	    if ($where_in){
	        $this->db->where_in($where_in['field'], $where_in['list']);
	    }
	    $res = $this->db->get();
	    return $res->row()->total_rows;
	}
	
	/**
	 * [get_list 商户列表]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where = array(), $where_in = array(), $limit = 20, $offset = 0)
	{
	    $this->db->select("a_u.*,a.card_name agent_name,a.proxy_pattern,a_c.commission_type,a_c.commission_proportion,a_c.withdraw_cash_amount,a_c.commission_status")
	    ->from('agent_user a_u')
	    ->where($where);
	    if ($where_in){
	        $this->db->where_in($where_in['field'], $where_in['list']);
	    }
	    $res = $this->db->join('ffc_agent a', "a_u.agent_id=a.id")
	    ->join('ffc_agent_commission a_c', "a_u.id=a_c.agent_user_id", 'left')
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [get_info 商户详情]
	 *
	 * @DateTime 2019-08-06
	 * @Author   black.zhang
	 */
	public function get_info($where = array())
	{
	    $res = $this->db
            	    ->select(self::$columns)
            	    ->where($where)
            	    ->get(self::$table_name);
	    return $res->row_array();
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
	 * [get_user 获取多条数据]
	 *
	 * @DateTime 2019-09-19
	 * @Author   black.zhang
	 */
	public function get_field_list($where = array(), $field = 'id')
	{
	    $res = $this->db->select($field)
	    ->where($where)
	    ->order_by('id', 'asc')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}