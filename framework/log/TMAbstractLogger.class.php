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
 * TMAbstractLogger
 * 抽象日志类
 *
 * @package src.framework.log
 */
class TMAbstractLogger {

    /**
     * 判断是否在命令行下运行
     */
    protected static $isRunInCli = false;


    public static function setIsRunInCli($isRunInCli)
    {
        self::$isRunInCli = $isRunInCli;
    }
}