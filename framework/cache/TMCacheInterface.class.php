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
     * 设置持久缓存对应关系
     * @access public
     * @param string $key    缓存名字
     * @param string $alive  缓存值
     * @param string $expire 过期时间，一般默认为0（不过期）
     * @return void
     */
    public function setAlive($key, $alive, $expire);

    /**
     * 获取缓存值
     * @access public
     * @param string $key   缓存名字
     * @return string       缓存值
     */
    public function get($key);

    /**
     * 获取持久缓存值
     * @access public
     * @param string $key   缓存名字
     * @return string       缓存值
     */
    public function getAlive($key);

    /**
     * 清除持久缓存值
     * @param string $key    缓存名字
     * @return void
     */
    public function clearAlive($key);

    /**
     * 缓存对象函数调用结果
     * execute $obj->$function and cache the execution result if $reset is true,or get the value from cache by default key
     * @param string $obj       需要缓存的对象
     * @param string $function  缓存对象调用的函数
     * @param string $param     缓存对象调用函数的参数数组
     * @param string $category  缓存过期方式。一般实现是定义一个缓存类型、过期时间对应数组，传递缓存类型来设置缓存时间
     * @param string $reset     是否重新设置缓存的值
     * @param array $options    是否使用新的key，如果设置了$options['key']就使用它为缓存键，不传递则自动生成一个key
     * @return mixed
     */
    public function cached($obj, $function, $param=array(), $category=null, $reset=false, $options=array());
}
