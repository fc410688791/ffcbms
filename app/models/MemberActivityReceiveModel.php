<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberActivityReceiveModel extends MY_Model {

	// 表名
	public static $table_name = 'member_activity_receive';

	// 包含字段
	public static $columns = '';
	
	/**
	 * [get_count 获得记录数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $res = $this->db->select('count(a.id) count')
	    ->from('member_activity_receive as a')
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
	    $select = 'a.id,a.uuid,a.activity_id,a.start_time,a.end_time,a.create_time,a.receive_status,a.use_time,b.card_name,b.card_type,b.trigger_type,c.out_trade_no,c.product_id,d.name as product_name,d.price as product_price';
	    $res = $this->db->select($select)
	    ->from('member_activity_receive as a')
	    ->where($where)
	    ->join('member_activity_card as b', 'a.activity_id=b.id')
	    ->join('order as c', 'a.order_id=c.id', 'LEFT')
	    ->join('product as d', 'c.product_id=d.id', 'LEFT')
	    ->order_by('a.id', 'DESC')
	    ->limit($limit, $offset)
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-28
	 * @Author   black.zhang
	 * @return   [type]               [description]
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
}