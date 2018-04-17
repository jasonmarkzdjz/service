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
 * TMConfigDispatcher
 * 用于将配置文件转化成静态php进行一些操作
 *
 * @package sdk.src.framework.config
 */
class TMConfigDispatcher
{
    protected static $needCheckCreate = TRUE;

    public static function setNeedCheckCreate($needCheckCreate)
    {
        self::$needCheckCreate = $needCheckCreate;
    }

    /**
     * 解析YML配置文件，生成相关配置，缓存到cache文件，并返回缓存文件的路径
     *
     * @param string $configType 配置类型，它的值是filter,available二者之一
     * @return string $configPath 配置的缓存文件路径
     */
    public function getConfigFile($configType)
    {
        $configPath = ROOT_PATH."cache/config/".$configType.".php";

        if(self::$needCheckCreate){
            $configFile = ROOT_PATH."config/".$configType.".yml";
            $class = 'TM'.ucfirst($configType)."ConfigHandle";

            if(!is_file($configFile))
            {
                return null;
            }

            if (!is_readable($configPath))
            {
                $content = $this->callHandle($class,$configFile);
                $this->writeConfigFile($configPath, $content);
            }
            else
            {
                $mtime = filemtime($configPath);
                $ymlTime = filemtime($configFile);
                if ($ymlTime > $mtime)
                {
                    $content = $this->callHandle($class,$configFile);
                    $this->writeConfigFile($configPath, $content);
                }
            }
        }

        return $configPath;
    }

    /**
     * 调用配置的处理函数，获取配置的内容
     *
     * @access protected
     * @param  string $class the handle class name
     * @param  string $configFile the config file path
     * @return string $content 配置内容
     */
    protected function callHandle($class, $configFile)
    {
        $handle = new $class();
        $content = $handle->execute($configFile);

        return $content;
    }

    /**
     * 将配置内容写入缓存文件
     *
     * @access protected
     * @param string $configPath 缓存文件的路径
     * @param string $content 配置内容
     * @return void
     */
    protected function writeConfigFile($configPath, $content)
    {
        $current_umask = umask(0000);
        if (!is_dir(dirname($configPath)))
        {
            if (false === mkdir(dirname($configPath), 0777, true))
            {
            }
        }
        umask($current_umask);

        if (!$fp = fopen($configPath, 'wb'))
        {
        }

        fwrite($fp, $content);
        fclose($fp);

        chmod($configPath, 0777);
    }
}