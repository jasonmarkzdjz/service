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
  * The class for render view
  *
  * @package sdk.src.framework.core
  * @author  ianzhang <ianzhang@tencent.com>
  * @version $Id: TMView.class.php 2700 2013-04-19 07:40:39Z ianzhang $
  */
class TMView {

    /**
     *
     * @var string 模板文件路径
     */
    private $filePath;

    /**
     * 构造函数
     * @param string $filePath
     * @return void
     */
    public function __construct($filePath = null)
    {
        if ($filePath != null)
        {
            $this->filePath = $filePath;
        }
    }

    /**
     * Get template path
     *
     * @return the template path
     */
    protected function getTemplatePath()
    {
        $dispatcher = TMDispatcher::getInstance();
        $filePath = $dispatcher->getController() . $dispatcher->getAction() . '.php';
        return $filePath;
    }

    /**
     * Render the template with layout
     *
     * @param array $vars    变量替换数组
     * @param string $layout   layout type
     * @param string $tpl     template file
     *
     * @return the response content
     */
    public function render($vars=array(), $layout=null, $tpl=null)
    {
        if(!is_array($vars))
        {
            $vars = array();
        }

        if ($layout===null && TMConfig::get("layout", "enable"))
        {
            $layout = TMConfig::get("layout", "default");
        }
        $dispatcher = TMDispatcher::getInstance();
        $dispatcher->setLayout($layout);
        if ($tpl == null)
        {
            $tpl = $this->getTemplatePath();
        }
        $view = new TMView();
        if (empty($layout))
        {
            return $view->renderFile($vars, $tpl);
        }
        else
        {
            return $view->renderFileByLayout($layout, $vars, $tpl);
        }
    }

    /**
     * Render the file template
     *
     * @param  array  $vars        the change variables array
     * @param  string $filePath    the template file path
     * @return string              render content
     * @throws TMViewException
     */
    public function renderFile($vars, $filePath)
    {
        if(!is_array($vars))
        {
            $vars = array();
        }

        foreach($vars as $key => $value)
        {
            $$key=$value;
        }
        ob_start();
        if ( substr($filePath,0,1) != "/" && !strpos($filePath, ":"))
        {
            $filePath = ROOT_PATH . TMConfig::get("template_path","template") . $filePath;
        }
        if(file_exists($filePath))
        {
            require($filePath);
        }
        else
        {
            throw new TMViewException("No view file!".$filePath);
        }
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    /**
     * 如何使用layout
     * 你需要在调用controller render方法的时候传入第一个参数即可
     * 例如 你的某个页面需要使用 simple layout（layout/simple.php），这样调用render:
     *   $this->render('simple');
     * 至于render的第二个参数是页面所需的模板，可以是相对于ROOT_PATH.'templates/'的相对路径，也可以是绝对路径
     *   $this->render('simple', 'list.php'); //use simple layout render list.php
     *   $this->render('simple', ROOT_PATH.'templates/detail.php'); //use simple layout render ROOT_PATH.'templates/detail.php'
     *   $this->render(null, 'list.php'); //render list.php directly
     *   $this->render('', 'list.php'); //render list.php directly same as $this->render(null, 'list.php');
     *
     * layout使用的原理，可以这样理解
     *   1. 获取页面所需要数据放入$vars中
     *   2. 利用output buffer处理template, 即$layoutContent = renderFile($vars, $tpl);
     *   3. $vars['layoutContent'] = $layoutContent;
     *   4. 利用output buffer处理layout, 即$output = renderFile($vars, $layout);
     *   5. 返回$output
     *
     *   注：不使用layout相当于把$output=$layoutContent作为结果返回
     *      即不考虑slot的情况下，如果不使用layout每个页面只需一次renderFile，而使用layout则需要两次renderFile
     *
     * @param  string $layout      the layout type
     * @param  array  $vars        the change variables array
     * @param  string $filePath    the template file path
     * @return string              render content
     * @throw TMViewException
     */
     public function renderFileByLayout($layout, $vars, $filePath)
     {
        if(!is_array($vars))
        {
            $vars = array();
        }

         foreach ($vars as $key => $value)
         {
             $$key=$value;
         }
         $layoutContent = $this->renderFile($vars, $filePath);

         ob_start();
         $layout_file = ROOT_PATH .TMConfig::get("template_path","layout") . $layout . ".php";
         if (file_exists($layout_file))
         {
             require($layout_file);
         }
         else
         {
             throw new TMViewException("No layout file! " . $layout_file);
         }
         return ob_get_clean();
     }
 }