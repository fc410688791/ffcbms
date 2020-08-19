<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/ShangHai');
set_time_limit(0);


class CrontabMonitor extends Controller
{
    const CMD = [
        'key' => 'instantQuery', 
        'content' => [1], 
    ];

    const IOT = [
        [
            'position'   => '办公室专用',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '866262042509918',
        ], 
/*        [
            'position'   => '监控佘总设备',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '869756046203052',
        ],
        [
            'position'   => '山东省潍坊市高密市',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046912420',
        ], 
        [
            'position'   => '山东省潍坊市诸城市',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046905689',
        ], 
        [
            'position'   => '山东省潍坊市诸城市',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046890956',
        ], 
        [
            'position'   => '山东省潍坊市安丘市',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046919169'
        ], 
        [
            'position'   => '昌邑汽车站候车大厅',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046905911'
        ],*/
        /*[
            'position'   => '沧州测试01_f200301003',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046921355'
        ],
        [
            'position'   => '沧州测试02-f200301039',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046891434'
        ],
        [
            'position'   => '大连星海广场',
            'productKey' => 'a1shrmY5JgP',
            'deviceName' => '861529046912693'
        ],*/

    ];

    // 监控设备状态
    private static $redis;
    private $redisMonitorData = [        
        'allcount'       => 0, // 请求总数，
        'UNKNOWN'        => 0, // 请求总数，
        'TIMEOUT'        => 0, // 请求总数，
        'OFFLINE'        => 0, // 请求总数，
        'HALFCONN'       => 0, // 请求总数，
        'UNKNOWNDefault' => 0, // 请求总数，
        'Status01'       => 0, // 请求总数，
        'Status02'       => 0, // 请求总数，
        'Status03'       => 0, // 请求总数，
        'Status04'       => 0, // 请求总数，
        'StatusDefault'  => 0, // 请求总数，
    ];
    const REDISKEY = 'monitor';

    // 监控设备离线
    const M_OFFLINE = 'machineOffline-'; // 离线三元组
    const M_HOME_OFFLINE = 'homeMachineOffline'; // 后台首页显示的设备掉线情况
    const M_OFFLINE_EMAIL_MAXCOUNT = 20; // 离线最大次数
    const M_OFFLINE_EMAIL = 'sendEmailOffline'; // 发送邮件入栈

    const SEND_ENMAI_TO = [
        'huangliang@qkc88.cn',
        'chenjinshui@qkc88.cn',
        // 'lihuangzi@qkc88.cn',
    ];
    const SEND_ENMAI_CC = [
        // 'lihuangzi@qkc88.cn',
    ];

    private $homeOfflineData = [];

    public function __construct()
    {
        parent::__construct();

        $this->load->library('/aliyuniot/Aliyuniot');

                // 加载 Redis 类库
        $this->load->library('RedisDB');

        $this->load->helper(array('log', 'common'));
    }

    /**
     * [monitorIotMachine 监控设备情况]
     *
     * @Author leeprince:2019-06-23T20:28:37+0800
     * @return [type]                             [description]
     */
    public function monitorIotMachine()
    {

        $ctime = time();

        // 连接 Redis
        $redis = $this->redisdb->connect();
        self::$redis = $redis;
        $monitorKey = self::REDISKEY;
        if ($data = $redis->get($monitorKey)) {
            $this->redisMonitorData = json_decode($data, true);
        } else {
            $redis->set($monitorKey, json_encode($this->redisMonitorData));
        }
        $this->bingOp(self::IOT, 0);
    }

    /**
     * [bingOp 操作]
     *
     * @Author leeprince:2019-06-23T20:28:30+0800
     * @param  [type]                             $data [description]
     * @param  [type]                             $i    [description]
     * @return [type]                                   [description]
     */
    private function bingOp($data, $i)
    {
        $this->redisMonitorData['allcount']++;

        $CMD = self::CMD;
        $position   = $data[$i]['position'];
        $productKey = $data[$i]['productKey'];
        $deviceName = $data[$i]['deviceName'];

        $iotRes = $this->aliyuniot->sendRequet($productKey, $deviceName, $CMD['key'], $CMD['content'], 5000);
        $Success = $iotRes->Success;
        $content = '';
        if ( ! $Success) { // API文档：UNKNOW；实际返回：UNKONW
            $code = $iotRes->RrpcCode;
            switch ($code) {
                case ($code == 'UNKNOWN' || $code == 'UNKONWN'):
                    $content = '系统异常';
                    $this->redisMonitorData['UNKNOWN']++;
                    break;
                case 'TIMEOUT':
                    $content = '设备响应超时';
                    $this->redisMonitorData['TIMEOUT']++;
                    break;
                case 'OFFLINE':
                    $content = '4G离线';
                    $this->redisMonitorData['OFFLINE']++;
                    break;
                case 'HALFCONN':
                    $content = '4G离线(心跳周期内)';
                    $this->redisMonitorData['HALFCONN']++;
                    break;
                
                default:
                    $content = '系统异常(阿里云返回异常)';
                    $this->redisMonitorData['UNKNOWNDefault']++;
                    break;
            }
        } else {
            $PayloadBase64Byte = $iotRes->PayloadBase64Byte;
            $findStatus = $this->aliyuniot->dataParsingSendAck($PayloadBase64Byte)[0];
            switch ($findStatus) {
                case '01':
                    $content = '4G和设备正常|待用';
                    $this->redisMonitorData['Status01']++;
                    break;
                case '02':
                    $content = '4G和设备正常|正在使用';
                    $this->redisMonitorData['Status02']++;
                    break;
                case '03':
                    $content = '4G和设备正常|设备离线';
                    $this->redisMonitorData['Status03']++;
                    break;
                case '04':
                    $content = '4G和设备正常|设备故障';
                    $this->redisMonitorData['Status04']++;
                    break;
                default:
                    $content = '设备返回异常命令字';
                    $this->redisMonitorData['StatusDefault']++;
                    break;
            }
        }

        $ctime = date('Y-m-d H:i:s');
        /* .csv 标题
        时间,位置,设备情况,系统异常比例,设备响应超时比例,4G离线比例,4G离线(心跳周期内)比例,系统异常(阿里云返回异常)比例,4G和设备正常|待用比例,4G和设备正常|正在使用比例,4G和设备正常|设备离线比例,4G和设备正常|设备故障比例, 设备返回异常命令字比例

        */
        // echo "{$ctime},{$position},{$deviceName},{$content}\r\n";
        echo "{$ctime},{$position},{$content}\r\n";

        ++$i;
        $leng = count($data);
        if ($i < $leng) {
            $this->bingOp($data, $i);
        } else {
            $allcount       = $this->redisMonitorData['allcount'];

            $UNKNOWN        = (round($this->redisMonitorData['UNKNOWN']/$allcount, 2)*100).'%';
            $TIMEOUT        = (round($this->redisMonitorData['TIMEOUT']/$allcount, 2)*100).'%';
            $OFFLINE        = (round($this->redisMonitorData['OFFLINE']/$allcount, 2)*100).'%';
            $HALFCONN       = (round($this->redisMonitorData['HALFCONN']/$allcount, 2)*100).'%';
            $UNKNOWNDefault = (round($this->redisMonitorData['UNKNOWNDefault']/$allcount, 2)*100).'%';
            $Status01       = (round($this->redisMonitorData['Status01']/$allcount, 2)*100).'%';
            $Status02       = (round($this->redisMonitorData['Status02']/$allcount, 2)*100).'%';
            $Status03       = (round($this->redisMonitorData['Status03']/$allcount, 2)*100).'%';
            $Status04       = (round($this->redisMonitorData['Status04']/$allcount, 2)*100).'%';
            $StatusDefault  = (round($this->redisMonitorData['StatusDefault']/$allcount, 2)*100).'%';

            echo ",,,$UNKNOWN,$TIMEOUT,$OFFLINE,$HALFCONN,$UNKNOWNDefault,$Status01,$Status02,$Status03,$Status04,$StatusDefault\r\n";


            self::$redis->set(self::REDISKEY, json_encode($this->redisMonitorData));
            self::$redis->close();
        }
        exit();
    }


    /**
     * [machineOffline 监控设备离线/断电]
     *
     * @Author leeprince:2019-08-06T19:38:36+0800
     * @return [type]                             [description]
     */
    public function machineOffline()
    {
        $this->load->model(['MachineModel']);
        self::$redis = $this->redisdb->connect();

        $CMD = self::CMD;

        $machineIotData = $this->MachineModel->getOneMachineIotTriadData();
        foreach ($machineIotData as $key => $value) {
            $product_key   = $value['product_key'];
            $device_name   = $value['device_name'];
            $device_secret = $value['device_secret'];

            try {
                $iotRes = $this->aliyuniot->sendRequet($product_key, $device_name, $CMD['key'], $CMD['content'], 5000);
            } catch (Exception $e) {
                continue;
            }
            $Success = $iotRes->Success;
            $isOffline = false;
            if (! $Success) {
                if (isset($iotRes->RrpcCode)) {
                    $RrpcCode = $iotRes->RrpcCode;
                    switch ($RrpcCode) {
                        case ($RrpcCode == 'OFFLINE' || $RrpcCode == 'HALFCONN'):
                            $isOffline = true;
                            break;
                        
                        default:
                            $isOffline = false;
                            break;
                    }
                }
            }
            $this->setMacineOffline($value, $isOffline);

        }

        $set = self::$redis->set(self::M_HOME_OFFLINE, json_encode($this->homeOfflineData));

        self::$redis->close();
        $cdata = date('Y-m-d H:i:s');
        
        exit("监控;;时间:".$cdata);
    }

    /**
     * [setMacineOffline 设置设备离线信息]
     *
     * @Author leeprince:2019-08-06T20:16:36+0800
     * @param  [type]                             $value [description]
     */
    private function setMacineOffline($value, $isOffline)
    {
        
        $redisKey  = self::M_OFFLINE.$value['device_name'];

        $havaRedis = self::$redis->get($redisKey);
        list($requesetCount, $offlineCount, $lastOnlieTime) = $this->getHavaOfflineRedisData(json_decode($havaRedis, true), $isOffline);

        $redisData = [
            "merchant_name" => $value['merchant_name'],
            "position_name" => $value['position_name'],
            "position"      => $value['position'],
            "machine_id"    => $value['machine_id'],
            "device_name"   => $value['device_name'],
            "requesetCount" => $requesetCount,
            "offlineCount"  => $offlineCount,
            "lastOnlieTime" => $lastOnlieTime,
        ];

        $ctime = time();
        if ( ! $isOffline) {
            $redisData['lastOnlieTime'] = $ctime;
        } else {
            // 首页显示掉线数据
            $this->homeOfflineData[] = $redisData;
        }

        // 判断设备已经离线
        if ($offlineCount >= self::M_OFFLINE_EMAIL_MAXCOUNT) {
            // 离线次数达到后压栈，等待发送邮件通知。并设置请求次数与离线次数为0
            $rpush = self::$redis->rpush(self::M_OFFLINE_EMAIL, json_encode($redisData));

            $redisData['requesetCount'] = 0;
            $redisData['offlineCount']  = 0;
        }

        $set = self::$redis->set($redisKey, json_encode($redisData));

        $redisData = null;
    }

    /**
     * [getOfflineCount 统计数量]
     *
     * @Author leeprince:2019-08-06T20:16:49+0800
     * @param  [type]                             $data [description]
     * @return [type]                                   [description]
     */
    private function getHavaOfflineRedisData($data, $isOffline)
    {
        $requesetCount = isset($data['requesetCount'])? ++$data['requesetCount'] : 1;
        $offlineCount  = isset($data['offlineCount'])? ++$data['offlineCount'] : 1;
        $lastOnlieTime = isset($data['lastOnlieTime'])? $data['lastOnlieTime'] : 0;

        // 如果有一次不离线则清空离线数量统计
        if ( ! $isOffline) {
            $offlineCount  = 0;
        }

        return [$requesetCount, $offlineCount, $lastOnlieTime];
    }


    /**
     * [sendMachineOfflineEmail 发送邮件]
    // 连续检测总次数; 检测到离线总次数; 上一次上线时间;估计断电时长;设备位置;4G模块编号;包含的设备ID;
     * 
     * @Author leeprince:2019-08-07T19:23:52+0800
     * @return [type]                             [description]
     */
    public function sendMachineOfflineEmail()
    {
        $redis = $this->redisdb->connect();
        $redisKey  = self::M_OFFLINE_EMAIL;

        $ctime = time();
        $cdata = date('Y-m-d H:i:s');

        $subject = '设备离线报告';
        $bodyContent = "";
        $offlineMachineCount = 0; // 离线设备次数
        while ( $sendEmailData = json_decode($redis->lpop($redisKey), true)) {
            /*echo '<pre>';
            var_dump($sendEmailData);exit();*/
            
            $lastOnlieTime = "【未知】";
            $offlineTimeNum = "【上一次上线时间未知，无法估计时间】";
            if ( ! empty($sendEmailData['lastOnlieTime'])) {
                $lastOnlieTime = date("Y-m-d H:i:s", $sendEmailData['lastOnlieTime']);
                $timeDiff = time_diff($sendEmailData['lastOnlieTime'], $ctime);
                $offlineTimeNum = "{$timeDiff['day']}天{$timeDiff['hours']}小时{$timeDiff['minutes']}分";
            }
            /*$bodyContent .= "
            <fieldset>
                <legend>投放点：".$sendEmailData['merchant_name']."</legend>
                <ul>
                    <li><div style='width:150px;display: inline-block;'>连续检测总次数: </div>".$sendEmailData['requesetCount']."</li>
                    <li><div style='width:150px;display: inline-block;'>其中检测到离线总次数: </div>".$sendEmailData['offlineCount']."</li>
                    <li><div style='width:150px;display: inline-block;'>上一次上线时间: </div>".$lastOnlieTime."</li>
                    <li><div style='width:150px;display: inline-block;'>估计断电时长: </div>".$offlineTimeNum."</li>
                    <li><div style='width:150px;display: inline-block;'>设备具体位置: </div>".$sendEmailData['position_name'].",".$sendEmailData['position']."</li>
                    <li><div style='width:150px;display: inline-block;'>4G模块编号: </div>".$sendEmailData['device_name']."</li>
                    <li><div style='width:150px;display: inline-block;'>包含的设备ID: </div>".$sendEmailData['machine_id']."</li>
                </ul>
            </fieldset>";*/
            $bodyContent .= "
                <tr>
                    <td>".$sendEmailData['merchant_name']."</td>
                    <td>".$sendEmailData['position']."</td>
                    <td>".$offlineTimeNum."</td>
                    <td>".$sendEmailData['machine_id']."</td>
                </tr>
            ";

            $offlineMachineCount++;
        }

        if ( ! empty($bodyContent)) {
            $body = "
                <h2>离线设备个数：".$offlineMachineCount."</h2>
                <h5> - 检测到以下投放点中的设备长时间处于离线状态，请及时处理！</h5>
                <table border='1'>
                    <thead>
                        <tr>
                            <td>投放点</td>
                            <td>具体位置</td>
                            <td>断电时长</td>
                            <td>设备ID</td>
                        </tr>
                    </thead>
                    <tbody>
                        ".$bodyContent."
                    </tbody>
                </table>
            ";

            try {
                $this->load->library('phpmailer/Email');
                $this->email->sendEmail(self::SEND_ENMAI_TO, $subject, $body, self::SEND_ENMAI_CC);
            } catch (Exception $e) {
                exit("发送失败;;时间:".$cdata."；失败原因:".$e->getMessage());
            }
            exit("发送成功;;时间:".$cdata);
        }
    }

    /**
     * [getRedisOutput 获取redis数据]
     *
     * @Author leeprince:2019-08-07T19:23:17+0800
     * @return [type]                             [description]
     */
    public function getRedisOutput()
    {
        $cmd = strtolower($this->input->get('cmd'));
        $params = $this->input->get('params');
        $isToJson = $this->input->get('isToJson');
        if ( ! $cmd) {
            prt_exit("缺少参数");
        }

        $redis = $this->redisdb->connect();

        if (empty($params)) {
            $data = $redis->$cmd();
        } else {
            if (isJson($params)) {
                $params = json_decode($params, true);
            }
            $data = $redis->$cmd($params);
        }
        if ($isToJson == 'true') {
            $data = json_encode($data);
        }
        prt_exit($data);exit;
    }
}
















