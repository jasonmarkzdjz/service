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
 * 流程链控制类
 *
 * LIB库内部调用
 * 根据config/filter.yml的配置，判断哪些流程需要执行，并依次调用执行
 *
 * @package sdk.src.framework.filter
 */
class TMFilterChain {

    /**
     * 执行链
     * @var array
     */
    protected $chain = array();

    /**
     * 当前执行下标
     * @var int
     */
    protected $index = -1;

    /**
     * 获取流程链配置
     */
    public function loadConfiguration()
    {
        $configDispatcher = new TMConfigDispatcher();
        require($configDispatcher->getConfigFile("filter"));
    }

    /**
     * Register the filter name into chain
     *
     * @param string $filterName  filter名称
     */
    public function register($filterName)
    {
        $this->chain[] = $filterName;
    }

    /**
     * Register the filter name into chain
     *
     * @param string $filterName  filter名称
     * @param string $uriInfo  配置的uri正则
     * @param boolean $isWhite  配置的uri是否允许运行filter
     * @param boolean $isRegex  配置的uri是否为正则表达式
     */
    public function registerExt($filterName, $uriInfo, $isWhite, $isRegex)
    {
        $uriInfo = unserialize($uriInfo);
        if(!($isWhite xor $this->matchURI($uriInfo, $isRegex))){
            $this->chain[] = $filterName;
        }
    }

    /**
     * Execute the filter event
     *
     */
    public function execute()
    {
        ++$this->index;

        if ($this->index < count($this->chain))
        {
            $className = $this->chain[$this->index];

            while(!class_exists($className)){
                //TaeSQMService::attrReport(TaeSQMService::ATTR_FILTER_RUN_NOT_EXIST);
                ++$this->index;
                if($this->index < count($this->chain))
                {
                    $className = $this->chain[$this->index];
                }else{
                    return;
                }
            }

            //if(preg_match("/^TM(.+)Filter$/", $className, $matches))
            $filterStartPos = strpos($className, "Filter");
            if($filterStartPos !== FALSE){

                if(strpos($className, "TM") === 0 && ($filterStartPos + 6) == strlen($className))
                {
                    $attrName = "ATTR_FILTER_".strtoupper(substr($className, 2, -6))."_TOUCH";

                    if(defined("TaeSQMService::".$attrName)){
                        //TaeSQMService::attrReport(constant("TaeSQMService::".$attrName));
                    }
                }
            }

            $filter = new $className();
            $filter->execute($this);
        }
    }

    /**
     * 根据配置，查看当前URI是否在配置
     *
     * @param array $uriInfo  配置的uri数组
     * @param boolean $isRegex  是否正则校验
     * @return boolean 是否匹配
     */
    private function matchURI($uriInfo, $isRegex)
    {
        $dispatcher = TMDispatcher::getInstance();
        $component = $dispatcher->getComponent();
        if(!empty($component))
        {
            $uri = '/'.$component.'/'.$dispatcher->getController().'/'.$dispatcher->getAction();
        }else{
            $uri = '/'.$dispatcher->getController().'/'.$dispatcher->getAction();
        }
        if($isRegex){
            foreach ($uriInfo as $path){
                if(preg_match($path, $uri)){
                    return true;
                }
            }
        }else{
            foreach ($uriInfo as $path){
                if(strtolower($uri) == strtolower($path)){
                    return true;
                }
            }
        }
        return false;
    }
}