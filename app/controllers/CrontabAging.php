
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/ShangHai');
set_time_limit(0);


/**
 * 老化测试
 */
class CrontabAging extends Controller
{
    const REDIS_AGING_KEY_PREFIX = 'aging-';
    const REDIS_AGING_MAIN_TIME = 2592000; // 30天

    /**
     * [__construct 构造函数]
     *
     * @Author leeprince:2019-09-05T10:24:25+0800
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model(['MachineIotTriadModel', 'TextModel']);

        $this->load->library('/aliyuniot/Aliyuniot');
        $this->load->library('RedisDB');
        $this->load->helper(array('log', 'common'));
    }

    /**
     * [startAging 开始老化]
     *
     * @Author leeprince:2019-09-05T11:10:26+0800
     * @return [type]                             [description]
     */
    public function startAging()
    {
        echo '开始老化时间：'.time();

        /**
         * 获取在老化中的deviceName和各路口设备
         */
        $machineData = $this->MachineIotTriadModel->getDeviceNameAndMachine(['t1.aging_status' => 1]); // 
        $iotData     = get2ArrayToKeyValueArray($machineData, 'device_name', ['product_key', 'machine_id', 'inter_num']);
        $iotTimeData = get2ArrayToKeyValueArray($machineData, 'device_name', ['aging_time', 'aging_start_time'], false);
        
        /**
         * 查询设备状态标记老化结果, 并更新老化状态
         */
        $data = $this->bingOp($iotData, $iotTimeData);
    }

    /**
     * [bingOp 发送指令
     *     数据格式
                {
                  "allRequestCount": 168, // 请求总次数
                  "faultMachines": [
                    "f200300553",
                    "f200300554",
                    "f200300555",
                    "f200300556",
                    "f200300557",
                    "f200300558",
                    "f200300571",
                    "f200300572",
                    "f200300573",
                    "f200300574",
                    "f200300575",
                    "f200300576"
                  ],
                  "faultCount": 168, // 故障次数
                  "faultTypeAndCount": {
                    "10116": {
                      "c": 0,
                      "m": []
                    },
                    "10117": {
                      "c": 168,
                      "m": [
                        "f200300553",
                        "f200300554",
                        "f200300555",
                        "f200300556",
                        "f200300557",
                        "f200300558",
                        "f200300571",
                        "f200300572",
                        "f200300573",
                        "f200300574",
                        "f200300575",
                        "f200300576"
                      ]
                    },
                    "10118": {
                      "c": 0,
                      "m": []
                    },
                    "10119": {
                      "c": 0,
                      "m": []
                    },
                    "other": {
                      "c": 0,
                      "m": []
                    }
                  }
                }
     * ]
     *
     * @Author leeprince:2019-09-05T11:15:02+0800
     * @param  [type]                             $iotData [description]
     * @return [type]                                      [description]
     */
    private function bingOp($iotData, $iotTimeData)
    {
        $aging = $this->TextModel->findOne(['type' => 7, 'status' => 1]);
        $accessRate = $aging['text_ext']; // 标准老化通过率

        // 桩
        foreach ($iotData as $key => $value) {
            $deviceName = $key;
            $ctime      = time();

            $allRequestCount = 0; // 请求总次数
            $faultCount      = 0; // 故障总次数
            $faultMachines   = []; // 故障的设备IDS

            $fault10116_m = [];
            $fault10116_c = 0;
            $fault10117_m = [];
            $fault10117_c = 0;
            $fault10118_m = [];
            $fault10118_c = 0;
            $fault10119_m = [];
            $fault10119_c = 0;
            $faultother_m = [];
            $faultother_c = 0;

            $machine_ids = array_column($value, 'machine_id');

            // 各路口启动检测
            foreach ($value as $vkey => $vvalue) {
                $product_key = $vvalue['product_key'];
                $machine_id  = $vvalue['machine_id'];
                $inter_num   = $vvalue['inter_num'];

                // 发送指令
                $iotRes = $this->aliyuniot->sendRequet($product_key, $deviceName, 'instantQuery', [$inter_num], 5000);
                $Success = $iotRes->Success;

                $allRequestCount++;
                if ( ! $Success) {
                    $faultCount = count($machine_ids); // 设备离线，12
                    $faultMachines = $machine_ids;

                    $fault10117_c = count($machine_ids); // 设备离线，12
                    $fault10117_m = $machine_ids;
                    break;
                }
                $PayloadBase64Byte = $iotRes->PayloadBase64Byte;
                $findStatus = $this->aliyuniot->dataParsingSendAck($PayloadBase64Byte)[0];
                if ($findStatus != '01') {
                    $faultCount++;
                    array_push($faultMachines, $machine_id);
                }
                switch ($findStatus) {
                    case '01':
                        break;
                    case '02':
                        $fault10116_c++;
                        array_push($fault10116_m, $machine_id);
                        break;
                    case '03':
                        $fault10118_c++;
                        array_push($fault10118_m, $machine_id);
                        break;
                    case '04':
                        $fault10119_c++;
                        array_push($fault10119_m, $machine_id);
                        break;
                    default:
                        $faultother_c++;
                        array_push($faultother_m, $machine_id);
                        break;
                }
            } 
            // 各路口 - end

            // 存入redis
            $redisKey = self::REDIS_AGING_KEY_PREFIX.$deviceName;
            $redis = $this->redisdb->connect();
            $existRedis = $redis->get($redisKey);
            if (! empty($existRedis)) { // 已存在redis中 
                $existRedis        = json_decode($existRedis, true);

                $faultMachines     = getArrayMergeAndUnique($faultMachines, $existRedis['faultMachines']);
                $faultTypeAndCount = $existRedis['faultTypeAndCount'];

                $allRequestCount += $existRedis['allRequestCount'];
                $faultCount      += $existRedis['faultCount'];

                $fault10116_c = $fault10116_c + $faultTypeAndCount[10116]['c'];
                $fault10116_m = array_merge($fault10116_m, $faultTypeAndCount[10116]['m']);

                $fault10117_c = $fault10117_c + $faultTypeAndCount[10117]['c'];
                $fault10117_m = array_merge($fault10117_m, $faultTypeAndCount[10117]['m']);

                $fault10118_c = $fault10118_c + $faultTypeAndCount[10118]['c'];
                $fault10118_m = array_merge($fault10118_m, $faultTypeAndCount[10118]['m']);

                $fault10119_c = $fault10119_c + $faultTypeAndCount[10119]['c'];
                $fault10119_m = array_merge($fault10119_m, $faultTypeAndCount[10119]['m']);

                $faultother_c = $faultother_c + $faultTypeAndCount['other']['c'];
                $faultother_m = array_merge($faultother_m, $faultTypeAndCount['other']['m']);
            }

            $redisData = [
                'allRequestCount'   => $allRequestCount,
                'faultMachines'     => $faultMachines,
                'faultCount'        => $faultCount,
                'faultTypeAndCount' => [
                    10116 => [
                        'c'  => $fault10116_c,
                        'm'  => array_unique($fault10116_m),
                        'mc' => array_count_values($fault10116_m),
                    ],
                    10117 => [
                        'c'  => $fault10117_c,
                        'm'  => array_unique($fault10117_m),
                        'mc' => array_count_values($fault10117_m),
                    ],
                    10118 => [
                        'c'  => $fault10118_c,
                        'm'  => array_unique($fault10118_m),
                        'mc' => array_count_values($fault10118_m),
                    ],
                    10119 => [
                        'c'  => $fault10119_c,
                        'm'  => array_unique($fault10119_m),
                        'mc' => array_count_values($fault10119_m),
                    ],
                    'other' => [
                        'c'  => $faultother_c,
                        'm'  => array_unique($faultother_m),
                        'mc' => array_count_values($faultother_m),
                    ],
                ],
            ];

            $setData = $redis->setex($redisKey, self::REDIS_AGING_MAIN_TIME, json_encode($redisData));

            /**
             * 老化时间结束，结果更新老化状态
             */
            $agingTime = $iotTimeData[$deviceName]['aging_time']; // 单位：分钟
            $startTime = $iotTimeData[$deviceName]['aging_start_time'];
            $diffTime  = $ctime - $startTime;
            $isEndAging = ($diffTime > $agingTime * 60)? true : false;
            if ($isEndAging) { // 老化结束

                $currentAccAgingRate = sprintf('%.2f', 1 - ($faultCount / $allRequestCount)); // 老化设备的通过率

                $aging_status = ($currentAccAgingRate < $accessRate)? 2 : 3; // 老化结束后桩存在故障设备

                $up = $this->MachineIotTriadModel->update([
                    'aging_status' => $aging_status,
                    'update_time' => $ctime,
                ], ['device_name' => "$deviceName"]);

                if (! $up) {
                    echo 'error: 更新老化状态错误';
                }
            }
        }// end - 桩
    }

}























