<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * [临时测试方法: 防止方法泄漏]
 *
 * @Author  leeprince:2019-12-20 20:25
 */
class Testfadfaawsdfser extends MY_Controller
{
       
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [下载密码器信息]
     *
     * @Author  leeprince:2019-12-20 20:24
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function downloadMachineInfo()
    {
        $this->load->model([
            'MachineModel',
            'PositionModel',
            'AgentModel',
            'AgentMerchantModel',
        ]);

        $t1 = $this->MachineModel->tableName();
        $t2 = $this->AgentModel->tableName();
        $t3 = $this->AgentMerchantModel->tableName();
        $t4 = $this->PositionModel->tableName();


        $select = '
                t1.machine_id, t1.position, 
                t2.card_name, t2.id agent_id, t2.mobile, t2.proxy_pattern,
                t3.name merchant_name, t3.address,
                t4.name
            ';

        $where = [
            't1.type' => 1,
            't1.agent_id != ' => 0,
        ];

        $data = $this->db->select($select)
            ->from("$t1 t1")
            ->join("$t2 t2", 't2.id = t1.agent_id')
            ->join("$t3 t3", 't3.id = t1.merchant_id')
            ->join("$t4 t4", 't4.id = t3.position_id')
            ->where($where)
            ->get()->result_array();


        $agentRole = [
            1 => '普通代理商',
            2 => "内部自营",
            3 => "元代理商"
        ];

        $this->load->library('phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("leeprince")
            ->setTitle("密码器设备信息")
            ->setSubject("密码器设备详细信息");
        $header = [
            "A1"=>"密码器设备ID",
            "B1"=>"投放点名称",
            "C1"=>"投放点详细地址",
            "D1"=>"设备详细位置",
            "E1"=>"代理商真实姓名",
            "F1"=>"代理商ID",
            "G1"=>"代理商手机号",
            "H1"=>"代理商角色"
        ];
        foreach($header as $key=>$val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($key,$val);
        }
        $pCoordinate = 2;
        foreach($data as $val)
        {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A".$pCoordinate, $val['machine_id'])
                ->setCellValue("B".$pCoordinate, $val['merchant_name'])
                ->setCellValue("C".$pCoordinate, $val['address'])
                ->setCellValue("D".$pCoordinate, $val['position'])
                ->setCellValue("E".$pCoordinate, $val['card_name'])
                ->setCellValue("F".$pCoordinate, $val['agent_id'])
                ->setCellValue("G".$pCoordinate, $val['mobile'])
                ->setCellValue("H".$pCoordinate, $agentRole[$val['proxy_pattern']]);
            $pCoordinate++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=密码器设备信息文件.xlsx');
        header('Cache-Control: max-age=0');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * [machineTypeOptimize 设备类型优化测试]
     *
     * @Author leeprince:2019-12-25T14:54:00+0800
     * @return [type]                             [description]
     */
    public function machineTypeOptimize()
    {
        $this->load->model(['MachineIotTriadModel', 'MachineModel', 'StorageRecordModel', 'StorageMachineTypeRecordModel', 'StorageOperateModel']);

        // 测试的三元组ID
        $triadArray = [12, 13];
        // 库存开始
        $recordStart = 228;
        // 库存设备类型开始
        $recordTypeStart = 181;
        // 库存操作开始
        $recordOprateStart = 293;

        // 更新三元组信息
        $triadBatchUpdate = [];
        foreach ($triadArray as $value) {
            $triadBatchUpdate[] = [
                'id'                  => $value,
                "agent_product_id"    => 0, 
                "bind_side_num"       => 0, 
                "bind_plate_code_num" => 0, 
                "bind_triad_mark"     => 0, 
                "aging_start_time"    => 0, 
                "aging_time"          => 0, 
                "aging_status"        => 0, 
                "batch_storage_id"    => 0, 
                "storage_status"      => 0, 
                "storage_time"        => 0, 
                "storage_out_time"    => 0, 
                "storage_user_id"     => 0, 
                "scan_storage_order"  => 0, 
                "bind_time"           => 0, 
                "update_time"         => 0, 
                "create_time"         => 1577257637
            ];
        }

        $this->db->trans_start();
        $this->MachineIotTriadModel->updateData($triadBatchUpdate, 'id');

        
        // 更新设备绑定信息
        $t1 = $this->db->dbprefix($this->MachineModel->tableName());

        $triadIdString  = '('.implode(',', $triadArray).')';
        $this->db->query("
            update 
                $t1 
            set 
                agent_product_id = 0, 
                status = 2,
                triad_id  = 0,
                inter_num = 0,
                bind_triad_mark = 0
            where   
                triad_id in $triadIdString
            ");

        // 更新库存
        $this->StorageRecordModel->delete(['id >' => $recordStart]);
        $this->StorageMachineTypeRecordModel->delete(['id >' => $recordTypeStart]);
        $this->StorageOperateModel->delete(['id >' => $recordOprateStart]);

        $this->db->trans_commit();
        if ( $this->db->trans_status() ===  FALSE) {
            prt_exit('数据库执行过程中发生错误');
        }

        prt_exit('执行成功');


    }
}





















