<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MachineIotTriadModel extends MY_Model {

	// 表名
	public static $table_name = 'machine_iot_triad';

	// 包含字段
	public static $columns = 'id,product_key,device_name,device_secret,bind_side_num,aging_time,aging_status,storage_status,storage_time,storage_out_time,storage_user_id,update_time,create_time,aging_start_time,batch_storage_id,bind_side_num,bind_plate_code_num';

	/**
	 * [tableName 表明]
	 *
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
	 *
	 * @Author leeprince:2019-06-04T11:36:25+0800
	 * @return [type]                             [description]
	 */
	public function columns()
	{
	    return self::$columns;
	}

	/**
	 * [getLikeCount 查询统计]
	 *
	 * @Author leeprince:2019-07-09T21:05:41+0800
	 * @return [type]                             [description]
	 */
	public function getLikeCount($where, $whereLike)
	{
		$buider = $this->db->select($this->columns())
			->where($where)
			->like('device_name', $whereLike)
			->get($this->tableName())->num_rows();
		return $buider;
	}
	
	/**
	 * [getLikeData 查询数据]
	 *
	 * @Author leeprince:2019-07-09T21:11:43+0800
	 * @param  [type]                             $where     [description]
	 * @param  [type]                             $whereLike [description]
	 * @param  integer                            $limit     [description]
	 * @param  integer                            $offset    [description]
	 * @return [type]                                        [description]
	 */
	public function getLikeData($where, $whereLike, $limit = 20, $offset = 0)
	{
	    $res = $this->db->select($this->columns())
		    ->where($where)
		    ->like('device_name', $whereLike)
		    ->limit($limit, $offset)
		    ->order_by('id', 'DESC')
		    ->get(self::$table_name);
	    return $res->result_array();
	}

	/**
	 * [getDeviceNameAndMachine 条件获取获取设备三元股信息及路口设备]
	 *
	 * @Author leeprince:2019-09-05T10:22:34+0800
	 * @return [type]                             [description]
	 */
	public function getDeviceNameAndMachine($where)
	{
		$this->load->model('MachineModel');

		$t1 = $this->tableName();
		$t2 = $this->MachineModel->tableName();

		$select = '
			t1.product_key, t1.device_name, t1.aging_time, t1.aging_start_time,
			t2.machine_id, t2.inter_num
		';

		$data = $this->db->select($select)
			->from("$t1 as t1")
			->join("$t2 as t2", 't2.triad_id = t1.id')
			->where($where)
			->get();

		return $data->result_array();
	}
	
	/**
	 * [get_count 获得记录数]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_count($where)
	{
	    $res = $this->db->select('count(*) count')
	    ->where($where)
	    ->get(self::$table_name);
	    return (int)$res->row()->count;
	}
	
	/**
	 * [get_storage 获得库存数]
	 *
	 * @DateTime 2019-12-18
	 * @Author   black.zhang
	 */
	public function get_storage($where)
	{
	    $res = $this->db->select('bind_triad_mark')
	    ->from('machine_iot_triad as a')
	    ->join('agent_product as b', 'a.agent_product_id=b.id')
	    ->where($where)
	    ->group_by('a.bind_triad_mark')
	    ->get()
	    ->result_array();
	    return count($res);
	}
	
	/**
	 * [get_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_list($field, $where, $limit, $offset)
	{
	    if (!$field){
	        $field = self::$columns;
	    }
	    $res = $this->db->select($field)
	    ->where($where)
	    ->limit($limit, $offset)
	    ->order_by('id', 'DESC')
	    ->get(self::$table_name);
	    return $res->result_array();
	}
	
	/**
	 * [get_storage_list 获取多条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_storage_list($field, $where, $limit, $offset)
	{
	    $res = $this->db->select($field)
	    ->from('machine_iot_triad as a')
	    ->join('agent_product as b', 'a.agent_product_id=b.id')
	    ->join('agent_product_type as c', 'b.type=c.id')
	    ->join('machine_type as d', 'c.machine_type=d.id')
	    ->join('file as e', 'b.thumbnail_file_id=e.id', 'LEFT JOIN')
	    ->where($where)
	    ->group_by('a.bind_triad_mark')
	    ->limit($limit, $offset)
	    ->get()
	    ->result_array();
	    return $res;
	}
	
	/**
	 * [get_info 获取单条数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_info($where = array())
	{
	    $res = $this->db->select(self::$columns)
	    ->where($where)
	    ->get(self::$table_name);
	    return $res->row_array();
	}
	
	/**
	 * [update 更新]
	 *
	 * @DateTime 2019-01-23
	 * @Author   black.zhang
	 */
	public function update($save = array(), $where = array())
	{
	    return $this->db->update(self::$table_name, $save, $where);
	}
	
	/**
	 * [get_group_list 获取分组数据]
	 *
	 * @DateTime 2019-01-01
	 * @Author   black.zhang
	 */
	public function get_group_list($where, $where_in)
	{
	    $this->db->select('bind_machine_type,count(id) as count')
	    ->where($where);
	    if ($where_in){
	        $this->db->where_in($where_in['field'],$where_in['list']);
	    }
	    $res = $this->db->group_by('bind_machine_type')->get(self::$table_name);
	    return $res->result_array();
	}
}