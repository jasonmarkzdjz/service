<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2008 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&D Team, Advertising Platform & Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */
/**
 * TMMemCacheMgr
 * memcache管理类
 *
 * @package sdk.src.framework.cache
 */
class TMMemdCacheMgr implements TMCacheInterface {

    /**
     * 随机缓存基数值
     * @var int
     */
    const CACHE_ZIGZAG_BASE = 30;

    /**
     * 随机缓存最大倍数值
     * @var int
     */
    const CACHE_ZIGZAG_MULTIPLE = 5;

    /**
     * @var int DATA_EXPIRE_DIFF
     * 数据与Alive状态过期的时间差，即数据将延迟 DATA_EXPIRE_DIFF 秒以后过期
     */
    const DATA_EXPIRE_DIFF = 3600;

    /**
     * memcached对象
     * @var Memcached
     */
    private $cached;

    /**
     * memcached单例对象变量
     * @var TMMemdCacheMgr
     */
    private static $instance;

    /**
     * TMMemdCacheMgr对象映射数组
     * @var array
     */
    protected static $instanceMap = array();

    /**
     * 托管平台memcached对象映射数组
     * @var array
     */
    protected static $instanceZKMap = array();

    /**
     * memcache托管文件引入地址
     * @var string
     */
    const ZKNAME_REQUIRE_PATH = "/usr/local/zk_agent/names/nameapi.php";

    const MEMCACHED_ALIAS_LOCAL_DEFAULT = "local_default";

    /**
     * 是否开启
     * @var boolean
     */
    private $isEnable;

    /**
     * 是否持久连接
     * @var boolean
     */
    private $isPersistent;

    /**
     * 缓存类别、时间映射数组
     * @var array
     */
    protected static $_cacheCategories = array(
        'default'    => 30,
        'config'    => 3600
    );


    /**
     * 根据缓存类别获取缓存时间
     * @param string $category 缓存类别
     * @return int
     */
    protected static function cacheTimeout($category)
    {
        $categories = self::$_cacheCategories;
        if (empty($category) || !isset($categories[$category])){
            $category = 'default';
        }
        return $categories[$category];
    }

    /**
     * 构造函数
     * @param array 配置数组信息，这个配置信息是从getInstance方法传进来的
     * @return void 没有返回值
     */
    protected function __construct($config=null) {
        $isEnable = isset($config['enable'])?$config['enable']:TMConfig::get("memcached","enable");
        $isPersistent = isset($config['persistent'])?$config['persistent']:TMConfig::get("memcache","persistent");
        $configServers = isset($config['server'])?$config['server']:TMConfig::get("memcached","server");
        $this->initialize($isEnable, $isPersistent, $configServers);
    }

    /**
     * 得到一个memcached实例
     * @param array $options
     *      options["name"],options["enable"],options["persistent"]
     *      options['server']=array(array('host'=>$server->ip,'port'=>$server->port))
     * @return TMMemdCacheMgr
     */
    public static function getInstance($options = array()) {
        $name = self::MEMCACHED_ALIAS_LOCAL_DEFAULT;
        if(isset($options["name"]))
        {
            $name = $options["name"];
        }
        if(empty(self::$instanceMap[$name])) {
            $class = __CLASS__;
            self::$instanceMap[$name] = new $class($options);
        }
        return self::$instanceMap[$name];
    }

    /**
     * 初始化函数
     *
     * @param boolean $isEnable 是否打开
     * @param boolean $isPersistent 是否持久连接
     * @param array $configServers memcache配置数组
     */
    protected function initialize($isEnable, $isPersistent, $configServers)
    {
        $this->isEnable = $isEnable;
        $this->isPersistent = $isPersistent;
        if ($isEnable)
        {
            $this->cached = new Memcached();

            $persistent = $isPersistent;

            $servers = $configServers;
            foreach ($servers as $server)
            {
                $host = $server["host"];
                $port = empty($server["port"]) ? 11211 : (int) $server["port"];
                $this->cached->addServer($host, $port, $persistent);
            }
        }
    }

    /**
     * 析构函数，用于关闭所有memcache服务器连接
     *
     * @access public
     */
    public function __destruct() {
        if ($this->isEnable)
        {
            $persistent = $this->isPersistent;

            if (!$persistent)
            {
                $this->cached->quit();
            }
        }
    }
    /**
     * 增加缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @param mix $value 缓存值，只支持字符串
     * @param int $expire 缓存时间，0为不过期，单位为秒
     * @return boolean
     */
    public function add($key, $value, $expire = 0) {
        if (!$this->cached) {
            return false;
        }
        return $this->cached->add($key, $value, 0, $expire);
    }
    /**
     * 递增给定缓存名字的值，原子操作
     *
     * @param string $key 缓存名字
     * @param int $offset 每次递增的值
     */
    public function increment($key, $offset=1) {
        if (!$this->cached) {
            return false;
        }
        if(!$this->cached->get($key)) {
            return $this->cached->set($key, $offset);
        }
        return $this->cached->increment($key, $offset);
    }
    /**
     * 设置缓存对应关系
     *
     * @access public
     * @param string $key 缓存名字
     * @param mixed $value 缓存值
     * @param int $expire 缓存时间，0为不过期，单位为秒
     * @return void
     */
    public function set($key, $value, $expire = 0) {
        if (!$this->cached) {
            return ;
        }
        $this->cached->set($key, $value, 0, $expire);
    }
    /**
     * 获取缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @return mixed 缓存值
     */
    public function get($key) {
        if (!$this->cached) {
            return null;
        }
        return $this->cached->get($key);
    }
    /**
     * 递减给定缓存名字的值，原子操作
     *
     * @param string $key 缓存名字
     * @param int $offset 每次递减的值
     */
    public function decrement($key, $offset=1) {
        if (!$this->cached) {
            return false;
        }
        return $this->cached->decrement($key, $offset);
    }
}
?>
