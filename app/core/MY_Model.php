<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends Model {

	/**
     *  [__construct 扩展核心类: 定义的类必须继承自父类; 类名和文件名必须以 MY_ 开头]
     *
	 * @DateTime 2018-01-15
	 * @Author   leeprince
	 */
	public function __construct($group_name = '')
	{
		parent::__construct($group_name);
	}
    
    /**
	 * [get_all_data 根据条件返回该表所有行信息]
	 *
	 * @DateTime 2017-11-10
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_all_data($where = array())
	{
		if ( ! is_array($where))
		{
			return array();
		}

		$res = $this->db->select("*")
			->where($where)
			->order_by('id', 'DESC')
			->get($this->tableName());

		return $res->result_array();
	}

    /**
     * [get_all_data 根据条件返回该表所有行信息]
     *
     * @DateTime 2017-11-10
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_all_data_count($where = array())
    {
        if ( ! is_array($where))
        {
            return array();
        }

        $res = $this->db->select("*")
            ->where($where)
            ->get($this->tableName());

        return $res->num_rows();
    }

    /**
     * [get_all_by_query] 原生查询 返回对应行信息
     *
     * @DateTime 2018-06-8
     * @Author maoxiaoying
     * @return [type] [description]
     */
    public function get_all_by_query($sql){
        if(empty($sql)){
            return array();
        }
        $res = $this->db->query($sql)->result_array();
        return $res;
    }

    /**
	 * [get_one_data 获得条件查询的一条信息]
	 *
	 * @DateTime 2017-11-10
	 * @Author   leeprince
	 * @param    [type]     $where [description]
	 * @return   [type]               [description]
	 */
	public function get_one_data($where)
	{
		if ( ! is_array($where))
		{
			return array();
		}

		$res = $this->db->select("*")
			->where($where)
			->get($this->tableName());

		return $res->row_array();
	}
    /**
     * 更新
     * @param type $attributes
     * @param type $where
     * @return type
     */
    public function updateAll($attributes, $where = array())
    {
        foreach($attributes as $key => $val) {
            if(is_array($val)) {
                $field_val = isset($val[0]) ? $val[0] : NULL;
                $escape = isset($val[1]) && !$val[1] ? false : true;
                $this->db->set($key, $field_val, $escape);
                unset($attributes[$key]);
            }
        }
        return $this->db->where($where)->update($this->tableName(), $attributes);
    }
    /**
     * 更新
     * @param type $data
     * @param type $key
     * @return type
     */
    public function updateData($data, $key = 'id')
    {
        return $this->db->update_batch($this->tableName(), $data, $key);
    }
    /**
     * 删除
     *
     * @param array $where
     * @return boolean true for success, false for failure
     */
    public function deleteAll($where = array(), $limit = NULL)
    {
        return $this->db->delete($this->tableName(), $where, $limit);
    }
     /**
     * 添加
     *
     * @param array $data
     */
    public function insertData($data)
    {
        if(is_array(current($data))) {
            return $this->db->insert_batch($this->tableName(), $data);
        } else {
            return $this->db->insert($this->tableName(), $data);
        }
    }
	/**
	 * [get_last_sql 获得最后一条 sql 语句]
	 *
	 * @DateTime 2018-01-15
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_last_sql()
	{
		return $this->db->last_query();
	}

	/**
	 * [write_log_last_sql 写入最后一条 sql 语句到日志文件中, 日志等级为: debug]
	 *
	 * @DateTime 2018-01-15
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function write_last_sql()
	{
		log_message('debug', '>>>>>>>>>>>>>>>>>>>>>>>>您要获取的最后一条 sql 语句: '.$this->get_last_sql());
	}


    /**
     * [findAll 根据条件返回该表所有行信息]
     *
     * @DateTime 2018-06-02
     * @Author   leeprince
     * @param    array      $where [description]
     * @return   [type]               [description]
     */
    public function findAll($where = [], $select = '', $order_by = '', $groupBy = '')
    {
        $res = $this->db->select($this->getDefaultColumns($select))
            ->where($where)
            ->group_by($groupBy)
            ->order_by($order_by)
            ->get($this->tableName());

        return $res->result_array();
    }

    /**
     * [findOne 获得条件查询的一条信息]
     *
     * @DateTime 2018-06-02
     * @Author   leeprince
     * @param    [type]     $where [description]
     * @return   [type]               [description]
     */
    public function findOne($where, $select = '', $order_by = '', $groupBy = '')
    {
        $res = $this->db->select($this->getDefaultColumns($select))
            ->where($where)
            ->group_by($groupBy)
            ->order_by($order_by)
            ->get($this->tableName());

        return $res->row_array();
    }

    /**
     * [insert 添加]
     *
     * @DateTime 2018-06-02
     * @Author   leeprince
     * @param    [type]     $add_array [description]
     * @return   [type]                [description]
     */
    public function insert($insert_array)
    {
        if ( ! is_array($insert_array))
        {
            return FALSE;
        }

        return $this->db->insert($this->tableName(), $insert_array);
    }

    /**
     * [update 更新]
     *
     * @DateTime 2018-06-02
     * @Author   leeprince
     * @param    [type]     $up_array [description]
     * @param    [type]     $where [description]
     * @return   [type]               [description]
     */
    public function update($up_array, $where)
    {
        if ( ! is_array($up_array) || ! is_array($where))
        {
            return FALSE;
        }

        return $this->db->update($this->tableName(), $up_array, $where);
    }

    /**
     * [update 删除]
     *
     * @DateTime 2018-06-02
     * @Author   leeprince
     * @param    [type]     $up_array [description]
     * @param    [type]     $where [description]
     * @return   [type]               [description]
     */
    public function delete($where)
    {
        if (! is_array($where))
        {
            return FALSE;
        }

        return $this->db->delete($this->tableName(), $where);
    }


    /**
     * [getDefaultColumns  获取查询表的默认字段]
     *
     * @DateTime 2018-07-05
     * @Author   leeprince
     * @param    [type]     $select [description]
     * @return   [type]             [description]
     */
    private function getDefaultColumns($select)
    {
        $defSelect = '*';
        if (method_exists($this, 'columns')) {
            $defSelect = $this->columns();
        }
        if ( ! empty($select)) {
            $defSelect = $select;
        }

        return $defSelect;
    }

    /**
     * [setQueryBuilderWhereIn 查询构造器构造 where_in 的查询条件]
     * 
     * @author leeprince <[<email address>]>
     * @param [type] &$queryBuilder [description]
     * @param [type] $whereInArray  [description]
     */
    public function setQueryBuilderWhereIn(&$queryBuilder, $whereInArray)
    {
        if ( ! empty($whereInArray) && is_array($whereInArray)) {
            foreach ($whereInArray as $wk => $wv) {
                $queryBuilder->where_in($wk, $wv);
            }
        }
    }

    /**
     * [setPagination 查询构造器构造 offset, limit 的查询条件]
     *
     * @author leeprince <[<email address>]>
     * @param [type] $offsetLimit [description]
     */
    public function setQueryBuilderOffsetLimit(&$queryBuilder, $offset, $limit) 
    {
        if ( ! empty($limit)) {
            $queryBuilder->limit($limit, $offset);
        }
    }

    /**
     * [count 根据条件返回该表所有行总数]
     *
     * @Author leeprince:2019-06-05T11:56:35+0800
     * @param  [type]                             $where [description]
     * @param  string                             $key   [description]
     * @return [type]                                    [description]
     */
    public function count($where, $key = '*')
    {
        $res = $this->db->select("COUNT({$key}) AS count")
            ->where($where)
            ->get($this->tableName());

        return $res->row()->count;
    }

}





