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
 * TMMQMessage
 * 消息队列消息对象
 *
 * @package sdk.src.framework.mq
 */
class TMMQMessage implements Serializable{

    //属性
    /**
     *
     * 消息创建时间
     * @var timestamp
     */
    protected $createTime;

    /**
     *
     * 过期时间
     * @var int
     */
    protected $timeout;

    /**
     *
     * 用户IP
     * @var string
     */
    protected $userIp;

    /**
     *
     * 服务端IP
     * @var string
     */
    protected $serverIp;

    /**
     *
     * 业务名字
     * @var string
     */
    protected $busiName;

    /**
     *
     * 消息内容
     * @var string
     */
    protected $message;

    /**
     *
     * 构造函数
     * @param string $message 消息内容
     * @param int $timeout 过期时间
     */
    public function __construct($message, $timeout = 0)
    {
        $this->createTime = time();
        $this->timeout = $timeout;
        $this->userIp = TMUtil::getClientIp();
        $this->serverIp = TMConfig::get('machine_ip');//$_SERVER['MACHINE_IP'];

        if(TMUtil::isPHPRunInWeb()){
            $busiName = "web_cgi";//$request->getPathInfo();
        }else{
            $busiName = "crontab";
        }
        $this->busiName = $busiName;


        if(is_array($message))
        {
            $message = json_encode($message);
        }

        $this->setStringMessage($message);
    }

    /**
     * (non-PHPdoc)
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        return json_encode(
            array(
                "createTime" => $this->createTime,
                "timeout" => $this->timeout,
                "userIp" => $this->userIp,
                "serverIp" => $this->serverIp,
                "busiName" => $this->busiName,
                "message" => $this->message
            )
        );
    }

    /**
     * (non-PHPdoc)
     * @see Serializable::unserialize()
     */
    public function unserialize($data)
    {
        $result = json_decode($data, true);

        $this->createTime = $result["createTime"];
        $this->timeout = $result["timeout"];
        $this->userIp = $result["userIp"];
        $this->serverIp = $result["serverIp"];
        $this->busiName = $result["busiName"];
        $this->message = $result["message"];
    }

    /**
     *
     * 设置消息内容
     * @param string $message 消息内容
     */
    public function setStringMessage($message)
    {
        $this->message = $message;
    }

    /**
     *
     * 获取消息内容
     */
    public function getStringMessage()
    {
        return $this->message;
    }

    /**
     *
     * 设置过期时间
     * @param int $timeout 过期时间
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 判断该消息体是否已经过期
     * @return boolean
     */
    public function isTimeout()
    {
        //timeout为0，永不过期
        if($this->timeout == 0)
        {
            return false;
        }
        $createTime = $this->createTime;

        $currTime = time();

        if($currTime > ($createTime + $this->timeout))
        {
            return true;
        }else{
            return false;
        }
    }
}