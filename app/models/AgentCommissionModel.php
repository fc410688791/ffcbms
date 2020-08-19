<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentCommissionModel extends MY_Model {

	// 表名
	public static $table_name = 'agent_commission';

	// 包含字段
	public static $columns = 'id,agent_id,agent_user_id,c_commission_type,balance,withdraw_cash_amount,total_income,commission_type,commission_withdrawal_amount,commission_withdrawal_time,commission_proportion,commission_contract_start_time,commission_contract_end_time,commission_time,commission_status,update_time,create_time';
	
	/**
	 * [get_count 获得记录数]
	 *
	 * @DateTime 2019-06-21
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $res = $this->db->select('count(id) count')
	    ->where($where)
	    ->get(self::$table_name);
	    return (int)$res->row()->count;
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-06-21
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where, $limit, $offset)
	{
	    $this->db->select(self::$columns)
	    ->where($where)
	    ->order_by('id', 'DESC');
	    if ($limit){
	        $this->db->limit($limit, $offset);
	    }
	    $res = $this->db->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-06-21
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where)
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

}