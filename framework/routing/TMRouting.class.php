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
 * TMRouting
 * 路由解析类
 *
 * @package sdk.src.framework.routing
 */
class TMRouting {

    /**
     *
     * 当前的路由名字
     * @var string
     */
    protected    $currentRouteName   = null;

    /**
     *
     * 是否要做参数过滤
     * @var boolean
     */
    protected    $defaultParamsDirty = false;

    /**
     *
     * 解析好的路由规则
     * @var array
     */
    protected    $routes             = array();

    /**
     * Class constructor.
     *
     * @see initialize()
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     *
     * 初始化操作
     */
    public function initialize()
    {
        $this->loadConfiguration();
    }

    /**
     *
     * 进行配置文件的解析，并将解析内容引入runtime
     */
    protected function loadConfiguration()
    {
        $configDispatcher = new TMConfigDispatcher();
        $configFileName = $configDispatcher->getConfigFile("routing");
        if(!empty($configFileName)){
            include $configFileName;
        }
    }

    /**
     *
     * 解析链接路由以及参数
     * @param string $url 当前访问uri
     */
	public function parse($url) {
		if (false === $info = $this->findRoute($url))
        {
            $this->currentRouteName = null;

            return false;
        }

        return $info['parameters'];
	}

	/**
	 *
	 * 寻找匹配的路由规则
	 * @param string $url 当前访问uri
	 */
    public function findRoute($url)
    {
        $url = $this->normalizeUrl($url);

        $info = $this->getRouteThatMatchesUrl($url);

        return $info;
    }


    /**
     *
     * 格式化URL
     * @param string $url 当前访问的uri
     */
    protected function normalizeUrl($url)
    {
        if ('/' != substr($url, 0, 1))
        {
            $url = '/'.$url;
        }

        // 去掉问号
        if (false !== $pos = strpos($url, '?'))
        {
            $url = substr($url, 0, $pos);
        }

        // remove multiple /
        $url = preg_replace('#/+#', '/', $url);

        return $url;
    }

    /**
     *
     * 获取匹配规则的路由对象
     * @param string $url 当前访问的uri
     */
    protected function getRouteThatMatchesUrl($url)
    {
        foreach ($this->routes as $name => $route)
        {
            if (false === $parameters = $route->matchesUrl($url))
            {
                continue;
            }
            return array('name' => $name, 'pattern' => $route->getPattern(), 'parameters' => $parameters);
        }

        return false;
    }
}
?>