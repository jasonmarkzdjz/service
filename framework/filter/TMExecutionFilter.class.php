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
 * 执行业务逻辑类，即执行对应controller的action
 *
 * 开关：config/filter.yml - TMExecutionFilter
 *
 * @package sdk.src.framework.filter
 */
class TMExecutionFilter extends TMFilter
{
    /**
     * 执行页面主逻辑，即调用controller execute方法
     *
     * @param TMFilterChain $filterChain
     * @throws TM404Exception
     */
    public function execute($filterChain)
    {
        $dispatcher = TMDispatcher::getInstance();

        $controller = $dispatcher->getController();
        if (empty($controller))
        {
            $controller = TMConfig::get("controller", "default_name");
            if (empty($controller))
            {
                $controller = "default";
            }
            $dispatcher->setControllerName($controller);
        }

        $action = $dispatcher->getAction();
        if (empty($action))
        {
            $action = TMConfig::get("action", "default_name");
            if (empty($action))
            {
                $action = "default";
            }
            $dispatcher->setActionName($action);
        }

        $controllerFileName = $controller. "Controller";

        $classPath = ROOT_PATH . 'controllers/' . $controllerFileName . '.php';

        $component = $dispatcher->getComponent();
        if (!empty($component))
        {
            $componentArray = TMConfig::get("components");
            if(!is_array($componentArray) || in_array($component, $componentArray)){
                $componentPath = TMDispatcher::getComponentsDir($component);
                $classPath = $componentPath . 'controllers/' . $controllerFileName . '.php';
            }else{
                throw new TM404Exception("No Page Found.Component '{$component}' not open in config.yml");
            }
        }

        $content = '';
        if (file_exists($classPath))
        {
            require_once($classPath);
            $controllerInstance = new $controllerFileName($controller, $action);
            $funcName = $action."Action";
            $content = $controllerInstance->execute($controllerInstance,$funcName);
        }
        else if (empty($component))
        {
            $dispatcher->getResponse()->setNeedTrack();
            $view = new TMView();
            $content = $view->render();
        }
        else
        {
            throw new TM404Exception("No Page Found.".$classPath." doesn't exist");
        }

        $dispatcher->getResponse()->setContent($content);

        $filterChain->execute();
    }
}
