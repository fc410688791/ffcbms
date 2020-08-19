<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberActivityScopeDetailModel extends MY_Model {

	// 表名
	public static $table_name = 'member_activity_scope_detail';

	// 包含字段
	public static $columns = '';
	
	/**
	 * [get_product_list 查询商品列表]
	 *
	 * @DateTime 2019-05-23
	 * @Author   black.zhang
	 * @return   [type]     [description]
	 */
	public function get_product_list($where = array())
	{
	    $res = $this->db->select('a.manage_id,b.name,b.price,b.incentive_price,b.type')
	    ->from('member_activity_scope_detail as a')
	    ->where($where)
	    ->join('product as b', 'a.manage_id=b.id')
	    ->order_by('b.type,b.id DESC')
	    ->get();
	    return $res->result_array();
	}
}