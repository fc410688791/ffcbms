<?php
/**
 *
 * 主入口
 *
 * @package	SystemCore
 * @author	Nurmuhammad 493661143@qq.com
 * @since	Version 0.0.1
 *
 */

/**
 *---------------------------------------------------------------
 * 环境配置
 *---------------------------------------------------------------
 *     development
 *     testing
 *     production
 */
define('ENVIRONMENT','development');
/**
 * 判断当前环境
 *
 */
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>=')) {
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		} else {
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;
	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo '当前环境有问题';
		exit(1); // EXIT_ERROR
}
/**
 * 内核路径
 *
 */
$core_path = 'SalimCore';
/**
 * 应用路径
 *
 */
$app_folder = 'app';

/**
 * 兼容web访问与cli形式访问
 */
if ((PHP_SAPI === 'cli' OR defined('STDIN')) ) {
  $cur_dir = dirname(__FILE__);
  chdir($cur_dir);
}

/**
 * 初始化内核绝对路径
 *
 */
if (($_temp = realpath($core_path)) !== FALSE) {
  $core_path = $_temp.DIRECTORY_SEPARATOR;

} else {
  $core_path = strtr(
    rtrim($core_path, '/\\'),
    '/\\',
    DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
  ).DIRECTORY_SEPARATOR;

}
if ( ! preg_match("/cli/i", php_sapi_name())){
  if ( ! is_dir($core_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo '内核路径有问题'.pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3); // EXIT_CONFIG
  }
}

/**
 * 初始化一些常量
 *
 */
//主入口就这个文件名
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
// 内核路径
define('BASEPATH', $core_path);
// 根目录绝对路径
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
// core 目录 名字
define('SYSDIR', basename(BASEPATH));

// app 路劲初始化
if (is_dir($app_folder)) {
  if (($_temp = realpath($app_folder)) !== FALSE) {
    $app_folder = $_temp;
  } else {
    $app_folder = strtr(
      rtrim($app_folder, '/\\'),
      '/\\',
      DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
    );
  }
} elseif (is_dir(BASEPATH.$app_folder.DIRECTORY_SEPARATOR)) {
  $app_folder = BASEPATH.strtr(
    trim($app_folder, '/\\'),
    '/\\',
    DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
  );
} else {
  header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
  echo '你的APP目录有问题 '.SELF;
  exit(3); // EXIT_CONFIG
}
// app 路劲
define('APPPATH', $app_folder.DIRECTORY_SEPARATOR);

// views路劲初始化
if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR)) {
  $view_folder = APPPATH.'views';
} elseif (is_dir($view_folder)) {
  if (($_temp = realpath($view_folder)) !== FALSE) {
    $view_folder = $_temp;
  } else {
    $view_folder = strtr(
      rtrim($view_folder, '/\\'),
      '/\\',
      DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
    );
  }
} elseif (is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR)) {
  $view_folder = APPPATH.strtr(
    trim($view_folder, '/\\'),
    '/\\',
    DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
  );
} else {
  header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
  echo '您的Views目录有问题'.SELF;
  exit(3); // EXIT_CONFIG
}
//views路劲
define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);
/*
 * --------------------------------------------------------------------
 * 记载启动初始化文件
 * --------------------------------------------------------------------
 */
require_once BASEPATH.'core/SalimCore.php';
 ?>
