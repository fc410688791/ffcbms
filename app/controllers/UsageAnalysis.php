<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UsageAnalysis extends Admin_Controller
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
        $viem = $this->input->get('viem')??'behavior';
        $operate = $this->input->get('operate');
        $time_type = $this->input->get('time_type')??1;
        $this->_data['time_type'] = $time_type;
        $reservation = $this->input->get('reservation');
        $this->_data['reservation'] = $reservation;
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
                case 'source':
                    $data = $this->source($start_time,$end_time);
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
                    case 'source':
                        $this->download_source($start_time,$end_time);
                        break;
                    case 'page':
                        if ($time_type==1){
                            $start_time = $todat-86400*7;
                            $end_time = $todat;
                        }elseif ($time_type==2){
                            $start_time = $todat-86400*30;
                            $end_time = $todat;
                        }
                        $this->download_page($start_time,$end_time);
                        break;
                    default:
                        $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                        break;
                }
            }else {
                switch ($viem){
                    case 'page':
                        if ($time_type==1){
                            $start_time = $todat-86400*7;
                            $end_time = $todat;
                        }elseif ($time_type==2){
                            $start_time = $todat-86400*30;
                            $end_time = $todat;
                        }
                        $series = $this->page($start_time,$end_time);
                        break;
                    default:
                        break;
                }
            }
        }
        $this->template->admin_render('usage_analysis/'.$viem, $this->_data);
    }
    
    private function behavior($start_time,$end_time)
    {
        $date = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        for($i = $start_time;$i<$end_time;$i += 86400){
            $date [] = date('Y-m-d',$i);
            //打开次数
            $where = array();
            $where ['access_type'] = 1;
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $data1 [] = $this->AnalysisUserModel->get_count($where);
            //访问次数
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $data2 [] = $this->AnalysisUserModel->get_count($where);
            //访问人数
            $where = array();
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $data3 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
            //新访问人数
            $where = array();
            $where ['is_new_member'] = 1;
            $where ['create_time >='] = $i;
            $where ['create_time <'] = $i+86400;
            $data4 [] = $this->AnalysisUserModel->get_count($where);
        }
        $series = array();
        $series [] = array('name'=>'打开次数', 'data'=>$data1, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'访问次数', 'data'=>$data2, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'访问人数', 'data'=>$data3, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'新访问人数', 'data'=>$data4, 'type'=>'line', 'smooth'=>true);
        $data = array();
        $data ['category'] = $date;
        $data ['series'] = $series;
        return $data;
    }
    
    private function behavior_time($start_time,$end_time)
    {
        $tag = $this->input->get('tag')??1;
        //实验组
        $category1 = array();
        $data1 = array();
        $label1 = date('Y-m-d',$start_time).'至'.date('Y-m-d',$end_time-86400);
        for($i = $start_time;$i<$end_time;$i += 86400){
            $category1 [] = date('Y-m-d',$i);
            switch ($tag){
                case '1':  // 打开次数
                    $where = array();
                    $where ['access_type'] = 1;
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data1 [] = $this->AnalysisUserModel->get_count($where);
                    $this->_data['tag_name'] = '打开次数';
                    break;
                case '2':  //访问次数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data1 [] = $this->AnalysisUserModel->get_count($where);
                    $this->_data['tag_name'] = '访问次数';
                    break;
                case '3':  //访问人数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data1 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
                    $this->_data['tag_name'] = '访问人数';
                    break;
                case '4':  //新访问人数
                    $where = array();
                    $where ['is_new_member'] = 1;
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data1 [] = $this->AnalysisUserModel->get_count($where);
                    $this->_data['tag_name'] = '新访问人数';
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
                case '1':  // 打开次数
                    $where = array();
                    $where ['access_type'] = 1;
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data2 [] = $this->AnalysisUserModel->get_count($where);
                    break;
                case '2':  //访问次数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data2 [] = $this->AnalysisUserModel->get_count($where);
                    break;
                case '3':  //访问人数
                    $where = array();
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data2 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
                    break;
                case '4':  //访问人数
                    $where = array();
                    $where ['is_new_member'] = 1;
                    $where ['create_time >='] = $i;
                    $where ['create_time <'] = $i+86400;
                    $data2 [] = $this->AnalysisUserModel->get_count($where);
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
    
    private function source($start_time,$end_time)
    {
        $series = array();
        $data1 = array();
        $data2 = array();
        $list = $this->TextModel->get_option(array('type'=>4));
        $category = array();
        foreach ($list as $k=>$v){
            $category [] = $v;
            //打开次数
            $where = array();
            $where ['access_type'] = 1;
            $where ['source_type_id'] = $k;
            $where ['create_time >='] = $start_time;
            $where ['create_time <'] = $end_time;
            $data1 [] = $this->AnalysisUserModel->get_count($where);
            
            //访问人数
            $where = array();
            $where ['access_type'] = 1;
            $where ['source_type_id'] = $k;
            $where ['create_time >='] = $start_time;
            $where ['create_time <'] = $end_time;
            $data2 [] = count($this->AnalysisUserModel->get_group_count($where,'uuid'));
        }
        $series = array();
        $series [] = array('name'=>'打开次数', 'data'=>$data1, 'type'=>'bar');
        $series [] = array('name'=>'访问人数', 'data'=>$data2, 'type'=>'bar');
        $data = array();
        $data ['category'] = $category;
        $data ['series'] = $series;
        return $data;
    }
    
    private function page($start_time,$end_time)
    {
        $o = $this->input->get('o');
        $this->_data['o'] = $o;
        $select = 'page_type_id,sum(access_count) as access_count,sum(access_user_count) as access_user_count,sum(stay_count_time)/sum(access_count) as avg_time,sum(entry_count) as entry_count,sum(exit_count) as exit_count,sum(exit_count)/sum(access_count) as exit_rate,sum(share_count) as share_count';
        $where = array();
        $where ['statistical_time >='] = $start_time;
        $where ['statistical_time <'] = $end_time;
        switch ($o){
            case 'a_u':
                $order = 'access_count DESC';
                break;
            case 'a_d':
                $order = 'access_count ASC';
                break;
            case 'b_u':
                $order = 'access_user_count DESC';
                break;
            case 'b_d':
                $order = 'access_user_count ASC';
                break;
            case 'c_u':
                $order = 'avg_time DESC';
                break;
            case 'c_d':
                $order = 'avg_time ASC';
                break;
            case 'd_u':
                $order = 'entry_count DESC';
                break;
            case 'd_d':
                $order = 'entry_count ASC';
                break;
            case 'e_u':
                $order = 'exit_count DESC';
                break;
            case 'e_d':
                $order = 'exit_count ASC';
                break;
            case 'f_u':
                $order = 'exit_rate DESC';
                break;
            case 'f_d':
                $order = 'exit_rate ASC';
                break;
            case 'g_u':
                $order = 'share_count DESC';
                break;
            case 'g_d':
                $order = 'share_count ASC';
                break;
            default:
                $order = '';
                break;
        }
        $series = $this->AnalysisStatisticalPageModel->statistics_page($select, $where, $order);
        //页面名称
        $page_option = $this->TextModel->get_option($where=array('type'=>3));
        $this->_data['page_option'] = $page_option;
        $this->_data['series'] = $series;
    }
    
    private function download_behavior($start_time,$end_time){
        $data = $this->behavior($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','使用分析-行为分析-指标对比')
        ->setCellValue('A3','时间')
        ->setCellValue('B3','打开次数')
        ->setCellValue('C3','访问次数')
        ->setCellValue('D3','访问人数')
        ->setCellValue('E3','新访问人数');
        $pCoordinate = 4;
        foreach($data['category'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        foreach($data['series'] as $series)
        {
            switch ($series['name']){
                case '打开次数':
                    $sort = 'B';
                    break;
                case '访问次数':
                    $sort = 'C';
                    break;
                case '访问人数':
                    $sort = 'D';
                    break;
                case '新访问人数':
                    $sort = 'E';
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
        header('Content-Disposition: attachment;filename=使用分析-行为分析-指标对比.xlsx');
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
            case '1':  // 打开次数
                $name = '打开次数';
                break;
            case '2':  //访问次数
                $name = '访问次数';
                break;
            case '3':  //访问人数
                $name = '访问人数';
                break;
            case '4':  //新访问人数
                $name = '新访问人数';
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
        ->setCellValue('B2','使用分析-行为分析-时间对比')
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
        header('Content-Disposition: attachment;filename=使用分析-行为分析-时间对比.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_source($start_time,$end_time){
        $data = $this->source($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','使用分析-来源分析-整体来源分析')
        ->setCellValue('A3','场景')
        ->setCellValue('B3','打开次数')
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
                case '打开次数':
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
        header('Content-Disposition: attachment;filename=使用分析-来源分析-整体来源分析.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_page($start_time,$end_time){
        $this->page($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','使用分析-页面分析')
        ->setCellValue('A3','页面名称')
        ->setCellValue('B3','访问次数')
        ->setCellValue('C3','访问人数')
        ->setCellValue('D3','次均时长(s)')
        ->setCellValue('E3','入口页次数')
        ->setCellValue('F3','退出页次数')
        ->setCellValue('G3','退出率')
        ->setCellValue('H3','分享次数');
        $pCoordinate = 4;
        $page_option = $this->_data['page_option'];
        $series = $this->_data['series'];
        foreach($series as $key=>$val){   
            $page_type_id = $val['page_type_id'];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$page_option[$page_type_id])
            ->setCellValue("B".($key+$pCoordinate),$val['access_count'])
            ->setCellValue("C".($key+$pCoordinate),$val['access_user_count'])
            ->setCellValue("D".($key+$pCoordinate),$val['avg_time'])
            ->setCellValue("E".($key+$pCoordinate),$val['entry_count'])
            ->setCellValue("F".($key+$pCoordinate),$val['exit_count'])
            ->setCellValue("G".($key+$pCoordinate),$val['exit_rate'])
            ->setCellValue("H".($key+$pCoordinate),$val['share_count']);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=使用分析-页面分析.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
