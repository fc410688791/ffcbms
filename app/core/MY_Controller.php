<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends Controller 
{

	// 初始话信息
	protected $_data = array();

	// cookie 和 session 名称
	private $_cookie_session_name = 'ffc_admin';

	// 存放个人基本信息的 session 名称
	protected $_base_info_name = 'owner_data';

	/**
	 * [__construct 扩展核心类: 定义的类必须继承自父类; 类名和文件名必须以 MY_ 开头]
	 *
	 * @DateTime 2018-01-15
	 * @Author   leeprince
	 */
	public function __construct()
	{
		parent::__construct();

		//set_exception_handler(array($this, "_myException"));

		$this->load->helper(array('url', 'common', 'db_proxy', 'log'));

		$this->load->library(array('form_validation', 'session', 'template', 'admin_process'));

		$this->config->load('csc_config');
		$this->config->load('code');

		$this->load->model('sys_log_model');

		$this->_data['company_name']               = $this->config->item('company_name');
		$this->_data['title']                      = $this->config->item('title');
		$this->_data['assets_dir']                 = $this->config->item('assets_dir');

		$this->_data['curr_controller'] = $this->router->class;
		$this->_data['curr_action']     = $this->router->method;
	}

	/**
	 * [top_set_session 保存 session]
	 *
	 * @DateTime 2017-12-13
	 * @Author   leeprince
	 * @param    [type]     $key  [description]
	 * @param    [type]     $data [description]
	 * @return   [type]           [description]
	 */
	protected function top_set_session($key, $data)
	{
		if ( ! is_string($key))
		{
			return FALSE;
		}

		$cname = $this->get_cookie_session_name();
		$_SESSION[$cname][$key] = $data;
	}

	/**
	 * [top_get_session 获取 session 信息]
	 *
	 * @DateTime 2017-12-13
	 * @Author   leeprince
	 * @param    [type]     $key [description]
	 * @return   [type]          [description]
	 */
	protected function top_get_session($key = '')
	{
		$cname = $this->get_cookie_session_name();

		if ( is_string($key) && ! empty($key))
		{
			return isset($_SESSION[$cname][$key])? $_SESSION[$cname][$key] : '';
		}
		else
		{
			return $_SESSION[$cname];
		}
		
	}

	/**
	 * [top_del_session 删除 session]
	 *
	 * @DateTime 2017-12-21
	 * @Author   leeprince
	 * @param    string     $k [description]
	 * @return   [type]        [description]
	 */
	protected function top_del_session($k = '')
	{
		$cname = $this->get_cookie_session_name();

		if ( is_string($k) && ! empty($k))
		{
			if (isset($_SESSION[$cname][$k]))
			{
				unset($_SESSION[$cname][$k]);
			}
			else
			{
				return NULL;
			}
		}
		elseif ( is_array($k) && ! empty($k))
		{
			foreach ($k as $sk)
			{
				if (isset($_SESSION[$cname][$sk]))
				{
					unset($_SESSION[$cname][$sk]);
				}
			}
		}
		else
		{
			unset($_SESSION[$cname]);
		}
	}

	/**
	 * [get_cookie_session_name 获取自定义的 cookie 名称]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	protected function get_cookie_session_name()
	{

		$cookie_session_name = $this->_cookie_session_name;
		$encrypt_name = md5(md5($cookie_session_name));

		return $encrypt_name;

		// AES-128 加密算法和 CBC 加密模式或者其他加密算法和模式加密后的长度太长, 当 session 的 key 存入数据失败
		/*$cookie_session_name = $this->_cookie_session_name;
		$this->load->library('encryption');
		$this->encryption->initialize(
	    	array(
	    		'driver' => 'openssl',
	            'cipher' => 'DES',
	            'mode' => 'CBC'
	    	)
		);
		$encrypt_name = $this->encryption->encrypt($cookie_session_name);
		return $encrypt_name;*/
	}

	/**
	 * [cookie_encrypt 加密 cookie 信息]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @param    [type]     $value [description]
	 * @return   [type]            [description]
	 */
	protected function cookie_encrypt($value)
	{
		if ( ! $value)
		{
			return FALSE;
		}

		$this->load->library('encryption');
		
		return $this->encryption->encrypt($value);
	}

	/**
	 * [cookie_decrypt 解密 cookie 信息]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @param    [type]     $value [description]
	 * @return   [type]            [description]
	 */
	protected function cookie_decrypt($value)
	{
		if( ! $value)
		{
			return FALSE;
		}

		$this->load->library('encryption');

		return $this->encryption->decrypt($value);
	}

	/**
	 * [set_cookie_remember 通过输入类设置 cookie 信息; 还有一种方式是通过 cookie 辅助函数设置]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @param    [type]     $encrypted [description]
	 * @param    integer    $day       [description]
	 */
	protected function set_cookie_remember($encrypted, $day = 30)
	{
		$this->input->set_cookie($this->get_cookie_session_name(), $encrypted, 3600*24*$day);
	}

	/**
	 * [get_cookie_remember 通过输入类获取 cookie 信息; 还有一种方式是通过 cookie 辅助函数获取]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	protected function get_cookie_remember()
	{
		$encrypted = $this->input->cookie($this->get_cookie_session_name());

		return $this->cookie_decrypt($encrypted);
	}

	/**
	 * [del_cookie_remember 删除记住密码 cookie]
	 *
	 * @DateTime 2017-11-01
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	protected function del_cookie_remember()
	{
		$this->input->set_cookie($this->get_cookie_session_name(), '', 0);
	}

	/**
	 * [auth_is_login 检查是否登录]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	protected function auth_is_login()
	{
		if ( empty($this->top_get_session('user_id')))
		{
			// 记住密码暂时不需要了
			/*if ( empty($this->get_cookie_remember()))
			{
				return FALSE;
			}*/

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * 已废弃! [get_curr_url_ctrl_act 获得当前访问的 url 的控制器和方法 ]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	protected function get_curr_url_ctrl_act()
	{
		$url_string        = uri_string();

		$sys_dir_separator = '/';
		$url_array         = explode($sys_dir_separator, $url_string);

		$ctrl_act_array = array();
		if (empty($url_array) || count($url_array) != 2)
		{
			return array();
		}

		$ctrl_act_array['controller'] = strtolower($url_array[0]);
		$ctrl_act_array['action']     = strtolower($url_array[1]);

		return $ctrl_act_array;
	}

	/**
	 * [add_sys_log 添加系统日志]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    [type]     $class_obj [操作对象]
	 * @param    [type]     $result    [操作结果]
	 */
	protected function add_sys_log($class_obj, $result)
	{
		if ( empty($class_obj))
		{
			return FALSE;
		}

		if (is_array($class_obj))
		{
			$class_obj = implode(',', $class_obj);
		}

		$curr_controller = $this->_data['curr_controller'];
		$curr_action     = $this->_data['curr_action'];

		if ( empty($this->_data['owner_data'])) {
			// 获得用户登录成功后的 session 信息
			$user_id = $this->top_get_session('user_id');
			$this->load->model('user_model');
			// 获得该用户的所有信息
			$owner_data = $this->user_model->get_one_data(array('user_id' => $user_id));
			$this->_data['owner_data'] = $owner_data;
		}
		$user_name = $this->_data['owner_data']['user_name'];
		return $this->sys_log_model->add_log($user_name, $curr_action, $curr_controller, $class_obj, json_encode($result));
	}

	/**
	 * [env_is_production 判断不是线上环境, 跳过部分步骤方便测试. 跳过部分步骤时, 请谨慎考虑后面流程]
	 *
	 * @DateTime 2018-03-21
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	protected function env_isnot_production()
	{
		if (ENVIRONMENT == 'production')
		{
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * [myException 异常处理器]
	 *
	 * @DateTime 2018-03-27
	 * @Author   leeprince
	 * @param    [type]     $e [description]
	 * @return   [type]        [description]
	 */
	public function _myException($ex)
	{
		$logMsg = '[抛出异常内容]'. $ex->getMessage(). PHP_EOL .
				   '[发生异常的文件]'. $ex->getFile() . PHP_EOL .
				   '[发生异常的文件行号]'. $ex->getLine();
	    $this->exceptionLog($logMsg);

	    $exceptionMsg = '服务器异常';
	    if ( $this->env_isnot_production())
	    {
	    	$exceptionMsg .= "[{$ex->getMessage()}]";
	    }
	    exit(json_encode(array('code' => -10000, 'msg' => $exceptionMsg)));
	}

	/**
	 * [exceptionLog 异常的日志记录]
	 *
	 * @DateTime 2018-05-03
	 * @Author   leeprince
	 * @param    [type]     $msg [description]
	 * @return   [type]          [description]
	 */
	private function exceptionLog($msg)
	{
		if (is_array($msg) || is_object($msg))
		{
			$msg = json_encode($msg);
		}

		$msg = '[发生异常! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!]'. PHP_EOL .
		$msg . PHP_EOL .
        '[请求URL]:'.current_url() . PHP_EOL .
		'[请求IP]:'.get_ip() . PHP_EOL .
        '[请求的参数]:'.var_export($_REQUEST, true) . PHP_EOL .
        '[请求的浏览器类型]:'.$_SERVER['HTTP_USER_AGENT'];

		if ( ! empty($selfFile))
		{
			log_message('ERROR', $msg);
		}
		else
		{
			log_message('ERROR', $msg);
		}
	}

}


class Admin_Controller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ( ! $this->auth_is_login())
		{
			redirect('auth/login');
		}

		$this->_data['company_name_abbr'] = $this->config->item('company_name_abbr');
		$this->_data['version']           = $this->config->item('version');

		if ( empty($this->_data['owner_data'])) {
			// 获得用户登录成功后的 session 信息
			$user_id = $this->top_get_session('user_id');
			$this->load->model('user_model');
			// 获得该用户的所有信息
			$owner_data = $this->user_model->get_one_data(array('user_id' => $user_id));
			$this->_data['owner_data'] = $owner_data;
		}

		// 获得菜单模块对应的菜单信息
		$module_menu_tree = $this->admin_process->get_module_menu_tree();
		$this->_data['module_menu_tree'] = $module_menu_tree;
		$menu_url_tree = $this->admin_process->get_menu_url_tree();
		$this->_data['menu_url_tree'] = $menu_url_tree;

		//校验权限
        $url = $this->_data['curr_controller'].'/'.$this->_data['curr_action'];
		$this->load->model('User_group_model');
		$group_id  = $owner_data['user_group'];
		$res_group = $this->User_group_model->get_one_data(['group_id' => $group_id]);
		$roles     = explode(',',$res_group['group_role']);
        $this->_data['roles'] = $roles;
		$this->load->model('Menu_url_model');
        $res_menu = $this->Menu_url_model->get_one_data(['menu_url' => $url]);

        if(in_array($res_menu['menu_id'],$roles)===FALSE)
        {
        	$this->template->admin_render('msg/permission', $this->_data, TRUE);
            exit('无权操作');
        }
	}

	/**
	 * [check_user 检查某个功能链接的 id 是否在权限范围内]
	 *
	 * @DateTime 2018-02-27
	 * @Author   leeprince
	 * @param    [type]     $role_id [description]
	 * @return   [type]              [description]
	 */
	public function check_user($role_id)
	{
		return in_array($this->_data['roles'],$role_id);
	}

	/**
	 * [get_user_id 获得用户 id]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_user_id()
	{
		return $this->_data['owner_data']['user_id'];
	}

	/**
	 * [get_user_group 获得用户所属角色 ID]
	 *
	 * @DateTime 2017-12-11
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_user_group()
	{
		return $this->_data['owner_data']['user_group'];
	}

	/**
	 * [get_user_name 获得用户名称]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_user_name()
	{
		return $this->_data['owner_data']['user_name'];
	}

	/**
	 * [create_pagination 创建分页]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @param    [type]     $total_rows [分页记录数]
	 * @return   [type]                 [description]
	 */
	public function create_pagination($total_rows, $per_page = 0)
	{
		$this->load->library('pagination');

		$curr_controller = $this->_data['curr_controller'];
		$curr_action     = $this->_data['curr_action'];

		if ( empty($per_page)) {
			$per_page = $this->config->item('per_page');
		}

		$config['base_url']   = $this->config->item('base_url')."/{$curr_controller}/{$curr_action}";
		$config['total_rows'] = $total_rows;
		$config['per_page']   = $per_page;

		$this->pagination->initialize($config);

		return $this->pagination->create_links();
	}

	/**
	 * [get_sys_info 获得系统信息]
	 *
	 * @DateTime 2017-11-09
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function get_sys_info()
	{
		$sys_info_array = array ();
		$sys_info_array['gmt_time']     = date("Y年m月d日 H:i:s");
		$sys_info_array['server_ip']    = gethostbyname($_SERVER ["SERVER_NAME"]);
		$sys_info_array['software']     = $_SERVER ["SERVER_SOFTWARE"];
		$sys_info_array['port']         = $_SERVER ["SERVER_PORT"];
		$sys_info_array['admin']        = $_SERVER ["SERVER_ADMIN"]?? '';
		$sys_info_array['diskfree']     = intval ( diskfreespace (".") / (1024 * 1024) ) . 'Mb';
		$sys_info_array['current_user'] = $this->get_user_name();
		$sys_info_array['timezone']     = date_default_timezone_get();

		$sys_info_array ['mysql_version'] = $this->db->version();
		return $sys_info_array;
	}

	/**
	 * [form_err_return 表单验证类不能满足验证失败后返回当前表单的情况]
	 *
	 * @DateTime 2017-11-12
	 * @Author   leeprince
	 * @param    string     $err_msg [description]
	 * @return   [type]              [description]
	 */
	protected function form_err_return($err_msg = "表单验证失败.")
	{
		$curr_controller = $this->_data['curr_controller'];
		$curr_action     = $this->_data['curr_action'];

		$this->session->set_flashdata('err_msg', $err_msg);
		$this->template->admin_render("{$curr_controller}/{$curr_action}", $this->_data);
	}

	/**
	 * [form_succ_return 表单验证类不能满足验证失败后返回当前表单的情况]
	 *
	 * @DateTime 2017-12-26
	 * @Author   leeprince
	 * @param    string     $succ_msg     [description]
	 * @param    boolean    $is_auto_jump [是否自动跳转到列表页]
	 * @return   [type]                   [description]
	 */
	protected function form_succ_return($succ_msg = "表单验证成功.", $is_auto_jump = TRUE, $view = null)
	{
		$curr_controller = $this->_data['curr_controller'];
		$curr_action     = $this->_data['curr_action'];
        if($view == null)
        {
            $view = "{$curr_controller}/{$curr_action}";
        }

		$this->session->set_flashdata('succ_msg', $succ_msg);
		$this->session->set_flashdata('is_auto_jump', $is_auto_jump);

		$this->template->admin_render($view, $this->_data);
	}

	/**
	 * [jump_error_page 操作或者请求失败后跳转到失败页面]
	 *
	 * @DateTime 2017-11-18
	 * @Author   leeprince
	 * @param    string     $msg                 [错误内容]
	 * @param    string     $return_jump_url     [错误内容页中返回的 url | 跳转失败页面后, 可控的返回跳转页面]
	 * @param    string     $return_jump_content [错误内容页中返回的 url 的 a 标签内容] 
	 * @return   [type]                          [description]
	 */
	public function jump_error_page($msg = '操作失败.', $return_jump_url = '', $return_jump_content = '返回列表页',$auto_jump = false)
	{
		if ( ! empty($return_jump_url))
		{
			$this->_data['return_jump_url']= $return_jump_url;
		}
		$this->_data['msg']                 = $msg;
		$this->_data['return_jump_content'] = $return_jump_content;
        $this->_data['auto_jump']  = $auto_jump;
		$this->template->admin_render('msg/error', $this->_data);
	}

	/**
	 * [jump_success_page 操作或者请求失败后跳转到成功页面]
	 *
	 * @DateTime 2017-11-18
	 * @Author   leeprince
	 * @param    string     $msg                 [成功内容]
	 * @param    string     $return_jump_url     [成功内容页中返回的 url | 跳转成功页面后, 可控的返回跳转页面]
	 * @param    string     $return_jump_content [成功内容页中返回的 url 的 a 标签内容]
	 * @return   [type]                          [description]
	 */
	public function jump_success_page($msg = '操作成功.', $return_jump_url = '', $return_jump_content = '返回上一页')
	{
		if ( ! empty($return_jump_url))
		{
			$this->_data['return_jump_url']= $return_jump_url;
		}

		$this->_data['msg']                 = $msg;
		$this->_data['return_jump_content'] = $return_jump_content;
		$this->template->admin_render('msg/success', $this->_data);
	}

	/**
	 * [ajax_return ajax 返回 json 数据]
	 *
	 * @DateTime 2017-11-16
	 * @Author   leeprince
	 * @param    [type]     $data_array [description]
	 * @return   [type]                 [description]
	 */
	public function ajax_return($data_array)
	{
		if ( ! is_array($data_array))
		{
			return FALSE;
		}

		echo json_encode($data_array);
		exit;
	}

	/**
	 * [form_toastr_return 表单操作成功跳转回到本页面, 并弹窗提示操作结果后自动跳转到列表页 - 暂只适用添加操作, 不适用所有操作,因为某些操作没有单独的页面..谨慎使用]
	 *
	 * @DateTime 2017-12-05
	 * @Author   leeprince
	 * @param    string     $msg [description]
	 * @return   [type]          [description]
	 */
	public function form_toastr_return($msg = '操作成功',$curr_controller = "",$curr_action = "")
	{
        if($curr_controller == ""){
            $curr_controller = $this->_data['curr_controller'];
        }
        if($curr_action == ""){
            $curr_action     = $this->_data['curr_action'];
        }
		$j = base_url("{$curr_controller}/index");
		$this->_data['toastr_return'] = "
		<!-- 本页面弹出操作结果模态框 -->
		<div class='modal fade modal-success' id='toastrModal'  tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		  <div class='modal-dialog'>
		    <div class='modal-content'>

		      <div class='modal-header'>
		        <h4 class='modal-title' id='toastrModalLabel'>
		          <i class='fa fa-fw fa-check'></i>
		          {$msg}
		        </h4>
		      </div>
		    </div><!-- /.modal-content -->
		  </div>
		</div>
		<!-- 本页面弹出操作结果模态框 - end -->

		<script type='text/javascript'>
		  $(function () {
		    $('#toastrModal').modal({
		      backdrop: 'static', // 空白处不关闭.
		      keyboard: false // ESC 键盘不关闭.
		    });
		    $('#toastrModal').modal('show');
		    setTimeout(function(){
		      window.location.href= '{$j}'; // http://p.opm.com/products/index;
		      // $('#toastrModal').modal('hide');
		    }, 1500);
		    // setTimeout(window.location.href ='{$j}', 1000);
		  });

		</script>

		";
		$this->template->admin_render("{$curr_controller}/{$curr_action}", $this->_data);
	}

	/**
	 * [redirect_index_page 跳转至列表页或者指定链接]
	 *
	 * @DateTime 2017-12-05
	 * @Author   leeprince
	 * @param    string     $msg [description]
	 * @return   [type]          [description]
	 */
	public function redirect_index_page($msg = '操作成功.', $ctrl_action_url = '')
	{
		if ( ! empty($ctrl_action_url))
		{
			redirect("{$ctrl_action_url}");
		}
		else
		{
			$curr_controller = $this->_data['curr_controller'];

			redirect("{$curr_controller}/index");
		}
		
	}

	/**
	 * [prt_sql 打印最后一条 sql]
	 *
	 * @DateTime 2018-01-19
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function prt_sql($msg = '您最后执行的 sql')
	{
		prt('[[[['.$msg.']]]]: <br>'.$this->db->last_query());
	}

	/**
	 * [prt_exit_sql 打印最后一条 sql 并结束脚本]
	 *
	 * @DateTime 2018-01-19
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function prt_exit_sql($msg = '您最后执行的 sql')
	{	
		prt_exit('[[[['.$msg.']]]]:  <br>'.$this->db->last_query());
	}

	/**
	 * [log_sql 将最后一条 sql 写入日志]
	 *
	 * @DateTime 2018-01-19
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function log_sql($msg = '您最后执行的 sql')
	{
		log_message('debug', '[[[['.$msg.']]]]: '."\r\n".$this->db->last_query());
	}
}