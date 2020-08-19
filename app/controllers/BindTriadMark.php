<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BindTriadMark extends Admin_Controller
{    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineModel');
        $this->load->model('OrderModel');
        $this->load->model('AgentProductModel');
        $this->load->model('AgentMerchantModel');
    }
    

    /**
     * [index 桩列表]
     *
     * @DateTime 2019-12-31
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        $this->_data['offset'] = $offset;
        $merchant_id = $this->input->get('merchant_id');
        $this->_data['merchant_id'] = $merchant_id;

        $where = array();
        $where ['bind_triad_mark <>'] = '';
        $order = 'id';
        if ($merchant_id){
            $where ['merchant_id'] = $merchant_id;
        }
        $group = 'bind_triad_mark,merchant_id';
        $total_rows = $this->MachineModel->get_group_count($where, $group);
        $field = 'bind_triad_mark,agent_product_id,merchant_id,position';
        $list = $this->MachineModel->get_group_list($field, $where, $group, $order, $limit, $offset);
        foreach ($list as &$info){
            $agent_product = $this->AgentProductModel->get_info(array('id'=>$info['agent_product_id']));
            $info['name'] = $agent_product['name'];
            $bind_triad_mark = explode('_', $info['bind_triad_mark']);
            $machine_arr = array();
            $arr = array();
            foreach ($bind_triad_mark as $triad_id){
                $machine_list = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id');
                foreach ($machine_list as $machine){
                    $arr [] = $machine['machine_id'];
                }
                if ($machine_list){
                    $machine_arr [] = '左'.$machine_list[0]['machine_id'];
                }
            }
            if ($machine_list){
                $merchant = $this->AgentMerchantModel->get_info(array('id'=>$info['merchant_id']));
                $info['merchant'] = $merchant['name'];
                $info['position'] = $merchant['address'].$info['position'];
                $where = array();
                $where ['status'] = 1;
                $where ['complete_status <>'] = 3;
                $where_in = array('field'=>'machine_id', 'list'=>$arr);
                $order_statistics = $this->OrderModel->orderStatistics($where, $where_in);
                $info['pay_count'] = $order_statistics['pay_count'];
                $info['cash_fee'] = round($order_statistics['cash_fee_statistics']+$order_statistics['settlement_amount_statistics'], 2);
            }else {
                $info['merchant'] = '';
                $info['position'] = '';
                $info['pay_count'] = 0;
                $info['cash_fee'] = 0;
            }
            $info['mac'] = join('-', $machine_arr);
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
        
        $this->template->admin_render('bind_triad_mark/index', $this->_data);
    }
    
    /**
     * [output 导出流水]
     *
     * @DateTime 2019-01-06
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function output()
    {
        $today = strtotime(date('Y-m-d', time()));
        $time_type = $this->input->get('time_type');
        $merchant_id = $this->input->get('merchant_id');
        
        $where = array();
        $where ['bind_triad_mark <>'] = '';
        $order = 'id';
        if ($merchant_id){
            $where ['merchant_id'] = $merchant_id;
        }
        $group = 'bind_triad_mark,merchant_id';
        $total_rows = $this->MachineModel->get_group_count($where, $group);
        $field = 'bind_triad_mark,agent_product_id,merchant_id,position';
        $list = $this->MachineModel->get_group_list($field, $where, $group, $order, $total_rows, 0);
        for($i=ord("A");$i <= ord("Z");$i++){
            $column [] = chr($i);
        }
        foreach ($column as $v){
            $column [] = 'A'.$v;
        }
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','设备')
        ->setCellValue('B1','位置');
        if ($time_type=='m'){
            $num = date('d', $today);
        }else {
            $num = $time_type;
        }
        $date = array();
        for ($i=2;$i<=$num+1;$i++){
            $date [] = $day = date('m月d日', $today-($num+1-$i)*86400);
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($column[$i].'1', $day);
        }
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue($column[$i].'1', '总额')
        ->setCellValue($column[$i+1].'1', '日均');
        $pCoordinate = 2;
        foreach ($list as $key=>$info){
            $bind_triad_mark = explode('_', $info['bind_triad_mark']);
            $machine_arr = array();
            $arr = array();
            foreach ($bind_triad_mark as $triad_id){
                $machine_list = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id');
                foreach ($machine_list as $machine){
                    $arr [] = $machine['machine_id'];
                }
                $machine_arr [] = '左'.$machine_list[0]['machine_id'];
            }
            if ($arr){
                $merchant = $this->AgentMerchantModel->get_info(array('id'=>$info['merchant_id']));
                $info['position'] = $merchant['address'].$info['position'];
            }else {
                $info['position'] = '';
            }
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($pCoordinate+$key), join('-', $machine_arr))
            ->setCellValue('B'.($pCoordinate+$key), $info['position']);
            $where = array();
            $where ['status'] = 1;
            $where ['complete_status <>'] = 3;
            $where_in = array('field'=>'machine_id', 'list'=>$arr);
            $sum = 0;
            for ($i=2;$i<=$num+1;$i++){
                $start = $today-($num+1-$i)*86400;
                $end = $start+86400;
                $where ['pay_time >='] = $start;
                $where ['pay_time <'] = $end;
                $order_statistics = $this->OrderModel->orderStatistics($where, $where_in);
                $fee = round($order_statistics['cash_fee_statistics']+$order_statistics['settlement_amount_statistics'], 2);
                $sum += $fee;
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($column[$i].($pCoordinate+$key), $fee);
            }
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($column[$i].($pCoordinate+$key), $sum)
            ->setCellValue($column[$i+1].($pCoordinate+$key), round($sum/$num,3));
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=桩每日流水.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
