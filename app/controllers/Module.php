<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Module extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('module_model');
	}

	/**
	 * [index 模块列表]
	 *
	 * @DateTime 2017-11-11
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$page = $this->input->get('per_page')?:1;
		$limit = $this->config->item('per_page');
		$offset = ($page-1)*$limit;

		// 获得表数据的记录数
		$total_rows = $this->module_model->get_all_count();
		// 传入一个参数返回分页链接;
		$this->_data['pagination'] = $this->create_pagination($total_rows);

		// 获得表数据的分页数据
		$modules = $this->module_model->get_limit_data($limit, $offset);
		$this->_data['modules'] = $modules;

		// 删除弹窗
		$this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该模块吗 ?', 'danger');

		$this->template->admin_render('module/index', $this->_data);
	}

	/**
	 * [add 添加模块]
	 *
	 * @DateTime 2017-11-12
	 * @Author   leeprince
	 */
	public function add()
	{
		if (IS_POST)
		{
			$this->form_validation->set_rules('module_name', '模块名称', 'required|is_unique[module.module_name]', array(
				'required' => '{field} 是必填项',
				'is_unique' => '{field} 已存在',
			));
			$this->form_validation->set_rules('controller', '模块链接 - 控制器', "required", array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('action', '模块链接 - 方法', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('module_icon', '模块图标', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('module_sort', '模块排序数字', 'required', array(
				'required' => '{field} 是必填项',
			));
			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('module/add', $this->_data);
			}
			else
			{
				$module_name = $this->input->post('module_name');
				$controller  = $this->input->post('controller');
				$action      = $this->input->post('action');
				$module_icon = $this->input->post('module_icon');
				$module_sort = $this->input->post('module_sort');
				$module_desc = $this->input->post('module_desc');

				if ($controller != '#' && $action != '#')
				{
					$wh_url_data = array(
						'module_url' => $controller.'/'.$action,
					);
					$exist_url= $this->module_model->get_one_data($wh_url_data);
					if ( ! empty($exist_url))
					{
						$this->form_err_return('模块链接已存在');
					}
				}

				$add_data = array(
					'module_name' => $module_name, 
					'module_url'  => $controller.'/'.$action, 
					'module_icon' => $module_icon, 
					'module_sort' => $module_sort, 
					'module_desc' => $module_desc, 
				);

				$res = $this->module_model->add_data($add_data);
				if ( ! $res)
				{
					$this->form_err_return('服务器异常');
				}
				else
				{
					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$this->add_sys_log($module_name, $add_data);

					// 操作成功跳转
					$this->jump_success_page('添加模块成功.');
				}
			}
		}

		$this->template->admin_render('module/add', $this->_data);
	}

	/**
	 * [del 删除模块]
	 *
	 * @DateTime 2017-11-12
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function del()
	{
		$module_id = $this->input->get('module_id');

		if ( empty($module_id))
		{
			$this->jump_error_page('缺少参数.');
		}

		$wh_data = array(
			'module_id' => $module_id,
		);
		$module = $this->module_model->get_one_data($wh_data);

		if ( empty($module))
		{
			$this->jump_error_page('模块不存在.');
		}

		$res = $this->module_model->del_data($wh_data);
		if( ! $res)
		{
			$this->jump_error_page('服务器异常.');
		}
		else
		{
			// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
			$this->add_sys_log($module_id, $module);

			// 操作成功跳转
			$this->jump_success_page('删除模块成功.');
		}
	}

	public function modify()
	{
		$module_id = $this->input->get_post('module_id');

		if ( empty($module_id))
		{
			$this->jump_error_page('缺少参数.');
		}

		$wh_data = array(
			'module_id' => $module_id,
		);
		$module = $this->module_model->get_one_data($wh_data);
		if ( empty($module))
		{
			$this->jump_error_page('模块不存在.');
		}

		$url                       = explode('/', $module['module_url']);
		$controller                = $url[0];
		$action                    = $url[1];
		$this->_data['module']     = $module;
		$this->_data['controller'] = $controller;
		$this->_data['action']     = $action;

		if (IS_POST)
		{
			$this->form_validation->set_rules('module_name', '模块名称', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('controller', '模块链接 - 控制器', "required", array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('action', '模块链接 - 方法', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('module_icon', '模块图标', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('module_sort', '模块排序数字', 'required', array(
				'required' => '{field} 是必填项',
			));
			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('module/modify', $this->_data);
			}
			else
			{
				$module_name = $this->input->post('module_name');
				$controller  = $this->input->post('controller');
				$action      = $this->input->post('action');
				$online      = $this->input->post('online');
				$module_icon = $this->input->post('module_icon');
				$module_sort = $this->input->post('module_sort');
				$module_desc = $this->input->post('module_desc');

				$wh_name_data = array(
					'module_name' => $module_name,
				);
				$exist_name = $this->module_model->get_one_data($wh_name_data);
				if ( ! empty($exist_name) && $exist_name['module_id'] != $module_id)
				{
					$this->form_err_return('模块名称已存在');
				}

				if ($controller != '#')
				{
					$wh_url_data = array(
						'module_url' => $controller.'/'.$action,
					);
					$exist_url= $this->module_model->get_one_data($wh_url_data);
					if ( ! empty($exist_url) && $exist_url['module_id'] != $module_id)
					{
						$this->form_err_return('模块链接已存在');
					}
				}

				$up_data = array(
					'module_name' => $module_name, 
					'module_url'  => $controller.'/'.$action, 
					'online'      => $online, 
					'module_icon' => $module_icon, 
					'module_sort' => $module_sort,
					'module_desc' => $module_desc, 
				);

				$res = $this->module_model->update_module($up_data, $wh_data);
				if ( ! $res)
				{
					$this->form_err_return('服务器异常');
				}
				else
				{
					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$this->add_sys_log($module_id, $up_data);

					// 操作成功跳转
					$this->jump_success_page('修改模块成功.');
				}
			}
		}

		$this->template->admin_render('module/modify', $this->_data);
	}

	/**
	 * [list 获得菜单模块对应的功能列表]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function list()
	{
		$module_id = $this->input->get_post('module_id');

		if ( empty($module_id))
		{
			$this->jump_error_page('缺少参数.');
		}

		$wh_data = array(
			'module_id' => $module_id
		);
		$exist_module = $this->module_model->get_one_data($wh_data);
		if ( empty($exist_module))
		{
			$this->jump_error_page('该菜单模块不存在.');
		}

		$menus = $this->module_model->get_menu_list_by_module_id($module_id);
		if ( empty($menus))
		{
			$this->jump_error_page('该菜单模块不存在菜单列表.');
		}
		$this->_data['menus'] = $menus;

		$this->_data['module'] = $exist_module;

		// 获得菜单下拉列表 menu_id => menu_name
		$menu_name_option = $this->admin_process->get_menu_name_option();
		$this->_data['menu_name_option'] = $menu_name_option;

		// 所有在线模块拥有下级菜单下拉列表
		$wh_data = array(
			'module_url' => '#/#',
			'online'     => 1
		);
		$module_name_option = $this->admin_process->get_module_name_option($wh_data);
		$this->_data['module_name_option'] = $module_name_option;

		if (IS_POST)
		{
			$this->form_validation->set_rules('menu_ids[]', '功能列表', 'required', array(
				'required' => "{field} 要选择其中一个"
			));
			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('module/list', $this->_data);
			}

			$menu_ids = $this->input->post('menu_ids');
			$new_module_id = $this->input->post('new_module_id');

			if ( empty($menu_ids) || empty($new_module_id))
			{
				$this->form_err_return('缺少参数');
			}

			$wh_in_data = array();
			$wh_in_data = $menu_ids;
			$up_data = array(
				'module_id' => $new_module_id
			);

			$this->load->model('menu_url_model');
			$res = $this->menu_url_model->batch_up_data($up_data, $wh_in_data, 'menu_id');

			if ( ! $res)
			{
				$this->form_err_return('服务器异常');
			}
			else
			{
				// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
				$this->add_sys_log($menu_ids, $up_data);

				// 操作成功
				$this->jump_success_page('更新功能列表成功');
			}
		}

		$this->template->admin_render('module/list', $this->_data);
	}
}