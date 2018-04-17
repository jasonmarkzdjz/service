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
 * 自动加载类
 * Used to load all the class automatically
 *
 * @package sdk.src.framework.core
 *
 * Usage:
 *
 *   1. Scan class file and cache
 *   TMAutoload::getInstance()
 *       ->setDirs(array(ROOT_PATH))         // dirs needed to scan
 *       ->setSavePath(CACHE_PATH.'autoload/')         // cache file's full path
 *       ->setSaveName('autoloader.cache.php')         // cache file's name
 *       ->execute();
 *
 *   2. Rescan dirs forcly
 *   TMAutoload::getInstance()->execute(true);
 *
 * CHANGELOG
 *  - 2011/06/09, by simonkuang
 *    * 变更缓存文件的写入方式为fopen，不使用file_put_contents，便于在扫描动作前后加锁
 *    * 在function execute的开始加入文件锁、结束时解锁。避免多个并发进程在同一时间段内同时扫描
 *    * 变更ignore列表的内容，从只支持文件名或目录名到支持指定多级目录结构。判断更精确
 *    * 支持自动忽略以“.”和“_”打头的文件及目录；自动忽略cache目录；自动忽略web目录
 *    * 修正opendir方法中没有closedir的问题，防止扫描目录数量庞大时php内存泄露带来风险
 **/
class TMAutoload
{
    /**
     *
     * @var array 包含文件路径
     */
    private    $includeFile;

    /**
     *
     * @var string 缓存文件路径
     */
    private    $cacheFile;

    /**
     *
     * @var string 保存的缓存文件路径
     */
    private    $savePath;

    /**
     *
     * @var string 保存的缓存文件名称
     */
    private    $saveName;

    /**
     *
     * @var resource 文件锁指针，防止并发生成cache
     */
    private    $fpCache;

    /**
     *
     * @var array 排除文件
     */
    private    $ignore;

    /**
     *
     * @var array 扫描目录
     */
    private    $dirs;

    /**
     *
     * @var array 解析php后缀的目录
     */
    protected $phpParsedDirs = array();

    /**
     *
     * @var array 自动加载路径
     */
    public $autoloadPaths;

    /**
     * 单例实例
     *
     *@var TMAutoload
     *@access private
     */
    private static $instance;

    /**
     * @var string PHP namespace separator
     */
    protected static $namespaceSeparator = '\\';


    /**
     * 构造函数
     *
     * @param string $scanDirs
     * @param string $savePath
     * @param string $saveName
     */
    protected function __construct($scanDirs = null, $savePath = null, $saveName = null)
    {
        spl_autoload_register(array('TMAutoload', '__autoload'));

        $this->initialize($scanDirs, $savePath, $saveName);
    }

    /**
     * Get a instance of TMAutoload
     * @access public
     * @param bool $newInstance   create a new instance or not
     * @return TMAutoload
     */
    public static function getInstance($newInstance = false)
    {
        if($newInstance || self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 进行初始化
     *
     * @param string $scanDirs
     * @param string $savePath
     * @param string $saveName
     */
    private function initialize($scanDirs = null, $savePath = null, $saveName = null)
    {
        $this->autoloadPaths = array();
        $this->includeFile = array();
        $this->savePath = $savePath ? $savePath : (empty($this->savePath) ? '/tmp/' : $this->savePath);
        $this->saveName = $saveName ? $saveName : (empty($this->saveName) ? 'autoloader.php' : $this->saveName);
        $this->ignore = isset($this->ignore) ? $this->ignore : array(ROOT_PATH."template", ROOT_PATH."web", ROOT_PATH."cache");
        $this->dirs = $scanDirs && is_array($scanDirs) ? $scanDirs : (empty($this->dirs) ? array() : $this->dirs);
        $this->addPhpParsedDir(ROOT_PATH."library/doctrine");
    }

    /**
     * 增加解析php后缀的目录
     * @param $dir
     */
    public function addPhpParsedDir($dir)
    {
        $this->phpParsedDirs[] = $dir;
    }

    /**
     * 设置忽略文件夹
     * @param array $ignoreArray
     * @return TMAutoload
     */
    public function setIgnore($ignoreArray)
    {
        $this->ignore = array_merge($this->ignore, $ignoreArray);

        return self::$instance;
    }

    /**
     * Customized __autoload function:Autoload the class which registered in autoload.php
     * @access public
     * @param string $className    the class name
     * @return void|string    the error information
     */
    public static function __autoload($className)
    {
        //进行是否是php5.3的类加载模式
        if(strpos($className, self::$namespaceSeparator) !== FALSE)
        {
            return;
        }

        $filePath = self::getInstance()->getClassPath($className);
        if($filePath)
        {
            require $filePath;
        }
        else if($className != "TMAdidFilter")
        {
            self::getInstance()->initialize();
            self::getInstance()->execute(true);
            $filePath = self::getInstance()->getClassPath($className);
            if($filePath)
            {
                require $filePath;
            }
            else
            {
                //echo 'Cant find class: '.$className;
            }
        }
    }

    /**
     * add dir to scan dirs dynamically
     * @access public
     * @param string $dir    the dir needs scan
     * @return void
     */
    public function addDir($dir) {
        array_push($this->dirs, $dir);
        return self::$instance;
    }

    /**
     * 增加多个扫描目录
     *
     * @param array $scanDirs 扫描目录路径或数组
     * @return TMAutoload
     */
    public function addDirs($scanDirs) {
        $scanDirs = is_array($scanDirs) ? $scanDirs : array($scanDirs);
        $this->dirs = array_merge($this->dirs,$scanDirs);
        return self::$instance;
    }

    /**
     * create autoload.php and add customized __autoload function to spl__autoload stack
     * @access public
     * @param bool $reload Reload the autoload file if this parameter is true
     * @return void
     */
    public function execute($reload = false)
    {
        $cacheFile = $this->savePath . $this->saveName;

        $needMakeCache = false;

        if ($reload)
        {
            $needMakeCache = true;
        }else{
            $canUserCurrentCache = is_file($cacheFile) && is_readable($cacheFile);
            if(!$canUserCurrentCache) {
                $needMakeCache = true;
            }
        }

        if ($needMakeCache)
        {
            // create directory
            $cacheDir = $this->savePath;
            if(!is_dir($cacheDir))
            {
                $oldumask = umask(0);
                @mkdir($cacheDir, 0777, true);
                umask($oldumask);
            }
            else if (!is_writeable($cacheDir))
            {
                @chmod($cacheDir, 0777);
            }

            $generateCacheFile = $cacheFile."_generating";
            // add file lock
            $this->fpCache = fopen($generateCacheFile, 'w+b');
            if(!$this->fpCache)    // cannot open cache file
            {
                if(!$canUserCurrentCache){
                    // show busy and exit. no exception
                    header("HTTP/1.1 503 Service Unavailable");
                    echo "Service Unavailable...\n";
                    exit();
                }else{
                    return self::$instance;
                }
            }
            if(!flock($this->fpCache,LOCK_EX|LOCK_NB))    // maybe another process is trying to create autoloader cache
            {
                if(!$canUserCurrentCache){
                    fclose($this->fpCache);

                    // show busy and exit. no exception
                    header("HTTP/1.1 503 Service Unavailable");
                    echo "Service Unavailable...\n";
                    exit();
                }else{
                    return self::$instance;
                }
            }


            foreach ($this->dirs as $dir)
            {
                $this->opendir($dir);
            }

            $this->commit();

            // remove file lock
            flock($this->fpCache,LOCK_UN);
            fclose($this->fpCache);
            rename($generateCacheFile, $cacheFile);
            @chmod($cacheFile, 0777);

            $this->autoloadPaths = include($this->savePath . $this->saveName);
        }

        return self::$instance;
    }

    /**
     * Used to get the path for a unknow class
     * @access public
     * @param string $className the class name
     * @return string|false
     */
    public function getClassPath($className)
    {
        if (!$this->autoloadPaths)
        {
            if (!is_readable($this->savePath . $this->saveName))
            {
                $this->execute();
            }
            $this->autoloadPaths = include($this->savePath . $this->saveName);
        }

        if (isset($this->autoloadPaths[$className]))
        {
            return $this->autoloadPaths[$className];
        }
        else
        {
            return false;
        }
    }

    /**
     * Used to save the autoload file:autoload.php
     * @access private
     * @return void
     */
    private function commit()
    {
        if(empty($this->fpCache))
        return null;

        $files = array();
        foreach ($this->includeFile as $key => $file)
        {
            if (preg_match('/\.class\.php$/', $key)) {
                $className = preg_replace('/\.class\.php/', '', $key);
                $files[$className] = is_array($file) ? $file[0] : $file;
            }else if(preg_match('/\.php$/', $key)){
                $className = preg_replace('/\.php/', '', $key);
                $files[$className] = is_array($file) ? $file[0] : $file;
            }
        }

        $content = "<?php\n\nreturn ".var_export($files, true).";\n";

        return fwrite($this->fpCache, $content);
    }

    /**
     * Open dir and save to property
     * @access private
     * @param string $dir the dir needs scan
     * @param $parsePhp can parse .php file
     * @return void
     */
    private function opendir($dir, $parsePhp = false)
    {
        $dir = rtrim($dir,'/ ').'/';

        if($parsePhp === false && in_array(rtrim($dir, '/'), $this->phpParsedDirs))
        {
            $parsePhp = true;
        }

        $handle = opendir($dir);
        while (false !== ($file = readdir($handle)))
        {
            $firstChar = substr($file,0,1);
            if('.'==$firstChar || '_'==$firstChar) continue;

            $theFile = $dir . $file;
            if(!$this->isIgnorePath($theFile))//if (!in_array($file, $this->ignore))
            {
                if (is_file($theFile))
                {
                    if (isset($this->includeFile[$file])) {//处理不同文件夹下具有相同文件名的类文件 存成数组
                        // the third one
                        // do nothing here
                    }
                    elseif (preg_match('/\.class\.php$/', $file))    // do not store non-class file, save memory usage
                    {
                        $this->includeFile[$file] = $theFile;
                    }
                    elseif ($parsePhp && preg_match('/\.php$/', $file))
                    {
                        $this->includeFile[$file] = $theFile;
                    }
                }
                else
                {
                    $this->opendir($theFile.'/', $parsePhp);
                }
            }
        }
        closedir($handle);
    }

    /**
     * 设置扫描目录
     *
     * @param array $scanDirs 扫描目录地址或数组
     * @return TMAutoload
     */
    public function setDirs($scanDirs) {
        $this->dirs = is_array($scanDirs) ? $scanDirs : array($scanDirs);
        return self::$instance;
    }

    /**
     * 设置文件保存路径
     *
     * @param string $savePath
     * @return TMAutoload
     */
    public function setSavePath($savePath) {
        $this->savePath = rtrim($savePath,'/ ').'/';
        return self::$instance;
    }

    /**
     * 设置保存缓存文件名
     *
     * @param string $saveName
     * @return TMAutoload
     */
    public function setSaveName($saveName) {
        $this->saveName = $saveName;
        return self::$instance;
    }

    /**
     * 根据ignore列表，决定指定的路径是否已经略过
     *
     * @param string $path
     * @return boolean
     */
    public function isIgnorePath($path) {
        if(in_array($path, $this->ignore))
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 设置命名空间分隔符
     *
     * @param string $namespaceSeparator
     */
    public static function setNamespaceSeparator($namespaceSeparator)
    {
        self::$namespaceSeparator = $namespaceSeparator;
    }

    /**
     * 获取命名空间分隔符
     * @return string
     */
    public static function getNamespaceSeparator()
    {
        return self::$namespaceSeparator;
    }

}
