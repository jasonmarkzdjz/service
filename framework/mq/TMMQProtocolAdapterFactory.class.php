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
 * TMMQProtocolAdapterFactory
 * 消息队列协议适配器工厂
 *
 * @package sdk.src.framework.mq
 */
class TMMQProtocolAdapterFactory {

    /**
     *
     * 获取HttpSQS协议连接适配器
     * @param array $options 连接参数数组，包含host，port，auth
     * @throws TMConfigException
     */
    public static function getHttpSQSProtocolAdapter($options = array())
    {
        // 自定义参数不需要读取配置参数
        if (empty($options))
        {
            $mqConfig = TMConfig::get("mq", "httpsqs");

            if(empty($mqConfig))
            {
                throw new TMConfigException("mq's config not exist");
            }
        } else {
            $mqConfig = array();
        }

        $mqConfig = array_merge($mqConfig, $options);

        $host = isset($mqConfig["host"]) ? $mqConfig["host"] : '127.0.0.1';
        $port = isset($mqConfig["port"]) ? $mqConfig["port"] : 1218;
        $auth = isset($mqConfig["auth"]) ? $mqConfig["auth"] : '';

        return new TMHttpSQSProtocolAdapter($host, $port, $auth);
    }
}