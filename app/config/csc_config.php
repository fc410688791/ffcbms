<?php
defined('BASEPATH') or exit('No direct script access allowed');

// 设置时区
date_default_timezone_set('Asia/ShangHai');

// 公司信息
$config['company_name']      = '全快充(北京)科技有限公司';
$config['company_name_abbr'] = 'FFC';

// 系统信息
$config['title']             = '后台管理系统';
$config['version']           = '1.0.0 20190101';

// 静态资源路径
$config['assets_dir'] = base_url('assets');

// 每页记录数
$config['per_page'] = 20;

// 操作日志: 默认过去多长时间的数据; 单位: 天
$config['past_time'] = 7;


// 图片上传配置
$config['upload']['upload_path']   = './tempdata/uploads/'; //文件保存路径 //配置该项已转移到设备系统默认值里面
$config['upload']['allowed_types'] = 'gif|jpg|jpeg|png'; //允许文件上传类型
$config['upload']['file_name']     = date('YmdHis'); //文件重命名
// $config['upload']['max_size']      = '51200'; //单位kb: 50 M //配置该项已转移到设备系统默认值里面

//定义请求数据的方法
define('IS_POST', strtolower($_SERVER["REQUEST_METHOD"]) == 'post');//判断是否是post方法
define('IS_GET', strtolower($_SERVER["REQUEST_METHOD"]) == 'get');//判断是否是get方法
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');//判断是否是ajax请求

// 功能列表中的功能类型的下拉列表
$config['func_type_option'] = [
    1 => "菜单",
    2 => "按钮",
    3 => "链接",
    4 => "数据模块",
    5 => "数据",
];

//  设备类型
$config['machine_type'] = [
    1=>'密码器',
    2=>'充电桩',
    3=>'ONE智能快充',
    4=>'立式充电桩'
];

// 代理商角色
$config['agent_proxy_pattern'] = [
	1 => '普通代理商',
	2 => '内部自营',
	3 => '0元代理商'
];

// 支付类型
$config['pay_type'] = [
    1 => '微信支付',
    2 => '支付宝支付',
    3 => '充币支付'
];