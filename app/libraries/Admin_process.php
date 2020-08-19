<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_process {

    protected $_CI;

    protected $menu_url_tree = array();

    /**
     * [__construct 构造函数]
     *
     * @DateTime 2018-01-15
     * @Author   leeprince
     */
    public function __construct()
    {
        $this->_CI = &get_instance();
    }

    /**
     * [get_module_menu_tree 获得在线菜单模块下面的菜单列表]
     *
     * @DateTime 2017-11-14
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_module_menu_tree()
    {
        $this->_CI->load->model('module_model');

        $res = $this->_CI->module_model->get_module_menu_tree();
        if ( empty($res)) {
            return array();
        } else {
            foreach($res as $re) {
                $module_id   = $re['module_id'];
                $menu_id     = $re['menu_id'];
                $father_menu = $re['father_menu'];
                $module_name = $re['module_name'];
                $module_url  = $re['module_url'];
                $module_sort = $re['module_sort'];
                $module_icon = $re['module_icon'];
                $menu_name   = $re['menu_name'];
                $menu_url    = $re['menu_url'];
                $is_show     = $re['is_show'];
                $online      = $re['online'];
                $menu_desc   = $re['menu_desc'];

                // 获得在线菜单模块下面的菜单列表
                if ($is_show == 1) {
                    $menu_array[$module_id][] = array(
                        'menu_id'     => $menu_id,
                        'father_menu' => $father_menu,
                        'menu_name'   => $menu_name,
                        'menu_url'    => $menu_url,
                        'is_show'     => $is_show,
                    );
                    $module_array[$module_id] = array(
                        'module_id'   => $module_id,
                        'module_name' => $module_name,
                        'module_url'  => $module_url,
                        'module_icon' => $module_icon,
                        "menu_list"   => $menu_array[$module_id]
                    );
                }

                // 通过功能链接获得菜单模块名称和功能列表名称 menu_url => array(module_name, menu_name)
                $url_array[$menu_url] = array(
                    'menu_id'     => $menu_id,
                    'menu_name'   => $menu_name,
                    'module_id'   => $module_id,
                    'module_name' => $module_name
                );
                $this->set_menu_url_tree($url_array);

            }
            return $module_array;
        }

    }

    /**
     * [set_menu_url_tree 设置 通过功能链接获得菜单模块名称和功能列表名称 menu_url => array(module_name, menu_name)]
     *
     * @DateTime 2017-11-15
     * @Author   leeprince
     * @param    [type]     $url_array [description]
     */
    protected function set_menu_url_tree($url_array)
    {
        $this->menu_url_tree = $url_array;
    }

    /**
     * [get_menu_url_tree 获得 通过功能链接获得菜单模块名称和功能列表名称 menu_url => array(module_name, menu_name)]
     *
     * @DateTime 2017-11-15
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_menu_url_tree()
    {
        return $this->menu_url_tree;
    }

    /**
     * [get_module_name_option 获得模块下拉列表 module_id => module_name]
     *
     * @DateTime 2017-11-13
     * @Author   leeprince
     * @param    [type]     $wh_array [description]
     * @return   [type]               [description]
     */
    public function get_module_name_option($wh_array = array())
    {
        if ( ! is_array($wh_array)) {
            return array();
        }

        $this->_CI->load->model('module_model');

        $res = $this->_CI->module_model->get_module_name_option($wh_array);

        if (count($res) <= 0) {
            return array();
        } else {
            $module_name_option = array();

            foreach($res as $v) {
                $module_name_option[$v['module_id']] = $v['module_name'];
            }

            return $module_name_option;
        }
    }

    /**
     * [get_menu_unlimit_child 功能列表中递归查找父 menu_id 为 $parent_id 的结点]
     *
     * @DateTime 2018-02-28
     * @Author   leeprince
     * @param    [type]     $data_array [数据数组]
     * @param    [type]     $parent_id  [指定的父级 ID, 初始值为 0]
     * @return   [type]                 [description]
     */
    public function get_menu_unlimit_child($data_array, $parent_id){
        $tree = array(); //每次都声明一个新数组用来放子元素
        foreach($data_array as $v){
            if($v['father_menu'] == $parent_id){   //匹配子记录
                $v['children'] = $this->get_menu_unlimit_child($data_array, $v['menu_id']); //递归获取子记录
                if($v['children'] == null){
                    unset($v['children']); //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                $tree[] = $v;  //将记录存入新数组
            }
        }

        return $tree; //返回新数组
    }

    /**
     * [get_menu_name_option 获得菜单下拉列表 menu_id => menu_name]
     *
     * @DateTime 2017-11-14
     * @Author   leeprince
     * @param    array      $wh_array [description]
     * @return   [type]               [description]
     */
    public function get_menu_name_option($wh_array = array())
    {
        if ( ! is_array($wh_array)) {
            return array();
        }

        $this->_CI->load->model('menu_url_model');

        $res = $this->_CI->menu_url_model->get_menu_name_option($wh_array);

        if (count($res) <= 0) {
            return array();
        } else {
            $module_name_option = array();

            foreach($res as $v) {
                $module_name_option[$v['menu_id']] = $v['menu_name'];
            }

            return $module_name_option;
        }
    }

    /**
     * [get_user_group_option 获得用户角色的下拉列表的关联数组 group_id => group_name]
     *
     * @DateTime 2017-11-09
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_user_group_option()
    {
        $this->_CI->load->model('user_group_model');

        $res = $this->_CI->user_group_model->get_user_group_option();

        if ( count($res) <= 0 ) {
            return  array();
        } else {
            $user_group_option = array();

            foreach($res as $v) {
                $user_group_option[$v['group_id']] = $v['group_name'];
            }

            return $user_group_option;
        }

    }

    /**
     * [get_user_option 获得用户的下拉列表关联数组 user_id => user_name]
     *
     * @DateTime 2017-11-09
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_user_option($wh_array = array())
    {
        if ( ! is_array($wh_array)) {
            return array();
        }

        $this->_CI->load->model('user_model');

        $res = $this->_CI->user_model->get_user_option($wh_array);

        if (count($res) <= 0)
        {
            return array();
        }
        else {
            $user_option = array();

            foreach ($res as $v) {
                $user_option[$v['user_id']] = $v['user_name'];
            }

            return $user_option;
        }
    }

    /**
     * [get_user_real_name_option 获得用户的下拉列表关联数组 user_id => real_name]
     *
     * @DateTime 2017-11-10
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_user_real_name_option($wh_array = array())
    {
        if ( ! is_array($wh_array)) {
            return array();
        }

        $wh_array['status'] = 1;

        $this->_CI->load->model('user_model');

        $res = $this->_CI->user_model->get_user_real_name_option($wh_array);

        if (count($res) <= 0) {
            return array();
        } else {
            $user_option = array();

            foreach ($res as $v)
            {
                $user_option[$v['user_id']] = $v['real_name'];
            }

            return $user_option;
        }
    }


    /**
     * [get_sys_value_option 获得系统默认值下拉列表]
     *
     * @DateTime 2017-11-15
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function get_sys_value_option()
    {
        $this->_CI->load->model('system_model');

        $res = $this->_CI->system_model->get_all_data();
        if ( ! $res) {
            return array();
        } else {
            foreach($res as $re) {
                $option[$re['key_name']] = $re['key_value'];
            }

            return $option;
        }
    }

    /**
     * [loadModelGetData 加载模型文件并调用公共方法]
     *
     * @author leeprince <[<email address>]>
     * @param  [type] $model [description]
     * @param  [type] $wh    [description]
     * @return [type]        [description]
     */
    public function loadModelGetData($model, $wh, $function = 'findAll')
    {
        $this->_CI->load->model($model);
        return $this->_CI->$model->$function($wh);
    }

    /**
     * [ajaxReturn ajax 返回]
     *
     * @author leeprince <[<email address>]>
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    private function ajaxReturn($code = 0, $msg = '', $ret = '')
    {
        $this->_CI->ajax_return(['code' => $code, 'msg' => $msg, 'data' => $ret]);
    }

    /**
     * [render_js_chart 图表展示]
     *
     * @DateTime 2017-12-06
     * @Author   leeprince
     * @param    [type]     $data   [图表数据]
     * @param    string     $labels [对应于ykeys选项中的值]
     * @param    string     $type   [图表类型 - 暂没扩展, 只有折线图]
     * @return   [type]             [description]
     */
    public function render_js_chart($data, $labels = '对应值', $type = 'Line')
    {
        if ( empty($data)) {
            return FAlse;
        }

        $assets_dir = $this->_CI->config->item('assets_dir');

        $chart_html = "
            <!-- Raphael Javascript是一个 Javascript的矢量库 -->
            <script src='$assets_dir/bower_components/raphael/raphael.min.js'></script>
            <!-- morris.js 折线图 -->
            <script src='$assets_dir/bower_components/morris.js/morris.min.js'></script>
            <script type='text/javascript'>
                Morris.Line({
                  element: 'userRiseChart',
                  resize: true,
                  data: $data,
                  xkey: 'x_k',
                  ykeys: ['y_k'],
                  labels: ['$labels']
                });
            </script>
        ";

        return $chart_html;
    }

    /**
     * [get_user_option_by_userids 根据 user_id 数组, 查询 user_id => group_name-real_name 的下拉列表]
     *
     * @DateTime 2017-12-12
     * @Author   leeprince
     * @param    [type]     $array_ids [description]
     * @return   [type]                [description]
     */
    public function get_user_option_by_userids($array_ids)
    {
        if ( empty($array_ids) || ! is_array($array_ids)) {
            return array();
        }

        $this->_CI->load->model('user_model');
        $res = $this->_CI->user_model->get_user_option_by_userids($array_ids);

        if ( ! $res) {
            return array();
        } else {
            $options = array();
            foreach ($res as $v) {
                $options[$v['user_id']] = "{$v['group_name']}: {$v['real_name']}";
            }
        }

        return $options;
    }

    /**
     * [getPagginationOffsetLimit 设置分页]
     *
     * @author leeprince <[<email address>]>
     * @return [type] [description]
     */
    public function getPaginationOffsetLimit(int $limit=0)
    {
        $current_page = (round(abs($this->_CI->input->get('per_page'))) == 0)?1 : round(abs($this->_CI->input->get('per_page'))) ;

        if ( empty($limit)) {
            $limit = $this->_CI->config->item('per_page');
        }
        $offset = ($current_page - 1) * $limit;
        return [$offset, $limit];
    }

    public function logTypeByMenuUrlData($data)
    {
        if ( ! is_array($data) || count($data) <= 0) {
            return null;
        }
        $logtypeArray = [];
        foreach ($data as $k => $v) {
            $menu_name = $v['menu_name'];
            $menu_url = $v['menu_url'];
            if ( stripos($menu_url, 'index') == false) {
                continue;
            }
            $logType = explode('/', $menu_url)[0];
            $logtypeArray[$logType] = $menu_name;
        }
        return $logtypeArray;
    }
     /**
     * [get_prod_option 查询充电商品名称下拉列表]
     *
     * @DateTime 2019-01-27
     * @Author   breite
     * @return   [type]     [description]
     */
    public function get_prod_option()
    {
        $this->_CI->load->model('ProductModel');

        $res = $this->_CI->ProductModel->get_prod_option();

        if (empty($res))
        {
            return array();
        }
        else
        {
            foreach ($res as $re)
            {
                $options[$re['id']] = $re['name'];
            }

            return $options;
        }
    }




}
