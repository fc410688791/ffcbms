<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserPortrait extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MemberModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-06-05
     * @Author   black.zhang
     */
    public function index()
    {
        $viem = $this->input->get('viem')??'device_type';
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
                case 'device_type':
                    $data = $this->device_type($start_time,$end_time,0,10);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                default:
                    $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                    break;
            }
        }else {
            if ($operate == 'download'){
                switch ($viem){
                    case 'device_type':
                        $this->download_device_type($start_time,$end_time);
                        break;
                    default:
                        $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                        break;
                }
            }
        }
        $this->template->admin_render('user_portrait/'.$viem, $this->_data);
    }
    
    private function device_type($start_time,$end_time,$offset,$limit)
    {
        $data = array();
        $where = array();
        $where ['create_time >='] = $start_time;
        $where ['create_time <'] = $end_time;
        $list = $this->MemberModel->statistics_group($where, 'device_type', $offset, $limit);
        $series = array();
        $count = 0;
        foreach ($list as $info){
            $series [] = array('name'=>$info['device_type'],'value'=>$info['count']);
            $count += $info['count'];
        }
        $data = array();
        $data ['series'] = $series;
        $data ['count'] = $count;
        return $data;
    }
    
    private function download_device_type($start_time,$end_time){
        $data = $this->device_type($start_time,$end_time,0,10000);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','用户画像-终端类型')
        ->setCellValue('A3','设备类型')
        ->setCellValue('B3','数量')
        ->setCellValue('C3','占比');
        $pCoordinate = 4;
        foreach($data['series'] as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate),$val['name'])
            ->setCellValue('B'.($key+$pCoordinate),$val['value'])
            ->setCellValue('C'.($key+$pCoordinate),round($val['value']/$data['count']*100,2).'%');
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=用户画像-终端类型.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
