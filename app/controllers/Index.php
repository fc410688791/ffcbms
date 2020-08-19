<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller{

    public function __construct()
    {
        parent::__construct();
    }

    
    public function index()
    {
        phpinfo();
        exit();
    }
    
    //获取代理商收货信息
    public function get_agent_address()
    {
        $this->load->model('AgentAddressModel');
        $agent_id = $this->input->get('agent_id');
        $where = array();
        if ($agent_id){
            $where ['agent_id'] = $agent_id;
            $where ['status'] = 1;
        }
        $list = $this->AgentAddressModel->get_list($where, 1000, 0);
        $data = array();
        foreach ($list as $key=>$info){
            $data [$key]['id'] = $info ['id'];
            $data [$key]['position'] = $info ['p_name'].$info ['position'];
            $data [$key]['name'] = $info ['name'];
            $data [$key]['mobile'] = $info ['mobile'];
        }
        $re = array('code'=>200, 'data'=>$data);
        exit(json_encode($re));
    }
    
    //获取商品列表
    public function get_product_list()
    {
        $this->load->model('ProductModel');
        $type = $this->input->get('type');
        $where = array();
        if ($type){
            $where ['type'] = $type;
        }
        $where ['status'] = 1;//展示
        $where ['is_default'] = 0;//非默认
        $list = $this->ProductModel->get_prod_option($where);
        
        $where ['is_default'] = 1;//默认
        $list2 = $this->ProductModel->get_prod_option($where);

        $re = array('code'=>200, 'data'=>$list, 'data2'=>$list2);
        exit(json_encode($re));
    }
    
    //获取投放点已商品
    public function get_merchant_product_list()
    {
        $this->load->model('MachineModel');
        $this->load->model('ProductModel');
        $merchant_id = $this->input->get('merchant_id');
        if (!$merchant_id){
            exit(json_encode(array('code'=>400,'msg'=>'缺少参数。')));
        }
        $where = array();
        $where ['merchant_id'] = $merchant_id;
        $list = $this->MachineModel->get_field_list($where, 'product_id');
        $product_list = array();
        foreach ($list as $info){
            $product = explode(',', $info['product_id']);
            $product_list = array_merge($product_list, $product);
        }
        unset($list, $info);
        $product_list = array_unique($product_list);
        $list = $this->ProductModel->get_prod_option(array(),$product_list);
        $re = array('code'=>200, 'data'=>$list);
        exit(json_encode($re));
    }
    
    //上传图片
    public function upload()
    {
        $file = $_FILES['file'];
        $source = $this->input->post('source')??0;
        if (!$file){
            $this->ajax_return(array('code'=>400, 'msg'=>'缺少参数.'));
        }
        $this->load->library('FileLibrary');
        $re = $this->filelibrary->upload($type = 1, $source, $file, true);
        exit(json_encode($re));
    }
    
    //获取代理商角色类型
    public function get_proxy_pattern()
    {
        $proxy_pattern = array('1'=>'普通代理商','2'=>'内部自营','3'=>'0元代理商');
        $re = array('code'=>200, 'data'=>$proxy_pattern);
        exit(json_encode($re));
    }
    
    //获取通讯三元组对应的模块、亚克力板、设备
    public function get_machine_iot_triad()
    {
        $this->load->model('MachineIotTriadModel');
        $this->load->model('MachineModel');
        $id = $this->input->get('id');
        $bind_triad_mark = $this->input->get('bind_triad_mark');
        $data = array();
        if ($id){
            $data['bind_triad_mark'] = 1;
            $machine_iot_triad_info = $this->MachineIotTriadModel->get_info(array('id'=>$id));
            $data['bind_side_num'] = $machine_iot_triad_info['bind_side_num'];
            $data['bind_plate_code_num'] = $machine_iot_triad_info['bind_plate_code_num'];
            $machine_list = $this->MachineModel->get_field_list(array('triad_id'=>$id), 'machine_id,type,inter_num');
            $mac_count = count($machine_list);
            $list = array();
            switch ($mac_count){
                case 0:
                    $mac = '～';
                    break;
                case 1:
                    $mac = $machine_list[0]['machine_id'];
                    $list [] = $machine_list[0]['machine_id'];
                    break;
                default:
                    $mac = '左'.$machine_list[0]['machine_id'].'～右'.$machine_list[$mac_count-1]['machine_id'];
                    $n = $mac_count/$machine_iot_triad_info['bind_side_num'];
                    for($i=0;$i<$machine_iot_triad_info['bind_side_num'];$i++){
                        $list [] = '左'.$machine_list[$i*$n]['machine_id'].'～右'.$machine_list[($i+1)*$n-1]['machine_id'];
                    }
                    break;
            }
        }else {
            $bind_triad_mark = explode('_', $bind_triad_mark);
            $data['bind_triad_mark'] = count($bind_triad_mark);
            $machine_iot_triad_info = $this->MachineIotTriadModel->get_info(array('id'=>$bind_triad_mark[0]));
            $data['bind_side_num'] = $machine_iot_triad_info['bind_side_num'];
            $data['bind_plate_code_num'] = $machine_iot_triad_info['bind_plate_code_num'];
            $list = array();
            $mac = array();
            foreach ($bind_triad_mark as $triad_id){
                $machine_list = $this->MachineModel->get_field_list(array('triad_id'=>$triad_id), 'machine_id,type,inter_num');
                $mac_count = count($machine_list);
                switch ($mac_count){
                    case 0:
                        break;
                    case 1:
                        $mac [] = $machine_list[0]['machine_id'];
                        $list [] = $machine_list[0]['machine_id'];
                        break;
                    default:
                        $mac [] = '左'.$machine_list[0]['machine_id'];
                        $n = $mac_count/$machine_iot_triad_info['bind_side_num'];
                        for($i=0;$i<$machine_iot_triad_info['bind_side_num'];$i++){
                            $list [] = '左'.$machine_list[$i*$n]['machine_id'].'～右'.$machine_list[($i+1)*$n-1]['machine_id'];
                        }
                        break;
                }
            }
            $mac = join('_', $mac);
        }
        
        $data ['mac'] = $mac;
        $data ['list'] = $list;
        $re = array('code'=>200, 'data'=>$data);
        exit(json_encode($re));
    }
    
    /**
     * [recovery_settlement 更新订单数据，在确认该方法前请勿在线上操作！！！！！！！！！！]
     *
     * @Author leeprince:2020-01-02T12:23:14+0800
     * @return [type]                             [description]
     */
    public function recovery_settlement()
    {
        exit();
        $now = time();
        $this->load->model('AgentSettlementModel');
        $list = $this->AgentSettlementModel->get_all_data();
        $data = array();
        foreach ($list as $info){
            $data[$info['create_time']][] = $info['agent_id'];
        }
        foreach ($data as $key=>$value){
            //处理订单表
            $where = array();
            $where ['settlement_time'] = $key;
            $where ['is_settlement'] = 1;
            $save = array();
            $save ['settlement_time'] = 0;
            $save ['is_settlement'] = 0;
            $save ['update_time'] = $now;
            $this->db->where($where);
            $this->db->where_not_in('agent_id', $value);
            $this->db->update('order', $save);
            
            //处理退款订单表
            $where = array();
            $where ['ffc_refund.settlement_time'] = $key;
            $where ['ffc_refund.is_settlement'] = 1;
            $save = array();
            $save ['ffc_refund.settlement_time'] = 0;
            $save ['ffc_refund.is_settlement'] = 0;
            $save ['ffc_refund.update_time'] = $now;
            $this->db->where($where);
            $this->db->where_not_in('ffc_order.agent_id', $value);
            $this->db->update('(ffc_refund join ffc_order on ffc_refund.order_id = ffc_order.id)', $save);
            //UPDATE (ffc_refund join ffc_order on ffc_refund.order_id = ffc_order.id) SET `ffc_refund`.`settlement_time` = 0, `ffc_refund`.`is_settlement` = 0, `ffc_refund`.`update_time` = 1577773140 WHERE `ffc_refund`.`settlement_time` = 1577773109 AND `ffc_refund`.`is_settlement` = 1 AND `ffc_order`.`agent_id` NOT IN('1010000037')
        }
        echo date('Y-m-d', $now);
        exit();
    }
}