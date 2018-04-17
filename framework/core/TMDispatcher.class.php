<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2012 BY  TENCENT  CORPORATION.  ALL RIGHTS
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
 * The class for dispatcher request and send response
 *
 * @package sdk.src.framework.core
 * @author  ianzhang <ianzhang@tencent.com>
 * @version $Id: TMDispatcher.class.php 3620 2013-11-26 06:26:03Z ianzhang $
 */
class TMDispatcher
{
    /**
     * @var string    the component name
     *
     * @access private
     */
    private $componentName;

    /**
     * @var string    the controller name
     *
     * @access private
     */
    private $controllerName;

    /**
     * @var string    the action name
     *
     * @access private
     */
    private $actionName;

    /**
     * @var string    the layout name
     *
     * @access private
     */
    private $layout;

    /**
     * @var TMWebRequest the request object
     */
    private $request;

    /**
     * @var TMWebResponse the response object
     */
    private $response;

    /**
     * @var string    the component name
     *
     * @access private
     */
    private static $instance = null;

    /**
     * 构造函数
     * @return void
     */
    private function __construct()
    {
    }

    /**
     *
     * 析构函数
     */
    public function __destruct()
    {

    }

    /**
     * Get Instance
     *
     * @return TMDispatcher $instance
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            return self::createInstance();
        }

        return self::$instance;
    }

    /**
     * Creates a new static TMDispatcher instance.
     *
     * @param  array $parameters             additional request parameters array
     * @param  array $attributes             additional request attributes array
     * @param  array $options                additional request options array
     *
     * @return TMDispatcher                  An dispatcher instance
     */
    public static function createInstance($parameters = array(), $attributes = array(), $options = array())
    {

        if(self::$instance == null)
        {
            $class = __CLASS__;
            self::$instance = new $class ( );

            self::$instance->initialize ( $parameters, $attributes, $options );
        }
        return self::$instance;
    }

    /**
     * Initializes the current sfContext instance.
     *
     * @param  array $parameters             additional request parameters array
     * @param  array $attributes             additional request attributes array
     * @param  array $options                additional request options array
     *
     * @return void
     */
    public function initialize($parameters = array(), $attributes = array(), $options = array())
    {
        $this->request = TMWebRequest::getInstance( $parameters, $attributes );
        $this->response = TMWebResponse::getInstance( $options );
        $this->handleHeadRequest();
        $this->handleRoute();
        $this->setComponent();
        $this->setController();
        $this->setAction();
        $this->setLayout();
    }

    private function handleHeadRequest()
    {
        if($this->request->getMethod() == TMRequest::HEAD){
            $this->response->setHeaderOnly();
        }
    }

    /**
     * 处理路由系统
     * @return void
     * @throws TM404Exception | TMStopException
     */
    public function handleRoute()
    {
    	if(is_file(ROOT_PATH."config/routing.yml")){
	        $routing = new TMRouting();
	        $parameters = $routing->parse($this->request->getPathInfo());
	        if($parameters === FALSE)
	        {
	            try{
	                throw new TM404Exception("routing error");
	            }catch(TM404Exception $te)
	            {
	                $this->response->setContent($te->handle());
	                $this->response->send();
	                throw new TMException();
	            }
	        }
	        if(!empty($parameters) && is_array($parameters)){
	            $this->request->addRequestParameters($parameters);
	        }
    	}
    }

    /**
     * Dispatch the request
     * First: search controller
     * Second: execute controller->execute
     * Third: setContent
     * Forth: send response, send http header, send http body
     *
     * @return void
     */
    public function dispatch()
    {
        try
        {
            $filterChain = new TMFilterChain();
            $filterChain->loadConfiguration();
            $filterChain->execute();
        }
        catch(TMException $te)
        {


            $content = $te->handle();

            $this->response->setContent($content);

            $length = strlen($content);
            $this->response->setHttpHeader("Content-Length", $length);

            $this->response->send();
        }
//        TMPHPEventHandler::setRunSuccessStatus();
    }

    /**
     * Set controller name from url
     *
     * @return void
     */
    private function setController()
    {
        $controller = $this->request->getGetParameter ( TMConfig::get("controller", "key") );
        $this->controllerName = strtolower ( $controller );
    }

    /**
     * Set controller name
     *
     * @access public
     * @param  string $name     the controller name
     * @return void
     */
    public function setControllerName($name)
    {
        $this->controllerName = $name;
    }

    /**
     * Set action name
     *
     * @access public
     * @param  string $name     the action name
     * @return void
     */
    public function setActionName($name)
    {
        $this->actionName = $name;
    }

    /**
     * Set component name
     *
     * @access public
     * @param  string $name     the action name
     * @return void
     */
    public function setComponentName($name)
    {
        $this->componentName = $name;
    }

    /**
     * Get controller name
     *
     * @return controllerName   The controller name
     */
    public function getController()
    {
        if(empty($this->controllerName))
        {
            $this->setController();
        }
        return $this->controllerName;
    }

    /**
     * Set action name from url
     *
     * @return void
     */
    private function setAction()
    {
        $action = $this->request->getGetParameter ( TMConfig::get("action", "key") );
        $this->actionName = strtolower ( $action );
    }

    /**
     * Get controller name
     *
     * @return actionName   The action name
     */
    public function getAction()
    {
        if(empty($this->actionName))
        {
            $this->setAction();
        }
        return $this->actionName;
    }

    /**
     * Set component name from url
     *
     * @return void
     */
    private function setComponent()
    {
        $component = $this->request->getGetParameter(TMConfig::get("component", "key"), '');
        if(!empty($component)){
            $this->componentName = strtolower($component);
        }
    }

    /**
     * Get component name
     */
    public function getComponent()
    {
        return $this->componentName;
    }

    /**
     * Set layout name
     *
     * @param string $layout
     * @return void
     */
    public function setLayout($layout=null)
    {
        if ($layout == null) {
            $layout = TMConfig::get("layout", "default");
        }
        $this->layout = $layout;
    }

    /**
     * Get layout name
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Get response name
     *
     * @return TMWebResponse $response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get request name
     *
     * @return TMWebRequest $request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * 实现请求转发，对于用户来说不透明
     *
     * @param  string $controller      the forwarded controller name
     * @param  string $action          the forwarded action name
     * @param  string $component        the forwarded component name, 默认为空(即不是跳转到某个component)
     * @return string $content         the content string
     */
    public function forward($controller, $action, $component='')
    {
        $this->setControllerName($controller);
        $this->setActionName($action);
        $this->setComponentName($component);
        $this->setLayout();
        $this->dispatch();
        new TMException();
    }

    /**
     * 载入某个组件
     *
     * @access public
     * @param string $componentName 组件名
     * @param array $parameters 组件构造函数所需参数
     * @return 组件的对象
     */
    public static function loadComponents($componentName, $parameters=array())
    {
        $className = 'TM' . ucfirst($componentName) . 'Component';
        require_once ROOT_PATH . "components/" . $componentName . "/classes/" . $className . ".class.php";
        return call_user_func_array(array($className, 'getInstance'), $parameters);
    }

    /**
     * 获取组件的文件夹路径
     *
     * @access public
     * @param string $component 组件名
     * @return string 组件的文件夹路径
     */
    public static function getComponentsDir($component) {
        return ROOT_PATH . 'components/' . $component . '/';
    }
}
