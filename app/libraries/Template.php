<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template {

    protected $_CI;

    protected $template;

    public function __construct()
    {	
		$this->_CI = &get_instance();
    }

    /**
     * [auth_render 登录视图模板]
     *
     * @DateTime 2017-11-07
     * @Author   leeprince
     * @param    [type]     $content [description]
     * @param    [type]     $data    [description]
     * @return   [type]              [description]
     */
    public function auth_render($content, $data = NULL)
    {
        if ( ! $content) {
            return NULL;
        } else {
            // 取消 ci 自定义的更改错误定界符 [$this->form_validation->set_error_delimiters('<div style="color:#D81B60">', '<div>');] 方式
            $error_info = validation_errors() ? validation_errors() : $this->_CI->session->flashdata('err_msg');
            if ($error_info) {
                $data['form_error_contents'] = 
                "<span class='text-red'>
                    <h5>".$error_info."</h5>
                </span>";
            } else {
                $data['form_error_contents'] = '';
            }

            $this->template['content']      = $this->_CI->load->view($content, $data, TRUE);

            return $this->_CI->load->view('_templates/template', $this->template);
        }
    }

    /**
     * [admin_render 后台视图模板]
     *
     * @DateTime 2017-11-08
     * @Author   leeprince
     * @param    [type]     $content     [显示哪个页面]
     * @param    [type]     $data        [传输那些数据]
     * @param    boolean    $hide_header [是否隐藏内容板头部]
     * @return   [type]                  [整个页面渲染]
     */
    public function admin_render($content, $data = NULL, $hide_header = FALSE)
    {
        if ( ! $content) {
            return NULL;
        } else {
            // 取消 ci 自定义的更改错误定界符 [$this->form_validation->set_error_delimiters('<div style="color:#D81B60">', '<div>');] 方式
            // CodeIgniter 支持 "flashdata" ，它指的是一种只对下一次请求有效的 session 数据， 之后将会自动被清除。;;;官方文档有关 SESSION 已建议全部使用 $_SESSION;
            $error_info = validation_errors() ? validation_errors() : $this->_CI->session->flashdata('err_msg');
            $succ_info = $this->_CI->session->flashdata('succ_msg');
            $is_auto_jump = $this->_CI->session->flashdata('is_auto_jump');
            if ( ! empty($error_info)) {
                $data['form_error_contents'] = 
                "<div class='callout callout-danger'>
                    <h5>".$error_info."</h5>
                </div>";

                unset($_SESSION['err_msg']);
            } elseif (  ! empty($succ_info)) {
                $curr_controller = $data['curr_controller'];
                $jump_url = base_url("{$curr_controller}/index");

                if ($is_auto_jump) {
                    $data['form_error_contents'] = 
                    "<div class='callout callout-success'>
                        <h5>".$succ_info."</h5>
                        <script type='text/javascript'>
                            $(function () {
                                setTimeout(window.location.href = '{$jump_url}', 1500);
                            });
                        </script>
                    </div>";
                } else {
                    $data['form_error_contents'] = 
                    "<div class='callout callout-success'>
                        <h5>".$succ_info."</h5>
                    </div>";
                }

                unset($_SESSION['succ_msg']);
                unset($_SESSION['is_auto_jump']);
            } else {
                $data['form_error_contents'] = '';
            }

            // 主页面标题
            $this->template['header']              = $this->_CI->load->view('_templates/header', $data, TRUE);
            // 主页面头部
            $this->template['main_header']         = $this->_CI->load->view('_templates/main_header', $data, TRUE);
            // 主页面左侧栏
            $this->template['main_sidebar']        = $this->_CI->load->view('_templates/main_sidebar', $data, TRUE);
            // 内容页头部
            if ( ! $hide_header) {
                $this->template['main_content_header'] = $this->_CI->load->view('_templates/main_content_header', $data, TRUE);
            }
            // 内容页
            $this->template['content']             = $this->_CI->load->view($content, $data, TRUE);
            // 主页面尾部
            $this->template['footer']              = $this->_CI->load->view('_templates/footer', $data, TRUE);

            // 以下三句取代: return $this->_CI->load->view('_templates/template', $this->template); 并可以终止脚本不在加载其它 view
            $this->_CI->load->view('_templates/template', $this->template);
            $this->_CI->output->_display();
            die();
        }
	}

}