<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MachineIotTriad extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineIotTriadModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-05-09
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $deviceName = $this->input->get('deviceName');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;

        $where = [];
        $whereLike = '';
        if ($deviceName) {
            $whereLike = $deviceName;
        }

        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineIotTriadModel->getLikeCount($where, $whereLike);
        $list = $this->MachineIotTriadModel->getLikeData($where, $whereLike, $limit, $offset);
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        $this->template->admin_render('machine_iot_triad/index', $this->_data);
    }

    //导入通讯三元组信息
    public  function import()
    {
        $now = time();
        $config['upload_path']      = $this->config->item('upload')['upload_path'];
        $config['max_size']         = 1000;
        $config['allowed_types']    = 'xlsx';
        $this->load->library('upload',$config);
        $this->load->library('phpexcel/PHPExcel');
        if ( ! $this->upload->do_upload('file'))
        {
            $error = array('error' => $this->upload->display_errors());
            dump($error);
            die();
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $reader = PHPExcel_IOFactory::createReader('Excel2007');
            $PHPExcel = $reader->load($data['upload_data']['full_path']); // 文档名称
            $objWorksheet = $PHPExcel->getSheet(0);
            $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
            $highestColumn = $objWorksheet->getHighestColumn(); // 取得总列数
            $fields = array( 'device_name','device_secret','product_key');
            // 一次读取一列
            $add_data = array();
            for ($row = 2; $row <= $highestRow; $row++) {
                $one_data = array();
                for ($column = 0; $column < 3; $column++) {
                    $val = $objWorksheet->getCellByColumnAndRow($column, $row)->getValue();
                    $field = $fields[$column];
                    $one_data[$field] = $val;
                }
                $one_data ['create_time'] = $now;
                if ($one_data ['device_name']&&$one_data ['device_secret']&&$one_data ['product_key']){
                    $add_data [] = $one_data;
                }
            }
            unlink($data['upload_data']['full_path']); //保留原文件不删除
            if ($add_data){
                $re = $this->db->insert_batch('machine_iot_triad', $add_data);
                if ($re){
                    $this->add_sys_log('machine_iot_triad', $add_data);
                    echo "<script>alert('导入成功');window.location.href='/MachineIotTriad/index';</script>";
                    exit();
                }else {
                    echo "<script>alert('导入失败');window.location.href='/MachineIotTriad/index';</script>";
                    exit();
                }
            }else{
                echo "<script>alert('没有数据');window.location.href='/MachineIotTriad/index';</script>";
                exit();
            }
        }
    }

}
