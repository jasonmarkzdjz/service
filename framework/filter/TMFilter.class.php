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
 * 流程链的抽象类
 *
 * @package sdk.src.framework.filter
 * @author  ianzhang <ianzhang@tencent.com>
 * @version $Id: TMFilter.class.php 2181 2012-11-07 09:04:21Z ianzhang $
 */
abstract class TMFilter implements TMFilterInterface
{
    /**
     * dispatcher实例
     * @var TMDispatcher
     */
    protected $dispatcher;

    /**
     * get dispatcher
     *
     * @return TMDispatcher $dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * set dispatcher
     *
     * @param  TMDispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Executes this filter.
     *
     * @param TMFilterChain $filterChain
     */
    public function execute($filterChain)
    {
        //
    }
}