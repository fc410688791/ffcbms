<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class PositionModel extends MY_Model {

	// 表名
	public static $table_name = 'position';

	// 包含字段
	public static $columns = 'id,province_id,city_id,street_id,village_id,name,create_time,update_time';

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
	 * [del_data 删除]
	 *
	 * @DateTime 2019-01-17
	 * @Author   black.zhang
	 * @param    [type]     $wh_array [description]
	 * @return   [type]               [description]
	 */
	public function del_data($wh_array = array())
	{
	    return $this->db->delete(self::$table_name, $wh_array);
	}

	/**
	 * [getPositionById description]
	 *
	 * @Author leeprince:2019-06-05T10:10:42+0800
	 * @return [type]                             [description]
	 */
	public function getPositionById($province_id, $city_id, $street_id, $village_id)
	{
		$where = [];
		if ($village_id) {
			$where['village_id'] = $village_id;
		} elseif ($street_id) {
			$where['street_id'] = $street_id;
		} elseif ($city_id) {
			$where['city_id'] = $city_id;
		} else {
			$where['province_id'] = $province_id;
		}

		$data = $this->findALl($where, 'id');
		return $data;
	}

}