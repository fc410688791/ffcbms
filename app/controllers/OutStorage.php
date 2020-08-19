<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OutStorage extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineModel');
        $this->load->model('StorageMachineTypeRecordModel');
        $this->load->model('StorageOperateModel');
        $this->load->model('StorageBatchModel');
        $this->load->model('MachineIotTriadModel');
        $this->load->model('AgentOrderModel');
        $this->load->model('AgentAddressModel');
        $this->load->model('PositionModel');
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
            $where ['a.storage_type'] = 2;
            if ($key){
                $where = "c.id = '$key' or c.user_name = '$key'";
                $this->_data['key'] = $key;
            }
            if ($reservation){
                list($start_time, $end_time) = switch_reservation($reservation);
                $where ['a.create_time >='] = $start_time;
                $where ['a.create_time <'] = $end_time;
                $this->_data['reservation'] = $reservation;
            }
            if ($agent_product_type){
                $where ['b.type'] = $agent_product_type;
            }
            $total_rows = $this->StorageMachineTypeRecordModel->get_count($where);
            $list = $this->StorageMachineTypeRecordModel->get_list($where, $limit, $offset);
            foreach ($list as &$info){
                $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
                $agent_order = $this->AgentOrderModel->get_info(array('id'=>$info['agent_order_id']));
                $agent_address = $this->AgentAddressModel->get_info(array('id'=>$agent_order['address_id']));
                $position = $this->PositionModel->get_info(array('id'=>$agent_address['position_id']));
                $info['purchase_trade_no'] = $agent_order['purchase_trade_no'];
                $info['address_name'] = $agent_address['name'];
                $info['mobile'] = $agent_address['mobile'];
                $info['position'] = str_replace(",", "", $position['name']).$agent_address['position'];
            }
            $this->_data['list'] = $list;
            // 传入一个参数返回分页链接;
            $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
            $this->template->admin_render('out_storage/index', $this->_data);
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
            
            $out_info = $this->StorageMachineTypeRecordModel->get_info(array('a.id'=>$id));
            $out_info['storage_out_time'] = date('Y-m-d H:i:s', $out_info['create_time']);
            $agent_order = $this->AgentOrderModel->get_info(array('id'=>$out_info['agent_order_id']));
            $agent_address = $this->AgentAddressModel->get_info(array('id'=>$agent_order['address_id']));
            $position = $this->PositionModel->get_info(array('id'=>$agent_address['position_id']));
            $out_info['purchase_trade_no'] = $agent_order['purchase_trade_no'];
            $out_info['address_name'] = $agent_address['name'];
            $out_info['mobile'] = $agent_address['mobile'];
            $out_info['position'] = str_replace(",", "", $position['name']).$agent_address['position'];
            
            $where = array();
            $where['storage_machine_type_record_id'] = $id;
            $total_rows = $this->StorageOperateModel->get_storage($where);
            $list = $this->StorageOperateModel->get_storage_list($where, $total_rows, $offset);
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
            $this->_data['out_info'] = $out_info;
            $this->_data['list'] = $list;
            $this->template->admin_render('out_storage/info', $this->_data);
        }
    }
    
    private function download()
    {
        $key = $this->input->get('key');
        $agent_product_type = $this->input->get('agent_product_type');
        $reservation = $this->input->get('reservation');
        
        $where = array();
        $where ['a.storage_type'] = 2;
        if ($key){
            $where = "c.id = '$key' or c.user_name = '$key'";
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
        ->setCellValue('B1','库存管理_出库列表')
        ->setCellValue('A2','序号')
        ->setCellValue('B2','商品名称')
        ->setCellValue('C2','采购商品类型')
        ->setCellValue('D2','设备类型')
        ->setCellValue('E2','出库数')
        ->setCellValue('F2','出库时间')
        ->setCellValue('G2','代理商ID')
        ->setCellValue('H2','姓名')
        ->setCellValue('I2','出库信息');
        $pCoordinate = 3;
        foreach ($list as $key=>$value){
            $agent_order = $this->AgentOrderModel->get_info(array('id'=>$value['agent_order_id']));
            $agent_address = $this->AgentAddressModel->get_info(array('id'=>$agent_order['address_id']));
            $position = $this->PositionModel->get_info(array('id'=>$agent_address['position_id']));
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate), $key+1)
            ->setCellValue('B'.($key+$pCoordinate), $value['name'])
            ->setCellValue('C'.($key+$pCoordinate), $value['agent_product_type_name'])
            ->setCellValue('D'.($key+$pCoordinate), $value['type_name'])
            ->setCellValue('E'.($key+$pCoordinate), $value['op_type_storage_num'])
            ->setCellValue('F'.($key+$pCoordinate), date('Y-m-d H:i:s', $value['create_time']))
            ->setCellValue('G'.($key+$pCoordinate), $value['agent_id'])
            ->setCellValue('H'.($key+$pCoordinate), $value['agent_name'])
            ->setCellValue('I'.($key+$pCoordinate), $agent_address['name']."\r\n".$agent_address['mobile']."\r\n".(str_replace(",", "", $position['name']).$agent_address['position']));
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=库存管理_出库列表.xlsx');
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
        
        $out_info = $this->StorageMachineTypeRecordModel->get_info(array('a.id'=>$id));
        $out_info['storage_out_time'] = date('Y-m-d H:i:s', $out_info['create_time']);
        $agent_order = $this->AgentOrderModel->get_info(array('id'=>$out_info['agent_order_id']));
        $agent_address = $this->AgentAddressModel->get_info(array('id'=>$agent_order['address_id']));
        $position = $this->PositionModel->get_info(array('id'=>$agent_address['position_id']));
        $out_info['purchase_trade_no'] = $agent_order['purchase_trade_no'];
        $out_info['name'] = $agent_address['name'];
        $out_info['mobile'] = $agent_address['mobile'];
        $out_info['position'] = str_replace(",", "", $position['name']).$agent_address['position'];
        
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
        ->setCellValue('G2','出库时间')
        ->setCellValue('H2','代理商ID')
        ->setCellValue('I2','姓名')
        ->setCellValue('J2','出库信息');
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
            ->setCellValue('C'.($key+$pCoordinate), $out_info['name'])
            ->setCellValue('D'.($key+$pCoordinate), $out_info['agent_product_type_name'])
            ->setCellValue('E'.($key+$pCoordinate), $out_info['type_name'])
            ->setCellValue('F'.($key+$pCoordinate), $info['batch_storage_num'])
            ->setCellValue('G'.($key+$pCoordinate), $out_info['storage_out_time'])
            ->setCellValue('H'.($key+$pCoordinate), $out_info['agent_id'])
            ->setCellValue('I'.($key+$pCoordinate), $out_info['agent_name'])
            ->setCellValue('J'.($key+$pCoordinate), $agent_address['name']."\r\n".$agent_address['mobile']."\r\n".(str_replace(",", "", $position['name']).$agent_address['position']));
        }
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B1','库存管理_出库列表_'.$out_info['agent_product_type_name'].'出库数');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=库存管理_出库列表_'.$out_info['agent_product_type_name'].'出库数.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    /**
     * [get_replace]
     *
     * @DateTime 2019-11-29
     * @Author   black.zhang
     */
    public function get_replace()
    {
        $id = $this->input->get('id');
        $out_info = $this->StorageMachineTypeRecordModel->get_info(array('a.id'=>$id));
        $where = array('a.agent_product_id'=>$out_info['agent_product_id'], 'a.storage_status <>'=>0);
        $field = 'a.id,a.storage_status,a.batch_storage_id,a.bind_triad_mark';
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineIotTriadModel->get_storage($where);
        $list = $this->MachineIotTriadModel->get_storage_list($field, $where, $total_rows, 0);
        foreach ($list as &$info){
            $bind_triad_mark = explode('_', $info['bind_triad_mark']);
            $machine_arr = array();
            foreach ($bind_triad_mark as $triad_id){
                $machine_id = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id,type')[0]['machine_id'];
                $machine_arr [] = '左'.$machine_id;
            }
            $info['mac'] = join('-', $machine_arr);
            $batch_storage = $this->StorageBatchModel->get_info(array('id'=>$info['batch_storage_id']));
            $info['batch_storage'] = $batch_storage['batch_storage_date'].'-'.$batch_storage['batch_storage_no'];
            if ($info['storage_status']==1){
                $info['status_name'] = '已入库';
                $info['status_color'] = 'green';
            }elseif ($info['storage_status']==2){
                $info['status_name'] = '已出库';
                $info['status_color'] = 'red';
            }else{
                $info['status_name'] = '-';
            }
        }
        $data = array('out_info'=>$out_info, 'list'=>$list);
        $this->ajax_return(array('code'=>200, 'data'=>$data));
    }
    
    /**
     * [replace]
     *
     * @DateTime 2019-11-29
     * @Author   black.zhang
     */
    public function replace()
    {
        $id = $this->input->post('id');
        $bind_triad_mark = $this->input->post('bind_triad_mark');
        $replace_bind_triad_mark = $this->input->post('replace_bind_triad_mark');
        if ($bind_triad_mark){
            $op_type_storage_num = 1;
        }else {
            $out_info = $this->StorageMachineTypeRecordModel->get_info(array('a.id'=>$id));
            $op_type_storage_num = abs($out_info['op_type_storage_num']);
        }
        if (count($replace_bind_triad_mark)==$op_type_storage_num){//替换数量相等
            $where = array();
            $where['storage_machine_type_record_id'] = $id;
            if ($bind_triad_mark){
                $where['bind_triad_mark'] = $bind_triad_mark;
            }
            $total_rows = $this->StorageOperateModel->get_storage($where);
            $list = $this->StorageOperateModel->get_storage_list($where, $total_rows, 0);
            // 事务开始
            $this->db->trans_start();
            foreach ($list as $key=>$value){
                $replace_triad_arr = explode('_', $replace_bind_triad_mark[$key]);
                $triad_arr = explode('_', $value['bind_triad_mark']);
                foreach ($triad_arr as $k=>$v){
                    $this->StorageOperateModel->update(array('bind_triad_mark'=>$replace_bind_triad_mark[$key],'triad_id'=>$replace_triad_arr[$k]), array('storage_machine_type_record_id'=>$id,'triad_id'=>$v));//替换
                    $this->MachineIotTriadModel->update(array('storage_status'=>1), array('id'=>$v));//恢复入库
                    $this->MachineIotTriadModel->update(array('storage_status'=>2), array('id'=>$replace_triad_arr[$k]));//出库
                }  
            }
            // 事务提交
            $this->db->trans_complete();
            if ($this->db->trans_status() == FALSE)
            {
                $this->ajax_return(array('code'=>400, 'msg'=>'替换失败.'));
            }
            else
            {
                $this->add_sys_log($id, $replace_bind_triad_mark);
                $this->ajax_return(array('code'=>200, 'msg'=>'替换成功.'));
            }
        }else {//替换数量不相等
            $this->ajax_return(array('code'=>400, 'msg'=>'替换数量不相等'));
        }
    }
}
