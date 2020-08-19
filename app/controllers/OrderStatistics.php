<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrderStatistics extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OrderModel');
        $this->load->model('ProductModel');
        $this->load->model('MemberChargeOrderModel');
        $this->load->model('MemberActivityChargeModel');
        $this->load->model('RefundModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-06-05
     * @Author   black.zhang
     */
    public function index()
    {
        $viem = $this->input->get('viem')??'product';
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
                case 'product':
                    $data = $this->product($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'product_time':
                    $data = $this->product_time($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'charge':
                    $data = $this->charge($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'charge_time':
                    $data = $this->charge_time($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'refund':
                    $data = $this->refund($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                case 'refund_time':
                    $data = $this->refund_time($start_time,$end_time);
                    $this->ajax_return(array('code'=>200,'data'=>$data));
                    break;
                default:
                    $this->ajax_return(array('code'=>400,'msg'=>'page error'));
                    break;
            }
        }else {
            if ($operate == 'download'){
                switch ($viem){
                    case 'product':
                        $this->download_product($start_time,$end_time);
                        break;
                    case 'product_time':
                        $this->download_product_time($start_time,$end_time);
                        break;
                    case 'charge':
                        $this->download_charge($start_time,$end_time);
                        break;
                    case 'charge_time':
                        $this->download_charge_time($start_time,$end_time);
                        break;
                    case 'refund':
                        $this->download_refund($start_time,$end_time);
                        break;
                    case 'refund_time':
                        $this->download_refund_time($start_time,$end_time);
                        break;
                    case 'refund_list':
                        $this->download_refund_list($start_time,$end_time);
                        break;
                    default:
                        break;
                }
            }else {
                switch ($viem){
                    case 'product_time':
                        $product_list = $this->ProductModel->get_prod_option($where=array());
                        $product_option = array();
                        foreach ($product_list as $product){
                            $id = $product['id'];
                            $product_option [$id] = $product['name'].'('.$product['price'].')';
                        }
                        $this->_data['product_option'] = $product_option;
                        break;
                    case 'charge_time':
                        $activity_list = $this->MemberActivityChargeModel->get_charge_option($where=array());
                        $activity_option = array();
                        foreach ($activity_list as $activity){
                            $id = $activity['id'];
                            $activity_option [$id] = $activity['charge_name'];
                        }
                        $this->_data['activity_option'] = $activity_option;
                        break;
                    case 'refund_list':
                        $limit = $this->config->item('per_page');
                        $series = $this->refund_list($start_time,$end_time,$limit);
                        break;
                    default:
                        break;
                }
            }
        }
        
        $this->template->admin_render('order_statistics/'.$viem, $this->_data);
    }
    
    private function product($start_time,$end_time)
    {
        $series = array();
        
        $product_list = $this->ProductModel->get_prod_option($where=array());
        $product_option = array();
        foreach ($product_list as $product){
            $id = $product['id'];
            $product_option [$id] = $product['name'].'('.$product['price'].')';
        }
        
        $where = array();
        $where ['create_time >='] = $start_time;
        $where ['create_time <'] = $end_time;
        $where ['status'] = 1;  //已支付
        $where ['complete_status <>'] = 3;  //不包含已退款
        $list = $this->OrderModel->statistics_group($where,'product_id');
        foreach ($list as $info){
            $data = array();
            $product_id = $info['product_id'];
            $data ['name'] = $product_option[$product_id];
            $data ['value'] = $info['order_count'];
            $series [] = $data;
        }
        
        $data = array();
        $data ['series'] = $series;
        return $data;
    }
    
    private function product_time($start_time,$end_time)
    {
        $tag = $this->input->get('tag');
        if (!is_array($tag)){
            $tag = explode(',', $tag);
        }
        $series = array();
        $category = array();
        
        $product_list = $this->ProductModel->get_prod_option($where=array());
        $product_option = array();
        foreach ($product_list as $product){
            $id = $product['id'];
            $product_option [$id] = $product['name'].'('.$product['price'].')';
        }
        
        foreach ($tag as $k=>$v){
            $d = array();
            for($i = $start_time;$i<$end_time;$i += 86400){
                if ($k===0){
                    $category [] = date('Y-m-d',$i);
                }
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['status'] = 1;  //已支付
                $where ['complete_status <>'] = 3;  //不包含已退款
                $where ['product_id'] = $v;  //指定商品
                $list = $this->OrderModel->statistics_group($where,'product_id');
                if ($list){
                    $d [] = $list[0]['order_count'];
                }else {
                    $d [] = 0;
                }
            }
            $data = array();
            $data ['name'] = $product_option[$v];
            $data ['data'] = $d;
            $data ['type'] = 'bar';
            $series [] = $data;
        }
        
        $data = array();
        $data ['category'] = $category;
        $data ['series'] = $series;
        return $data;
    }
    
    private function charge($start_time,$end_time)
    {
        $series = array();
        
        $activity_list = $this->MemberActivityChargeModel->get_charge_option($where=array());
        $activity_option = array();
        foreach ($activity_list as $activity){
            $id = $activity['id'];
            $activity_option [$id] = $activity['charge_name'];
        }
        
        $where = array();
        $where ['create_time >='] = $start_time;
        $where ['create_time <'] = $end_time;
        $where ['order_status'] = 1;  //已支付
        $list = $this->MemberChargeOrderModel->get_count_data($where,'activity_id');
        foreach ($list as $info){
            $data = array();
            $activity_id = $info['activity_id'];
            $data ['name'] = $activity_option[$activity_id];
            $data ['value'] = $info['order_count'];
            $series [] = $data;
        }
        
        $data = array();
        $data ['series'] = $series;
        return $data;
    }
    
    private function charge_time($start_time,$end_time)
    {
        $tag = $this->input->get('tag');
        if (!is_array($tag)){
            $tag = explode(',', $tag);
        }
        $series = array();
        $category = array();
        
        $activity_list = $this->MemberActivityChargeModel->get_charge_option($where=array());
        $activity_option = array();
        foreach ($activity_list as $activity){
            $id = $activity['id'];
            $activity_option [$id] = $activity['charge_name'];
        }
        
        foreach ($tag as $k=>$v){
            $d = array();
            for($i = $start_time;$i<$end_time;$i += 86400){
                if ($k===0){
                    $category [] = date('Y-m-d',$i);
                }
                $where = array();
                $where ['create_time >='] = $i;
                $where ['create_time <'] = $i+86400;
                $where ['order_status'] = 1;  //已支付
                $where ['activity_id'] = $v;  //指定商品
                $list = $this->MemberChargeOrderModel->get_count_data($where,'activity_id');
                if ($list){
                    $d [] = $list[0]['order_count'];
                }else {
                    $d [] = 0;
                }
            }
            $data = array();
            $data ['name'] = $activity_option[$v];
            $data ['data'] = $d;
            $data ['type'] = 'bar';
            $series [] = $data;
        }
        
        $data = array();
        $data ['category'] = $category;
        $data ['series'] = $series;
        return $data;
    }
    
    private function refund($start_time,$end_time)
    {
        $date = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data5 = array();
        for($i = $start_time;$i<$end_time;$i += 86400){
            $date [] = date('Y-m-d',$i);
            
            //累计
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            $data1 [] = $this->RefundModel->get_count($where);
            
            //不通电
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            $where ['r.refund_text_id'] = 1;
            $data2 [] = $this->RefundModel->get_count($where);
            
            //充电速度慢
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            $where ['r.refund_text_id'] = 2;
            $data3 [] = $this->RefundModel->get_count($where);
            
            //设备损坏
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            $where ['r.refund_text_id'] = 3;
            $data4 [] = $this->RefundModel->get_count($where);
            
            //其他
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            $where ['r.refund_text_id'] = 4;
            $data5 [] = $this->RefundModel->get_count($where);
        }
        
        $series = array();
        $series [] = array('name'=>'累计退款单数', 'data'=>$data1, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'不通电', 'data'=>$data2, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'充电速度慢', 'data'=>$data3, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'设备损坏', 'data'=>$data4, 'type'=>'line', 'smooth'=>true);
        $series [] = array('name'=>'其他', 'data'=>$data5, 'type'=>'line', 'smooth'=>true);
        
        $data = array();
        $data ['category'] = $date;
        $data ['series'] = $series;
        return $data;
    }
    
    private function refund_time($start_time,$end_time)
    {
        $tag = $this->input->get('tag');
        //实验组
        $category1 = array();
        $data1 = array();
        $label1 = date('Y-m-d',$start_time).'至'.date('Y-m-d',$end_time-86400);
        for($i = $start_time;$i<$end_time;$i += 86400){
            $category1 [] = date('Y-m-d',$i);
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            if ($tag){
            $where ['r.refund_text_id'] = $tag;
            }
            $data1 [] = $this->RefundModel->get_count($where);
        }
        //对比组
        $category2 = array();
        $data2 = array();
        $i = $start_time - ($end_time-$start_time);
        $label2 = date('Y-m-d',$i).'至'.date('Y-m-d',$start_time-86400);
        for($i;$i<$start_time;$i += 86400){
            $category2 [] = date('Y-m-d',$i);
            $where = array();
            $where ['r.status'] = 1;
            $where ['r.create_time >='] = $i;
            $where ['r.create_time <'] = $i+86400;
            if ($tag){
                $where ['r.refund_text_id'] = $tag;
            }
            $data2 [] = $this->RefundModel->get_count($where);
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
    
    private function refund_list($start_time,$end_time,$limit)
    {
        $page = $this->input->get('per_page')??1;
        $offset = ($page-1)*$limit;
        
        $where = array();
        $where ['r.create_time >='] = $start_time;
        $where ['r.create_time <'] = $end_time;
        $where ['r.status'] = 1;  //已退款
        $total_rows = $this->RefundModel->get_count($where);
        $list = $this->RefundModel->get_refund_list($where, $limit, $offset);
        foreach ($list as &$info){
            $info ['o_create_time'] = date('Y-m-d H:i:s', $info ['o_create_time']);
            $info ['r_create_time'] = date('Y-m-d H:i:s', $info ['r_create_time']);
        }
        $this->_data['list'] = $list;
        // 传入一个参数返回分页链接;
        $this->_data['pagination'] = $this->create_pagination($total_rows, $limit);
    }
    
    private function download_product($start_time,$end_time){
        $data = $this->product($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-商品数据-商品分布')
        ->setCellValue('A3','商品')
        ->setCellValue('B3','订单数量');
        $pCoordinate = 4;
        foreach($data['series'] as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate),$val['name'])
            ->setCellValue('B'.($key+$pCoordinate),$val['value']);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单统计-商品数据-商品分布.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_product_time($start_time,$end_time){
        $data = $this->product_time($start_time,$end_time);
        $n = count($data['series']);
        $title = ['B','C','D','E','F'];
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-商品数据-商品数据')
        ->setCellValue('A3','时间');
        
        $pCoordinate = 4;
        foreach($data['category'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        for ($i=0;$i<$n;$i++){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($title[$i].'3',$data['series'][$i]['name']);
            foreach($data['series'][$i]['data'] as $key=>$val)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($title[$i].($key+$pCoordinate),$val);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单统计-商品数据-商品数据.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_charge($start_time,$end_time){
        $data = $this->charge($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-充值数据-充值分布')
        ->setCellValue('A3','商品')
        ->setCellValue('B3','订单数量');
        $pCoordinate = 4;
        foreach($data['series'] as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+$pCoordinate),$val['name'])
            ->setCellValue('B'.($key+$pCoordinate),$val['value']);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单统计-充值数据-充值分布.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_charge_time($start_time,$end_time){
        $data = $this->charge_time($start_time,$end_time);
        $n = count($data['series']);
        $title = ['B','C','D','E','F'];
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-充值数据-充值数据')
        ->setCellValue('A3','时间');
        
        $pCoordinate = 4;
        foreach($data['category'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        for ($i=0;$i<$n;$i++){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($title[$i].'3',$data['series'][$i]['name']);
            foreach($data['series'][$i]['data'] as $key=>$val)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($title[$i].($key+$pCoordinate),$val);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单统计-充值数据-充值数据.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_refund($start_time,$end_time){
        $data = $this->refund($start_time,$end_time);
        $n = count($data['series']);
        $title = ['B','C','D','E','F'];
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-客诉数据-指标对比')
        ->setCellValue('A3','时间');
        $pCoordinate = 4;
        foreach($data['category'] as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val);
        }
        for ($i=0;$i<$n;$i++){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($title[$i].'3',$data['series'][$i]['name']);
            foreach($data['series'][$i]['data'] as $key=>$val)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($title[$i].($key+$pCoordinate),$val);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单统计-客诉数据-指标对比.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_refund_time($start_time,$end_time){
        $tag = $this->input->get('tag');
        switch ($tag){
            case '':
                $name = '累计退款单数';
                break;
            case '1':
                $name = '不通电';
                break;
            case '2':
                $name = '充电速度慢';
                break;
            case '3':
                $name = '设备损坏';
                break;
            case '4':
                $name = '其他';
                break;
            default:
                throw new Exception("指标选择栏出错", 1);
                break;
        }
        $data = $this->refund_time($start_time,$end_time);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-客诉数据-时间对比')
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
        header('Content-Disposition: attachment;filename=订单统计-客诉数据-时间对比.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
    
    private function download_refund_list($start_time,$end_time){
        $this->refund_list($start_time,$end_time,$limit=10000);
        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','时间范围：')
        ->setCellValue('B1',date('Y-m-d',$start_time).'-'.date('Y-m-d',$end_time-86400))
        ->setCellValue('A2','表格内容：')
        ->setCellValue('B2','订单统计-客诉数据-退款列表')
        ->setCellValue('A3','用户ID')
        ->setCellValue('B3','投放点名称')
        ->setCellValue('C3','设备ID')
        ->setCellValue('D3','下单时间')
        ->setCellValue('E3','申请退款时间')
        ->setCellValue('F3','退款类型')
        ->setCellValue('G3','退款原因')
        ->setCellValue('H3','设备类型');
        $pCoordinate = 4;
        $list = $this->_data['list'];
        foreach($list as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".($key+$pCoordinate),$val['uuid'])
            ->setCellValue("B".($key+$pCoordinate),$val['name'])
            ->setCellValue("C".($key+$pCoordinate),$val['machine_id'])
            ->setCellValue("D".($key+$pCoordinate),$val['o_create_time'])
            ->setCellValue("E".($key+$pCoordinate),$val['r_create_time'])
            ->setCellValue("F".($key+$pCoordinate),$val['text'])
            ->setCellValue("G".($key+$pCoordinate),$val['reason'])
            ->setCellValue("H".($key+$pCoordinate),$val['device_type']);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单统计-客诉数据-退款列表.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}
