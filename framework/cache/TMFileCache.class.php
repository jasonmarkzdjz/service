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
 * 文件缓存类，通常用于yml文件的缓存（TMYamlCacher）
 *
 * @package sdk.src.framework.cache
 */
class TMFileCache
{
    /**
     * 缓存单例对象变量
     * @var TMFileCache
     */
    private static $instance;

    /**
     * 获取文件缓存单例对象
     * @access public
     * @return TMFileCache
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
     * 根据文件路径、文件名、文件内容创建文件
     * @access public
     * @return void
     */
    public function commit()
    {
        if (!is_dir(CACHE_PATH.$this->path))
        {
            $oldumask = umask(0);
            mkdir(CACHE_PATH.$this->path, 0777, true);
            umask($oldumask);
        }
        file_put_contents(CACHE_PATH.$this->path.$this->name, $this->content);
        chmod(CACHE_PATH.$this->path.$this->name, 0777);
    }

    /**
     * 设置文件路径、文件名、文件内容
     *
     * @access public
     * @param string $filePath  文件路径
     * @param string $fileName      文件名字
     * @param string $fileContent   文件内容
     * @return void
     */
    public function execute($filePath, $fileName, $fileContent)
    {
        $this->setPath($filePath);
        $this->setName($fileName);
        $this->setContent($fileContent);

        $this->commit();
    }

    /**
     * 设置文件路径
     *
     * @access protected
     * @param string $path 文件路径
     * @return void
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * 设置文件名字
     *
     * @access protected
     * @param string $name 文件名字
     * @return void
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 设置文件内容
     *
     * @access protected
     * @param string $content 文件内容
     * @return void
     */
    protected function setContent($content)
    {
        $this->content = $content;
    }
}
