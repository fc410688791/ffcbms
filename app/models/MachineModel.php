<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MachineModel extends MY_Model {

	// 表名
	public static $table_name = 'machine';

	// 包含字段
	public static $columns = 'id,machine_id,batch_id,mac,status,book_id,group_index,password_index,copywriting_id,position_id,create_time,update_time';

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
	 * [get_count 获得表的总行数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @return   [type]                 [description]
	 */
	public function get_count($where, $where_in = array())
	{
	    $this->db
	         ->select("count(m.id) as total_rows")
             ->from('machine as m')
             ->join('ffc_agent_merchant as a_m', 'm.merchant_id = a_m.id', 'left')
             ->join('ffc_position as p', 'm.position_id = p.id', 'left')
             ->join('ffc_agent_product as a_p', 'm.agent_product_id = a_p.id', 'left');
	    
	    if ($where_in){
	        $this->db
	             ->where_in($where_in['field'], $where_in['list']);
	    }
	    
	    $res = $this->db
	                ->where($where)
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
	public function get_list($where = array(), $limit = 20, $offset = 0, $where_in = array())
	{
	    $res = $this->db
	                ->select("m.*,m_t.type_name,m_t.module_num,p.name as p_name,p_c.button_text,a_m.name as a_m_name,a_p_t.type_name as product_type_name")
            	    ->from('machine as m')
            	    ->join('ffc_machine_type as m_t', 'm.type = m_t.id', 'left')
            	    ->join('ffc_agent_merchant as a_m', 'm.merchant_id = a_m.id', 'left')
            	    ->join('ffc_position as p', 'm.position_id = p.id', 'left')
            	    ->join('ffc_password_copywriting as p_c', 'm.copywriting_id = p_c.id', 'left')
            	    ->join('ffc_agent_product as a_p', 'm.agent_product_id = a_p.id', 'left')
            	    ->join('ffc_agent_product_type as a_p_t', 'a_p.type = a_p_t.id', 'left');
	    if ($where_in){
	        $this->db
	        ->where_in($where_in['field'], $where_in['list']);
	    }
	    
	    $res = $this->db
	                ->where($where)
	                ->order_by('m.id', 'DESC')
	                ->limit($limit, $offset)
	                ->get();
	    
	    return $res->result_array();
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
	 * [get_info 获取一条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 * @param    [type]     $where           [description]
	 * @return   [type]                      [description]
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select("m.*,province_id,city_id,street_id,p.name as p_name,p_c.button_text")
	    ->where($where)
	    ->from('machine as m')
	    ->join('ffc_position as p', 'm.position_id = p.id', 'left')
	    ->join('ffc_password_copywriting as p_c', 'm.copywriting_id = p_c.id', 'left')
	    ->get();
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
	 * [get_count_data 设备状态统计]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_count_data()
	{
	    $res = $this->db->select("status,count(status) as dev_count")
	    ->group_by('status')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [get_field_list 获取部分字段数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_field_list($where, $field = 'machine_id', $order = 'inter_num,id'){
	    $res = $this->db->select($field)
	    ->where($where)
	    ->order_by($order)
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [get_group_count 分组统计]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_group_count($where, $group)
	{
	    $res = $this->db->select($group)
	    ->where($where)
	    ->group_by($group)
	    ->get(self::$table_name);
	    return count($res->result_array());
	}
	
	/**
	 * [get_group_list 分组]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_group_list($field, $where, $group, $order, $limit, $offset){
	    $res = $this->db->select($field)
	    ->where($where)
	    ->group_by($group)
	    ->order_by($order)
	    ->get(self::$table_name);
	    return $res->result_array();
	}


	/**
	 * [getMachineByMerchant 通过投放点查找设备]
	 *
	 * @Author leeprince:2019-06-04T19:08:27+0800
	 * @param  [type]                             $merchant_id [description]
	 * @return [type]                                          [description]
	 */
	public function getMachineByMerchant($merchant_id)
	{
		$t1 = $this->tableName();
		$t2 = $this->AgentMerchantModel->tableName();
		$t3 = $this->MachineIotTriadModel->tableName();
		$t4 = $this->PositionModel->tableName();

		$select = 't1.inter_num, t1.machine_id, t1.status, t1.position, t1.merchant_id, t1.bind_triad_mark,
			t2.name merchant_name,
			t3.id as triad_id, t3.product_key, t3.device_name, t3.device_secret,
			t4.id position_id, t4.name position_name
		';
		$where = "t2.id = {$merchant_id} and t1.type != 1 and t1.status in (1, 4)";
		$data = $this->db->select($select)
			->from("$t1 t1")
			->join("$t2 t2", 't2.id = t1.merchant_id')
			->join("$t3 t3", 't3.id = t1.triad_id')
			->join("$t4 t4", 't4.id = t1.position_id')
			->where($where)
			->order_by('t3.id asc, t1.inter_num asc')
			->get()->result_array();
		return $data;
	}

	/**
	 * [triadGroup 通讯模组为组]
	 *
	 * @Author leeprince:2019-06-04T19:46:42+0800
	 * @param  [type]                             $data [description]
	 * @return [type]                                   [description]
	 */
	public function triadGroup($data)
	{
		$triadGroup = [];
		foreach ($data as $key => $value) {
			$triad_id = $value['triad_id'];
			$triadGroup[$triad_id][] = [
				"triad_id"      => $triad_id,
				"machine_id"    => $value['machine_id'],
				"status"        => $value['status'],
				"inter_num"     => $value['inter_num'],
				"product_key"   => $value['product_key'],
				"device_name"   => $value['device_name'],
				"device_secret" => $value['device_secret'],
				'merchant_name' => $value['merchant_name'],
				'position_name' => $value['position_name'],
				'position'      => $value['position'],
			];
		}

		return $triadGroup;
	}

	/**
	 * [MerchantTriadGroup 投放点一维, 通讯模组为二维, 设备列表为三维]
	 *
	 * @Author leeprince:2019-12-20T10:47:30+0800
	 * @param  [type]                             $data    [description]
	 * @param  string                             $groupBy [description]
	 */
	public function MerchantTriadGroup($data, $groupBy = 'triad_id')
	{
		$MerchantGroup = [];

		// 投放点
		foreach ($data as $key => $value) { 
			$merchant_id = $value['merchant_id'];
			$triad_id    = $value['triad_id'];

			$newtriadGroup = [];
			// 模组
			foreach ($data as $key01 => $value01) {
				$merchant_id_inmerchant = $value01['merchant_id'];
				$triad_id_inmachine     = $value01['triad_id'];
				$groupById              = $value01[$groupBy];
				// 设备
				if ($merchant_id_inmerchant == $merchant_id) {
					$newtriadGroup[$groupById][$triad_id_inmachine][] = [
						"triad_id"      => $triad_id_inmachine,
						"merchant_id"   => $merchant_id,
						"machine_id"    => $value01['machine_id'],
						"status"        => $value01['status'],
						"inter_num"     => $value01['inter_num'],
						"product_key"   => $value01['product_key'],
						"device_name"   => $value01['device_name'],
						"device_secret" => $value01['device_secret'],
						'merchant_name' => $value01['merchant_name'],
						'position_name' => $value01['position_name'],
						'position'      => $value01['position'],
					];
				}
			}

			$MerchantGroup[$merchant_id] = [
				'merchantId'   => $value['merchant_id'],
				'merchantName' => $value['merchant_name'],
				'positionName' => $value['position_name'],
				'triadGroup'   => $newtriadGroup
			];
		}

		return $MerchantGroup;
	}

	/**
	 * [getMachineByPosition 通过投放点获取设备信息]
	 *
	 * @Author leeprince:2019-06-05T10:36:54+0800
	 * @param  [type]                             $positionArray [description]
	 * @return [type]                                            [description]
	 */
	public function getMachineByPosition($positionArray)
	{
		$t1 = $this->tableName();
		$t2 = $this->PositionModel->tableName();
		$t3 = $this->MachineIotTriadModel->tableName();
		$t4 = $this->AgentMerchantModel->tableName();

		$select = 't1.inter_num, t1.machine_id, t1.status, t1.position, t1.merchant_id,
			t2.name position_name, t2.id position_id, 
			t3.id as triad_id, t3.product_key, t3.device_name, t3.device_secret,
			t4.name merchant_name
		';
		$where = "t1.type = 2 and t1.status in (1, 4)";
		$data = $this->db->select($select)
			->from("$t1 t1")
			->join("$t2 t2", 't2.id = t1.position_id')
			->join("$t3 t3", 't3.id = t1.triad_id')
			->join("$t4 t4", 't4.id = t1.merchant_id')
			->where($where)
			->where_in('t1.position_id', $positionArray)
			->order_by('t3.id asc, t1.inter_num asc')
			->get()->result_array();
		return $data;
	}

	/**
	 * [getOneMachineIotTriadData 获取物联网三元组中的一路信息]
	 *
	 * @Author leeprince:2019-08-06T19:13:56+0800
	 * @return [type]                             [description]
	 */
	public function getOneMachineIotTriadData()
	{
		$this->load->model('MachineIotTriadModel');
		$this->load->model('PositionModel');
		$this->load->model('AgentMerchantModel');

		$t1 = $this->tableName();
		$t2 = $this->MachineIotTriadModel->tableName();
		$t3 = $this->PositionModel->tableName();
		$t4 = $this->AgentMerchantModel->tableName();

		$select = '
			t1.position, t1.machine_id, t1.type,
			t2.product_key, t2.device_name, t2.device_secret,
			t3.name as position_name,
			t4.name as merchant_name,
		';

		$data = $this->db->select($select)
			->from("$t1 t1")
			->join("$t2 t2", 't1.triad_id = t2.id')
			->join("$t3 t3", 't1.position_id = t3.id')
			->join("$t4 t4", 't1.merchant_id = t4.id')
			->where([
				't1.triad_id    !=' => 0, 
				't1.inter_num ' => 1, 
				't1.status '    => 1, 
			])
			->where_in('t1.type', [2, 3, 4, 5, 6])->get()->result_array();

		return $data;
	}
	
}












