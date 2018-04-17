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
 * TMExceptionView
 * 渲染异常输出模板的类
 *
 * @package sdk.src.framework.exception
 */
class TMExceptionView extends TMView{

    /**
     * 渲染异常页面
     *
     * @param array $data
     * @param string $tpl
     * @return string
     */
    public function renderException($data, $tpl) {
        if(empty($tpl))
        {
            $tpl = "error/syserror.php";
        }
        if($tpl[0] != "/")
        {
            $tpl = ROOT_PATH . TMConfig::get('template_path', 'template') . $tpl;
        }

        return $this->renderFile($data, $tpl);
    }
}
