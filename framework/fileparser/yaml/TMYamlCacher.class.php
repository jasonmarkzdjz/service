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
 * Check the yaml config file and set the content into cache file
 *
 * @package sdk.src.framework.fileparser.yaml
 */
class TMYamlCacher
{
    /**
     * This class is singleton.
     */
    private static $instance;

    /**
     *
     * 是否需要检查配置文件状态用于更新
     * @var boolean
     */
    private static $needCheckCreate = TRUE;


    public static function setNeedCheckCreate($needCheckCreate)
    {
        self::$needCheckCreate = $needCheckCreate;
    }

    /**
     * get a new static TMYamlCacher instance
     *
     * @return TMYamlCacher                  An TMYamlCacher instance
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
     * yaml save path
     * @var string
     */
    private $savePath = 'yaml/';
    /**
     * yaml content
     * @var string
     */
    private $content;
    /**
     * callback function name
     * @var string
     */
    private $callback;

    /**
     * Check the yaml config file and set the content into cache file
     *
     * @param  string $yamlFile The yaml file name and relative path
     * @param  boolean $resetCache reset the cache
     * @return array $content The yaml file content
     */
    public function execute($yamlFile, $resetCache = false)
    {
        $cacheFileName = $this->pathToName(str_replace(ROOT_PATH, '', $yamlFile));
        $cacheFile = CACHE_PATH.$this->savePath.$cacheFileName;

        if ($resetCache || self::$needCheckCreate)
        {
            $path = $this->savePath;
            $name = $cacheFileName;

            if(!is_file($yamlFile)){
                $this->content = array();
            }else if($this->isChange($yamlFile, $cacheFile)){
                $this->content = TMYaml::load($yamlFile);

                if ($this->callback)
                {
                    $this->content = $this->callback->execute($this->content);
                }

                TMFileCache::getInstance()->execute($path, $name, "<?php\nreturn ".var_export($this->content, true).';');
            }else{
                $this->content = include $cacheFile;
            }
        }
        else
        {
            $this->content = include $cacheFile;
        }

        return $this->content;
    }

    /**
     * set callback function
     *
     * @param string $instance The callback function name
     */
    public function setCallback($instance)
    {
        $this->callback = $instance;
    }

    /**
     * set yaml file save path
     * @param string $savePath the save path
     */
    public function setSavePath($savePath)
    {
        if (!empty($savePath))
        {
            $this->savePath = $savePath;
        }
    }

    /**
     * append yaml file save path
     * @param string $appendedPath the appended path
     *
     */
    public function appendSavePath($appendedPath){
        if (!empty($appendedPath))
        {
            $this->savePath.=$appendedPath;
        }
    }
    /**
     * get yaml cache file name from the yaml file name and absolute path
     *
     * @param  string $path The yaml file name and absolute path
     * @return string $filename    The yaml cache file name
     */
    private function pathToName($path)
    {
        return str_replace('/', '_', $path).'.php';
    }

    /**
     * Check the yaml config file is changed or not
     *
     * @param  string $yamlFile The yaml file name and relative path
     * @param  string $cacheFile The yaml cache file name and relative path
     * @return bool true or false
     */
    private function isChange($yamlFile, $cacheFile)
    {
        if (!file_exists($cacheFile) || filemtime($yamlFile) > filemtime($cacheFile))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}