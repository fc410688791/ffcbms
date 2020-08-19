<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['num_links'] = 3;

// 整个分页的周围用一些标签包起来，你可以通过下面这两个参数：
$config['full_tag_open'] = '<div class="dataTables_paginate paging_simple_numbers" id="example2_paginate"><ul class="pagination">';
$config['full_tag_close'] = '</ul></div>';

// 第一页链接的标签
$config['first_tag_open'] = '<li class="paginate_button previous" id="example2_previous">';
$config['first_tag_close'] = '</li>';

// 最后一页链接的标签。
$config['last_tag_open'] = '<li class="paginate_button next" id="example2_next">';
$config['last_tag_close'] = '</li>';

// 上一页链接的标签。
$config['prev_tag_open'] = '<li class="paginate_button previous" id="example2_previous">';
$config['prev_tag_close'] = '</li>';

// 下一页链接的标签。
$config['next_tag_open'] = ' <li class="paginate_button next" id="example2_next">';
$config['next_tag_close'] = '</li>';

// 当前页链接的标签。
$config['cur_tag_open'] = '<li class="paginate_button active"><a href="#"">';
$config['cur_tag_close'] = '</a></li>';

// 数字链接的标签。
$config['num_tag_open'] = '<li class="paginate_button ">';
$config['num_tag_close'] = '</li>';

$config['use_page_numbers'] = TRUE;
$config['page_query_string'] = TRUE;
$config['reuse_query_string'] = TRUE;
$config['first_link'] = '首页&nbsp;&nbsp;';
$config['last_link'] = '&nbsp;&nbsp;末页';
$config['prev_link'] = '上一页&nbsp;';
$config['next_link'] = '&nbsp;下一页';