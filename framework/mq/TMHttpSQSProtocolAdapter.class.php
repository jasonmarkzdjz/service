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
 * TMHttpSQSProtocolAdapter
 * httpsqs协议适配器
 *
 * @package sdk.src.framework.mq
 */
class TMHttpSQSProtocolAdapter implements TMMQProtocol {

    /**
     *
     * 插入队列成功表示常量
     * @var boolean
     */
    const HTTPSQS_PUT_OK = TRUE;

    /**
     *
     * 插入队列错误表示常量
     * @var string
     */
    const HTTPSQS_PUT_ERROR = "HTTPSQS_PUT_ERROR";

    /**
     *
     * 插入队列队列满表示常量
     * @var string
     */
    const HTTPSQS_PUT_END = "HTTPSQS_PUT_END";

    /**
     *
     * 读取数据无数据表示常量
     * @var boolean
     */
    const HTTPSQS_GET_END = "HTTPSQS_GET_END";

    /**
     *
     * 消息队列重置错误
     * @var boolean
     */
    const HTTPSQS_RESET_ERROR = FALSE;

    /**
     *
     * 消息队列修改队列长度成功表示常量
     * @var boolean
     */
    const HTTPSQS_MAXQUEUE_OK = TRUE;

    /**
     *
     * 适配器调用实体对象
     * @var mixed
     */
    protected $adaptee;

    /**
     *
     * 构造函数
     * @param $host 消息队列ip
     * @param $port 消息队列端口
     * @param $auth 消息队列认证码
     */
    public function __construct($host='127.0.0.1', $port=1218, $auth='')
    {
        $this->adaptee = new HttpSQS($host, $port, $auth);
    }

    /**
     *
     * 消息入队
     * @param string $messageKey 队列名
     * @param string $message 消息
     */
    public function put($messageKey, $message) {

        $result = $this->adaptee->put($messageKey, $message);
        return $result;
    }

    /**
     *
     * 读取消息
     * @param string $messageKey 队列名
     */
    public function get($messageKey)
    {
        $result = $this->adaptee->get($messageKey);

        return $result;
    }

    /**
     *
     * 清空队列
     * @param string $messageKey 队列名
     */
    public function clearQueue($messageKey)
    {
        $result = $this->adaptee->reset($messageKey);

        if($result == TMHttpSQSProtocolAdapter::HTTPSQS_RESET_ERROR)
        {
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /**
     *
     * 改变队列最大长度
     * @param int $messageKey 队列名
     * @param int $num 长度
     */
    public function changeQueueLength($messageKey, $num)
    {
        $result = $this->adaptee->maxqueue($messageKey, $num);

        if($result == TMHttpSQSProtocolAdapter::HTTPSQS_MAXQUEUE_OK)
        {
            return TRUE;
        }else{
            return FALSE;
        }
    }
}