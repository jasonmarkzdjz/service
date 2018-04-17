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
 * TMLog
 * 日志程序
 *
 * @package sdk.src.framework.log
 */
class TMLog implements TMLogInterface{

    /**
     *
     * TMLogInterface
     * @var TMLogInterface
     */
    protected $logger;

    /**
     *
     * 表示日志记录错误是否要抛出异常
     * @var boolean
     */
    protected static $needThrowException = false;

    /**
     *
     * 返回是否要抛出异常的状态
     * @return boolean
     */
    public static function getNeedThrowException()
    {
        return self::$needThrowException;
    }

    /**
     *
     * 设置日志记录错误是否要抛出异常
     * @param boolean $needThrowException
     */
    public static function setNeedThrowException($needThrowException)
    {
        self::$needThrowException = $needThrowException;
    }

    /**
     * 构造函数
     * @param string $path   日志地址
     * @param boolean $multiple 是否要多文件分割
     * @param mixed $splitRequest 不需要按照请求分离文件则传入FALSE
     * @return void
     */
    public function __construct($path = null, $multiple = true, $splitRequest = null) {

        $this->logger = TMLoggerFactory::getLoggerByConfigFile($path, $splitRequest, $multiple);
    }

    /**
     * Log High priority （高优先级日志）
     *
     * @param string $msg     the log message
     */
    public function lh($msg)
    {
        $this->logger->lh($msg);
    }

    /**
     * Log Middle priority （中优先级日志）
     *
     * @param string $msg     the log message
     */
    public function lm($msg)
    {
        $this->logger->lm($msg);
    }

    /**
     * Log Low priority （低优先级日志）
     *
     * @param string $msg     the log message
     */
    public function ll($msg)
    {
        $this->logger->ll($msg);
    }

    /**
     * 记录Alert级别的日志
     * @param string $msg
     */
    public function la($msg)
    {
        $this->logger->la($msg);
    }

    /**
     * 记录无关日志
     * @param string $msg
     */
    public function lo($msg)
    {
        $this->logger->lo($msg);
    }

    /**
     * 记录错误日志
     *
     * @param string $msg     the log message
     */
    public function error($msg)
    {
        $this->logger->lh($msg);
    }

    /**
     * 记录警告日志
     *
     * @param string $msg     the log message
     */
    public function warn($msg)
    {
        $this->logger->lm($msg);
    }

    /**
     * 记录普通日志
     *
     * @param string $msg     the log message
     */
    public function info($msg)
    {
        $this->logger->ll($msg);
    }

    /**
     * 记录严重错误级别日志
     * @param string $msg
     */
    public function fatal($msg)
    {
        $this->logger->la($msg);
    }

    /**
     * 记录调试日志
     * @param string $msg
     */
    public function debug($msg)
    {
        $this->logger->lo($msg);
    }
}
?>
