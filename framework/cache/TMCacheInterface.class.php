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
 * 缓存接口类，用于统一缓存实现的接口
 *
 * @package sdk.src.framework.cache
 */
interface TMCacheInterface{
    /**
     * 设置缓存对应关系
     * @access public
     * @param string $key    缓存名字
     * @param string $value  缓存值
     * @param string $expire 过期时间
     * @return void
     */
    public function set($key,$value,$expire=0);



    /**
     * 获取缓存值
     * @access public
     * @param string $key   缓存名字
     * @return string       缓存值
     */
    public function get($key);


    public function add($key,$value, $expire = 0);

    public  function increment($key, $offset=1);

    public function decrement($key, $offset=1);
}
