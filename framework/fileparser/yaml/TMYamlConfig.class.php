<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2013 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&amp;D Team, Advertising Platform &amp; Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */
/**
 * TMYamlConfig
 * yml配置文件读取类。在做filter或component时基本上有相应的配置文件，这些配置也经常会有层级关系，读取起来不方便。
 * 
 * 使用方法：
 * <code>
 * $config_obj = new TMYamlConfig(ROOT_PATH . 'config/filter/xhprof.yml');
 * var_dump($config_obj->getConfig('date', 'start'));
 * </code>
 *
 * @package sdk.src.framework.fileparser.yaml
 */
class TMYamlConfig {
    
    /**
     * 配置文件数组
     *
     * @var array
     */
    protected $_config = array();
    
    /**
     * 构造函数
     *
     * @param string $config_file
     *            配置文件
     * @throws TMException 配置文件不存在会抛出异常
    */
    public function __construct($config_file) {
        if (!file_exists($config_file)) {
            throw new TMException("The config file [{$config_file}] not exist");
        }
        $this->_config = TMBasicConfigHandle::getInstance()->execute($config_file);
    }
    
    /**
     * 获取配置文件
     *
     * @return mixed
     */
    public function getConfig() {
        return $this->_getConfig(func_get_args(), $this->_config);
    }
    
    /**
     * 获取配置参数
     * 把第一个参数取出，如果参数列表不为空则递归获取，否则说明已经找到最后一个参数
     *
     * @param array $args
     *            参数列表
     * @param array $configArray
     *            配置信息数组
     * @return mixed 返回配置内容，不存在则返回null
     */
    private function _getConfig($args, $configArray) {
        if (!empty($args)) {
            $arg = array_shift($args);
            if (array_key_exists($arg, $configArray)) {
                return empty($args) ? $configArray[$arg] : $this->_getConfig($args, $configArray[$arg]);
            }
        }
        return null;
    }
    
}