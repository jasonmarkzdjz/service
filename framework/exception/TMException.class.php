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
 * The general Exception class of Tecent AD Platform
 * @package sdk.src.framework.exception
 */
class TMException extends Exception
{
	/**
	 * 句柄
	 * @var TMLogInterface
	 * @access protected
	 */
    protected static $loggerInstances = array();

    /**
     * 是否需要展示信息
     * @var boolean
     * @access protected
     */
    protected static $needShowRealMsg = false;

    /**
     * 是否需要自动跳转
     * @var boolean
     * @access protected
     */
    protected static $needAutoRedirect = false;

    /**
     * 获取$needShowRealMsg的值
     *
     * @return void
     */
    public static function getNeedShowRealMsg()
    {
        return self::$needShowRealMsg;
    }

    /**
     * 设置$needShowRealMsg的值
     *
     * @param boolean $needShowRealMsg 是否要显示真实信息
     * @return void
     */
    public static function setNeedShowrealMsg($needShowRealMsg)
    {
        self::$needShowRealMsg = $needShowRealMsg;
    }

    /**
     * 获取$needAutoRedirect的值
     *
     * @return void
     */
    public static function getNeedAutoRedirect()
    {
        return self::$needAutoRedirect;
    }

    /**
     * 设置$needAutoRedirect的值
     *
     * @param boolean $needAutoRedirect 是否要自动跳转
     * @return void
     */
    public static function setNeedAutoRedirect($needAutoRedirect)
    {
        self::$needAutoRedirect = $needAutoRedirect;
    }

    /**
     * 增加一个监测日志类
     *
     * @param TMLogInterface $loggerInstance
     * @return void
     */
    public static function addLogger(TMLogInterface $loggerInstance)
    {
        self::$loggerInstances[] = $loggerInstance;
    }

    /**
     * 构造函数
     *
     * @param string $message
     * @param int $code
     * @return void
     */
    public function __construct($message='', $code=TMExceptionConstants::EXCEPTION_DEFAULT_CODE)
    {
        parent::__construct ($message, $code);
    }

    /**
     * 将异常记录到日志中
     *
     * @param string $message
     * @return void
     */
    protected function logException($message)
    {
        foreach(self::$loggerInstances as $loggerInstance)
        {
            $loggerInstance->lh($message);
        }
    }

    /**
     * 异常处理，输出显示给用户
     *
     * @param string $tpl
     * @param string $message
     * @return string
     */
    public function handle($tpl='', $message='')
    {
        return $this->output($tpl, $message);
    }

    /**
     * 输出异常页面
     *
     * @param string $tpl 模板文件在templates中的相对路径
     * @param string $message 显示给用户的异常信息
     * @return string
     */
    public function output($tpl='', $message='')
    {
        if (empty($message))
        {
            $message = $this->getMessage();
        }

        if(class_exists("TMExceptionView"))
        {
            $view = new TMExceptionView();
            $data = array(
            "autoRidrect"   => self::$needAutoRedirect,
            "errorMsg"  => $message,
            "code" => $this->getCode()
            );
            return $view->renderException($data, $tpl);
        }else{
            return $message;
        }

    }
}