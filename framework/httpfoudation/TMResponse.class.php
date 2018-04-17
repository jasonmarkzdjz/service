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
 * The class for response operation
 *
 * @package sdk.src.framework.httpfoudation
 */
abstract class TMResponse implements Serializable
{
    /**
     * 返回对象的可选参数容器
     *
     * @var array
     */
    protected $options    = array();

    /**
     * 返回对象的内容
     *
     * @var string
     */
    protected $content    = '';

    /**
     * Class constructor.
     *
     * @param array $options
     * @see TMResponse::initialize()
     */
    public function __construct($options = array())
    {
        $this->initialize($options);
    }

    /**
     * Initializes this TMResponse.
     *
     * @param  array $options  An array of options
     *
     * @return bool true, if initialization completes successfully, otherwise false
     *
     */
    public function initialize($options = array())
    {
        $this->options = $options;
    }

    /**
     * 得到设置的options数组
     *
     * @access public
     * @return array $options
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Sets the response content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Gets the current response content
     *
     * @return string Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Outputs the response content
     */
    public function sendContent()
    {
        echo $this->content;
    }

    /**
     * Sends the content.
     */
    public function send()
    {
        $this->sendContent();
    }


    /**
     * Serializes the current instance.
     *
     * @return array Objects instance
     */
    public function serialize()
    {
        return serialize($this->content);
    }

    /**
     * Unserializes a sfResponse instance.
     *
     * You need to inject a dispatcher after unserializing a sfResponse instance.
     *
     * @param string $serialized    A serialized TMResponse instance
     *
     */
    public function unserialize($serialized)
    {
        $this->content = unserialize($serialized);
    }
}