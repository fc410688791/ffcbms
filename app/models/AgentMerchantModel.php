<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentMerchantModel extends MY_Model
{

    // 表名
    public static $table_name = 'agent_merchant';

    // 包含字段
    public static $columns = 'id,name,logo_file_id,address,scene_id,is_weekend_work,business_hours,mobile,longitude,latitude,status,create_time,update_time';

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
     * @DateTime 2019-01-01
     * @Author   black.zhang
     * @return   [type]                 [description]
     */
    public function get_count($where)
    {
        $res = $this->db->select("count(a_m.id) as total_rows")
            ->from("agent_merchant a_m")
            ->where($where)
            ->join('ffc_agent a', "a_m.agent_id=a.id", "left")
            ->get();
        return $res->row()->total_rows;
    }

    /**
     * [get_list 获取多条数据]
     * @DateTime 2019-01-01
     * @Author   black.zhang
     *
     * @param    [type]     $where           [description]
     *
     * @return   [type]                      [description]
     */
    public function get_list($where = array(), $limit = 20, $offset = 0)
    {
        $res = $this->db->select("a_m.*,f.url,s.name as s_name")
            ->from("agent_merchant a_m")
            ->where($where)
            ->join('ffc_file f', "a_m.logo_file_id=f.id", "left")
            ->join('ffc_scene s', "a_m.scene_id=s.id", "left")
            ->limit($limit, $offset)
            ->order_by('id', 'DESC')
            ->get();
        return $res->result_array();
    }

    /**
     * [get_field 获取多条数据]
     * @DateTime 2019-01-01
     * @Author   black.zhang
     *
     * @param    [type]     $where           [description]
     *
     * @return   [type]                      [description]
     */
    public function get_field($where = array(), $limit = 20, $offset = 0)
    {
        $res = $this->db->select("a_m.id,a_m.name,a_m.create_time,a.card_name,a_u.name as a_u_name")
            ->from("agent_merchant a_m")
            ->where($where)
            ->join('ffc_agent a', "a_m.agent_id=a.id", "left")
            ->join('ffc_agent_user a_u', "a_m.create_id=a_u.id", "left")
            ->limit($limit, $offset)
            ->order_by('a_m.id', 'DESC')
            ->get();
        return $res->result_array();
    }

    /**
     * [findIotMerchantList description]
     * @Author leeprince:2019-06-04T18:00:03+0800
     */
    public function findIotMerchantList()
    {
        $this->load->model('MachineModel');

        $t1 = $this->tableName();
        $t2 = $this->MachineModel->tableName();
        $t3 = $this->PositionModel->tableName();

        $select = "t1.id, t1.name, count(distinct bind_triad_mark) count, t3.name position_name";
        $where = [
            't1.status' => 1,
            't2.type !=' => 1,
        ];
        $statusWhereIn = [1, 4];
        $data = $this->db->select($select)
            ->from("$t1 t1")
            ->join("$t2 t2", 't2.merchant_id = t1.id')
            ->join("$t3 t3", 't3.id = t1.position_id')
            ->where($where)
            ->where_in('t2.status', $statusWhereIn)
            ->group_by('t1.id')
            ->order_by('t1.id')
            ->get()->result_array();
        return $data;
    }

    /**
     * [get_info 获取单条数据]
     * @DateTime 2019-01-01
     * @Author   black.zhang
     *
     * @param    [type]     $where           [description]
     *
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
     * [update 更新]
     * @DateTime 2019-01-23
     * @Author   black.zhang
     * @return   [type]               [description]
     */
    public function update($save = array(), $where = array())
    {
        return $this->db->update(self::$table_name, $save, $where);
    }

    /**
     * [del_data 删除]
     * @DateTime 2019-01-17
     * @Author   black.zhang
     *
     * @param    [type]     $wh_array [description]
     *
     * @return   [type]               [description]
     */
    public function del_data($wh_array = array())
    {
        return $this->db->delete(self::$table_name, $wh_array);
    }

    /**
     * [get_name 获取投放点]
     * @DateTime 2019-06-13
     * @Author   black.zhang
     *
     * @param    [type]     $where_in        [description]
     *
     * @return   [type]                      [description]
     */
    public function get_name($where_in = array())
    {
        $res = $this->db->select('name')
            ->where_in('id', $where_in)
            ->get(self::$table_name);
        return $res->result_array();
    }

    /**
     * [get_user 获取多条数据]
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








