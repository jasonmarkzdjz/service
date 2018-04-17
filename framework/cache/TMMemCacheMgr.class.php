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
class TMMemCacheMgr implements TMCacheInterface{

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
     * memcache对象
     * @var Memcache
     */
    private $cache;

    /**
     * memcache单例对象变量
     * @var TMMemCacheMgr
     */
    private static $instance;

    /**
     * TMMemCacheMgr对象映射数组
     * @var array
     */
    protected static $instanceMap = array();

    /**
     * 托管平台memcache对象映射数组
     * @var array
     */
    protected static $instanceZKMap = array();

    /**
     * memcache托管文件引入地址
     * @var string
     */
    const ZKNAME_REQUIRE_PATH = "/usr/local/zk_agent/names/nameapi.php";

    const MEMCACHE_ALIAS_LOCAL_DEFAULT = "local_default";

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
        $isEnable = isset($config['enable'])?$config['enable']:TMConfig::get("memcache","enable");
        $isPersistent = isset($config['persistent'])?$config['persistent']:TMConfig::get("memcache","persistent");
        $configServers = isset($config['server'])?$config['server']:TMConfig::get("memcache","server");
        $this->initialize($isEnable, $isPersistent, $configServers);
    }

    /**
     * 得到一个memcache实例
     * @param array $options
     *      options["name"],options["enable"],options["persistent"]
     *      options['server']=array(array('host'=>$server->ip,'port'=>$server->port))
     * @return TMMemCacheMgr
     */
    public static function getInstance($options = array()) {
        $name = self::MEMCACHE_ALIAS_LOCAL_DEFAULT;
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
     * 根据缓存名字获取缓存对象
     * @param string $name
     * @return TMMemCacheMgr
     */
    public static function getInstanceByName($name)
    {
        if(is_file(self::ZKNAME_REQUIRE_PATH)){
            if(empty(self::$instanceZKMap[$name]))
            {
                $server = ZKNameService::getHostByKey($name);
                if($server)
                {
                    $config = array('server'=>array(array('host'=>$server->ip,'port'=>$server->port)));
                    $class = __CLASS__;
                    self::$instanceZKMap[$name] = new $class($config);
                }

            }
            return self::$instanceZKMap[$name];
        }else{
            return self::getInstance(array("name" => $name));
        }
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
            $this->cache = new Memcache();

            $persistent = $isPersistent;

            $servers = $configServers;
            foreach ($servers as $server)
            {
                $host = $server["host"];
                $port = empty($server["port"]) ? 11211 : (int) $server["port"];
                $this->cache->addServer($host, $port, $persistent);
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
                $this->cache->close();
            }
        }
    }

    /**
     * 缓存对象函数调用结果
     * execute $obj->$function and cache the execution result if $reset is true,or get the value from cache by default key
     *
     * @access public
     * @param object $obj 需要缓存的对象
     * @param string $function 缓存对象调用的函数
     * @param array $param 缓存对象调用函数的参数数组
     * @param mixed $category 缓存过期方式。一般实现是定义一个缓存类型、过期时间对应数组，传递缓存类型来设置缓存时间
     * @param bool $reset 是否重新设置缓存的值
     * @param array $options 是否使用新的key，如果设置了$options['key']就使用它为缓存键，不传递则自动生成一个key
     * @return mixed
     */
    public function cached($obj, $function, $param=array(), $category=null, $reset=false, $options=array()) {
        $tamsId = TMConfig::get("tams_id");
        $cacheZigzagBase = self::CACHE_ZIGZAG_BASE;
        $cacheZigzagMutiple = self::CACHE_ZIGZAG_MULTIPLE;
        $cacheTimeout = self::cacheTimeout($category);
        return $this->handleCached($obj, $function, $param, $tamsId, $category, $cacheZigzagBase, $cacheZigzagMutiple, $cacheTimeout, $reset, $options);
    }

    /**
     * execute $obj->$function and cache the execution result if $reset is true,or get the value from cache by default key
     *
     * Usage:
     * <code>
     * $sql = "select * from Tbl_User";
     * $service = new TMService();
     * //到缓存中获取列表，如果没有设置或者已经过期，则执行SQL获取并缓存30秒；如果缓存存在并且没有过期，则直接返回缓存中的结果
     * $results = TMMemCacheMgr::getInstance()->cached($service, "query", array($sql), 30);
     * //同上，只不过缓存时间是应用的TMConfig::$_cacheCategories['default']对应的时间
     * $results = TMMemCacheMgr::getInstance()->cached($service, "query", array($sql), 'default');
     * //同上，只不过缓存时间是TMConfig::CACHE_ZIGZAG_BASE到TMConfig::CACHE_ZIGZAG_BASE*TMConfig::CACHE_ZIGZAG_MULTIPLE之间的一个随机数
     * $results = TMMemCacheMgr::getInstance()->cached($service, "query", array($sql), 'zigzag');
     * </code>
     * @access public
     * @param object $obj 需要缓存的对象
     * @param string $function 缓存对象调用的函数
     * @param array $param 缓存对象调用函数的参数数组
     * @param string $namespace 缓存命令空间，一般是活动号
     * @param int|string $category 缓存过期方式（支持2种方式）。一、如果是数字则是过期时间二、如果字符串则是过期时间的类别
     * @param int $cacheZigzagBase 随机缓存基数值
     * @param int $cacheZigzagMutiple 随机缓存最大倍数值
     * @param int $cacheTimeout 缓存时间
     * @param bool $reset 是否重新设置缓存的值
     * @param array $options 是否使用新的key，如果设置了$options['key']就使用它为缓存键，不传递则自动生成一个key
     * @return mixed
     */
    protected function handleCached($obj, $function, array $param, $namespace, $category=null, $cacheZigzagBase=30, $cacheZigzagMutiple=5, $cacheTimeout=30, $reset=false, $options=array())
    {
        if (!$this->isEnable)
        {
            try
            {
                return call_user_func_array(array($obj, $function), $param);
            }
            catch (Exception $ex)
            {
                return null;
            }
        }

        $zigzag = false;
        if ($category == 'zigzag')
        {
            $zigzag = true;
            $category = null;
        }
        $category = isset($category) ? $category : 'default';

        //生成关键字
        if (!empty($options['key']))
        {
            $key = $options['key'];
        }
        else
        {
            $key = $namespace . "_" . get_class($obj) . "_{$function}_" . md5(serialize($param));
        }

        $alive = false;
        //如果没有指定重新获取数据，则判断缓存是否过期
        if (!$reset)
        {
            $alive = $this->getAlive($key);
        }

        //如果参数设置为重置，或者已经过期，则重新设置数据
        $result = null;
        if ($reset || empty($alive))
        {
            if ($zigzag && $cacheZigzagBase)
            {
                $max_multiple = $cacheZigzagMutiple;
                $timeout = !$cacheZigzagBase * rand(1, $max_multiple ? $max_multiple : 10);
            }
            else if (is_numeric($category))
            {
                $timeout = (int) $category;
            }
            else
            {
                $timeout = $cacheTimeout;
            }

            $this->setAlive($key, true, $timeout);
            try
            {
                $result = call_user_func_array(array($obj, $function), $param);
            }
            catch (Exception $ex)
            {
                $this->clearAlive($key);
                return null;
            }
            $this->set($key, serialize($result), $timeout + self::DATA_EXPIRE_DIFF);
        }

        //如果已经执行取得结果
        if ($result !== null)
        {
            return $result;
        }

        //从缓存读取结果
        $cached = $this->get($key);
        if (!empty($cached))
        {
            return unserialize($cached);
        }

        return null;
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
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->add($key, $value, 0, $expire);
    }

    /**
     * 递增给定缓存名字的值，原子操作
     *
     * @param string $key 缓存名字
     * @param int $offset 每次递增的值
     */
    public function increment($key, $offset=1) {
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->increment($key, $offset);
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
        if (!$this->cache)
        {
            return ;
        }
        $this->cache->set($key, $value, 0, $expire);
    }

    /**
     * 设置持久缓存对应关系
     *
     * @access public
     * @param string $key 缓存名字
     * @param boolean $alive is alive
     * @param int $expire 缓存时间，0为不过期，单位为秒
     * @return void
     */
    public function setAlive($key, $alive, $expire)
    {
        if (!$this->cache)
        {
            return ;
        }
        $this->cache->set("__ALIVE__" . $key, $alive, 0, $expire);
    }

    /**
     * 获取缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @return mixed 缓存值
     */
    public function get($key)
    {
        if (!$this->cache)
        {
            return null;
        }
        return $this->cache->get($key);
    }

    /**
     * 获取持久缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @return mixed 缓存值
     */
    public function getAlive($key)
    {
        if (!$this->cache)
        {
            return null;
        }
        return $this->cache->get("__ALIVE__" . $key);
    }

    /**
     * 清除缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @param boolean $alive 是否是持久的
     * @return void
     */
    public function clear($key, $alive=true)
    {
        if (!$this->cache)
        {
            return false;
        }
        $delete_result = $this->cache->delete($key);
        if ($alive)
        {
            return $this->clearAlive($key);
        } else {
            return $delete_result;
        }
    }

    /**
     * 清除持久缓存值
     *
     * @access public
     * @param  string $key 缓存名字
     * @return void
     */
    public function clearAlive($key)
    {
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->delete("__ALIVE__" . $key);
    }


    /**
     * memcache服务器的缓存状态
     *
     * @access public
     */
    public function stat()
    {
        if ($this->isEnable)
        {
            $extendStats = $this->cache->getExtendedStats();
        }
        else
        {
            $extendStats = array();
        }

        print_r($extendStats);
    }

    /**
     * 递减给定缓存名字的值，原子操作
     *
     * @param string $key 缓存名字
     * @param int $offset 每次递减的值
     */
    public function decrement($key, $offset=1) {
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->decrement($key, $offset);
    }
}
?>
