<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JoinStorage extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineModel');
        $this->load->model('StorageMachineTypeRecordModel');
        $this->load->model('StorageOperateModel');
        $this->load->model('StorageBatchModel');
        $this->load->model('MachineIotTriadModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-05-09
     * @Author   black.zhang
     */
    public function index()
    {
        $operation =  $this->input->get('operation');
        if ($operation=='download'){
            $this->download();
            exit();
        }else {
            $page = $this->input->get('per_page')?:1;
            $limit = $this->config->item('per_page');
            $offset = ($page-1)*$limit;
            $key = $this->input->get('key');
            $this->_data['key'] = $key;
            $agent_product_type = $this->input->get('agent_product_type');
            $this->_data['agent_product_type'] = $agent_product_type;
            $reservation = $this->input->get('reservation');
            $this->_data['reservation'] = $reservation;
            
            
            //采购商品类型列表
            $this->load->model('AgentProductTypeModel');
            $where = array();
            $total_rows = $this->AgentProductTypeModel->get_all_data_count($where);
            $agent_product_type_list = $this->AgentProductTypeModel->get_list($where, $total_rows, 0);
            $agent_product_type_option = array();
            foreach ($agent_product_type_list as $agent_product_type_info){
                $agent_product_type_option[$agent_product_type_info['id']] = $agent_product_type_info['type_name'];
            }
            $this->_data['agent_product_type_option'] = $agent_product_type_option;
            
            $where = array();
            $where ['a.storage_type'] = 1;
            if ($key){
                $where ['d.user_name'] = $key;
            }
            if ($reservation){
                list($start_time, $end_time) = switch_reservation($reservation);
                $where ['a.create_time >='] = $start_time;
                $where ['a.create_time <'] = $end_time;
            }
            if ($agent_product_type){
                $where ['b.type'] = $agent_product_type;
            }
            $total_rows = $this->StorageMachineTypeRecordModel->get_count($where);
            $list = $this->StorageMachineTypeRecordModel->get_list($where, $limit, $offset);
            foreach ($list as &$info){
                $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
            }
            $this->_data['list'] = $list;
            // 传入一个参数返回分页链接;
            $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
            $this->template->admin_render('join_storage/index', $this->_data);
        }
    }
    
    /**
     * [info]
     *
     * @DateTime 2019-05-09
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
            $id = $this->input->get('id');
            $this->_data['id'] = $id;
            
            $join_info = $this->StorageMachineTypeRecordModel->get_info(array('a.id'=>$id));
            $join_info['storage_time'] = date('Y-m-d H:i:s', $join_info['create_time']);
            $join_info['storage_user'] = $join_info['user_name'];

            $where = array();
            $where['storage_machine_type_record_id'] = $id;
            $total_rows = $this->StorageOperateModel->get_storage($where);
            $list = $this->StorageOperateModel->get_storage_list($where, $limit, $offset);
            foreach ($list as &$info){
                $bind_triad_mark = explode('_', $info['bind_triad_mark']);
                $machine_arr = array();
                foreach ($bind_triad_mark as $triad_id){
                    $machine_id = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id,type')[0]['machine_id'];
                    $machine_arr [] = '左'.$machine_id;
                }
                $info['mac'] = join('-', $machine_arr);
                $triad_info = $this->MachineIotTriadModel->get_info(array('id'=>$triad_id));
                $info['batch_storage_num'] = $this->StorageBatchModel->get_info(array('id'=>$triad_info['batch_storage_id']))['batch_storage_num'];
            }
            $this->_data['join_info'] = $join_info;
            $this->_data['list'] = $list;
            $this->template->admin_render('join_storage/info', $this->_data);
        }
    }
    
    private function download()
    {
        $key = $this->input->get('key');
        $agent_product_type = $this->input->get('agent_product_type');
        $reservation = $this->input->get('reservation');      
        
        
        $where = array();
        $where ['a.storage_type'] = 1;
        if ($key){
            $where ['d.user_name'] = $key;
        }
        if ($reservation){
            list($start_time, $end_time) = switch_reservation($reservation);
            $where ['a.create_time >='] = $start_time;
            $where ['a.create_time <'] = $end_time;
        }
        if ($agent_product_type){
            $where ['b.type'] = $agent_product_type;
        }
        $total_rows = $this->StorageMachineTypeRecordModel->get_count($where);
        $list = $this->StorageMachineTypeRecordModel->get_list($where, $total_rows, 0);
        
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','表格内容：')
        ->setCellValue('B1','库存管理_入库列表')
        ->setCellValue('A2','序号')
        ->setCellValue('B2','商品名称')
        ->setCellValue('C2','采购商品类型')
        ->setCellValue('D2','设备类型')
        ->setCellValue('E2','入库数')
        ->setCellValue('F2','入库时间')
        ->setCellValue('G2','操作人');
        $pCoordinate = 3;
        foreach ($list as $key=>$value){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate), $key+1)
            ->setCellValue('B'.($key+$pCoordinate), $value['name'])
            ->setCellValue('C'.($key+$pCoordinate), $value['agent_product_type_name'])
            ->setCellValue('D'.($key+$pCoordinate), $value['type_name'])
            ->setCellValue('E'.($key+$pCoordinate), $value['op_type_storage_num'])
            ->setCellValue('F'.($key+$pCoordinate), date('Y-m-d H:i:s', $value['create_time']))
            ->setCellValue('G'.($key+$pCoordinate), $value['user_name']);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=库存管理_入库列表.xlsx');
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
        $id = $this->input->get('id');

        $join_info = $this->StorageMachineTypeRecordModel->get_info(array('a.id'=>$id));
        $join_info['storage_time'] = date('Y-m-d H:i:s', $join_info['create_time']);
        $join_info['storage_user'] = $join_info['user_name'];
        
        $where = array();
        $where['storage_machine_type_record_id'] = $id;
        $total_rows = $this->StorageOperateModel->get_storage($where);
        $list = $this->StorageOperateModel->get_storage_list($where, $total_rows, 0);
        
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
        foreach ($list as $key=>$value){
            $bind_triad_mark = explode('_', $value['bind_triad_mark']);
            $machine_arr = array();
            foreach ($bind_triad_mark as $triad_id){
                $machine_id = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id,type')[0]['machine_id'];
                $machine_arr [] = '左'.$machine_id;
            }
            $info['mac'] = join('-', $machine_arr);
            $triad_info = $this->MachineIotTriadModel->get_info(array('id'=>$triad_id));
            $info['batch_storage_num'] = $this->StorageBatchModel->get_info(array('id'=>$triad_info['batch_storage_id']))['batch_storage_num'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate), $key+1)
            ->setCellValue('B'.($key+$pCoordinate), $info['mac'])
            ->setCellValue('C'.($key+$pCoordinate), $join_info['name'])
            ->setCellValue('D'.($key+$pCoordinate), $join_info['agent_product_type_name'])
            ->setCellValue('E'.($key+$pCoordinate), $join_info['type_name'])
            ->setCellValue('F'.($key+$pCoordinate), $info['batch_storage_num'])
            ->setCellValue('G'.($key+$pCoordinate), $join_info['storage_time'])
            ->setCellValue('H'.($key+$pCoordinate), $join_info['storage_user']);
        }
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B1','库存管理_入库列表_'.$join_info['agent_product_type_name'].'入库数');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=库存管理_入库列表_'.$join_info['agent_product_type_name'].'入库数.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
