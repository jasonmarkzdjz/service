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
 * TMFilterConfigHandle
 * 过滤器配置处理器
 *
 * @package sdk.src.framework.config
 */
class TMFilterConfigHandle
{
    /**
     * Execute filter config handle
     * Parse config/filter.yml, and return cache file content. <br>
     * This function will be called in TMConfigDispatcher::callHandle,
     * and then write the content into cache/config/filter.php.
     *
     * @access public
     * @param string $configFile config file path
     * @return string $content
     */
    public function execute($configFile)
    {
        $array = TMYaml::load($configFile);

        $content = "<?php\n";
        foreach($array as $filterInfo){
            if(is_array($filterInfo)){
                $filter = $filterInfo["name"];
                $uriInfo= serialize(empty($filterInfo["conditions"]) ? array() : $filterInfo["conditions"]);
                $isWhite = empty($filterInfo["is_white"]) ? false : $filterInfo["is_white"];
                $isRegex = empty($filterInfo["is_regex"]) ? false : $filterInfo["is_regex"];

                if(!empty($filter)){
                    $content .= $this->addFilterExt($filter, $uriInfo, $isWhite, $isRegex);
                }
            }else{
                $content .= $this->addFilter($filterInfo);
            }
        }
        $content .= "\n?>";
        return $content;
    }

    /**
     * Adds a filter statement to the data
     *
     * @param  string $class  the class name
     * @return string
     */
    protected function addFilter($class)
    {
        return <<<EOF
\$this->register('{$class}');
EOF;
    }

    /**
     * Adds a filter statement to the data
     *
     * @param  string $class  the class name
     * @param string $uriInfo  配置的uri正则
     * @param boolean $isWhite  配置的uri是否允许运行filter
     * @param boolean $isRegex  配置的uri是否为正则表达式
     * @return string
     */
    protected function addFilterExt($class, $uriInfo, $isWhite, $isRegex)
    {
        return <<<EOF
\$this->registerExt('{$class}', '{$uriInfo}', '{$isWhite}', '{$isRegex}');
EOF;
    }
}