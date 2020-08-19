<?php
defined('BASEPATH') OR exit('No direct script access allowed');
log_message('error', PHP_EOL .
    'FRM:' . $_SERVER['REMOTE_ADDR'] . PHP_EOL .
    'URL:http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . PHP_EOL .
    'UAG:' . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL .
    'ARG:' . var_export($_REQUEST, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}


.btn {
	border-radius: 3px;
	-webkit-box-shadow: none;
	box-shadow: none;
	border: 1px solid transparent;
	display: inline-block;
	padding: 6px 12px;
	margin-bottom: 0;
	font-size: 14px;
	font-weight: 400;
	line-height: 1.42857143;
	text-align: center;
	white-space: nowrap;
	vertical-align: middle;
	-ms-touch-action: manipulation;
	touch-action: manipulation;
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	background-image: none;
	border: 1px solid transparent;
	border-radius: 4px;
	text-decoration: none;
}
.btn-refresh {
	color: #fff;
	background-color: #00c4c2;
    border-color: #008d4c;
}

.btn-default {
	color: #444;
	background-color: #f4f4f4;
	border-color: #ddd;
	margin-right: 50px;
}


</style>
</head>
<body>
	<!-- <div id="container">
		<h1><?php //echo $heading; ?></h1>
		<?php //echo $message; ?>
	</div> -->

	<center>
		
		<img src="/assets/img/img_404.png">
		<p>
			<h2>Error, 页面出错啦!!   请点击 "返回" 或者 "刷新页面" ~</h2>
		</p><br>
		<p>
			
			<a class='btn btn-default' href='javascript:history.go(-1)'>返回</a>
			<a class='btn btn-refresh' href=''>刷新页面</a>
		</p>
	</center>
</body>
</html>