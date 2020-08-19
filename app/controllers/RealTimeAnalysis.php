<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RealTimeAnalysis extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AnalysisUserModel');
        $this->load->model('AnalysisStatisticalPageModel');
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
        $viem = $this->input->get('viem')??'real_time';
        $operate = $this->input->get('operate');
        $reservation = $this->input->get('reservation');
        $time_type = $this->input->get('time_type');
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
                case 'real_time':
                    $data = $this->real_time($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'burying_point':
                    $data = $this->burying_point($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                default:
                    $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                    break;
            }
        }else {
            if ($operate == 'download'){
                switch ($viem){
                    case 'real_time':
                        $page_option = $this->TextModel->get_option($where=array('type'=>3));
                        $this->_data['page_option'] = $page_option;
                        $this->download_real_time($start_time,$end_time);
                        break;
                    case 'burying_point':
                        $this->download_burying_point($start_time,$end_time);
                        break;
                    default:
                        break;
                }
            }else {
                switch ($viem){
                    case 'real_time':
                        $page_option = $this->TextModel->get_option($where=array('type'=>3));
                        $this->_data['page_option'] = $page_option;
                        break;
                    default:
                        break;
                }
            }
        }
        $this->template->admin_render('real_time_analysis/'.$viem, $this->_data);
    }
    
    private function real_time($start_time,$end_time)
    {
        $page = $this->input->get('page');
        $interval = $this->input->get('interval')??24;
        $interval *= 3600;
        //实验组
        $category1 = array();
        $data1 = array();
        $label1 = date('Y-m-d',$start_time).'至'.date('Y-m-d',$end_time-86400);
        for($i = $start_time;$i<$end_time;$i += $interval){
            $category1 [] = date('m-d H:i',$i);
            //访问次数
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+$interval;
            if ($page){
                $where ['page_type_id'] = $page;
            }
            $data1 [] = $this->AnalysisUserModel->get_count($where);
        }
        //对比组
        $category2 = array();
        $data2 = array();
        $i = $start_time - ($end_time-$start_time);
        $label2 = date('Y-m-d',$i).'至'.date('Y-m-d',$start_time-86400);
        for($i;$i<$start_time;$i += $interval){
            $category2 [] = date('m-d H:i',$i);
            //访问次数
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+$interval;
            if ($page){
                $where ['page_type_id'] = $page;
            }
            $data2 [] = $this->AnalysisUserModel->get_count($where);
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
    
    private function burying_point($start_time,$end_time)
    {
        $series = array();
        $data1 = array();
        $data2 = array();
        $list = $this->TextModel->get_option(array('type'=>5,'status'=>1));
        $category = array();
        foreach ($list as $k=>$v){
            $category [] = $v;
            //打开次数
            $where = array();
            $where ['event_type_id'] = $k;
            $where ['create_time >='] = $start_time;
            $where ['create_time <'] = $end_time;
            $data1 [] = $this->AnalysisUserModel->get_count($where);
            
            //访问人数
            $where = array();
            $where ['event_type_id'] = $k;
            $where ['create_time >='] = $start_time;
            $where ['create_time <'] = $end_time;
            $data2 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
        }
        $series = array();
        $series [] = array('name'=>'点击次数', 'data'=>$data1, 'type'=>'bar');
        $series [] = array('name'=>'访问人数', 'data'=>$data2, 'type'=>'bar');
        $data = array();
        $data ['category'] = $category;
        $data ['series'] = $series;
        return $data;
    }
    
    private function download_real_time($start_time,$end_time)
    {
        $page = $this->input->get('page');
        $page_option = $this->_data['page_option'];
        if ($page){
            $name = $page_option[$page];
        }else {
            $name = '所有页面';
        }
        $data = $this->real_time($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','实时分析-实时分析-实时访问')
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
        header('Content-Disposition: attachment;filename=实时分析-实时分析-实时访问.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_burying_point($start_time,$end_time)
    {
        $data = $this->burying_point($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','实时分析-实时分析-埋点数据')
        ->setCellValue('A3','埋点')
        ->setCellValue('B3','点击次数')
        ->setCellValue('C3','访问人数');
        $pCoordinate = 4;
        foreach($data['category'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        foreach($data['series'] as $series)
        {
            switch ($series['name']){
                case '点击次数':
                    $sort = 'B';
                    break;
                case '访问人数':
                    $sort = 'C';
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
        header('Content-Disposition: attachment;filename=实时分析-实时分析-埋点数据.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
