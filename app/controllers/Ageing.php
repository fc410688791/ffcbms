<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ageing extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MachineIotTriadModel');
        $this->load->model('MachineModel');
        $this->load->model('TextModel');
    }

    /**
     * [index]
     *
     * @DateTime 2019-05-09
     * @Author   black.zhang
     * @return   [type]     [description]
     */
    public function index()
    {
        $key = $this->input->get('key');
        $this->_data['key'] = $key;
        $aging_status = $this->input->get('aging_status');
        $this->_data['aging_status'] = $aging_status;
        $status = $this->input->get('status');
        $this->_data['status'] = $status;
        $code = $this->input->get('code');
        $this->_data['code'] = $code;
        $reservation = $this->input->get('reservation');
        $page = $this->input->get('per_page')?:1;
        $limit = $this->config->item('per_page');
        $offset = ($page-1)*$limit;
        
        $list = array();
        $whereLike = '';
        $where = array();
        $where ['storage_status'] = 0;// 未入库
        if ($aging_status){
            if ($aging_status==1){
                $where ['aging_status'] = 1;//老化中
            }else {
                $where ['aging_status >'] = 1;//老化完成
            }
        }else{
            $where ['aging_status<>'] = 0;// （老化中，老化完成）
        }
        if ($reservation){
            // 通过下单时间查询
            list($start_time, $end_time) = switch_reservation($reservation);
            $where ['aging_start_time >='] = $start_time;
            $where ['aging_start_time <'] = $end_time;
            $this->_data['reservation'] = $reservation;
        }
        // 获得 搜索/筛选 数据的记录数
        $total_rows = $this->MachineIotTriadModel->getLikeCount($where, $whereLike);
        $triad_list = $this->MachineIotTriadModel->getLikeData($where, $whereLike, $total_rows, $offset);
        // 加载 Redis 类库
        $this->load->library('RedisDB');
        // 连接 Redis
        $redis = $this->redisdb->connect();
        $redis->select(0);
        foreach ($triad_list as &$info){
            $re = $redis->get('aging-'.$info['device_name']);
            $data = json_decode($re, true);
            $machine_list = $this->MachineModel->get_field_list(array('triad_id'=>$info['id']), 'machine_id,type,inter_num');
            $mac_count = count($machine_list);
            switch ($mac_count){
                case 0:
                    $mac = '～';
                    break;
                case 1:
                    $mac = $machine_list[0]['machine_id'];
                    break;
                default:
                    $mac = '左'.$machine_list[0]['machine_id'].'～右'.$machine_list[$mac_count-1]['machine_id'];
                    break;
            }
            $info['mac'] = $mac;
            switch ($info['aging_status']){
                case 1:
                    if ($data['faultMachines']){
                        if (count($data['faultMachines'])>0){
                            $info['status_color'] = 'red';
                            $info['status_name'] = '故障';
                            $info['status'] = 2;
                        }else {
                            $info['status_color'] = 'green';
                            $info['status_name'] = '正常';
                            $info['status'] = 1;
                        }
                    }else {
                        $info['status_color'] = 'green';
                        $info['status_name'] = '正常';
                        $info['status'] = 1;
                    }
                    break;
                case 2:
                    $info['status_color'] = 'red';
                    $info['status_name'] = '故障';
                    $info['status'] = 2;
                    break;
                case 3:
                    $info['status_color'] = 'green';
                    $info['status_name'] = '正常';
                    $info['status'] = 1;
                    break;
                case 4:
                    $info['status_color'] = 'green';
                    $info['status_name'] = '正常';
                    $info['status'] = 1;
                    break;
                default:
                    break;
            }
            $info['fault_count'] = $data['faultCount'];
            $faultTypeAndCount = $data['faultTypeAndCount'];
            $info['fault_code_count'] = '';
            $info['fault_code_mac'] = '';
            $code_array = array();
            if ($faultTypeAndCount){
                foreach ($faultTypeAndCount as $k=>$v){
                    if ($v['c']>0){
                        $code_array [] = $k;
                    }
                    $info['fault_code_count'] .= '&#10;'.$k.':'.$v['c'].'次';
                    $info['fault_code_mac'] .= '&#10;'.$k.':'.join('、', $v['m']);
                }
            }else{
                $info['fault_code_count'] = '';
                $info['fault_code_mac'] = '';
            }
            
            $info['fault_mac'] = $data['faultMachines']?join(',', $data['faultMachines']):'';
            if ($info['aging_status']==1){
                $info['aging_dec'] = '老化中';
                $info['aging_status_color'] = 'orange';
            }else {
                $info['aging_dec'] = '老化完成';
                $info['aging_status_color'] = 'green';
            }
            $info['aging_start_time'] = $info['aging_start_time']?date('Y-m-d H:i:s', $info['aging_start_time']):'-';
            if ($key){
                foreach ($machine_list as $v){
                    if ($v['machine_id']==$key){
                        $list [] = $info;
                    }
                }
            }else {
                $flag = true;
                if ($status&&$status!=$info['status']){
                    $flag = false;
                }
                if ($code&&!in_array($code, $code_array)){
                    $flag = false;
                }
                if ($flag){
                    $list [] = $info;
                }
            }
        }
        // 关闭 Redis
        $redis->close();
        $this->_data['list'] = $list;
        $this->template->admin_render('ageing/index', $this->_data);
    }
    
    public function update()
    {
        $id = $this->input->get('id');
        $status = $this->input->post('status');
        
        $where = array();
        $where ['id'] = $id;
        $info = $this->MachineIotTriadModel->get_info($where);
        $save = array();
        if ($status!=$info['aging_status']){
            $save ['aging_status'] = $status;
        }
        if ($save){
            $save ['update_time'] = time();
            $re = $this->MachineIotTriadModel->update($save, $where);
            if ($re){
                // 添加系统数据库日志; 参数1:操作对象; 参数2:操作结果
                $this->add_sys_log($id, $save);
                $this->ajax_return(array('code'=>200, 'msg'=>'修改成功！'));
            }else {
                $this->ajax_return(array('code'=>400, 'msg'=>'修改失败！'));
            }
        }else {
            $this->ajax_return(array('code'=>400, 'msg'=>'没有任何修改！'));
        }
    }
}
