<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller {


	public function __construct()
	{
		parent::__construct();

		$this->load->model('user_model');
	}

	/**
	 * [index 显示账号信息]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function profile()
	{
		if (IS_POST)
		{
			$real_name = $this->input->post('real_name');
			$password  = $this->input->post('password');
			$mobile    = $this->input->post('mobile');
			$email     = $this->input->post('email');
			$user_desc = $this->input->post('user_desc');
			$sex       = $this->input->post('sex');

			$this->form_validation->set_rules('real_name', '姓名', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('mobile', '手机号', "required|numeric|exact_length[11]", array(
				'required' => '{field} 是必填项',
				'numeric' => '{field} 格式不正确',
				'exact_length' => '{field} 格式不正确',
			));
			$this->form_validation->set_rules('email', '邮件', 'required|valid_email', array(
				'required' => '{field} 是必填项',
				'valid_email' => '{field} 格式不正确',
			));

			
			$password    = $this->input->post('password');
			if ( $password != '')
			{
				$this->form_validation->set_rules('password', '密码', 'min_length[8]|max_length[18]|regex_match[/^(?![^a-zA-Z]+$)(?!\D+$).*$/]', array(
					'min_length' => '{field} 至少8 个字符',
					'max_length' => '{field} 不能超过18 个字符',
					'regex_match' => '{field} 必须要字母与数字组合',
				));
			}

			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('user/profile', $this->_data);
			}
			else
			{
				$user_id = $this->get_user_id();

				$up_data                = array();
				$up_data['real_name']   = $real_name;
				$up_data['mobile']      = $mobile;
				$up_data['email']       = $email;
				$up_data['user_desc']   = $user_desc;
				$up_data['update_time'] = time();
				$up_data['sex']         = $sex;

				if ( ! empty($password))
				{
					$up_data['password'] = md5(md5($password));
				}

				$wh_array = array(
					'user_id' => $user_id,
				);

				$res = $this->user_model->update_data($up_data, $wh_array);

				if ( ! $res)
				{
					// 返回本页面的闪存数据
					$this->session->set_flashdata('err_msg', '服务器异常');
					$this->template->admin_render('user/profile', $this->_data);
				}
				else
				{
					// 更新账号个人 session 信息; 账号信息已不保存 session 中, 只保存账号 ID, 每次读取数据库最新账号信息; 不需要更新 session 
					// $this->top_up_session($up_data);

					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$this->add_sys_log($user_id, $up_data);

					// 操作成功跳转
					$this->_data['msg'] = '更新个人信息成功.';
					$this->template->admin_render('msg/success', $this->_data);
				}
			}
		}
		else
		{
			$this->template->admin_render('user/profile', $this->_data);
		}
	}

	/**
	 * [index 公司账号列表]
	 *
	 * @DateTime 2017-11-07
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$like_data  = array();
		$wh_data    = array();
		$like_field = $this->input->get('like_field');
		$user_group = $this->input->get('user_group');

		$page = $this->input->get('per_page')?:1;
		$limit = $this->config->item('per_page');
		$offset = ($page-1)*$limit;

		if ( ! empty($like_field))
		{
			$like_data = array(
				'user_name' => $like_field,
				'real_name' => $like_field,
				'mobile'    => $like_field,
				'email'     => $like_field
			);
		}

		if ( ! empty($user_group))
		{
			$wh_data = array(
				'user_group'   => $user_group,
			);

			$like_data = array();
		}

		// 传入一个参数返回分页链接;
		$total_rows = $this->user_model->get_search_count($like_data, $wh_data);
		$this->_data['pagination'] = $this->create_pagination($total_rows);
		
		$users = $this->user_model->get_search_data($limit, $offset, $like_data, $wh_data);

		// 获得账号角色的下拉列表的关联数组 group_id => group_name
		$user_group_option = $this->admin_process->get_user_group_option();
		$this->_data['user_group_option'] = $user_group_option;

		foreach($users as &$user)
		{
			$group_id = $user['user_group'];

			if (array_key_exists($group_id, $user_group_option))
			{
				$user['user_group'] = $user_group_option[$group_id];
			}
		}

		$this->_data['users'] = $users;

		// 第三个的参数: modal 类型 default/info/danger/warning/success
		$this->_data['pause_confirm'] = render_js_confirm('fa-pause', '你确定要封停该账号吗 ?', 'warning');
		$this->_data['play_confirm']  = render_js_confirm('fa-play', '你确认要解封该账号吗 ?', 'warning');
		$this->_data['del_confirm']   = render_js_confirm('fa-trash-o', '你确认删除该账号吗 ?', 'danger');

		$this->_data['form'] = array(
			'like_field' => $like_field,
			'user_group' => $user_group,
		);

		$this->template->admin_render('user/index', $this->_data);
	}

	/**
	 * [add 添加账号]
	 *
	 * @DateTime 2017-11-08
	 * @Author   leeprince
	 */
	public function add()
	{
		// 所有角色的下拉列表关联数组
		$user_group_option = $this->admin_process->get_user_group_option();
		$this->_data['user_group_option'] = $user_group_option;

		if (IS_POST)
		{
			$this->form_validation->set_rules('user_name', '账号', 'required|is_unique[user.user_name]', array(
				'required' => '{field} 是必填项',
				'is_unique' => '{field} 已存在',
			));
			$this->form_validation->set_rules('password', '密码', 'required|min_length[8]|max_length[18]|regex_match[/^(?![^a-zA-Z]+$)(?!\D+$).*$/]', array(
				'required' => '{field} 是必填项',
				'min_length' => '{field} 至少8个字符',
				'max_length' => '{field} 不能超过18个字符',
				'regex_match' => '{field} 必须要字母与数字组合',
			));
			$this->form_validation->set_rules('real_name', '姓名', 'required|min_length[2]|max_length[16]', array(
				'required' => '{field} 是必填项',
				'min_length' => '{field} 至少2个字符',
			));
			$this->form_validation->set_rules('user_group', '角色', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('mobile', '手机号', "required|numeric|exact_length[11]", array(
				'required' => '{field} 是必填项',
				'numeric' => '{field} 格式不正确',
				'exact_length' => '{field} 格式不正确',
			));
			/*$this->form_validation->set_rules('card_id', '身份证', "required|min_length[15]|max_length[18]", array(
				'required' => '{field} 是必填项',
				'min_length' => '{field} 格式不正确',
				'max_length' => '{field} 格式不正确',
			));
			$this->form_validation->set_rules('province', '省份', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('city', '市县', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('email', '邮件', 'required|valid_email', array(
				'required' => '{field} 是必填项',
				'valid_email' => '{field} 格式不正确',
			));*/

			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('user/add', $this->_data);
			}
			else
			{
				$user_name   = $this->input->post('user_name');
				$password    = $this->input->post('password');
				$real_name   = $this->input->post('real_name');
				$user_group  = $this->input->post('user_group');
				$mobile      = $this->input->post('mobile');
				$card_id     = $this->input->post('card_id');
				$province    = $this->input->post('province');
				$city        = $this->input->post('city');
				$email       = $this->input->post('email');
				$addr_detail = $this->input->post('addr_detail');
				$user_desc   = $this->input->post('user_desc');
				$sex         = $this->input->post('sex');

				$add_data = array(
					'user_name'   => $user_name,
					'password'    => md5(md5($password)),
					'real_name'   => $real_name,
					'user_group'  => $user_group,
					'mobile'      => $mobile,
					'card_id'     => $card_id,
					'address'     => $province.'-'.$city,
					'email'       => $email,
					'addr_detail' => $addr_detail,
					'user_desc'   => $user_desc,
					'sex'         => $sex,
					'create_time' => time(),
				);

				$res = $this->user_model->add_data($add_data);

				if ( ! $res)
				{
					$this->session->set_flashdata('err_msg', '服务器异常');
					$this->template->admin_render('user/profile', $this->_data);
				}
				else
				{
					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$add_data['password'] = NULL;
					$this->add_sys_log($user_name, $add_data);

					// 操作成功跳转
					$this->form_succ_return('添加账号成功.');
				}
			}
		}
		else
		{
			$this->template->admin_render('user/add', $this->_data);
		}
	}

	public function modify()
	{
		$this->_data['user'] = array();
		$address = array();

		$user_id = $this->input->get_post('user_id');

		if ( empty($user_id))
		{
			$this->_data['msg'] = '缺少必要参数';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$wh_data = array(
			'user_id' => $user_id
		);

		$user = $this->user_model->get_one_data($wh_data);
		if ( ! $user)
		{
			// 错误信息
			$this->_data['msg'] = '账户不存在.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$this->_data['user'] = $user;

		// 所有角色的下拉列表关联数组
		$user_group_option = $this->admin_process->get_user_group_option();
		$this->_data['user_group_option'] = $user_group_option;

		$address = explode('-', $user['address']);
		$this->_data['user']['province'] = $address[0];
		$this->_data['user']['city'] = $address[1];

		if (IS_POST)
		{
			$this->form_validation->set_rules('real_name', '姓名', 'required', array(
				'required' => '{field} 是必填项',
			));

			if ( $user_id != 1)
			{
				$this->form_validation->set_rules('user_group', '角色', 'required', array(
					'required' => '{field} 是必填项',
				));
			}
			
			$this->form_validation->set_rules('mobile', '手机号', "required|numeric|exact_length[11]", array(
				'required' => '{field} 是必填项',
				'numeric' => '{field} 格式不正确',
				'exact_length' => '{field} 格式不正确',
			));
			/*$this->form_validation->set_rules('card_id', '身份证', "required|min_length[15]|max_length[18]", array(
				'required' => '{field} 是必填项',
				'min_length' => '{field} 格式不正确',
				'max_length' => '{field} 格式不正确',
			));
			$this->form_validation->set_rules('province', '省份', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('city', '市县', 'required', array(
				'required' => '{field} 是必填项',
			));
			$this->form_validation->set_rules('email', '邮件', 'required|valid_email', array(
				'required' => '{field} 是必填项',
				'valid_email' => '{field} 格式不正确',
			));*/

			$password    = $this->input->post('password');
			if ( $password != '')
			{
				$this->form_validation->set_rules('password', '密码', 'min_length[8]|max_length[18]|regex_match[/^(?![^a-zA-Z]+$)(?!\D+$).*$/]', array(
					'min_length' => '{field} 至少8 个字符',
					'max_length' => '{field} 不能超过18 个字符',
					'regex_match' => '{field} 必须要字母与数字组合',
				));
			}

			if ($this->form_validation->run() == FALSE)
			{
				$this->template->admin_render('user/modify', $this->_data);
			}
			else
			{
				$user_name   = $this->input->post('user_name');
				$password    = $this->input->post('password');
				$real_name   = $this->input->post('real_name');
				$user_group  = $this->input->post('user_group');
				$mobile      = $this->input->post('mobile');
				$card_id     = $this->input->post('card_id');
				$province    = $this->input->post('province');
				$city        = $this->input->post('city');
				$email       = $this->input->post('email');
				$addr_detail = $this->input->post('addr_detail');
				$user_desc   = $this->input->post('user_desc');
				$sex         = $this->input->post('sex');

				$up_data = array();
				$wh_data = array();

				$up_data = array(
					'real_name'   => $real_name,
					'mobile'      => $mobile,
					'card_id'     => $card_id,
					'address'     => $province.'-'.$city,
					'email'       => $email,
					'addr_detail' => $addr_detail,
					'user_desc'   => $user_desc,
					'sex'         => $sex,
					'update_time' => time(),
				);

				if ( $password != '')
				{
					$up_data['password'] = md5(md5($password));
				}
				if ( $user_id != 1)
				{
					$up_data['user_group'] = $user_group;
				}

				
				$wh_data = array(
					'user_id' => $user_id,
				);

				// 更新账号信息
				$res = $this->user_model->update_data($up_data, $wh_data);
				if ( ! $res)
				{
					$this->_data['err_msg'] = '服务器异常';
					$this->template->admin_render('user/modify', $this->_data);
				}
				else
				{
					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$up_data['password'] = NULL;
					$this->add_sys_log($user_id, $up_data);

					// 操作成功跳转
					$this->form_succ_return('修改账号成功.');
				}
			}
		}
		else
		{
			$this->template->admin_render('user/modify', $this->_data);
		}
	}

	/**
	 * [del 删除账号]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function del()
	{
		$user_id = $this->input->get('user_id');

		if ( empty($user_id) || ! is_numeric($user_id))
		{
			// 错误信息
			$this->_data['msg'] = '缺少必要参数.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		if ( $this->get_user_id() == $user_id)
		{
			// 错误信息
			$this->_data['msg'] = '不能删除个人信息.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		if ($user_id == 1)
		{
			// 错误信息
			$this->_data['msg'] = '不能删除初始管理员.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$wh_data = array(
			'user_id' => $user_id
		);

		$exist_user = $this->user_model->get_one_data($wh_data);
		if ( ! $exist_user)
		{
			// 错误信息
			$this->_data['msg'] = '账户不存在.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		/*$this->load->model('products_model');
		$exist_pro = $this->products_model->get_all_data($wh_data);
		if ( ! empty($exist_pro))
		{
			// 错误信息
			$this->_data['msg'] = '账号被使用，不能删除；若要删除，请先将属于该账号创建的商品划拨到其它创建人.';
			$this->template->admin_render('msg/error', $this->_data);
		}*/

		$res = $this->user_model->del_data($wh_data);
		if ( ! $res)
		{
			// 错误信息
			$this->_data['msg'] = '服务器异常.';
			$this->template->admin_render('msg/error', $this->_data);
		}
		else
		{
			// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
			$exist_user['password'] = null;
			$this->add_sys_log($user_id, $exist_user);

			// 跳转至列表页
			$this->redirect_index_page('删除成功.');
		}
	}

	/**
	 * [manage_state 解封/ 封停 账户状态管理]
	 *
	 * @DateTime 2017-11-17
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function manage_state()
	{
		$user_id = $this->input->get('user_id');
		$act = $this->input->get('act');

		if ( empty($user_id) || empty($act) || ! in_array($act, array('pause', 'play')))
		{
			// 错误信息
			$this->_data['msg'] = '缺少必要参数.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		if ($user_id == 1)
		{
			// 错误信息
			$this->_data['msg'] = '不能修改初始管理员状态.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		$wh_data = array(
			'user_id' => $user_id
		);

		$exist_user = $this->user_model->get_one_data($wh_data);
		if ( ! $exist_user)
		{
			// 错误信息
			$this->_data['msg'] = '账户不存在.';
			$this->template->admin_render('msg/error', $this->_data);
		}

		if ($act == 'play')
		{
			$up_data = array(
				'status' => 1,
				'update_time' => time()
			);
			$this->_data['msg'] = '解封账号成功';
		}
		else
		{
			$up_data = array(
				'status' => 0,
				'update_time' => time()
			);
			$this->_data['msg'] = '封停账号成功';
		}

		$res = $this->user_model->update_data($up_data, $wh_data);
		if ( ! $res)
		{
			// 错误信息
			$this->_data['msg'] = '服务器异常.';
			$this->template->admin_render('msg/error', $this->_data);
		}
		else
		{
			// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
			$exist_user['password'] = null;
			$this->add_sys_log($user_id, $exist_user);

			// 操作成功跳转
			$this->redirect_index_page($this->_data['msg']);
		}
	}
}
