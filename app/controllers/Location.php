<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Location extends Controller
{

    public function get_list()
    {
        $this->load->model('LocationModel');
        $pid = $this->input->get('pid');
        $where = array();
        if ($pid!==null){
            $where ['pid'] = $pid;
        }
        $list = $this->LocationModel->get_list($where);
        $data_array = array();
        if ($list){
            $data_array = array('code'=>200, 'msg'=>$list);
        }else {
            $data_array = array('code'=>400);
        }
        echo json_encode($data_array);
        exit();
    }
}
