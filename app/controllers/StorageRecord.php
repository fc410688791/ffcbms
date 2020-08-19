<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StorageRecord extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('StorageRecordModel');
        $this->load->model('MachineIotTriadModel');
        $this->load->model('MachineModel');
        $this->load->model('StorageMachineTypeRecordModel');
        $this->load->model('StorageBatchModel');
        $this->load->model('User_model');
        $this->load->model('AgentProductModel');
        $this->load->model('FileModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-09-12
     * @Author   black.zhang
     */
    public function index()
    {
        $operation =  $this->input->get('operation');
        if ($operation=='download'){
            $this->download();
            exit();
        }else {
            /* $list = $this->StorageMachineTypeRecordModel->get_group_list(array());
             $this->_data['list'] = $list; */
            $page = $this->input->get('per_page')?:1;
            $limit = $this->config->item('per_page');
            $offset = ($page-1)*$limit;
            $where = array();
            $total_rows = $this->AgentProductModel->get_count($where);
            $list = $this->AgentProductModel->get_list($where, $limit, $offset);
            foreach($list as &$info){
                $info['thumbnail_file'] = $this->FileModel->get_url(array('id'=>$info['thumbnail_file_id']));
                $info['op_type_storage_num'] = $this->MachineIotTriadModel->get_storage(array('agent_product_id'=>$info['id'],'storage_status'=>1));
            }
            $this->_data['list'] = $list;
            // 传入一个参数返回分页链接;4
            $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
            $this->template->admin_render('storage_record/index', $this->_data);
        }
    }
    
    /**
     * [info]
     *
     * @DateTime 2019-09-12
     * @Author   black.zhang
     */
    public function info()
    {
        $operation =  $this->input->get('operation');
        if ($operation=='download'){
            $this->download_info();
            exit();
        }else {
            $page = $this->input->get('per_page')?:1;
            $limit = $this->config->item('per_page');
            $offset = ($page-1)*$limit;
            $this->_data['offset'] = $offset;
            $agent_product_id = $this->input->get('agent_product_id');
            $this->_data['agent_product_id'] = $agent_product_id;

            $where = array();
            $where ['a.storage_status'] = 1;  //入库
            $where ['a.agent_product_id'] = $agent_product_id;
            // 获得 搜索/筛选 数据的记录数
            $total_rows = $this->MachineIotTriadModel->get_storage($where);
            $field = 'a.id,a.bind_triad_mark,e.url as thumbnail_file,a.agent_product_id,b.name,c.type_name as agent_product_type_name,d.type_name,a.storage_time,a.storage_user_id,a.batch_storage_id';
            $list = $this->MachineIotTriadModel->get_storage_list($field, $where, $limit, $offset);
            foreach ($list as &$info){
                $info['storage_time'] = date('Y-m-d H:i:s', $info['storage_time']);
                $bind_triad_mark = explode('_', $info['bind_triad_mark']);
                $machine_arr = array();
                foreach ($bind_triad_mark as $triad_id){
                    $machine_id = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id,type')[0]['machine_id'];
                    $machine_arr [] = '左'.$machine_id;
                }
                $info['mac'] = join('-', $machine_arr);
                $user = $this->User_model->get_one_data(array('user_id'=>$info['storage_user_id']));
                $info['storage_user'] = $user['user_name'];
                $info['batch_storage_num'] = $this->StorageBatchModel->get_info(array('id'=>$info['batch_storage_id']))['batch_storage_num'];
            }
            $this->_data['list'] = $list;
            // 传入一个参数返回分页链接;
            $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
            $this->template->admin_render('storage_record/info', $this->_data);
        }
    }
    
    /**
     * [journal]
     *
     * @DateTime 2019-09-16
     * @Author   black.zhang
     */
    public function journal()
    {
        $agent_product_id = $this->input->get('agent_product_id');
        
        $where = array();
        $where ['a.agent_product_id'] = $agent_product_id;
        $total_rows = $this->StorageMachineTypeRecordModel->get_count($where);
        $list = $this->StorageMachineTypeRecordModel->get_list($where, $total_rows, 0);
        foreach ($list as &$info){
            $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
            if ($info['storage_type']==1){
                $info['storage_type'] = '入库';
                $info['user'] = $info['user_name'];
            }elseif ($info['storage_type']==2){
                $info['storage_type'] = '出库';
                $info['user'] = $info['agent_name'];
            }
        }
        $this->ajax_return(array('code'=>200, 'list'=>$list));
    }
    
    private function download()
    {
        $where = array();
        $total_rows = $this->AgentProductModel->get_count($where);
        $list = $this->AgentProductModel->get_list($where, $total_rows, 0);
        foreach($list as &$info){
            $info['op_type_storage_num'] = $this->MachineIotTriadModel->get_storage(array('agent_product_id'=>$info['id'],'storage_status'=>1));
        }
        //$list = $this->StorageMachineTypeRecordModel->get_group_list(array());
        
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','表格内容：')
        ->setCellValue('B1','库存管理_库存列表')
        ->setCellValue('A2','序号')
        ->setCellValue('B2','商品名称')
        ->setCellValue('C2','采购商品类型')
        ->setCellValue('D2','设备类型')
        ->setCellValue('E2','库存');
        $pCoordinate = 3;
        foreach ($list as $key=>$value){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate), $key+1)
            ->setCellValue('B'.($key+$pCoordinate), $value['name'])
            ->setCellValue('C'.($key+$pCoordinate), $value['agent_product_type_name'])
            ->setCellValue('D'.($key+$pCoordinate), $value['type_name'])
            ->setCellValue('E'.($key+$pCoordinate), $value['op_type_storage_num']);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=库存管理_库存列表.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_info()
    {
        $agent_product_id = $this->input->get('agent_product_id');
              
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','表格内容：')
        ->setCellValue('A2','序号')
        ->setCellValue('B2','设备ID')
        ->setCellValue('C2','商品名称')
        ->setCellValue('D2','采购商品类型')
        ->setCellValue('E2','设备类型')
        ->setCellValue('F2','批次')
        ->setCellValue('G2','入库时间')
        ->setCellValue('H2','操作人');
        $pCoordinate = 3;
        
        $where = array();
        $where ['a.storage_status'] = 1;  //入库
        $where ['a.agent_product_id'] = $agent_product_id;
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineIotTriadModel->get_storage($where);
        $field = 'a.id,a.bind_triad_mark,b.name,c.type_name as agent_product_type_name,d.type_name,a.storage_time,a.storage_user_id,a.batch_storage_id';
        $list = $this->MachineIotTriadModel->get_storage_list($field, $where, $total_rows, 0);
        foreach ($list as $key=>$value){
            $bind_triad_mark = explode('_', $value['bind_triad_mark']);
            $machine_arr = array();
            foreach ($bind_triad_mark as $triad_id){
                $machine_id = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id,type')[0]['machine_id'];
                $machine_arr [] = '左'.$machine_id;
            }
            $user = $this->User_model->get_one_data(array('user_id'=>$value['storage_user_id']));
            $batch_storage_num = $this->StorageBatchModel->get_info(array('id'=>$value['batch_storage_id']))['batch_storage_num'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate), $key+1)
            ->setCellValue('B'.($key+$pCoordinate), join('-', $machine_arr))
            ->setCellValue('C'.($key+$pCoordinate), $value['name'])
            ->setCellValue('D'.($key+$pCoordinate), $value['agent_product_type_name'])
            ->setCellValue('E'.($key+$pCoordinate), $value['type_name'])
            ->setCellValue('F'.($key+$pCoordinate), $batch_storage_num)
            ->setCellValue('G'.($key+$pCoordinate), date('Y-m-d H:i:s', $value['storage_time']))
            ->setCellValue('H'.($key+$pCoordinate), $user['user_name']);
        }
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B1','库存管理_库存列表_'.$value['name'].'库存');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=库存管理_库存列表_'.$value['name'].'库存.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
