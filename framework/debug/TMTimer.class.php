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
 * 用来计算代码的执行时间
 *
 * @package    sdk.src.framework.debug
 */
class TMTimer
{
    /**
     *
     * 开始计时的时间
     * @var mixed
     */
    protected $startTime = null;

    /**
     *
     * 总共的耗时
     * @var mixed
     */
    protected $totalTime = null;

    /**
     *
     * 计时器的名字
     * @var string
     */
    protected $name = '';

    /**
     *
     * 计时器的调用次数
     * @var int
     */
    protected $calls = 0;

    /**
     * 创建实例
     *
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->name = $name;
        $this->startTimer();
    }

    /**
     * 开始计时器
     */
    public function startTimer()
    {
        $this->startTime = microtime(true);
    }

    /**
     * 增加从开始到调用时刻消耗的时间
     *
     * @return float Time spend for the last call
     */
    public function addTime()
    {
        $spend = microtime(true) - $this->startTime;
        $this->totalTime += $spend;
        ++$this->calls;

        return $spend;
    }

    /**
     * 得到该计时器被调用的次数
     *
     * @return integer Number of calls
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * 得到总共消耗的时间
     *
     * @return float Time in seconds
     */
    public function getElapsedTime()
    {
        if (null === $this->totalTime)
        {
            $this->addTime();
        }

        return $this->totalTime;
    }
}
