<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_log extends Admin_Controller 
{

	/**
	 * [index 系统日志信息]
	 *
	 * @DateTime 2017-10-31
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$search_wh = array();
		$reservation = $this->input->get('reservation');
		$class_name  = $this->input->get('class_name');
		$user_name   = $this->input->get('user_name');

		$page = $this->input->get('per_page')?:1;
		$limit = $this->config->item('per_page');
		$offset = ($page-1)*$limit;

		if ( ! empty($reservation))
		{
			list($star_time, $end_time) = switch_reservation($reservation);
			$search_wh['op_time >='] = $star_time;
			$search_wh['op_time <='] = $end_time;
		}
		else
		{
			$search_wh['op_time >='] = strtotime('- '.$this->config->item('past_time').' day');
			$search_wh['op_time <='] = strtotime(date('Y-m-d 23:59:59'));

			$reservation = default_reservation($this->config->item('past_time'));
		}

		if ( ! empty($class_name))
		{
			$search_wh['class_name'] = $class_name;
		}

		if ( ! empty($user_name))
		{
			$search_wh['user_name'] = $user_name;
		}

		// 分页
		$total_rows = $this->sys_log_model->get_all_count($search_wh);
		$this->_data['pagination'] = $this->create_pagination($total_rows);

		$sys_logs = $this->sys_log_model->get_page_log($limit, $offset, $search_wh);

		$this->_data['sys_logs'] = $sys_logs;
		$this->_data['form'] = array(
			'reservation' => $reservation,
			'class_name'  => $class_name,
			'user_name'   => $user_name,
		);

		// 记录类型
		$res = $this->admin_process->loadModelGetData('menu_url_model', [], 'findAll');
		$log_type = $this->admin_process->logTypeByMenuUrlData($res);
		$log_type['auth'] = '登录 / 退出';
		$this->_data['log_type'] = $log_type;

		$this->template->admin_render('sys_log/index', $this->_data);
	}
}
