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
 * TMConfig
 * 项目配置类
 *
 * @package sdk.src.framework.config
 */
class TMConfig {

    protected static $needCheckCreateYML = TRUE;

	/**
	 * 表示环境的变量
	 *
	 * @var string
	 *
	 * @static
	 * @access protected
	 */
    protected static $env;


    /**
     *
     * 修改是否需要检查创建配置文件的属性
     * @param $needCheckCreate
     */
    public static function setNeedCheckCreateYML($needCheckCreateYML)
    {
        self::$needCheckCreateYML = $needCheckCreateYML;
    }

    /**
     * 获得实例化对象
     *
     * @param boolean $needInitLog 是否初始化日志
     */
    public static function initialize()
    {
        self::checkEnv();//处理加载config文件
        self::initConfigYaml();
    }



    public static function recreateConfigYaml()
    {
        $configArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/config.yml");
        $tamsId = isset($configArray["tams_id"]) ? $configArray["tams_id"] : "";
        if(empty($tamsId))
        {
            throw new TMConfigException("config.yml need tams_id config");
        }


        $env = self::$env;

        $configEtcArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/default.yml");//默认数据库其他服务配置
        self::set($configEtcArray);


        self::set($configArray);

        $renderConfigArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/render.yml");
        self::set($renderConfigArray);

        $configEnvArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/config_{$env}.yml");
        self::set($configEnvArray);

        $input = TMRegisterTree::getAll();
        $content = "<?php\nreturn ".var_export($input, true).';';

        $path = "config/";
        $name = "config_all.php";
        TMFileCache::getInstance()->execute($path, $name, $content);
    }

    /**
     * 初始化yaml配置文件
     */
    protected static function initConfigYaml()
    {
        $cacheFile = ROOT_PATH."cache/config/config_all.php";

        if(!self::$needCheckCreateYML){//判断是否创建xml缓存文件
            $configAllArray = include $cacheFile;
            self::set($configAllArray);

            return;
        }

        $configArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/config.yml");//将xml文件判断缓存是否进行换成
        $tamsId = isset($configArray["tams_id"]) ? $configArray["tams_id"] : "";//活动号
        if(empty($tamsId))
        {
            throw new TMConfigException("config.yml need tams_id config");
        }
        $env = self::$env;

        $existsCacheFile = is_file($cacheFile);//判断缓存文件是否是正常文件

        if($existsCacheFile)
        {
            $cacheFileMtime = filemtime($cacheFile);
        }
            //缓存文件不存在将重新对yml文件重新进行缓存
        if(!$existsCacheFile
            || filemtime(ROOT_PATH."config/default.yml") > $cacheFileMtime
            || filemtime(ROOT_PATH."config/config.yml") > $cacheFileMtime
            || filemtime(ROOT_PATH."config/render.yml") > $cacheFileMtime
            || (is_file(ROOT_PATH."config/config_{$env}.yml") && filemtime(ROOT_PATH."config/config_{$env}.yml") > $cacheFileMtime))
        {
            $configEtcArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/default.yml");
            self::set($configEtcArray);


            self::set($configArray);

            $renderConfigArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/render.yml");
            self::set($renderConfigArray);

            $configEnvArray = TMBasicConfigHandle::getInstance()->execute(ROOT_PATH."config/config_{$env}.yml");
            self::set($configEnvArray);

            $input = TMRegisterTree::getAll();
            $content = "<?php\nreturn ".var_export($input, true).';';

            $path = "config/";
            $name = "config_all.php";
            TMFileCache::getInstance()->execute($path, $name, $content);
        }else{
            $configAllArray = include $cacheFile;
            self::set($configAllArray);
        }
    }

    /**
     * 设置配置数组
     * @param array $configArray 配置数组
     */
    public static function set($configArray)
    {
        if(!empty($configArray)){
            $allConfigArray = TMRegisterTree::getAll();
//            $allConfigArray = array_merge($allConfigArray, $configArray);

            $allConfigArray = TMUtil::arrayMergeRecursiveSimple($allConfigArray, $configArray);
            TMRegisterTree::setAll($allConfigArray);
        }
    }

    /**
     * 获取对应key的配置
     * @return mixed
     */
    public static function get()
    {
        $paraArray = func_get_args ();

        return call_user_func_array(array("TMRegisterTree", "get"), $paraArray);
    }

    /**
     * 检查代码所处的环境
     */
    protected static function checkEnv()
    {
        self::$env = TMEnvConfig::getEnv();
    }



    /**
     * 实现的魔术调用方法
     * @param string $method
     * @param array $arguments
     * @return mixed $result
     */
    public function __call($method, $arguments = array())
    {
        $searchKeyArray[] = $method;

        $searchKeyArray = array_merge($searchKeyArray, $arguments);

        $tmp = self::$configArray;
        foreach($searchKeyArray as $searchKey)
        {
            if(!isset($tmp[$searchKey]))
            {
                return null;
            }
            $tmp = $tmp[$searchKey];
        }

        if(!isset($tmp))
        {
            return null;
        }

        return $tmp;
    }
}
