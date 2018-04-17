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
 * TMMQClient
 * 访问消息队列连接客户端
 *
 * @package sdk.src.framework.mq
 */
class TMMQClient {

    /**
     * 消息队列协议接口实体
     * @var TMMQProtocol
     */
    protected $mqProtocol;

    /**
     * 消息队列连接池数组
     * @var TMMQClient
     */
    private static $instances = array();

    /**
     *
     * 消息队列入队网管上报属性
     * @var int
     */
    const TNM_ATTR_PUT_MQ = 22866;

    /**
     *
     * 消息队列出队网管上报属性
     * @var int
     */
    const TNM_ATTR_GET_MQ = 22867;

    /**
     *
     * 消息队列入队失败网管上报属性
     * @var int
     */
    const TNM_ATTR_PUT_MQ_FAILED = 22868;

    /**
     *
     * 构造函数
     */
    protected function __construct()
    {

    }

    /**
     *
     * 单例获取函数
     * @param array $options 连接参数，可以包括name，host，port，auth
     */
    public static function getInstance($options = array())
    {
        $instanceName = "default";
        if(isset($options["name"]))
        {
            $instanceName = $options["name"];
        }

        if(!isset(self::$instances[$instanceName]))
        {
            $class = __CLASS__;

            $instance = new $class();

            $protocol = TMMQProtocolAdapterFactory::getHttpSQSProtocolAdapter($options);

            $instance->setProtocol($protocol);

            self::$instances[$instanceName] = $instance;
        }
        return self::$instances[$instanceName];
    }

    /**
     *
     * 设置连接消息队列的协议接口
     * @param TMMQProtocol $protocol 连接消息队列的协议接口
     */
    public function setProtocol($protocol) {
        $this->mqProtocol = $protocol;
    }

    /**
     *
     * 获取消息队列的队列名
     * @param string $messageKey 消息队列名
     * @param boolean $need_prefix 是否要拼接上前缀
     */
    public function getMQKey($messageKey, $need_prefix) {
        return ($need_prefix ? TMConfig::get('tams_id') . '_' : '') . $messageKey;
    }

    /**
     *
     * 发布消息
     * @param TMMQMessage $messasge 消息
     * @param string $messageKey 队列名
     * @param boolean $need_prefix 是否需要活动id前缀
     */
    public function publishMessage($messasge, $messageKey = "default", $need_prefix = false)
    {
        $messageStr = serialize($messasge);
        $result = $this->mqProtocol->put($this->getMQKey($messageKey, $need_prefix), $messageStr);
        return $result;
    }

    /**
     * 获取一条消息
     * @param string $messageKey 队列名
     * @param boolean $need_prefix 是否需要活动id前缀
     */
    public function getMessage($messageKey = "default", $need_prefix = false)
    {
        $result = $this->mqProtocol->get($this->getMQKey($messageKey, $need_prefix));
        if($result === FALSE)
        {
            $log = new TMLog();
            $log->la("Get MQ Failed");

            return null;
        }
        else if($result == TMHttpSQSProtocolAdapter::HTTPSQS_GET_END)
        {
            return null;
        }
        else{
            return unserialize($result);
        }
    }

    /**
     * 重置队列
     * @param string $messageKey 队列名
     * @param boolean $need_prefix 是否需要活动id前缀
     */
    public function clearQueue($messageKey = "default", $need_prefix = true)
    {
        $result = $this->mqProtocol->clearQueue($this->getMQKey($messageKey, $need_prefix));

        return $result;
    }

    /**
     * 修改队列长度
     * @param $num 改变的长度
     * @param $messageKey 队列名
     * @param boolean $need_prefix 是否需要活动id前缀
     */
    public function changeQueueLength($num, $messageKey = "default", $need_prefix = true)
    {
        $result = $this->mqProtocol->changeQueueLength($this->getMQKey($messageKey, $need_prefix), $num);

        return $result;
    }
}