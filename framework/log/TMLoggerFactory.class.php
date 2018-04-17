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
 *       Advertising Platform R&amp;D Team, Advertising Platform &amp; Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */

/**
 * TMLoggerFactory
 *
 * @package sdk.src.framework.log
 */
class TMLoggerFactory {

    /**
     *
     * 根据配置文件获取日志记录类
     * @param string $path
     * @param boolean $splitRequest
     * @param boolean $multiple
     *
     * @return TMLogInterface
     */
    public static function getLoggerByConfigFile($path, $splitRequest, $multiple) {
        $loggerType = TMConfig::get("logger", "type");
        if(empty($loggerType) || $loggerType == "local")
        {
            $logger = new TMLocalLogger($path, $multiple, $splitRequest);
        }else{
            $reflectionClass = new ReflectionClass("TM".ucfirst($loggerType)."Logger");
            $logger = $reflectionClass->newInstanceArgs(array($path, $splitRequest));
        }

        return $logger;
    }
}