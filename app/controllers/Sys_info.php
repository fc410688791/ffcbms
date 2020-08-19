<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_info extends Admin_Controller {

	/**
	 * [index 系统信息]
	 *
	 * @DateTime 2017-11-06
	 * @Author   leeprince
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$this->_data['get_sys_info'] = $this->get_sys_info();

		$this->template->admin_render('sys_info/index', $this->_data);
	}
}
