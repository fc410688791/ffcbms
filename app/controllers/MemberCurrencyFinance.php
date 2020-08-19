<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberCurrencyFinance extends Admin_Controller {
      
    public function __construct() {
        parent::__construct();
        $this->load->model('MemberChargeOrderModel');
        $this->load->model('OrderModel');
    }
    
    /**
     * [index]
     *
     * @DateTime 2019-08-01
     * @Author   black.zhang
     */
    public function index() 
    {
        //支付类型列表;
        $pay_type_option = $this->config->item('pay_type');
        unset($pay_type_option['3']);
        $this->_data['pay_type_option'] = $pay_type_option;
        
        $data = $this->get_data();
        $this->_data['order_count'] = $data['all_count']['order_count'];
        $this->_data['order_data'] = $data['order_data'];
        $this->template->admin_render('member_currency_finance/index', $this->_data);
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
            "B1"=>"总订充值单(个)",
            "C1"=>"充值总额(元)",
            "D1"=>"银行入账金额(元)",
            "E1"=>"不含税净充值总额(元)",
            "F1"=>"税率",
            "G1"=>"总税额(元)",
            "H1"=>"手续费(元)"
        ];
        foreach($header as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($key,$val);
        }
        $pCoordinate = 2;
        foreach($data['order_data'] as $key => $val)
        {
            $fee = $val['order_count']['order_sum'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$pCoordinate,$key)
            ->setCellValue("B".$pCoordinate,$val['order_count']['order_count'])
            ->setCellValue("C".$pCoordinate,round($fee,2))
            ->setCellValue("D".$pCoordinate,round($fee*0.994,2))
            ->setCellValue("E".$pCoordinate,round($fee/1.06,2))
            ->setCellValue("F".$pCoordinate,'6%')
            ->setCellValue("G".$pCoordinate,round($fee/1.06*0.06,2))
            ->setCellValue("H".$pCoordinate,round($fee*0.006,2));
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$pay_type_name.$this->input->get('date').'月账单.xlsx');
        header('Cache-Control: max-age=0');
        //header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }    
    
    private function get_count($start_time, $end_time, $pay_type)
    {
        //充值订单统计
        $where = array();
        $where['order_status'] = 1;
        $where['pay_time >='] = $start_time;
        $where['pay_time <'] = $end_time;
        if ($pay_type){
            $where['pay_type'] = $pay_type;
        }
        $order_count_data = $this->MemberChargeOrderModel->get_count_data($where);;
        $order_count = array('order_sum'=>0, 'order_count'=>0);
        foreach ($order_count_data as $v){
            $order_count ['order_sum'] += $v['order_sum'];
            $order_count ['order_count'] += $v['order_count'];
        }
        
        return array('order_count'=>$order_count);
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
        if ($pay_type){
            $where['a.pay_type'] = $pay_type;
            if ($pay_type==1){
                $pay_type_name = '微信';
            }else if ($pay_type==2){
                $pay_type_name = '支付宝';
            }else {
                $pay_type_name = '';
            }
        }else {
            $pay_type_name = '';
        }
        $data =  $this->MemberChargeOrderModel->get_list($where, 10000, 0);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("财务报表日账单")
        ->setTitle("财务报表日账单")
        ->setSubject("财务报表日账单");
        $header = [
            "A1"=>"交易日期",
            "B1"=>"交易单号",
            "C1"=>"商品名称",
            "D1"=>"金额(元)",
            "E1"=>"订单状态",
            "F1"=>"银行入账金额(元)",
            "G1"=>"不含税净收入总额(元)",
            "H1"=>"税率",
            "I1"=>"率额",
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
            $status_name = '已支付';
            $fee = $val['cash_fee'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$pCoordinate, date('Y-m-d H:i:s', $val['create_time']))
            ->setCellValue("B".$pCoordinate, $val['out_trade_no'])
            ->setCellValue("C".$pCoordinate, $val['charge_name'])
            ->setCellValue("D".$pCoordinate, round($fee,2))
            ->setCellValue("E".$pCoordinate, $status_name)
            ->setCellValue("F".$pCoordinate, round($fee*0.994,2))
            ->setCellValue("G".$pCoordinate, round($fee/1.06,2))
            ->setCellValue("H".$pCoordinate, '6%')
            ->setCellValue("I".$pCoordinate, round($fee/1.06*0.06,2))
            ->setCellValue("J".$pCoordinate, round($fee*0.006,2));
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$pay_type_name.$this->input->get('date').'日账单.xlsx');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}