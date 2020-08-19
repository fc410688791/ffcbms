<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group extends Admin_Controller {
	public function __construct()
	{
		parent::__construct();

		$this->load->model('user_group_model');
	}

	/**
	 * [index 所有角色列表]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function index()
	{
		// 所有角色
		$page = $this->input->get('per_page')?:1;
		$limit = $this->config->item('per_page');
		$offset = ($page-1)*$limit;

		// 获得表数据的记录数
		$total_rows = $this->user_group_model->get_all_count();
		// 传入一个参数返回分页链接;
		$this->_data['pagination'] = $this->create_pagination($total_rows);

		// 获得表数据的分页数据
		$user_groups = $this->user_group_model->get_limit_data($limit, $offset);
		$this->_data['user_groups'] = $user_groups;

		// 获得用户的下拉列表关联数组 user_id => user_name
		$user_option = $this->admin_process->get_user_option();

		$this->_data['user_option'] = $user_option;
		
		// 第三个的参数: modal 类型 default/info/danger/warning/success
		$this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该角色吗 ?', 'danger');

		$this->template->admin_render('user_group/index', $this->_data);
	}

	/**
	 * [add 添加角色]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 */
	public function add()
	{
		if (IS_POST)
		{
			$group_name = $this->input->post('group_name');

			$this->form_validation->set_rules('group_name', '角色名称', 'required', array(
				'required' => '{field} 是必填项',
			));

			if ($this->form_validation->run() == false)
			{
				$this->template->admin_render('user_group/add', $this->_data);
			}

			$wh_data = array(
				'group_name' => $group_name
			);
			$exist_group_name = $this->user_group_model->get_one_data($wh_data);
			if ( ! empty($exist_group_name))
			{
				$this->session->set_flashdata('err_msg', '该角色名称已存在');
				$this->template->admin_render('user_group/add', $this->_data);
			}

			$add_data = array(
				'group_name' => $group_name,
				'owner_id' => $this->get_user_id()
			);

			$res = $this->user_group_model->add_data($add_data);

			if ( ! $res)
			{
				$this->session->set_flashdata('err_msg', '服务器错误');
				$this->template->admin_render('user_group/add', $this->_data);
			}
			else
			{
				// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
				$this->add_sys_log($group_name, $add_data);

				// 操作成功跳转
				$this->form_succ_return('添加角色成功.');
			}
		}

		$this->template->admin_render('user_group/add', $this->_data);
	}

	/**
	 * [del 删除角色]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function del()
	{
		$group_id = $this->input->get('group_id');

		if ( empty($group_id) || ! is_numeric($group_id))
		{
			// 错误信息
			$this->_data['msg'] = '缺少必要参数.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		if ( $group_id == 1)
		{
			// 错误信息
			$this->_data['msg'] = '不能删除初始化超级管理员组.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$wh_data = array(
			'group_id' => $group_id, 
		);

		$have_user = $this->user_group_model->get_user_by_group_id($group_id);

		if ( count($have_user) >= 1)
		{
			// 错误信息
			$this->_data['msg'] = '账号组被使用，不能删除；若要删除，请先将属于该组的用户划拨到其它账号组.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$group = $this->user_group_model->get_one_data($wh_data);
		$res = $this->user_group_model->del_data($wh_data);
		if ( ! $res)
		{
			// 错误信息
			$this->_data['msg'] = '服务器异常.';
			$this->template->admin_render('msg/error', $this->_data);
		}
		else
		{
			// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
			$this->add_sys_log($group_id, $group);

			// 跳转至列表页
			$this->redirect_index_page('删除角色成功.');
		}
	}

	/**
	 * [modify 修改角色信息]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
    public function modify() 
    {
        $group_id = $this->input->get_post('group_id');
        
        if (empty($group_id) || !is_numeric($group_id)) 
        {
            // 错误信息
            $this->_data['msg'] = '缺少必要参数.';
            $this->template->admin_render('msg/error', $this->_data);
        }
        
        $wh_data = array(
            'group_id' => $group_id,
        );
        $user_group = $this->user_group_model->get_one_data($wh_data);
        if (empty($user_group)) 
        {
            // 错误信息
            $this->_data['msg'] = '角色不存在.';
            $this->template->admin_render('msg/error', $this->_data);
        }
        
        $this->_data['user_group'] = $user_group;

		// 菜单模块与功能列表联查, 查询所有在线的功能列表与菜单模块的数据
		$module_menu_datas = $this->menu_url_model->get_module_menu_name_option();

		// 功能列表中递归查找父 menu_id 为 $parent_id 的结点
		$get_menu_unlimit_child = $this->admin_process->get_menu_unlimit_child($module_menu_datas, 0);

		$menu_unlimit_data = array();
		foreach ($get_menu_unlimit_child as $gmuc_k => $gmuc_v)
		{
			$module_name = $gmuc_v['module_name'];
			$module_id   = $gmuc_v['module_id'];
			$menu_id     = $gmuc_v['menu_id'];
			$menu_name   = $gmuc_v['menu_name'];
			$is_show     = $gmuc_v['is_show'];

			$menu_unlimit_data[$module_id]['module_name'] = $module_name;
			$menu_unlimit_data[$module_id]['module_id']   = $module_id;

			$menu_unlimit_data[$module_id]['menus'][$menu_id]['menu_id'] = $menu_id;
			$menu_unlimit_data[$module_id]['menus'][$menu_id]['menu_name'] = $menu_name;
			$menu_unlimit_data[$module_id]['menus'][$menu_id]['is_show'] = $is_show;

			if ( isset($gmuc_v['children']))
			{
				$menu_unlimit_data[$module_id]['menus'][$menu_id]['children'] = $gmuc_v['children'];
			}
		}
		$this->_data['menu_unlimit_data'] = $menu_unlimit_data;

		// 功能列表中的功能类型的下拉列表
		$func_type_option = $this->config->item('func_type_option');
		$this->_data['func_type_option'] = $func_type_option;

        if (IS_POST) 
        {
			$group_name   = $this->input->post('group_name');
			$roles        = $this->input->post('roles');
			$def_index_id = $this->input->post('def_index_id');

            if( empty($roles))
            {
            	$roles=[];
            }
            $roles_str = join(',',$roles);
            
            $this->form_validation->set_rules('def_index_id', '默认首页', 'required', array(
                'required' => '{filed} 是必填项'
            ));
            
            $this->form_validation->set_rules('group_name', '角色名称', 'required', array(
                'required' => '{filed} 是必填项'
            ));
            
            if ($this->form_validation->run() == FALSE) 
            {
                $this->template->admin_render('user_group/modify', $this->_data);
            }
            
            $up_data = array(
                'group_name' => $group_name,
                'group_role'=> $roles_str,
                'def_index_id' => $def_index_id
            );
            $exist_user_group = $this->user_group_model->get_one_data($up_data);
            if (!empty($exist_user_group) && $exist_user_group['group_id'] != $group_id) 
            {
                // 错误信息
                $this->session->set_flashdata('err_msg', '角色名称已存在');
                $this->template->admin_render('user_group/modify', $this->_data);
            }
            
            $res = $this->user_group_model->update_data($up_data, $wh_data);
            
            if (!$res) 
            {
                $this->session->set_flashdata('err_msg', '服务器异常');
                $this->template->admin_render('user_group/modify', $this->_data);
            } 
            else {
                // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                $this->add_sys_log($group_id, $up_data);
                
                // 操作成功跳转
                $this->form_succ_return('修改角色成功.');
            }
        } 
        else 
        {
            $this->template->admin_render('user_group/modify', $this->_data);
        }
    }

	/**
	 * [list 成员列表]
	 *
	 * @DateTime 2017-11-11
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function list()
	{
		$group_name = $this->input->get_post('group_name');
		$group_id   = $this->input->get_post('group_id');
		if ( empty($group_name) || empty($group_id) || ! is_numeric($group_id))
		{
			// 错误信息
			$this->_data['msg'] = '缺少必要参数.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$users = $this->user_group_model->get_user_by_group_id($group_id);

		// 所有角色的下拉列表关联数组
		$user_group_option = $this->admin_process->get_user_group_option();
		$this->_data['user_group_option'] = $user_group_option;

		$this->_data['users']      = $users;
		$this->_data['group_name'] = $group_name;
		$this->_data['group_id']   = $group_id;

		if (IS_POST)
		{
			$user_ids = $this->input->post('user_ids');
			$user_group = $this->input->post('user_group');

			if ( empty($user_group) || empty($user_ids))
			{
				$this->form_err_return('缺少参数.');
			}

			if (in_array(1, $user_ids))
			{
				$this->session->set_flashdata('err_msg', '不可更改初始管理员的账号组');
				$this->template->admin_render('user_group/list', $this->_data);
			}

			$wh_in_data = array();
			$wh_in_data = $user_ids;
			$up_data = array(
				'user_group' => $user_group
			);

			$this->load->model('user_model');
			$res = $this->user_model->batch_up_user_group($up_data, $wh_in_data, 'user_id');

			if ( ! $res)
			{
				$this->session->set_flashdata('err_msg', '服务器异常');
				$this->template->admin_render('user_group/list', $this->_data);
			}
			else
			{
				// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
				$this->add_sys_log($user_ids, $up_data);

				// 操作成功
				$this->_data['msg'] = '更新用户组成功';
				$this->template->admin_render('msg/success', $this->_data);
			}
			
		}

		$this->template->admin_render('user_group/list', $this->_data);
	}
}