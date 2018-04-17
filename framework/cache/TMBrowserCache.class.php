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
 * 浏览器缓存管理类
 *
 * @package sdk.src.framework.cache
 */
class TMBrowserCache
{
    /**
     * 设置浏览器缓存时间
     *
     * @access public
     * @param int $seconds 缓存时间
     * @return void
     */
    static public function cache($seconds)
    {
        $response = TMWebResponse::getInstance();
        $response->setHttpHeader("Last-Modified", gmdate ("D, d M Y H:i:s", time()) . " GMT");
        $response->setHttpHeader("Expires", gmdate ("D, d M Y H:i:s", time() + $seconds) . " GMT");
        $response->setHttpHeader("Cache-Control", "public");
    }

    /**
     * 使页面内容每次都是最新的，没有缓存
     *
     * @access public
     * @return void
     */
    static public function nonCache()
    {
        $response = TMWebResponse::getInstance();
        $response->setHttpHeader("Last-Modified", gmdate ("D, d M Y H:i:s", time()) . " GMT");
        $response->setHttpHeader("Expires", gmdate ("D, d M Y H:i:s", time() - 3600) . " GMT");
        $response->setHttpHeader("Cache-Control", "private");
    }
}
