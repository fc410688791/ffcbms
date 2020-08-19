<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends MY_Model {

	// 表名
	public static $table_name = 'order';

	// 包含字段
	public static $columns = 'id,out_trade_no,transaction_id,uuid,type,machine_id,position_id,product_id,product_price,cash_fee,real_cash_fee,at_receive_id,pay_client_type,pay_type,status,complete_status,create_time,pay_time,update_time,open_time,charge_time';

	 /**
     * [tableName 表名]
     *
     * @DateTime 2019-01-07
     * @Author   breite
     * @return   [type]     [description]
     */
    public function tableName()
    {
        // return 'activity_content';
        return self::$table_name;
    }

    /**
     * [columns 字段名]
     *
     * @DateTime 2019-01-07
     * @Author   breite
     * @return   [type]     [description]
     */
    public function columns()
    {
        return self::$columns;
    }
	
	/**
	 * [get_count_all 获得表的总行数]
	 *
	 * @DateTime 2019-01-21
	 * @Author   breite
	 * @return   [type]                 [description]
	 */
	public function get_count_all()
	{
	    $res = $this->db->count_all(self::$table_name);
	    return $res;
	}
	/**
	 * [get_list_count 获取数据条数]
	 *
	 * @DateTime 2019-01-21
	 * @Author   breite
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list_count($where = array())
	{
		$selfTable=$this->tableName();
		
		$productTable = 'product'; //商品表
		
		$postionTable = 'position'; //位置表

		$memberTable = 'member'; //用户表

		$select = 'count(a.id) count';

		$res = $this->db->select($select)
		->from("{$selfTable} as a")
		->join("{$postionTable} as c", "a.position_id = c.id",'LEFT')
		->join("ffc_machine as d", "a.machine_id = d.machine_id",'LEFT')
		->join("ffc_agent_product as e", "d.agent_product_id = e.id",'LEFT')
		->where($where)
		->get()->row_array();

		return $res['count'];
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   breite
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_list($where = array(), $limit = 20, $offset = 0)
	{
		$selfTable=$this->tableName();
		
		$machineTable = 'machine'; //设备表
		
		$productTable = 'product'; //商品表
		
		$postionTable = 'position'; //位置表

		$memberTable = 'member'; //用户表
		
		$merchant = 'agent_merchant';

		$select = 'a.id id,a.uuid,a.machine_id,a.out_trade_no,a.transaction_id,a.cash_fee,a.real_cash_fee,a.product_price,a.pay_type,a.status,a.complete_status,a.create_time,a.pay_time,a.type,a.product_id,a.at_receive_id,b.name as merchant_name,e.type as agent_product_type';

		$res = $this->db->select($select)
		->from("{$selfTable} as a")
		->join("{$merchant} as b", "a.merchant_id = b.id",'LEFT')
		->join("{$postionTable} as c", "a.position_id = c.id",'LEFT')
		->join("ffc_machine as d", "a.machine_id = d.machine_id",'LEFT')
		->join("ffc_agent_product as e", "d.agent_product_id = e.id",'LEFT')
		->where($where)
		->order_by("a.id desc")
		->limit($limit,$offset)
		->get()
		->result_array();
	
		return $res;
	}
	 /**
	 * [get_all_data 根据条件返回该表所有行信息]
	 *
	 * @DateTime 2019-01-19
	 * @Author   black.zhang
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function get_all_data($wh_array = array())
	{
		if ( ! is_array($wh_array))
		{
			return array();
		}

		$res = $this->db->select(self::$columns)
			->where($wh_array)
			->order_by('id DESC')
			->get(self::$table_name);

		return $res->result_array();
	}
	
	public function get_count_data($where = array())
	{
	    $res = $this->db->select("status,count(id) as order_count,sum(cash_fee) as order_sum")
	    ->where($where)
	    ->group_by('status')
	    ->get(self::$table_name);
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
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [orderStatistic 订单统计]
	 *
	 * @Author black.zhang
	 * @return [array]
	 */
	public function orderStatistics($where = array(), $where_in = array())
	{
	    if (!is_array($where)){
	        return;
	    }
	    $select = "pay_type,count(id) as pay_count,sum(cash_fee) as cash_fee_statistics,sum(settlement_amount) as settlement_amount_statistics";
	    $this->db->select($select)->where($where);
	    if ($where_in){
	        $this->db->where_in($where_in['field'], $where_in['list']);
	    }
	    $res = $this->db
	    ->group_by('pay_type')
	    ->get(self::$table_name)
	    ->result_array();
	    $re = array('pay_count'=>0,'cash_fee_statistics'=>0,'settlement_amount_statistics'=>0);
	    foreach ($res as $v){
	        $re ['pay_count'] += $v['pay_count'];
	        if ($v['pay_type'] == 3){  //充币
	            $re ['settlement_amount_statistics'] += $v['settlement_amount_statistics'];
	        }else {  //微信、支付宝
	            $re ['cash_fee_statistics'] += $v['cash_fee_statistics'];
	        }
	    }
	    return  $re;
	}
	
	/**
	 * [statistics_agent_income 代理商收益统计]
	 *
	 * @Author black.zhang
	 * @return [array]
	 */
	public function statistics_agent_income($where = array())
	{
	    $res = $this->db->select("agent_id,sum(cash_fee) as order_sum")
	    ->where($where)
	    ->group_by('agent_id')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [statistics_group 分组统计]
	 * @Author black.zhang
	 */
	public function statistics_group($where = array(),$group_by)
	{
	    $res = $this->db->select("count(id) as order_count,sum(cash_fee) as order_sum,".$group_by)
	    ->where($where)
	    ->group_by($group_by)
	    ->get(self::$table_name);
	    return $res->result_array();
	}
}