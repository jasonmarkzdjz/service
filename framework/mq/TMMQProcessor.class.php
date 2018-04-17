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
 * TMMQProcessor
 * 消费者处理器基类
 *
 * @package sdk.src.framework.mq
 * @abstract
 */
abstract class  TMMQProcessor  {

	/**
	 * 日志记录类
	 * @var TMLog
	 */
	protected  $log;

	/**
	 *
	 * 构造函数
	 * @param string $name 队列名字
	 */
	public function __construct($name)
	{
		$this->log = new TMLog(ROOT_PATH.'log/mq/'.$name.'.processor.log',true,false);
	}

	/**
	 *
	 * 获取消息体进行处理
	 * @param TMMQMessage $message 消息体对象
	 * @abstract
	 */
	public abstract function process(TMMQMessage $message);

	/**
	 *
	 * 记录日志
	 * @param string $message 日志内容
	 */
	protected function log($message)
	{
	    $this->log->lo($message);
	}

	/**
	 * 记录debug日志
	 * @param string $message 日志内容
	 */
	protected function debug($message)
	{
	}
}