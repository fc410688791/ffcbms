<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys extends Admin_Controller {


	public function __construct()
	{
		parent::__construct();

		$this->load->model('system_model');
	}

	/**
	 * [index 默认值列表]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$page = $this->input->get('per_page')?:1;
		$limit = $this->config->item('per_page');
		$offset = ($page-1)*$limit;

		// 获得表数据的记录数
		$total_rows = $this->system_model->get_all_count();
		// 传入一个参数返回分页链接;
		$this->_data['pagination'] = $this->create_pagination($total_rows);

		// 获得表数据的分页数据
		$systems = $this->system_model->get_limit_data($limit, $offset);
		$this->_data['systems'] = $systems;

		// 第三个的参数: modal 类型 default/info/danger/warning/success
		$this->_data['del_confirm'] = render_js_confirm('fa-trash-o', '你确认删除该默认值吗 ?', 'danger');

		$this->template->admin_render('system/index', $this->_data);
	}

	// 添加系统默认值
	public function add()
	{
		if (IS_POST)
		{
			$this->form_validation->set_rules('key_name', 'key_name', 'required|is_unique[system.key_name]', array(
				'required' => '{field} 是必填项',
				'is_unique' => '{field} 已存在',
			));
			$this->form_validation->set_rules('key_value', 'key_value', 'required', array(
				'required' => '{field} 是必填项'
			));

			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('system/add', $this->_data);
			}
			else
			{
				$key_name  = $this->input->post('key_name');
				$key_value = $this->input->post('key_value');

				$add_data = array(
					'key_name' => $key_name,
					'key_value' => $key_value,
				);

				$res = $this->system_model->add_data($add_data);
				if ( ! $res)
				{
					$this->form_err_return('服务器异常');
				}
				else
				{
					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$this->add_sys_log($key_name, $add_data);

					// 操作成功跳转
					$this->jump_success_page('添加系统默认值成功');
				}
			}
		}

		$this->template->admin_render('system/add', $this->_data);
	}

	/**
	 * [modify 修改系统默认值]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function modify()
	{
		$key_name = $this->input->get_post('key_name');

		if ( empty($key_name))
		{
			$this->jump_error_page('缺少参数');
		}

		$wh_data = array(
			'key_name' => $key_name
		);
		$systems = $this->system_model->get_one_data($wh_data);
		if ( empty($systems))
		{
			$this->jump_error_page('系统默认值不存在');
		}
		$this->_data['systems'] = $systems;

		if (IS_POST)
		{
			$this->form_validation->set_rules('key_value', 'key_value', 'required', array(
				'required' => '{field} 是必填项'
			));

			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('system/add', $this->_data);
			}
			else
			{
				$key_value = $this->input->post('key_value');

				$add_data = array(
					'key_value' => $key_value,
				);

				$res = $this->system_model->update_data($add_data, $wh_data);
				if ( ! $res)
				{
					$this->form_err_return('服务器异常');
				}
				else
				{
					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$this->add_sys_log($key_name, $add_data);

					// 操作成功跳转
					$this->jump_success_page('修改系统默认值成功');
				}
			}
		}

		$this->template->admin_render('system/modify', $this->_data);
	}

	/**
	 * [del 删除系统默认值]
	 *
	 * @DateTime 2017-11-14
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function del()
	{
		$key_name = $this->input->get('key_name');

		if ( empty($key_name))
		{
			$this->jump_error_page('缺少参数');
		}

		$wh_data = array(
			'key_name' => $key_name
		);
		$systems = $this->system_model->get_one_data($wh_data);
		if ( empty($systems))
		{
			$this->jump_error_page('系统默认值不存在');
		}

		$res = $this->system_model->del_data($wh_data);
		if ( ! $res)
		{
			$this->jump_error_page('服务器异常');
		}
		else
		{
			// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
			$this->add_sys_log($key_name, $systems);

			// 操作成功跳转
			$this->redirect_index_page('删除系统默认值成功');
		}
	}
}