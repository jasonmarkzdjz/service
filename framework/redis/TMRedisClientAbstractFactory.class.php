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
 * 生成Redis访问客户端抽象工厂类
 *
 * @abstract
 * @package sdk.src.framework.redis
 */
abstract class TMRedisClientAbstractFactory {

    /**
     *
     * 获取连接
     * @abstract
     * @param string $name 连接的别名
     */
    abstract public static function getClient($name = "phpredis");

    /**
     * 根据各个参数获取一个redis客户端
     *
     * @abstract
     * @param string $host ip
     * @param int $port 端口
     * @param int $timeout 超时
     * @param string $auth 校验密码
     * @param string $name 连接的别名
     */
    abstract public static function getClientForDetails($host, $port, $timeout = 0, $auth = "", $name = "");

    /**
     * 获取一个不连接的redis客户端
     *
     * @abstract
     * @param string $name 连接的别名
     * @throws TMConfigException
     */
    abstract public static function getNotConnectedClient($name = "phpredis");
}