<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('user_model');
	}

	public function index()
	{
        if ( ! $this->auth_is_login() )
        {
        	$this->login();
        }
        else
        {
        	// 获得用户登录成功后的 session 信息
			$user_id = $this->top_get_session('user_id');
			$this->load->model('user_model');
			// 获得该用户的所有信息
			$owner_data = $this->user_model->get_one_data(['user_id' => $user_id]);
			$group_id = $owner_data['user_group'];

			// 获得该用户所属角色的默认首页 ID
			$this->load->model('User_group_model');
	        $res_group = $this->User_group_model->get_one_data(['group_id' => $group_id]);
	        $def_index_id = $res_group['def_index_id'];

	        // 获得该用户所属角色的默认首页 ID 的 url
			$this->load->model('menu_url_model');
	        $menu = $this->menu_url_model->get_one_data(['menu_id' => $def_index_id]);
	        $index_url = $menu['menu_url'];
			if ( ! empty($index_url))
			{
				redirect($index_url);
			}
			else
			{
				redirect('admin/index');
			}
	        
        }
	}

	/**
	 * [index 登录]
	 *
	 * @DateTime 2017-10-28
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function login()
	{
		if (IS_POST)
		{
			$user_name    = $this->input->post('user_name');
			$password     = $this->input->post('password');
			$captcha_code = $this->input->post('captcha_code');

			// 表单验证验证类: 在控制器中回调自定义的验证函数
			// 要调用一个回调函数只需把函数名加一个 "callback_" 前缀 并放在验证规则里。 如果你需要在你的回调函数中调用一个额外的参数，你只需要在回调函数后面用[]把参数 （这个参数只能是字符串类型）括起来，例如：callback_foo[bar] ， 其中 bar 将成为你的回调函数中的第二个参数。
			$this->form_validation->set_rules('user_name', '账号 / 手机号', 'trim|required', array(
				'required' => '{field} 是必填项.',
			));

			$this->form_validation->set_rules('password', '密码', "trim|required|callback_formvalidate_check_user[$user_name]", array(
				'required' => '{field} 是必填项.',
			));

			if ( ! empty($this->top_get_session('captcha_code')))
			{
				$this->form_validation->set_rules('captcha_code', '验证码', 'trim|required|callback_check_code', array(
					'required' => '{field} 是必填项.',
				));
			}

			if (($this->form_validation->run() == FALSE))
			{
				// 统计失败次数; 失败次数达到三次之后每次都需要输入验证码
				// 开始保存 session
				if ( ! empty($this->top_get_session('err_count')))
				{
					$this->top_set_session('err_count', $this->top_get_session('err_count') + 1);
				}
				else
				{
					$this->top_set_session('err_count', 1);
				}

				if ($this->top_get_session('err_count') >= 3)
				{
					$this->load->helper('captcha');
					$vals = array(
						'img_path'    => './tempdata/captcha/',
						'img_url'     => base_url('/tempdata/captcha/'),
						'word_length' => 4,
						'font_size'   => 16,
						'img_width'   => '150',
						'img_height'  => 30,
						'expiration'  => 7200,
						'pool'        => '0123456789',
					    );
					$cap = create_captcha($vals);

					$this->top_set_session('captcha_code', $cap['word']);
					$this->_data['img'] = $cap['image'];
				}

				// 登录失败
				$this->template->auth_render('auth/login', $this->_data);
			}
			else
			{
				// 清除验证码 session
				$this->top_del_session(array('captcha_code', 'err_count'));

				// 登录成功
				// 获取用户 id
				$where = "password = '".md5(md5($password)).
					"' AND status = 1".
					" AND (user_name = '$user_name' OR mobile = '$user_name')";
				$user_id = $this->user_model->get_user_id($where);

				// 更新用户登录信息
				$up_array = array(
					'login_ip' => get_ip(),
					'login_time' => time()
				);
				$wh_array = array(
					'user_id' => $user_id
				);
				$res = $this->user_model->update_data($up_array, $wh_array);

				if ( ! $res)
				{
					// 返回本页面的闪存数据
					$this->session->set_flashdata('err_msg', '服务器异常');
					$this->template->auth_render('auth/login', $this->_data);
				}
				else
				{
					// 开始保存 session
					$this->top_set_session('user_id', $user_id);
					
					// 记住密码, 暂时不需要了
					/*$remember = (bool)$this->input->post('remember');
					if ($remember)
					{
						$encrypted = $this->cookie_encrypt($user_id);
						$this->set_cookie_remember($encrypted, 30);
					}*/

					// 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
					$this->add_sys_log($user_id, $up_array);

					$this->index();
				}
			}
		}
		else
		{
			$this->template->auth_render('auth/login', $this->_data);
		}
	}

	/**
	 * [formvalidate_check_user 表单验证验证类, 在控制器中回调自定义的验证函数]
	 *
	 * @DateTime 2017-10-30
	 * @Author   leeprince
	 * @param    [type]     $password  [description]
	 * @param    [type]     $user_name [description]
	 * @return   [type]                [description]
	 */
	public function formvalidate_check_user($password, $user_name)
	{
		$where = "password = '".md5(md5($password)).
			"' AND status = 1".
			" AND (user_name = '$user_name' OR mobile = '$user_name')";
		$exist = $this->user_model->get_user_id($where);

		if ( ! $exist)
		{
			$this->form_validation->set_message('formvalidate_check_user', '账号或者密码错误.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * [check_code 检查验证码是否正确]
	 *
	 * @DateTime 2017-12-21
	 * @Author   leeprince
	 * @param    [type]     $captcha_code [description]
	 * @return   [type]                   [description]
	 */
	public function check_code($captcha_code)
	{

		if ( $captcha_code != $this->top_get_session('captcha_code'))
		{
			$this->form_validation->set_message('check_code', '验证码错误.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * [logout 注销退出登录]
	 *
	 * @DateTime 2017-11-01
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function logout()
	{
		$this->del_cookie_remember();

		$this->top_del_session();

		redirect('auth/login');
	}
}
