<?php
defined('BASEPATH') OR exit('不允许直接访问');
/**
 *
 */
class RedisDB
{

    /**
     * SalimCore资源
     * @var object
     */
    protected $SC;

    /**
     * Redis服务器主机名或UNIX域套接字。
     * @var string
     */
    protected $hostname;

    /**
     * 端口
     * @var int
     */
    protected $port;

    /**
     * 超时时间
     * @var float
     */
    protected $timeout;

    /**
     * 是否是持续连接
     * @var boolean
     */
    protected $pconnect;

    /**
     * 服务器密码
     * @var string
     */
    protected $password;

    /**
     * Redis实例
     * @var object
     */
    protected $Redis;


    /**
     * 获得SC资源，Redis实例，并初始化属性值。
     *
     * @return void
     */
    public function __construct()
    {
        $this->SC = &get_instance();
        $this->Redis = new Redis();
        $this->init();
    }

    /**
     * 根据配置文件设置属性值。
     *
     * @return void
     */
    protected function init() 
    {
        $this->SC->config->load('redis',true);
        $conf  = $this->SC->config->item('redis');
        
        $this->hostname = isset($conf['hostname']) ? $conf['hostname'] : '127.0.0.1';
        $this->port     = isset($conf['port']) ? $conf['port'] : '6379';
        $this->timeout  = isset($conf['timeout']) ? $conf['timeout'] : '0';
        $this->password = isset($conf['password']) ? $conf['password'] : NULL;
        $this->pconnect = isset($conf['pconnect']) ? $conf['pconnect'] : FALSE;
    }

    /**
     * 连接Redis服务器
     *
     * 根据是否是持久连接，执行不同的连接方式。
     *
     * @return object 返回Redis实例
     */
    public function connect()
    {
        $re = $this->pconnect ? $this->_pconnect() : $this->_connect();

        //如果提供了密码，执行认证。
        if(is_string($this->password) && $this->password != '')
            $this->Redis->auth($this->password);

        //连接成功返回Redis实例
        if($re)
            return $this->Redis;
    }
    /**
     * 普通方式连接
     *
     * @return int
     */
    protected function _connect()
    {
        return $this->Redis->connect($this->hostname, $this->port, $this->timeout);
    }
    /**
     * 持久连接方式连接
     *
     * @return int
     */
    protected function _pconnect()
    {
        return $this->Redis->pconnect($this->hostname,$this->port);
    }
    /**
     * 关闭连接
     *
     * @return int
     */
    public function close()
    {
        return $this->Redis->close();
    }
}
