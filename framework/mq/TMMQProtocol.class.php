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
 * TMMQProtocol
 * 消息队列连接协议接口
 *
 * @package sdk.src.framework.mq
 */
Interface TMMQProtocol{

    /**
     * 放入一个消息到消息队列中
     * @param string $messageKey 队列名字
     * @param string $message 消息内容
     * @return boolean
     */
    public function put($messageKey, $message);

    /**
     * 获取一个消息队列中的消息
     * @param string $messageKey 队列名字
     * @return string/FALSE
     */
    public function get($messageKey);

    /**
     * 清除消息队列
     * @param string $messageKey 队列名字
     * @return boolean
     */
    public function clearQueue($messageKey);

    /**
     * 修改队列长度
     * @param string $messageKey 队列名字
     * @param int $num 队列的长度
     * @return boolean
     */
    public function changeQueueLength($messageKey, $num);
}