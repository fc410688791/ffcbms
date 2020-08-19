<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TransactionAnalysis extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AnalysisUserModel');
        $this->load->model('OrderModel');
        $this->load->model('TextModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-06-05
     * @Author   black.zhang
     */
    public function index()
    {
        $viem = $this->input->get('viem')??'behavior';
        $operate = $this->input->get('operate');
        $reservation = $this->input->get('reservation');
        $time_type = $this->input->get('time_type')??1;
        $todat = strtotime(date('Y-m-d',time()));
        if ($time_type==1){
            $start_time = $todat-86400*6;
            $end_time = $todat+86400;
        }elseif ($time_type==2){
            $start_time = $todat-86400*29;
            $end_time = $todat+86400;
        }else {
            if ($reservation){
                $date = explode(' - ', $reservation);
                list($start_time , $end_time) = $date;
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time)+86400;
            }
        }
        if (IS_AJAX){
            switch ($viem){
                case 'behavior':
                    $data = $this->behavior($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'behavior_time':
                    $data = $this->behavior_time($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                default:
                    $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                    break;
            }
        }else {
            if ($operate == 'download'){
                switch ($viem){
                    case 'behavior':
                        $this->download_behavior($start_time,$end_time);
                        break;
                    case 'behavior_time':
                        $this->download_behavior_time($start_time,$end_time);
                        break;
                    default:
                        $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                        break;
                }
            }
        }
        $this->template->admin_render('transaction_analysis/'.$viem, $this->_data);
    }
    
    private function behavior($start_time,$end_time)
    {
        $date = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        for($i = $start_time;$i<$end_time;$i += 86400){
            $date [] = date('Y-m-d',$i);
            //打开次数
            $where = array();
            $where ['access_type'] = 1;
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $data1 [] = $this->AnalysisUserModel->get_count($where);
            //访问人数
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $data2 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
            
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $where ['status'] = 1;  //已支付
            $where ['complete_status <>'] = 3;  //不包含已退款
            $data = $this->OrderModel->statistics_group($where,'status');
            if ($data){
                //支付笔数
                $data3 [] = $data[0]['order_count'];
                //支付金额
                $data4 [] = $data[0]['order_sum'];
            }else {
                //支付笔数
                $data3 [] = 0;
                //支付金额
                $data4 [] = 0;
            }
            //支付人数
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $where ['status'] = 1;  //已支付
            $where ['complete_status <>'] = 3;  //不包含已退款
            $data5 [] = count($this->OrderModel->statistics_group($where,'uuid'));
        }
        $series = array();
        $series [] = array('name'=>'打开次数', 'data'=>$data1, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'访问人数', 'data'=>$data2, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'支付笔数', 'data'=>$data3, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'支付金额', 'data'=>$data4, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'支付人数', 'data'=>$data5, 'type'=>'line', 'smooth'=>true);
        $data = array();
        $data ['category'] = $date;
        $data ['series'] = $series;
        return $data;
    }
    
    private function behavior_time($start_time,$end_time)
    {
        $tag = $this->input->get('tag')??1;
        $this->_data['tag'] = $tag;
        //实验组
        $category1 = array();
        $data1 = array();
        $label1 = date('Y-m-d',$start_time).'至'.date('Y-m-d',$end_time-86400);
        for($i = $start_time;$i<$end_time;$i += 86400){
            $category1 [] = date('Y-m-d',$i);
            switch ($tag){
                case '1':
                    //打开次数
                    $where = array();
                    $where ['access_type'] = 1;
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data1 [] = $this->AnalysisUserModel->get_count($where);
                    $this->_data['tag_name'] = '打开次数';
                    break;
                case '2':
                    //访问人数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data1 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
                    $this->_data['tag_name'] = '访问人数';
                    break;
                case '3':  
                    //支付笔数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $where ['status'] = 1;  //已支付
                    $where ['complete_status <>'] = 3;  //不包含已退款
                    $data = $this->OrderModel->statistics_group($where,'status');
                    if ($data){
                        $data1 [] = $data[0]['order_count'];
                    }else {
                        $data1 [] = 0;
                    }
                    $this->_data['tag_name'] = '支付笔数';
                    break;
                case '4':
                    //支付金额
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $where ['status'] = 1;  //已支付
                    $where ['complete_status <>'] = 3;  //不包含已退款
                    $data = $this->OrderModel->statistics_group($where,'status');
                    if ($data){
                        $data1 [] = $data[0]['order_sum'];
                    }else {
                        $data1 [] = 0;
                    }
                    $this->_data['tag_name'] = '支付金额';
                    break;
                case '5':
                    //支付人数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $where ['status'] = 1;  //已支付
                    $where ['complete_status <>'] = 3;  //不包含已退款
                    $data1 [] = count($this->OrderModel->statistics_group($where,'uuid'));
                    $this->_data['tag_name'] = '支付人数';
                    break;
                default:
                    throw new Exception("指标选择栏出错", 1);
                    break;
            }
        }
        //对比组
        $category2 = array();
        $data2 = array();
        $i = $start_time - ($end_time-$start_time);
        $label2 = date('Y-m-d',$i).'至'.date('Y-m-d',$start_time-86400);
        for($i;$i<$start_time;$i += 86400){
            $category2 [] = date('Y-m-d',$i);
            switch ($tag){
                case '1':
                    //打开次数
                    $where = array();
                    $where ['access_type'] = 1;
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data2 [] = $this->AnalysisUserModel->get_count($where);
                    break;
                case '2':
                    //访问人数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data2 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
                    break;
                case '3':
                    //支付笔数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $where ['status'] = 1;  //已支付
                    $where ['complete_status <>'] = 3;  //不包含已退款
                    $data = $this->OrderModel->statistics_group($where,'status');
                    if ($data){
                        $data2 [] = $data[0]['order_count'];
                    }else {
                        $data2 [] = 0;
                    }
                    break;
                case '4':
                    //支付金额
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $where ['status'] = 1;  //已支付
                    $where ['complete_status <>'] = 3;  //不包含已退款
                    $data = $this->OrderModel->statistics_group($where,'status');
                    if ($data){
                        $data2 [] = $data[0]['order_sum'];
                    }else {
                        $data2 [] = 0;
                    }
                    break;
                case '5':
                    //支付人数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $where ['status'] = 1;  //已支付
                    $where ['complete_status <>'] = 3;  //不包含已退款
                    $data2 [] = count($this->OrderModel->statistics_group($where,'uuid'));
                    break;
                default:
                    throw new Exception("指标选择栏出错", 1);
                    break;
            }
        }
        
        $data = array();
        $data ['category1'] = $category1;
        $data ['label1'] = $label1;
        $data ['data1'] = $data1;
        $data ['category2'] = $category2;
        $data ['label2'] = $label2;
        $data ['data2'] = $data2;
        return $data;
    }
    
    private function download_behavior($start_time,$end_time){
        $data = $this->behavior($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','交易分析-交易分析-指标对比')
        ->setCellValue('A3','时间')
        ->setCellValue('B3','支付笔数')
        ->setCellValue('C3','支付金额')
        ->setCellValue('D3','支付人数')
        ->setCellValue('E3','访问人数')
        ->setCellValue('F3','打开次数');
        $pCoordinate = 4;
        foreach($data['category'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        foreach($data['series'] as $series)
        {
            switch ($series['name']){
                case '支付笔数':
                    $sort = 'B';
                    break;
                case '支付金额':
                    $sort = 'C';
                    break;
                case '支付人数':
                    $sort = 'D';
                    break;
                case '访问人数':
                    $sort = 'E';
                    break;
                case '打开次数':
                    $sort = 'F';
                    break;
                default:
                    break;
            }
            if ($sort){
                foreach ($series['data'] as $key=>$val){
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($sort.($key+$pCoordinate),$val);
                }
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=交易分析-交易分析-指标对比.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_behavior_time($start_time,$end_time){
        $tag = $this->input->get('tag');
        switch ($tag){
            case '1':
                $name = '打开次数';
                break;
            case '2':
                $name = '访问人数';
                break;
            case '3':
                $name = '支付笔数';
                break;
            case '4':
                $name = '支付金额';
                break;
            case '5':
                $name = '支付人数';
                break;
            default:
                throw new Exception("指标选择栏出错", 1);
                break;
        }
        $data = $this->behavior_time($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','交易分析-交易分析-时间对比')
        ->setCellValue('A3','时间')
        ->setCellValue('B3',$name)
        ->setCellValue('C3','对比时间')
        ->setCellValue('D3','对比数据');
        $pCoordinate = 4;
        foreach($data['category1'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        foreach($data['data1'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("B".($key+$pCoordinate),$val);
        }
        foreach($data['category2'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("C".($key+$pCoordinate),$val);
        }
        foreach($data['data2'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("D".($key+$pCoordinate),$val);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=交易分析-交易分析-时间对比.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
