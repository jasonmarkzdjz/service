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
 * 存放多个计时器的管理容器
 *
 * @package    sdk.src.framework.debug
 */
class TMTimerManager
{
    /**
     *
     * 计时器的集合
     * @var array
     */
    static public $timers = array();

    /**
     * 得到一个TMTimer的实例
     *
     * @param string $name 计时器的名字
     *
     * @return TMTimer
     */
    public static function getTimer($name)
    {
        if (!isset(self::$timers[$name]))
        {
            self::$timers[$name] = new TMTimer($name);
        }

        self::$timers[$name]->startTimer();

        return self::$timers[$name];
    }

    /**
     * 得到一个TMTimer的实例，但不直接进去计时
     *
     * @param string $name 计时器的名字
     *
     * @return TMTimer
     */
    public static function getNotStartedTimer($name)
    {
        if (!isset(self::$timers[$name]))
        {
            return null;
        }

        return self::$timers[$name];
    }

    /**
     * 返回所有保存在TMTimerManager中的TMTimer
     *
     * @return array An array of all TMTimer instances
     */
    public static function getTimers()
    {
        return self::$timers;
    }

    /**
     * 清空所有在TMTimerManager中的TMTimer
     */
    public static function clearTimers()
    {
        self::$timers = array();
    }
}
