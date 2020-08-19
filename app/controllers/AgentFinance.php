<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentFinance extends Admin_Controller {
      
    public function __construct() {
        parent::__construct();
        $this->load->model('AgentOrderModel');
    }
    
    /**
     * [index description]
     *
     * @DateTime 2019-06-19
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index() 
    {
        $data = $this->get_data();
        $this->_data['statistics_data'] = $data['statistics_data'];
        $this->_data['order_data'] = $data['order_data'];
        $this->template->admin_render('agent_finance/index', $this->_data);
    }
    
    private function get_data(){
        $reservation = $this->input->get('reservation');
        $proxy_pattern = $this->input->get('proxy_pattern');
        $now = time();
        if (!$reservation){
            $reservation = date("Y-m-d").' - '.date("Y-m-d");
        }
        
        $this->_data['reservation'] = $reservation;
        $reservation = explode(' - ', $reservation);
        list($start_time,$end_time) = $reservation;
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time) + 86400;
        $where = array();
        if ($proxy_pattern){
            $where ['a.proxy_pattern'] = $proxy_pattern;
            $this->_data['proxy_pattern'] = $proxy_pattern;
        }
        $where ['a_o.status'] = 1;
        $where ['a_o.pay_time >='] = $start_time;
        $where ['a_o.pay_time <'] = $end_time;
        $statistics_data = $this->AgentOrderModel->get_statistics($where);
        $order_data = array();
        for($i=$start_time;$i<$end_time;){
            $d = date("Y-m-d", $i);
            $j = $i + 86400;
            $where ['a_o.pay_time >='] = $i;
            $where ['a_o.pay_time <'] = $j;
            $data = $this->AgentOrderModel->get_statistics($where);
            $order_data[$d] = $data;
            $i = $j;
        }
        return array('statistics_data'=>$statistics_data,'order_data'=>$order_data);
    }
    
    /**
     * 导出月账单
     */
    public function outputExcel()
    {
        $data =  $this->get_data();
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("财务报表月账单")
        ->setTitle("财务报表月账单")
        ->setSubject("财务报表月账单");
        $header = [
            "A1"=>"日期",
            "B1"=>"总采购订单数",
            "C1"=>"总采购设备数",
            "D1"=>"总采购收入(元)",
            "E1"=>"银行入账金额(元)",
            "F1"=>"不含税净收入总额(元)",
            "G1"=>"税率",
            "H1"=>"总税额(元)",
            "I1"=>"手续费(元)"
        ];
        foreach($header as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($key,$val);
        }
        $pCoordinate = 2;
        foreach($data['order_data'] as $key => $val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$pCoordinate,$key)
            ->setCellValue("B".$pCoordinate,$val['order_count'])
            ->setCellValue("C".$pCoordinate,$val['purchase_num_sum']??0)
            ->setCellValue("D".$pCoordinate,$val['cash_fee_sum']??'0.00')
            ->setCellValue("E".$pCoordinate,round($val['cash_fee_sum']*0.994,2))
            ->setCellValue("F".$pCoordinate,round($val['cash_fee_sum']/1.06,2))
            ->setCellValue("G".$pCoordinate,'6%')
            ->setCellValue("H".$pCoordinate,round($val['cash_fee_sum']/1.06*0.06,2))
            ->setCellValue("I".$pCoordinate,round($val['cash_fee_sum']*0.006,2));
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$this->input->get('date').'月代理商账单.xlsx');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }    
    
    /**
     * 导出日账单
     */
    public function outputOrderExcel(){
        $date = $this->input->get('date');
        if ($date){
            $start_time = strtotime($date);
            $end_time = $start_time+86400;
        }else {
            exit();
        }
        $where = array();
        $where['a_o.pay_time>='] = $start_time;
        $where['a_o.pay_time<'] = $end_time;
        $where['a_o.status'] = 1;
        $data =  $this->AgentOrderModel->get_list($where, 10000, 0);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("财务报表日账单")
        ->setTitle("财务报表日账单")
        ->setSubject("财务报表日账单");
        $header = [
            "A1"=>"下单时间",
            "B1"=>"订单编号",
            "C1"=>"商品名称",
            "D1"=>"商品类型",
            "E1"=>"数量(件)",
            "F1"=>"价格(元)",
            "G1"=>"订单状态",
            "H1"=>"银行入账金额(元)",
            "I1"=>"不含税净收入总额(元)",
            "J1"=>"税率",
            "K1"=>"税额(元)",
            "L1"=>"手续费(元)"
        ];
        foreach($header as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($key,$val);
        }
        $pCoordinate = 2;
        foreach($data as $val)
        {
            if ($val['logistics_status']==1){
                $val['status_name'] = '已发货';
            }elseif ($val['logistics_status']==2){
                $val['status_name'] = '已完成';
            }else {
                $val['status_name'] = '待发货';
            }
            if ($val['type']==1){
                $val['type_name'] = '密码器';
            }elseif ($val['type']==2){
                $val['type_name'] = '物联网二代设备';
            }else {
                $val['type_name'] = '';
            }
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$pCoordinate, date('Y-m-d H:i:s', $val['create_time']))
            ->setCellValue("B".$pCoordinate, $val['purchase_trade_no'])
            ->setCellValue("C".$pCoordinate, $val['a_p_name'])
            ->setCellValue("D".$pCoordinate, $val['type_name'])
            ->setCellValue("E".$pCoordinate, $val['purchase_num'])
            ->setCellValue("F".$pCoordinate, round($val['cash_fee'],2))
            ->setCellValue("G".$pCoordinate, $val['status_name'])
            ->setCellValue("H".$pCoordinate, round($val['cash_fee']*0.994,2))
            ->setCellValue("I".$pCoordinate, round($val['cash_fee']/1.06,2))
            ->setCellValue("J".$pCoordinate, '6%')
            ->setCellValue("K".$pCoordinate, round($val['cash_fee']/1.06*0.06,2))
            ->setCellValue("L".$pCoordinate, round($val['cash_fee']*0.006,2));
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$this->input->get('date').'日代理商账单.xlsx');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}