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
 * LockService
 * 缓存并发锁
 *
 * @package sdk.src.framework.service
 */

class LockService {

    /**
     * 在缓存管理器中设置指定的值，来做标记（上锁）
     *
     * @param string $key 标记名
     * @param integer $expire 过期时间
     * @return 缓存管理器所返回的提示信息
     */
    public static function lock($key, $expire = 0)
    {
        return self::memcacheLock($key, $expire);
    }

    /**
     * 清除之前在缓存管理器中设定的指定值(解锁)
     *
     * @param string $key 标记名
     * @return void
     */
    public static function unlock($key)
    {
        self::memcacheUnLock($key);
    }


    /**
     * 在memcache中设置指定的值，来做标记（上锁）
     *
     * @param string $key 标记名
     * @param integer $expire 过期时间
     * @return 缓存管理器所返回的提示信息
     */
    public static function memcacheLock($key, $expire = 0)
    {
        $memcache = TMMemCacheMgr::getInstance();
        return $memcache->add($key, 'lock', $expire);
    }

    /**
     * 清除之前在memcache中设定的指定值(解锁)
     *
     * @param string $key 标记名
     * @return void
     */
    public static function memcacheUnLock($key)
    {
        $memcache = TMMemCacheMgr::getInstance();
        $memcache->clear($key);
    }
}
?>