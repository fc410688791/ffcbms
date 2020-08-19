<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MachineBatch extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineBatchModel');
        $this->load->model('MachineModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-01-16
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
       
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineBatchModel->get_count_all();
        $list = $this->MachineBatchModel->get_list($where = array(), $limit, $offset);
        foreach ($list as &$info){
            $info ['create_time'] = date('Y-m-d H:i:s', $info ['create_time']);
            $machine_list = $this->MachineModel->get_field_list(array('batch_id'=>$info['id']), 'machine_id', 'id');
            $start = $machine_list[0]['machine_id'];
            $end = $machine_list[count($machine_list)-1]['machine_id'];
            $info ['clip_contents'] =  "<p>二维码链接示例:https://f.qkc88.cn/userwxapp?c=".$start."</p><p>链接中唯一的参数是设备ID, 本批次设备ID范围为:".$start." ~ ".$end."</p>";
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        
        //密码本列表
        $this->load->model('PasswordBookModel');
        $password_book_list = $this->PasswordBookModel->get_list($where = array(), $limit=1000);
        $this->_data['password_book_list'] = $password_book_list;
        
        //文案列表
        $this->load->model('PasswordCopywritingModel');
        $password_copywriting_list = $this->PasswordCopywritingModel->get_list($where = array(), $limit=1000);
        $this->_data['password_copywriting_list'] = $password_copywriting_list;

        $this->template->admin_render('machine_batch/index', $this->_data);
    }

    /**
     * [add 添加功能]
     *
     * @DateTime 2019-01-01
     * @Author   black.zhang
     */
    public function add()
    {
        $book_id = (int)$this->input->post('book_id');
        $copywriting_id = (int)$this->input->post('copywriting_id');
        $product_id = $this->input->post('product_id');
        $default_product_id = $this->input->post('default_product_id');
        $name = $this->input->post('name');
        $number = (int)$this->input->post('number');
        $module_plate_num = (int)$this->input->post('module_plate_num');
        $module_plate_code_num = (int)$this->input->post('module_plate_code_num');
        if(!$number||!$module_plate_num||!$module_plate_code_num){
            $this->ajax_return(array('code'=>400, 'msg'=>'请求参数错误.'));
        }
        
        $now = time();
        $batch_no = $this->MachineBatchModel->get_count_all();
        $batch_no = $batch_no+1;
        $y = date('y');
        $machine_batch = array();
        $machine_batch ['book_id'] = $book_id;  //密码本ID
        $machine_batch ['copywriting_id'] = $copywriting_id;  //文案ID
        $machine_batch ['name'] = $name;  //产品编号
        $machine_batch ['number'] = $number;  //产品数量
        $machine_batch ['batch_no'] = $batch_no;  //产品编号
        $machine_batch ['create_time'] = $now;  //创建时间
        
        // 事务开始
        $this->db->trans_start();
        $this->db->insert('machine_batch', $machine_batch);
        $re = $this->db->insert_id();
        if ($re) {
            $machine_data = array();
            $batch_no = substr(strval($batch_no+1000),1,3);
            for ($i=1;$i<=$number;$i++){
                $machine_info = array();
                $no = $newNumber = substr(strval($i+100000),1,5);
                $machine_info ['machine_id'] = 'f'. $y . $batch_no . $no;  //设备码;前缀（f）+ 年份后两位数 + 批次（三位数）+ 设备数量递增（5位数）
                $machine_info ['batch_id'] = $re;
                $machine_info ['status'] = 2;  //状态：1:正常；2:主板待绑定；3:组装待印文案；4:待激活；
                $machine_info ['book_id'] = $book_id;
                $machine_info ['copywriting_id'] = $copywriting_id;
                $machine_info ['product_id'] = join(',',$product_id);
                $machine_info ['default_product_id'] = $default_product_id;
                $machine_info ['create_time'] = $now;
                $machine_info ['module_plate_num'] = $module_plate_num;
                $machine_info ['module_plate_code_num'] = $module_plate_code_num;
                $machine_data [] = $machine_info;
            }
            $this->db->insert_batch('machine', $machine_data);
        }
        // 事务提交
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE)
        {
            $this->ajax_return(array('code'=>400, 'msg'=>'创建设备ID失败.'));
        }
        else
        {
            $this->add_sys_log('machine_batch', $machine_data);
            $this->ajax_return(array('code'=>200, 'msg'=>'创建设备ID成功.'));
        }
    }
}
