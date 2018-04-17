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
 * TMEnvConfig
 * 环境相关配置
 *
 * @package sdk.src.framework.config
 */
class TMEnvConfig {
	/**
	 * 测试环境
	 *
	 * @var string
	 */
    const ENV_TYPE_TEST = "test";

    /**
     * 正式环境
     *
     * @var string
     */
    const ENV_TYPE_PRODUCTION = "production";

    /**
     * 预发布环境
     *
     * @var string
     */
    const ENV_TYPE_BETA = "beta";

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
     * 环境类型数组
     *
     * @var array
     *
     * @static
     * @access protected
     */
    protected static $envTypes = array(
            self::ENV_TYPE_TEST => "test",
            self::ENV_TYPE_BETA => "beta",
            self::ENV_TYPE_PRODUCTION => "production"
        );

    /**
     * 获取环境类型
     *
     * @static
     * @access public
     * @param string 环境类型值
     * @return TMBasicConfigHandle $instance
     */
    public static function envType($mixed)
    {
        return self::$envTypes[$mixed];
    }


    /**
     * 初始化环境
     *
     * @static
     * @access public
     * @return void
     */
    public static function initEnv()
    {
        if(file_exists(ROOT_PATH . 'config/env.php')) {
            include ROOT_PATH . 'config/env.php';
        }

        if(! isset( $_ENV['SERVER_TYPE'] )) {
            if( isset($_SERVER['SERVER_TYPE'] ) ) {
                $_ENV['SERVER_TYPE'] = $_SERVER['SERVER_TYPE'];
            }
            else {
                $_ENV['SERVER_TYPE'] = self::envType(self::ENV_TYPE_PRODUCTION);
            }
        }

        self::$env = $_ENV['SERVER_TYPE'];
    }

    /**
     * 获取环境类型
     *
     * @static
     * @access public
     * @return void
     */
    public static function getEnv()
    {
        if(empty(self::$env))
        {
            self::initEnv();
        }

        return self::$env;
    }

    /**
     * 设置环境类型
     *
     * @static
     * @access public
     * @param string 环境类型
     * @return void
     */
    public static function setEnv($env)
    {
        self::$env = $env;
    }
}