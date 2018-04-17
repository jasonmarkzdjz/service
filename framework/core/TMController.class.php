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
 * 所有controller的基类
 *
 * @package sdk.src.framework.core
 * @author  ianzhang <ianzhang@tencent.com>
 * @version $Id: TMController.class.php 2615 2013-02-18 08:15:54Z ianzhang $
 */
abstract class TMController
{
    /**
     *
     * 可测试结果
     * @var string
     */
    protected static $testingResult;

    /**
     * @var array    用于页面替换的变量
     *
     * @access protected
     */
    protected $vars = array();

    /**
     * @var string    controller name
     *
     * @access protected
     */
    protected $controllerName;

    /**
     * @var string    action name
     *
     * @access protected
     */
    protected $actionName;

    /**
     * @var TMWebRequest    request实例
     *
     * @access private
     */
    protected $request;

    public static $smarty=null;

    /**
     * 构造函数
     *
     * @access public
     * @param  string $controllerName     the controller name
     * @param  string $actionName           the action name
     *
     */
    public function __construct($controllerName, $actionName)
    {
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->request = TMDispatcher::getInstance()->getRequest();
    }

    public static function smarty(){
        if(is_null(self::$smarty)){
            self::$smarty = new TaeTemplate();
            self::$smarty->template_dir = ROOT_PATH .TEMPLATE_DIR_TEMPLATE;
            self::$smarty->compile_dir  = ROOT_PATH .TEMPLATE_DIR_COMPILED;
        }
        return self::$smarty;
    }

    /**
     * Get template path
     *
     * @return the template path
     */
    protected function getTemplatePath()
    {
        $filePath = $this->controllerName . $this->actionName . '.php';
        return $filePath;
    }

    /**
     * Render the template
     *
     * @param string $path the render file path
     * @return the response content
     */
    protected function renderSlot($path = null)
    {
        if ($path == null)
        {
            $path = $this->getTemplatePath();
        }

        $view = new TMView();
        return $view->renderFile($this->vars, $path);
    }

    /**
     * Render the template in ajax mode
     *
     * @param string $path the render file path
     *
     * @return the response content
     */
    protected function renderAjax($path = null)
    {
        TMDispatcher::getInstance()->getResponse()->setAjax();
        return $this->renderSlot($path);
    }

    /**
     * Render the template with Content-Type:text/xml header
     *
     * @param string $tpl     template file
     *
     * @return the response content
     */
    protected function renderXml($tpl=null)
    {
        $response = TMDispatcher::getInstance()->getResponse();
        $response->setHttpHeader('Content-Type','text/xml');
        return $this->render('', $tpl, false);
    }

    /**
     * Render the template with Content-Type:application/x-javascript header
     *
     * @param string $tpl     template file
     *
     * @return the response content
     */
    protected function renderJs($tpl=null)
    {
        $response = TMDispatcher::getInstance()->getResponse();
        $response->setHttpHeader('Content-Type','application/x-javascript');
        return $this->render('', $tpl, false);
    }

    /**
     * Render the template with layout
     *
     * @param string $layout   layout type
     * @param string $tpl     template file
     * @param boolean $needTrack   是否需要监测
     *
     * @return the response content
     */
    protected function render($layout=null, $tpl=null, $needTrack=true)
    {
        if ($needTrack)
        {
            TMDispatcher::getInstance()->getResponse()->setNeedTrack();
        }
        $view = new TMView();
        return $view->render($this->vars, $layout, $tpl);
    }

    /**
     *
     * 获取标准格式成功返回
     * @param string $message 成功的信息
     * @param array $data 成功附带的数据
     * @param string $type 输出格式类型，默认是json，支持json，xml，jsonp
     */
    protected function success($message, array $data, $type = TMResultCreator::TYPE_JSON)
    {
        return TMResultCreator::getEncoder($type)->success($message, $data);
    }

    /**
     *
     * 获取标准格式错误返回
     *
     * @param int $code 错误的返回码
     * @param string $message 成功的信息
     * @param string $type 输出格式类型，默认是json，支持json，xml，jsonp
     */
    protected function error($code, $message, $type = TMResultCreator::TYPE_JSON)
    {
        return TMResultCreator::getEncoder($type)->fail($code, $message);
    }

    /**
     *
     * 获取标准格式异常错误返回
     *
     * @param TMException $te 异常
     * @param string $type 输出格式类型，默认是json，支持json，xml，jsonp
     */
    protected function exception(TMException $te, $type = TMResultCreator::TYPE_JSON)
    {
        return TMResultCreator::getEncoder($type)->fail($te);
    }

    /**
     *
     * 通过拦截器来实现PHP没有的方法重写
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args) {
        if ('fail' === $method) {
            if (is_object($args[0])) {
                if(isset($args[1])){
                    return $this->exception($args[0], $args[1]);
                }else{
                    return $this->exception($args[0]);
                }
            }
            else if (isset($args[1])) {
                if(isset($args[2])){
                    return $this->error($args[0], $args[1], $args[2]);
                }else{
                    return $this->error($args[0], $args[1]);
                }
            }
        }
    }

    /**
     * Set array vars for rendering
     *
     * @param $key                the vars key
     * @param $value              the value of the key
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->vars [$key] = $value;
    }

    /**
     * Get array vars value used key
     *
     * @param  $key              the vars key
     * @return value
     */
    public function __get($key)
    {
        return $this->vars[$key];
    }

    /**
     * Send http request to back location，需要2次请求
     *
     * @param string $alert             the alert message
     * @param string $url               default is TMConfig::Domain
     * @return string $content
     */
    public function sendAlertBack($alert = "", $url="")
    {
        $content = TMDispatcher::getInstance()->getResponse()->getAlertBackString($alert, $url);
        return $content;
    }

    /**
     * 实现请求转发，对于用户来说透明
     *
     * @param  string $controller      the forwarded controller name
     * @param  string $action          the forwarded action name
     * @param  string $component        the forwarded component name, 默认为空(即不是跳转到某个component)
     * @return string $content         the content string
     */
    public function forward($controller, $action, $component='')
    {
        $dispatcher = TMDispatcher::getInstance();
        $dispatcher->forward($controller, $action, $component);
    }

    /**
     * 实现链接跳转，需要多1次请求
     *
     * @param  string $url
     * @param  int $delay    跳转延时
     * @param  int $statusCode   跳转编码
     */
    public function redirect($url, $delay = 0, $statusCode = 302)
    {
        // redirect
        $response = TMDispatcher::getInstance()->getResponse();
        $response->redirect($url, $delay, $statusCode);
    }

    /**
     * 去执行指定controller中的action方法，如果方法不存在，则默认直接渲染页面
     *
     * @access public
     * @param string $controllerInstance   controller instance
     * @param string $funcName  action function name
     * @return string $content
     */
    public function execute($controllerInstance,$funcName)
    {
        try
        {
            if (method_exists($controllerInstance,$funcName))
            {
                $content = call_user_func_array(array(&$controllerInstance, $funcName), array());
            }
            else
            {
                $content = $this->render();
            }

            return $content;
        }
        catch (TMException $te)
        {
            return $this->exceptionHandle($te);
        }
    }

    /**
     * 处理execute方法中捕获到的exception
     *
     * @param  TMException $exception   TMException实例
     * @return string $content
     * @throws TM404Exception
     */
    protected function exceptionHandle($exception)
    {
        return $exception->handle();
    }

    /**
     *
     * 设置可测试结果
     * @param string $testingResult
     */
    public static function setTestingResult($testingResult)
    {
        self::$testingResult = $testingResult;
    }

    /**
     *
     * 获得可测试结果
     */
    public static function getTestingResult()
    {
        return self::$testingResult;
    }

    /**
     *
     * 清除之前的可测试结果
     */
    public static function clearTestingResult()
    {
        self::$testingResult = null;
    }
}
