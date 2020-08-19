<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class RefundModel extends MY_Model {

	// 表名
	public static $table_name = 'refund';

	// 包含字段
	public static $columns = 'id,order_id,out_refund_no,refund_id,reason,remark,mobile,source,status,file_ids,create_time,update_time';
	
	/**
	 * [get_count 获得表的总行数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where)
	{
	    $res = $this->db->select("count(r.id) as total_rows")
	    ->from('refund as r')
	    ->where($where)
	    ->join('ffc_order as o', 'r.order_id = o.id', 'left')
	    ->join('ffc_position as po', 'o.position_id = po.id')
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
	    $res = $this->db->select("r.id,r.status,r.mobile,t.text,r.reason,r.file_ids,r.create_time,o.out_trade_no,o.product_id,o.position_id,o.cash_fee,o.pay_type,po.name as position_name,m.position as position_position,r.agent_process_status,r.agent_process_text,r.remark")
	    ->where($where)
	    ->from('refund as r')
	    ->limit($limit, $offset)
	    ->join('ffc_order as o', 'r.order_id = o.id', 'left')
	    ->join('ffc_position as po', 'o.position_id = po.id', 'left')
	    ->join('ffc_machine as m', 'o.machine_id = m.machine_id', 'left')
	    ->join('ffc_text as t', 'r.refund_text_id = t.id', 'left')
	    ->order_by('r.id', 'DESC')
	    ->get();
	    return $res->result_array();
	}
	
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-01-28
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select("r.id,r.order_id,r.out_refund_no,r.status as r_status,r.mobile as r_mobile,r.refund_text_id,r.reason,r.remark,r.source,o.out_trade_no,o.transaction_id,o.status as o_status,o.pay_type,o.cash_fee,o.at_receive_id,o.product_id,p.name as product_name,p.describe")
	    ->where($where)
	    ->from('refund as r')
	    ->join('ffc_order as o', 'r.order_id = o.id', 'left')
	    ->join('ffc_product as p', 'o.product_id = p.id', 'left')
	    ->get();
	    return $res->row_array();
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
	 * [statistics_agent_income 代理商收益统计]
	 *
	 * @Author black.zhang
	 * @return [array]
	 */
	public function statistics_agent_income($where = array(), $where_in = array())
	{
	    $select = "o.agent_id,sum(o.cash_fee) as refund_order_sum,sum(o.settlement_amount) as refund_settlement_amount";
	    $this->db->select($select)
	    ->from('ffc_refund r')
	    ->join('ffc_order o', 'r.order_id=o.id')
	    ->where($where);
	    if ($where_in){
	        $this->db->where_in($where_in['field'], $where_in['list']);
	    }
	    $res = $this->db->group_by('o.agent_id')->get();
	    return $res->row_array();
	}
	
	/**
	 * [refundStatistics 退款统计]
	 *
	 * @Author black.zhang
	 * @return [array]
	 */
	public function refundStatistics($where = array(), $where_in = array())
	{
	    $select = "o.pay_type,sum(o.cash_fee) as refund_order_sum,sum(o.settlement_amount) as refund_settlement_amount";
	    $this->db->select($select)
	    ->from('ffc_refund r')
	    ->join('ffc_order o', 'r.order_id=o.id')
	    ->where($where);
	    if ($where_in){
	        $this->db->where_in($where_in['field'], $where_in['list']);
	    }
	    $res = $this->db
	    ->group_by('o.pay_type')
	    ->get()
	    ->result_array();
	    $re = array('refund_order_sum'=>0,'refund_settlement_amount'=>0);
	    foreach ($res as $v){
	        if ($v['pay_type'] == 3){  //充币
	            $re ['refund_settlement_amount'] += $v['refund_settlement_amount'];
	        }else {  //微信、支付宝
	            $re ['refund_order_sum'] += $v['refund_order_sum'];
	        }
	    }
	    return $re;
	}
	
	/**
	 * [settlement 结算]
	 *
	 * @DateTime 2019-06-26
	 * @Author   black.zhang
	 * @return   [type]               [description]
	 */
	public function settlement($save = array(), $where = array())
	{
	    $res = $this->db->set($save)
	    ->where($where)
	    ->update('(ffc_refund join ffc_order on ffc_refund.order_id = ffc_order.id)');
	    return $res;
	}
	
	/**
	 * [get_refund_list 运营-退款列表]
	 *
	 * @DateTime 2019-09-03
	 * @Author   black.zhang
	 */
	public function get_refund_list($where = array(), $limit = 20, $offset = 0)
	{
	    $res = $this->db->select("o.uuid,a_m.name,o.machine_id,o.create_time as o_create_time,t.text,r.reason,r.create_time as r_create_time,m.device_type")
	    ->from('refund as r')
	    ->join('ffc_order as o', 'r.order_id = o.id')
	    ->join('ffc_text as t', 'r.refund_text_id = t.id')
	    ->join('ffc_agent_merchant as a_m', 'o.merchant_id = a_m.id')
	    ->join('ffc_member as m', 'o.uuid = m.uuid')
	    ->where($where)
	    ->order_by('r.id', 'DESC')
	    ->limit($limit, $offset)
	    ->get();
	    return $res->result_array();
	}
}