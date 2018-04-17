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
 * TMCrontabJsonParamsHandler
 * crontab处理逻辑基类
 *
 *
 */
abstract class TMCrontabJsonParamsHandler {
    /**
     *
     * @var string 日志文件名
     */
    protected  $name;

    /**
     * @var TMLog 日志对象
     */
    protected  $log;

    /**
     * 构造函数
     *
     * @param string $name 日志文件名
     */
    public function __construct($name)
    {
        $this->log = new TMLog(ROOT_PATH.'log/crontab/'.$name.'.log',true,false);
    }

    /**
     * 处理函数
     */
    abstract public function handle($params = null);

    /**
     * 记录日志
     * @param string $message
     */
    protected function log($message)
    {
        $this->log->lo($message);
    }
}