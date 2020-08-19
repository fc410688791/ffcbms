<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** 监控视图 */
class Monitor extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
        	'MachineModel',
        	'PositionModel',
        	'LocationModel',
        	'AgentMerchantModel',
        	'MachineIotTriadModel',
            'OrderModel',
        ]);
        if (IS_AJAX) {
        	$method = $this->input->post('method');
        	switch ($method) {
        		case 'getAddress': // 获取地址
        			$parent_id = $this->input->post('key');
        			$data = $this->LocationModel->findAll(['pid' => $parent_id]);
        			if ( ! $data) {
        				$this->ajax_return(['code' => -2, 'msg' => '数据为空']);
        			}
        			$this->ajax_return(['code' => 0, 'data' => $data]);
        			break;
        		case 'byMerchant': // 通过投放点查找设备
        			$merchant_id = $this->input->post('id');
        			$data = $this->MachineModel->getMachineByMerchant($merchant_id);
        			if ( ! $data) {
        				$this->ajax_return(['code' => -2, 'msg' => '数据为空']);
        			}

        			$data = $this->MachineModel->MerchantTriadGroup($data, 'bind_triad_mark');
        			$this->ajax_return(['code' => 0, 'data' => $data]);
        		case 'byPosition': // 通过位置查找设备
                    $province_id = $this->input->post('province');
                    $city_id     = $this->input->post('city');
                    $street_id   = $this->input->post('street');
                    $village_id  = $this->input->post('village');
					if ( ! $province_id) {
						$this->ajax_return(['code' => -200, 'msg' => '参数错误']);
					}

					$positionData = $this->PositionModel->getPositionById($province_id, $city_id, $street_id, $village_id);
					if (empty($positionData)) {
        				$this->ajax_return(['code' => -2, 'msg' => '数据为空']);
					}
					$positionArray = get2ArrayToValueArray($positionData, 'id');

        			$data = $this->MachineModel->getMachineByPosition($positionArray);
        			
        			if ( ! $data) {
        				$this->ajax_return(['code' => -2, 'msg' => '数据为空']);
        			}

        			$data = $this->MachineModel->MerchantTriadGroup($data);
        			$this->ajax_return(['code' => 0, 'data' => $data]);
        		case 'findMachineStatus': // 查询设备状态
					$machine_id     = $this->input->post('machine_id');
					$machine_status = $this->input->post('machine_status');
					$product_key    = $this->input->post('product_key');
					$device_name    = $this->input->post('device_name');
					$device_secret  = $this->input->post('device_secret');
					$inter_num      = $this->input->post('inter_num');
					if (! $machine_id || ! $product_key || ! $device_name || ! $device_secret || ! $inter_num) {
						$this->ajax_return(['code' => -1, 'msg' => '参数错误']);
					}

					$cmdCtxArrayIndex = [$inter_num];
        			$this->load->library('/aliyuniot/Aliyuniot');

        			$returnData = ['machine_id' => $machine_id, 'device_name' => $device_name];
                    
                    // 测试数据
                    /*// sleep(1);
                    $returnData['status'] = random_int(1, 8); // random_int(1, 4)
                    $this->ajax_return(['code' => 0, 'data' => $returnData]);*/

        			$mStatus = [
        				'pre' => 1, // 待使用
        				'use' => 2, // 正在使用中
        				'off' => 3, // 离线
        				'err' => 4, // 故障
                        'out' => 5, // 设备响应超时
                        'mof' => 6, // 4G离线
                        'noc' => 7, // 异常命令字
                        'ali' => 8, // 系统异常(阿里云返回异常)
        			];
        			if ($machine_status == 4) {
        				$returnData['status'] = $mStatus['err'];
    					$this->ajax_return(['code' => 0, 'data' => $returnData]);
        			}
        			try {
        				$iotRes = $this->aliyuniot->sendRequet($product_key, $device_name, 'instantQuery', $cmdCtxArrayIndex, 5000);
        				$Success = $iotRes->Success;
        				if ( ! $Success) { // API文档：UNKNOW；实际返回：UNKONW
        					errorLog('调用失败：'.json_encode(object_to_array($iotRes)));
                            $code = $iotRes->RrpcCode;
                            switch ($code) {
                                case 'TIMEOUT':
                                    $returnData['status'] = $mStatus['out'];
                                    break;
                                case ($code == 'OFFLINE' || $code == 'HALFCONN'):
                                    $returnData['status'] = $mStatus['mof'];
                                    break;
                                default:
                                    $returnData['status'] = $mStatus['ali'];
                                    break;
                            }
        					$this->ajax_return(['code' => 0, 'data' => $returnData]);
        				}

        				$PayloadBase64Byte = $iotRes->PayloadBase64Byte;
        				$findStatus = $this->aliyuniot->dataParsingSendAck($PayloadBase64Byte)[0];
        				switch ($findStatus) {
        					case '01':
        						$returnData['status'] = $mStatus['pre'];
        						break;
        					case '02':
        						$returnData['status'] = $mStatus['use'];
        						break;
        					case '03':
        						$returnData['status'] = $mStatus['off'];
        						# code...
        						break;
        					case '04':
        						$returnData['status'] = $mStatus['err'];
        						# code...
        						break;
        					default:
        						$returnData['status'] = $mStatus['noc'];
        						break;
        				}
    					$this->ajax_return(['code' => 0, 'data' => $returnData]);
        			} catch (Exception $e) {
        				$this->ajax_return(['code' => -2000, 'msg' => $e->getMessage()]);
        			}
        			break;
                case 'findMachine': // 查询设备信息
                    $machine_id      = $this->input->post('machine_id');
                    if (! $machine_id) {
                        $this->ajax_return(['code' => -1, 'msg' => '参数错误']);
                    }
                    $order = $this->OrderModel->findOne(['machine_id'=>$machine_id, 'status'=>1], 'pay_time, product_time, out_trade_no', 'id desc');
                    if (empty($order)) {
                        $this->ajax_return(['code' => 201, 'msg' => '暂无订单']);
                    }
                    $this->ajax_return(['code' => 0, 'data' => $order]);
                case 'operateMachine':
                    $product_key = $this->input->post('product_key');
                    $device_name = $this->input->post('device_name');
                    $inter_num   = $this->input->post('inter_num');

                    $cmdCtxArrayIndex = [$inter_num, 0, 0];

                    $this->load->library('aliyuniot/Aliyuniot');
                    $iotRes = $this->aliyuniot->sendRequet($product_key, $device_name, 'openLock', $cmdCtxArrayIndex, 5000);
                    $Success = $iotRes->Success;
                    if ( ! $Success) { // API文档：UNKNOW；实际返回：UNKONW
                        errorLog('调用失败：'.json_encode(object_to_array($iotRes)));
                        $this->ajax_return(['code' => -200, 'msg' => '清除失败, 请重试']);
                    }

                    $PayloadBase64Byte = $iotRes->PayloadBase64Byte;
                    $status = $this->aliyuniot->dataParsingSendAck($PayloadBase64Byte)[0];
                    if ($status != '03') {
                        $this->ajax_return(['code' => -210, 'msg' => '调用失败, 请重试!']);
                    }
                    $this->ajax_return(['code' => 0, 'msg' => '清除成功']);
                    break;
        		default:
        			$this->ajax_return(['code' => -1000, 'msg' => '请求错误']);
        			break;
        	}
        }
    }

    /**
     * [index 首页]
     *
     * @Author leeprince:2019-06-04T10:08:10+0800
     * @return [type]                             [description]
     */
    public function index()
    {
        $machineTypeWhere = " type != 1";

        $machineCount  = $this->MachineModel->count("{$machineTypeWhere} and status in (1,4)", 'id');
        // 一个桩对应一个模块
        // $triadCount  = $this->MachineModel->count("{$machineTypeWhere} and status in (1,4) and triad_id > 0", 'DISTINCT bind_triad_mark');
        // 一个桩可能存在多个模块
        $triadCount = count($this->MachineModel->findAll("{$machineTypeWhere} and status in (1,4) and triad_id > 0", "bind_triad_mark", '', 'bind_triad_mark'));

        $merchantList  = $this->AgentMerchantModel->findIotMerchantList();
        $provinceList  = $this->LocationModel->findAll(['pid' => 0]);
        $triadBindList = $this->MachineIotTriadModel->findAll([], 'id, bind_side_num, bind_plate_code_num');
        
        $this->_data['machineCount']  = $machineCount;
        $this->_data['triadCount']    = $triadCount;
        $this->_data['merchantList']  = $merchantList;
        $this->_data['provinceList']  = $provinceList;
        $this->_data['triadBindList'] = json_encode(get2ArrayToKeyValueArray($triadBindList, 'id', ['bind_side_num', 'bind_plate_code_num'], false));
    	$this->template->admin_render('monitor/index', $this->_data, true);
    }

}









