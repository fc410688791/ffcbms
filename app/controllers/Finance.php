<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends Admin_Controller {
      
    public function __construct() {
        parent::__construct();
        $this->load->model('OrderModel');
    }
    
    /**
     * [index description]
     *
     * @DateTime 2019-01-17
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index() 
    {
        //支付类型列表;
        $pay_type_option = $this->config->item('pay_type');
        $this->_data['pay_type_option'] = $pay_type_option;
        $data = $this->get_data();
        $this->_data['order_count'] = $data['all_count']['order_count'];
        $this->_data['refund_count'] = $data['all_count']['refund_count'];
        $this->_data['order_data'] = $data['order_data'];
        $this->template->admin_render('finance/index', $this->_data);
    }
    
    private function get_data(){
        $reservation = $this->input->get('reservation');
        $pay_type = $this->input->get('pay_type');
        $now = time();
        if (!$reservation){
            $reservation = date("Y-m-d").' - '.date("Y-m-d");
        }
        $this->_data['pay_type'] = $pay_type;
        $this->_data['reservation'] = $reservation;
        $reservation = explode(' - ', $reservation);
        list($start_time,$end_time) = $reservation;
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time) + 86400;
        
        $all_count = $this->get_count($start_time, $end_time, $pay_type);
        
        //每日数据
        // 加载 Redis 类库
        $this->load->library('RedisDB');
        // 连接 Redis
        $redis = $this->redisdb->connect();
        $redis->select(1);
        $order_data = array();
        for($i=$start_time;$i<$end_time;){
            $d = date("Y-m-d", $i);
            $j = $i + 86400;
            /* $data = $redis->get($d);
            if ($data){
                $data = json_decode($data, true);
            }else {
                $data = $this->get_count($i, $j);
                if ($i<($now-86400)){
                    $redis->set($d,json_encode($data));
                }
            } */
            $data = $this->get_count($i, $j, $pay_type);
            $order_data[$d] = $data;
            $i = $j;
        }
        return array('all_count'=>$all_count, 'order_data'=>$order_data);
    }
    
    /**
     * 导出财务数据
     */
    public function outputExcel()
    {
        $data =  $this->get_data();
        $pay_type = $this->input->get('pay_type');
        if ($pay_type==1){
            $pay_type_name = '微信';
        }else if ($pay_type==2){
            $pay_type_name = '支付宝';
        }else {
            $pay_type_name = '';
        }
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("财务报表月账单")
        ->setTitle("财务报表月账单")
        ->setSubject("财务报表月账单");
        $header = [
            "A1"=>"日期",
            "B1"=>"总订单数(个)",
            "C1"=>"总收入(元)",
            "D1"=>"退款订单(个)",
            "E1"=>"退款金额(元)",
            "F1"=>"净收入总额(元)",
            "G1"=>"银行入账金额(元)",
            "H1"=>"不含税净收入总额(元)",
            "I1"=>"税率",
            "J1"=>"总税额(元)",
            "K1"=>"手续费(元)"
        ];
        foreach($header as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($key,$val);
        }
        $pCoordinate = 2;
        foreach($data['order_data'] as $key => $val)
        {
            $fee = $val['order_count']['order_sum'] - $val['refund_count']['order_sum'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$pCoordinate,$key)
            ->setCellValue("B".$pCoordinate,$val['order_count']['order_count'])
            ->setCellValue("C".$pCoordinate,round($val['order_count']['order_sum'],2))
            ->setCellValue("D".$pCoordinate,$val['refund_count']['order_count'])
            ->setCellValue("E".$pCoordinate,round($val['refund_count']['order_sum'],2))
            ->setCellValue("F".$pCoordinate,round($fee,2))
            ->setCellValue("G".$pCoordinate,round($fee*0.994,2))
            ->setCellValue("H".$pCoordinate,round($fee/1.06,2))
            ->setCellValue("I".$pCoordinate,'6%')
            ->setCellValue("J".$pCoordinate,round($fee/1.06*0.06,2))
            ->setCellValue("K".$pCoordinate,round($fee*0.006,2));
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$pay_type_name.$this->input->get('date').'月账单.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }    
    
    private function get_count($start_time, $end_time, $pay_type)
    {
        //订单统计
        $where = array();
        $where ['status'] = 1;
        $where['pay_time >='] = $start_time;
        $where['pay_time <'] = $end_time;
        if ($pay_type){
            $where['pay_type'] = $pay_type;
        }
        $order_count_data = $this->OrderModel->get_count_data($where);;
        $order_count = $refund_count = array('order_sum'=>0, 'order_count'=>0);
        foreach ($order_count_data as $v){
            $order_count ['order_sum'] += $v['order_sum'];
            $order_count ['order_count'] += $v['order_count'];
        }
        //退款统计
        $refund_where = array();
        $refund_where ['complete_status'] = 3;
        $refund_where ['complete_time >='] = $start_time;
        $refund_where ['complete_time <'] = $end_time;
        if ($pay_type){
            $refund_where['pay_type'] = $pay_type;
        }
        $refund_count_data = $this->OrderModel->get_count_data($refund_where);
        foreach ($refund_count_data as $v){
            $refund_count ['order_sum'] += $v['order_sum'];
            $refund_count ['order_count'] += $v['order_count'];
        }
        return array('order_count'=>$order_count,'refund_count'=>$refund_count);
    }
    
    public function outputOrderExcel(){
        $date = $this->input->get('date');
        $pay_type = $this->input->get('pay_type');
        if ($date){
            $start_time = strtotime($date);
            $end_time = $start_time+86400;
        }else {
            exit();
        }
        $where = array();
        $where['a.pay_time>='] = $start_time;
        $where['a.pay_time<'] = $end_time;
        $where['a.status'] = 1;
        if ($pay_type){
            $where['a.pay_type'] = $pay_type;
            if ($pay_type==1){
                $pay_type_name = '微信';
            }else if ($pay_type==2){
                $pay_type_name = '支付宝';
            }else if ($pay_type==3){
                $pay_type_name = '充币';
            }
        }else {
            $pay_type_name = '';
        }
        $data =  $this->OrderModel->get_list($where, 10000, 0);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("财务报表日账单")
        ->setTitle("财务报表日账单")
        ->setSubject("财务报表日账单");
        $header = [
            "A1"=>"下单时间",
            "B1"=>"订单编号",
            "C1"=>"金额(元)",
            "D1"=>"订单状态",
            "E1"=>"净收入总额(元)",
            "F1"=>"银行入账金额(元)",
            "G1"=>"不含税净收入总额(元)",
            "H1"=>"税率",
            "I1"=>"税额(元)",
            "J1"=>"手续费(元)"
        ];
        foreach($header as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($key,$val);
        }
        $pCoordinate = 2;
        foreach($data as $val)
        {
            $val['r_cash_fee'] = 0;
            if ($val['complete_status']==3){//已退款
                $val['r_cash_fee'] = $val['cash_fee'];
                $val['status_name'] = '已退款';
            }else{
                $val['status_name'] = '已支付';
            }
            $val['fee'] = $val['cash_fee'] - $val['r_cash_fee'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$pCoordinate, date('Y-m-d H:i:s', $val['create_time']))
            ->setCellValue("B".$pCoordinate, $val['out_trade_no'])
            ->setCellValue("C".$pCoordinate, round($val['cash_fee'],2))
            ->setCellValue("D".$pCoordinate, $val['status_name'])
            ->setCellValue("E".$pCoordinate, round($val['fee'],2))
            ->setCellValue("F".$pCoordinate, round($val['fee']*0.994,2))
            ->setCellValue("G".$pCoordinate, round($val['fee']/1.06,2))
            ->setCellValue("H".$pCoordinate, '6%')
            ->setCellValue("I".$pCoordinate, round($val['fee']/1.06*0.06,2))
            ->setCellValue("J".$pCoordinate, round($val['fee']*0.006,2));
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=日账单.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}