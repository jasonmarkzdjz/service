<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2012 BY  TENCENT  CORPORATION.  ALL RIGHTS
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
 * TMBasicConfigHandle
 * 读取yml配置的基础类，包括yml的解析和缓存
 *
 * yml的解析采用 TMYaml::parse($configFile)<br>
 * 使用TMYamlCacher::getInstance()->execute($configFile)，即缓存到ROOT_PATH . 'cache/yml/' 下，方便查看缓存的结果是否正确<br>
 * 程序会自动判断缓存内容和当前yml文件中的是否一致，如果不一致则重新生成缓存<br>
 * Usage:
 * <code>
 * $soreConfig = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH . 'config/score.yml');
 * </code>
 *
 * @package sdk.src.framework.config
 */
class TMBasicConfigHandle
{
    /**
     * 单例实例
     *
     * @var TMBasicConfigHandle
     *
     * @static
     * @access private
     */
    private static $instance;

    /**
     * 缓存数组
     *
     * @var array
     *
     * @static
     * @access private
     */
    private static $cacheArray = array();

    /**
     * get the instance
     *
     * @static
     * @access public
     * @return TMBasicConfigHandle $instance
     */
    public static function getInstance()
    {
        if(self::$instance == null)
        {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * get Config from yml
     *
     * @param string $configFile yml config filepath
     * @param bool $resetCache if reset the cache(when the config file changed, this function should be call and set this param `true` to reset the cache)
     * @return array $config
     */
    public function execute($configFile, $resetCache=false)
    {
        if(isset(self::$cacheArray[$configFile]))
        {
            return self::$cacheArray[$configFile];
        }

        $config =  TMYamlCacher::getInstance()->execute($configFile, $resetCache);

        self::$cacheArray[$configFile] = $config;
        return $config;
    }

    /**
     * 缓存配置到APC中，并自动判断原始文件是否已经变动，如果有变动将自动更新缓存
     *
     * @access public
     * @param string $configFile the yml config file url
     * @param bool $resetCache whether reset
     * @return array $cached['data']
     */
    public function cached($configFile, $resetCache=false)
    {
        $cache = TMAPCMgr::getInstance();
        if ($resetCache)
        {
            $cached = $cache->cached($this, "get", array($configFile), 'config', true);
        }
        else
        {
            $cached = $cache->cached($this, "get", array($configFile), 'config');
            $fileChanged = filemtime($configFile);
            if ($fileChanged && $fileChanged > $cached['time'])
            {
                $cached = $cache->cached($this, "get", array($configFile), 'config', true);
            }
        }

        return $cached['data'];
    }

    /**
     * 从yml中解析出数组用于操作
     *
     * @access public
     * @param string $configFile the yml config file url
     * @return array $result
     */
    public function get($configFile)
    {
        $array = TMYaml::load($configFile);
        $time = time();
        return array('data'=>$array, 'time'=>$time);
    }
}