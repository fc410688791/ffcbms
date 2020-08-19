<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentModel extends MY_Model {

	// 表名
	public static $table_name = 'agent';

	// 包含字段
	public static $columns = 'id,open_id,rel_agent_id,proxy_pattern,mobile,user_name,card_name,card,card_address,card_valid_date,is_verification,is_agreement,status,front_file_id,back_file_id,hold_file_id,default_address_id,rank_id,machine_num,create_time,verify_time,update_time,open_place,sex,age,login_time,organization,proxy_pattern,onetime_discount_rate,onetime_num,onetime_share';

    /**
     * [tableName 表明]
     * @Author leeprince:2019-06-04T11:36:22+0800
     * @return [type]                             [description]
     */
    public function tableName()
    {
        // return 'activity_content';
        return self::$table_name;
    }

    /**
     * [columns 字段]
     * @Author leeprince:2019-06-04T11:36:25+0800
     * @return [type]                             [description]
     */
    public function columns()
    {
        return self::$columns;
    }

	/**
	 * [get_count 获得表的总行数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $query = $this->db->select('agent_id,commission_proportion,commission_status')
	    ->from('ffc_agent_commission')
	    ->where(array('c_commission_type'=>1))
	    ->get_compiled_select();
	    
	    $res = $this->db->select("count(a.id) as total_rows")
	    ->from("agent as a")
	    ->where($where)
	    ->join("($query) as a_c", "a.id=a_c.agent_id", "left")
	    ->get();
	    return $res->row()->total_rows;
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
	    $query = $this->db->select('agent_id,commission_proportion,commission_status')
	    ->from('ffc_agent_commission')
	    ->where(array('c_commission_type'=>1))
	    ->get_compiled_select();
	    
	    $res = $this->db->select("a.id,a.user_name,a.card_name,a.mobile,a.onetime_discount_rate,a.is_verification,a.onetime_share,a.verify_time,a.proxy_pattern,a.default_address_id,a.rel_agent_id,a_c.commission_proportion,a_c.commission_status")
	    ->where($where)
	    ->from('agent as a')
	    ->join("($query) as a_c", "a.id=a_c.agent_id", "left")
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
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select('*')
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
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_user($where = array(), $field = 'id')
	{
	    $res = $this->db->select($field)
	    ->where($where)
	    ->order_by('id', 'asc')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}