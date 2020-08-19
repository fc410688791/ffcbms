<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_url extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('menu_url_model');
    }

    /**
     * [index 功能列表]
     *
     * @DateTime 2017-11-11
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function index()
    {
        $like_data  = array();
        $wh_data    = array();
        $like_field = $this->input->get('like_field');
        $module_id  = $this->input->get('module_id');

        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;

        if (! empty($like_field)) {
            $like_data = array(
                'menu_name' => $like_field,
                'menu_url'  => $like_field,
            );
        }

        if (! empty($module_id)) {
            $wh_data = array(
                'module_id' => $module_id
            );

            $like_data = array();
        }

        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->menu_url_model->get_search_count($like_data, $wh_data);
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows);

        // 获得 搜索/筛选 数据
        $menu_urls = $this->menu_url_model->get_search_data($limit, $offset, $like_data, $wh_data);
        $this->_data['menu_urls'] = $menu_urls;

        // 所有在线模块下拉列表
        $wh_option_data = array(
            'online' => 1
        );
        $module_name_option = $this->admin_process->get_module_name_option($wh_option_data);
        $this->_data['module_name_option'] = $module_name_option;

        // 获得菜单下拉列表 menu_id => menu_name
        $menu_name_option = $this->admin_process->get_menu_name_option();
        $this->_data['menu_name_option'] = $menu_name_option;

        // 删除弹窗
        $this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该功能吗 ?', 'danger');

        // 搜索返回值
        $this->_data['form'] = array(
            'like_field' => $like_field,
            'module_id'  => $module_id,
        );

        $this->template->admin_render('menu_url/index', $this->_data);
    }

    /**
     * [add 添加功能]
     *
     * @DateTime 2017-11-13
     * @Author   leeprince
     */
    public function add()
    {
        // 功能列表中的功能类型的下拉列表
        $func_type_option = $this->config->item('func_type_option');
        $this->_data['func_type_option'] = $func_type_option;

        // 所有在线模块拥有下级菜单下拉列表
        $wh_data = array(
            // 'module_url' => '#/#',
            'online'     => 1
        );

        // 获得菜单模块下拉列表 module_id => module_name
        $module_name_option = $this->admin_process->get_module_name_option($wh_data);
        $this->_data['module_name_option'] = $module_name_option;

        // 菜单模块下的功能列表下拉框; module_name => array(menu_id, menu_name)
        /*$module_menu_name_option = $this->admin_process->get_module_menu_name_option();
        $this->_data['module_menu_name_option'] = $module_menu_name_option;*/

        // 菜单模块与功能列表联查, 查询所有在线的功能列表与菜单模块的数据
        $module_menu_datas = $this->menu_url_model->get_module_menu_name_option();

        // 功能列表中递归查找父 menu_id 为 $parent_id 的结点
        $get_menu_unlimit_child = $this->admin_process->get_menu_unlimit_child($module_menu_datas, 0);

        $menu_unlimit_data = array(); // 无限下拉列表的数据
        foreach ($get_menu_unlimit_child as $gmuc_k => $gmuc_v) {
            $module_name = $gmuc_v['module_name'];
            $menu_id     = $gmuc_v['menu_id'];
            $menu_name   = $gmuc_v['menu_name'];

            $menu_unlimit_data[$module_name][$menu_id]['menu_id'] = $menu_id;
            $menu_unlimit_data[$module_name][$menu_id]['menu_name'] = $menu_name;

            if (isset($gmuc_v['children'])) {
                $menu_unlimit_data[$module_name][$menu_id]['children'] = $gmuc_v['children'];
            }
        }

        $this->_data['menu_unlimit_data'] = $menu_unlimit_data;

        if (IS_POST) {
            $this->form_validation->set_rules('menu_name', '功能名称', 'required|is_unique[menu_url.menu_name]', array(
                'required' => '{field} 是必填项',
                'is_unique' => '{field} 已存在',
            ));
            $this->form_validation->set_rules('is_show', '是否在左侧菜单栏显示', 'required', array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('controller', '功能链接 - 控制器', "required", array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('action', '功能链接 - 方法', 'required', array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('module_id', '所属模块', 'required', array(
                'required' => '{field} 是必填项',
            ));

            if ($this->form_validation->run() == false) {
                $this->template->admin_render('menu_url/add', $this->_data);
            } else {
                $menu_name        = $this->input->post('menu_name');
                $is_show          = $this->input->post('is_show');
                $controller       = $this->input->post('controller');
                $action           = $this->input->post('action');
                $module_id        = $this->input->post('module_id');
                $father_menu      = $this->input->post('father_menu');
                $menu_desc        = $this->input->post('menu_desc');

                $controller = str_replace('/', '', $controller);
                $action     = str_replace('/', '', $action);
                $menu_url = $controller.'/'.$action;

                if ($is_show == 1) {
                    if ($action != 'index') {
                        $this->form_err_return('如果是左侧菜单栏显示功能 {功能链接 - 方法} 请填写:index');
                    }
                    if (! empty($father_menu)) {
                        $this->form_err_return('如果是左侧菜单栏显示功能不该拥有上一级菜单');
                    }

                    if (($action == 'del' || $action == 'modify' || $action == 'delete' || $action == 'update')) {
                        $this->form_err_return('修改 / 删除类功能不允许创建左侧菜单显示');
                    }
                }

                $wh_url_data = array(
                    'menu_url' => $menu_url
                );
                $exist_url= $this->menu_url_model->get_one_data($wh_url_data);
                if (! empty($exist_url)) {
                    $this->form_err_return('功能链接已存在');
                }

                $add_menu = array(
                    'menu_name'        => $menu_name,
                    'is_show'          => $is_show,
                    'menu_url'         => $menu_url,
                    'module_id'        => $module_id,
                    'menu_desc'        => $menu_desc,
                );

                $father_menu===''? '' :$add_menu['father_menu']=$father_menu;

                $res = $this->menu_url_model->add_data($add_menu);
                if (! $res) {
                    $this->form_err_return('服务器异常');
                } else {
                    // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                    $this->add_sys_log($menu_name, $add_menu);

                    // 操作成功跳转
                    $this->jump_success_page('添加功能成功.');
                }
            }
        }

        $this->template->admin_render('menu_url/add', $this->_data);
    }

    /**
     * [del 删除功能]
     *
     * @DateTime 2017-11-13
     * @Author   leeprince
     * @return   [type]     [description]
     */
    public function del()
    {
        $menu_id = $this->input->get('menu_id');

        if (empty($menu_id)) {
            $this->jump_error_page('缺少参数.');
        }

        $wh_data = array(
            'menu_id' => $menu_id,
        );
        $menu_url = $this->menu_url_model->get_one_data($wh_data);

        if (empty($menu_url)) {
            $this->jump_error_page('功能不存在.');
        }

        $res = $this->menu_url_model->del_data($wh_data);
        if (! $res) {
            $this->jump_error_page('服务器异常.');
        } else {
            // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
            $this->add_sys_log($menu_id, $menu_url);

            // 操作成功跳转
            $this->jump_success_page('删除功能成功.');
        }
    }

    public function modify()
    {
        $menu_id = $this->input->get_post('menu_id');

        if (empty($menu_id)) {
            $this->jump_error_page('缺少参数.');
        }

        $wh_data = array(
            'menu_id' => $menu_id,
        );
        $data = $this->menu_url_model->get_one_data($wh_data);

        if (empty($data)) {
            $this->jump_error_page('功能不存在.');
        }

        // 向数据库兼容输出, 影响不大
        $menu_url = $data['menu_url'];
        if (substr($menu_url, 0, 1) == '/') {
            $menu_url = substr($menu_url, 1, strlen($menu_url));
        }
        $url                       = explode('/', $menu_url);
        $controller                = $url[0];
        $action                    = $url[1];
        $this->_data['data']       = $data;
        $this->_data['controller'] = $controller;
        $this->_data['action']     = $action;

        // 所有在线模块拥有下级菜单下拉列表
        $wh_data = array(
            // 'module_url' => '#/#',
            'online'     => 1
        );
        $module_name_option = $this->admin_process->get_module_name_option($wh_data);
        $this->_data['module_name_option'] = $module_name_option;

        // 所有菜单对应的模块; module_name => array(menu_id, menu_name)
        /*$module_menu_name_option = $this->admin_process->get_module_menu_name_option();
        $this->_data['module_menu_name_option'] = $module_menu_name_option;*/

        $module_menu_datas = $this->menu_url_model->get_module_menu_name_option();

        // 递归查找父 menu_id 为 $parent_id 的结点
        $get_menu_unlimit_child = $this->admin_process->get_menu_unlimit_child($module_menu_datas, 0);

        $menu_unlimit_data = array();
        foreach ($get_menu_unlimit_child as $gmuc_k => $gmuc_v) {
            $module_name = $gmuc_v['module_name'];
            $g_menu_id     = $gmuc_v['menu_id'];
            $menu_name   = $gmuc_v['menu_name'];

            $menu_unlimit_data[$module_name][$g_menu_id]['menu_id'] = $g_menu_id;
            $menu_unlimit_data[$module_name][$g_menu_id]['menu_name'] = $menu_name;

            if (isset($gmuc_v['children'])) {
                $menu_unlimit_data[$module_name][$g_menu_id]['children'] = $gmuc_v['children'];
            }
        }

        $this->_data['menu_unlimit_data'] = $menu_unlimit_data;

        // 功能列表中的功能类型的下拉列表
        $func_type_option = $this->config->item('func_type_option');
        $this->_data['func_type_option'] = $func_type_option;

        if (IS_POST) {
            $this->form_validation->set_rules('menu_name', '模块名称', 'required', array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('is_show', '模块排序数字', 'required', array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('controller', '功能链接 - 控制器', "required", array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('action', '功能链接 - 方法', 'required', array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('module_id', '所属模块', 'required', array(
                'required' => '{field} 是必填项',
            ));
            $this->form_validation->set_rules('online', '是否在线', 'required', array(
                'required' => '{field} 是必填项',
            ));

            if ($this->form_validation->run() == false) {
                $this->template->admin_render('menu_url/modify', $this->_data);
            } else {
                $menu_name        = $this->input->post('menu_name');
                $is_show          = $this->input->post('is_show');
                $controller       = $this->input->post('controller');
                $action           = $this->input->post('action');
                $module_id        = $this->input->post('module_id');
                $father_menu      = $this->input->post('father_menu');
                $menu_desc        = $this->input->post('menu_desc');
                $online           = $this->input->post('online');

                $controller = str_replace('/', '', $controller);
                $action     = str_replace('/', '', $action);
                $menu_url   = $controller.'/'.$action;

                $wh_name_data = array(
                    'menu_name' => $menu_name
                );
                $exist_name = $this->menu_url_model->get_one_data($wh_name_data);
                if (! empty($exist_name) && ($exist_name['menu_id'] != $menu_id)) {
                    $this->form_err_return('功能名称已存在');
                }

                if ($is_show == 1) {
                    if ($action != 'index') {
                        $this->form_err_return('如果是左侧菜单栏显示功能 {功能链接 - 方法} 请填写:index');
                    }
                    if (! empty($father_menu)) {
                        $this->form_err_return('如果是左侧菜单栏显示功能不该拥有上一级菜单');
                    }

                    if (($action == 'del' || $action == 'modify' || $action == 'delete' || $action == 'update')) {
                        $this->form_err_return('修改 / 删除类功能不允许创建左侧菜单显示');
                    }
                }

                $wh_url_data = array(
                    'menu_url' => $menu_url
                );
                $exist_url = $this->menu_url_model->get_one_data($wh_url_data);
                if (! empty($exist_url) && ($exist_url['menu_id'] != $menu_id)) {
                    $this->form_err_return('功能链接已存在');
                }

                $up_menu = array(
                    'menu_name'        => $menu_name,
                    'is_show'          => $is_show,
                    'menu_url'         => $menu_url,
                    'module_id'        => $module_id,
                    'father_menu'      => $father_menu,
                    'menu_desc'        => $menu_desc,
                    'online'           => $online,
                );
                $wh_data = array(
                    'menu_id' => $menu_id,
                );
                $res = $this->menu_url_model->update_data($up_menu, $wh_data);
                if (! $res) {
                    $this->form_err_return('服务器异常');
                } else {
                    // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                    $this->add_sys_log($menu_name, $up_menu);

                    // 操作成功跳转
                    $this->jump_success_page('修改功能成功.');
                }
            }
        }

        $this->template->admin_render('menu_url/modify', $this->_data);
    }
}
