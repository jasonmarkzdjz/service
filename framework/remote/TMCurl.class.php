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
 * Encapsulate the curl function to open remote url infterface
 *
 * @package sdk.src.framework.remote
 */
class TMCurl
{
    /**
     * @var resource    curl instance
     *
     * @access private
     */
    private $channel;

    /**
     * @var string    the curl url address
     *
     * @access private
     */
    private $url;

    /**
     * @var array    set curl options array
     *
     * @access private
     */
    private $optionArray;

    /**
     *
     * 内网http代理类型常量
     * @var string
     */
    const HTTP_PROXY_TYPE_INTRANET = "intranet";

    /**
     *
     * 外网http代理类型常量
     * @var string
     */
    const HTTP_PROXY_TYPE_EXTRANET = "extranet";

    /**
     *
     * curl默认代理配置关键字映射表
     * @var array
     */
    private static $proxyConfigMap = array(
        "proxy" => CURLOPT_PROXY,
        "proxyuserpwd" => CURLOPT_PROXYUSERPWD
    );

    /**
     * construct
     *
     * @access public
     * @param  string $url     the curl url address
     * @return void
     */
    public function __construct($url = null)
    {
            $this->url = $url;
            $this->channel = curl_init($url);
            $this->optionArray = array();
    }

    /**
     * destruct
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        curl_close($this->channel);
    }

    /**
     * Set the options of CURL visit. About the option constant option, you could search "curl" from PHP manual
     *
     * @param array $arrayOption  The array of options, it is as array(CURL_HEADER=>false). CURL_HEADER is the CURL library constant,
     * you could find all constants definition in the PHP manual.
     * Example:
     * $option_array = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_COOKIE => 'uin='.$_COOKIE["uin"].';skey='.$_COOKIE["skey"].';zzpaneluin='.$_COOKIE["zzpaneluin"].';zzpanelkey='.$_COOKIE["zzpanelkey"],
            CURLOPT_POSTFIELDS => "hang_annex=1&albumlife[0][o]=http://fordfiesta.qzone.qq.com/data/mini_23973630_2009_03_01_15_22_14_1.jpg
            &albumlife[0][t]=http://fordfiesta.qzone.qq.com/data/mini_23973630_2009_03_01_15_22_14_1.jpg",
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_HTTPHEADER => array(
                'Accept-Language:zh-cn',
                'Connection:Keep-Alive',
                'Content-Type: application/x-www-form-urlencoded'
            )
        );
     */
    public function setOptions($arrayOption)
    {
        foreach($arrayOption as $key => $value){
        	$this->optionArray[$key] = $value;
        }
    }


    /**
     *
     * 获取curl访问的options
     */
    public function getOptions()
    {
        return $this->optionArray;
    }

    /**
     *
     * 进行optionArray的合并操作
     * @param array $options 需要合并的option
     */
    public function mergeOptions($options)
    {
        $this->optionArray = $this->optionArray + $options;
    }

    /**
     * this is the necessary step. After this method is executed, all options set will be effective
     *
     * @access public
     * @return string $result
     * @throws TMRemoteException
     */
    public function execute()
    {
        curl_setopt_array($this->channel,$this->optionArray);
        $result = curl_exec($this->channel);
        if(curl_errno($this->channel) != 0)
        {
            throw new TMRemoteException("Remote visiting is failed: ".curl_error($this->channel));
        }
        else
        {
            return $result;
        }
    }

    /**
     * send a post curl request
     *
     * @access public
     * @param  array $arrayParam     发送参数的键值对数组
     * @param string $url
     * @return string $result
     */
    public function sendByPost($arrayParam,$url="")
    {
        return $this->send($arrayParam,true,$url);
    }

    /**
     * send a get curl request
     *
     * @access public
     * @param  array $arrayParam 发送参数的键值对数组
     * @param  string $url
     * @return string $result
     */
    public function sendByGet($arrayParam, $url="")
    {
        return $this->send($arrayParam,false,$url);
    }

    /**
     * get the information of the channel. About the detail of parameter $opt, please read the php manual.
     *
     * @access public
     * @param  array $opt     查询可选参数
     * @return mixed $result
     */
    public function getInfo($opt=null)
    {
        return curl_getinfo($this->channel,$opt);
    }

    /**
     * 将curl实例返回
     *
     * @access public
     * @return resource $channel
     */
    public function get()
    {
        return $this->channel;
    }

    /**
     * Set virtual host when using curl
     *
     * @param  string $hostString     the host string
     * @return void
     */
    public function setVHost($hostString)
    {
        $option_array = $this->optionArray;
        if(isset($option_array[CURLOPT_HTTPHEADER])){
            $headerArray = $option_array[CURLOPT_HTTPHEADER];
        }else{
            $headerArray = array();
        }

        $headerArray[] = "Host: ".$hostString;

        $option_array[CURLOPT_HTTPHEADER] = $headerArray;

        $this->setOptions($option_array);
    }

    /**
     * send post or get curl request
     *
     * @access private
     * @param  array $arrayParam    发送参数的键值对数组
     * @param bool $post            是否是post方法，post方法为true
     * @param string $url           curl request url
     * @return string $result
     */
    public function send($arrayParam,$post=true,$url="")
    {
        if (empty($url))
        {
            $url = $this->url;
        }else{
            $this->url = $url;
        }

        $this->setIgnoreExpect100Continue();

        $cookies = TMUtil::handleParameter($_COOKIE, "; ");
        $option_array = $this->optionArray;
        $option_array[CURLOPT_HEADER] = 0;
        $option_array[CURLOPT_COOKIE] = $cookies;
        $option_array[CURLOPT_RETURNTRANSFER] = 1;

        if($post)
        {
            $option_array[CURLOPT_URL] = $url;
            $option_array[CURLOPT_POST] = 1;
            $option_array[CURLOPT_POSTFIELDS] = $arrayParam;
        }
        else
        {
            $parameter = $arrayParam;
            if(is_array($arrayParam)){
                $parameter = TMUtil::handleParameter($arrayParam);
            }
            if(!empty($parameter)){
                if(strpos($url, "?") === FALSE){
                    $option_array[CURLOPT_URL] = $url. "?" . $parameter;
                }else{
                    $option_array[CURLOPT_URL] = $url. "&" . $parameter;
                }
            }else{
                $option_array[CURLOPT_URL] = $url;
            }
        }
        $this->setOptions($option_array);
        return $this->execute();
    }

    /**
     *
     * 设置忽略expect 100-continue操作
     */
    public function setIgnoreExpect100Continue()
    {
        $option_array = $this->optionArray;
        if(isset($option_array[CURLOPT_HTTPHEADER])){
            $headerArray = $option_array[CURLOPT_HTTPHEADER];
        }else{
            $headerArray = array();
        }

        $headerArray[] = "Expect:";

        $option_array[CURLOPT_HTTPHEADER] = $headerArray;

        $this->setOptions($option_array);
    }

    /**
     *
     * 设置内网Http代理
     */
    public function setIntranetHttpProxy()
    {
        $this->setHttpProxy(self::HTTP_PROXY_TYPE_INTRANET);
    }

    /**
     *
     * 设置外网Http代理
     */
    public function setExtranetHttpProxy()
    {
        $this->setHttpProxy(self::HTTP_PROXY_TYPE_EXTRANET);
    }

    /**
     *
     * 设置Http代理
     *
     * @param mixed $proxyOptions 可以为string，string可以是self::HTTP_PROXY_TYPE_EXTRANET或者self::HTTP_PROXY_TYPE_INTRANET；也可以传入数组，数组中为proxy相关的opt
     */
    public function setHttpProxy($proxyOptions = self::HTTP_PROXY_TYPE_EXTRANET)
    {
        $optionArray = array();
        if(is_array($proxyOptions))
        {
            $optionArray = $proxyOptions;
        }else{
            $proxyConfigs = TMConfig::get("curl_http_proxy", $proxyOptions);
            if(!empty($proxyConfigs) && is_array($proxyConfigs))
            {
                foreach($proxyConfigs as $key => $proxyConfig)
                {
                    if(isset(self::$proxyConfigMap[$key]))
                    {
                        $optionArray[self::$proxyConfigMap[$key]] = $proxyConfig;
                    }else{
                        $optionArray[$key] = $proxyConfig;
                    }
                }
            }
        }

        $this->mergeOptions($optionArray);
    }

}