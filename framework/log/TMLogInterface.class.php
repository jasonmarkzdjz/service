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
 * TMLogInterface
 * 日志接口
 *
 * @package sdk.src.framework.log
 */
interface TMLogInterface {
    /**
     * 打印高风险日志
     *
     * @param string $msg
     */
    public function lh($msg);

    /**
     *
     * 打印中级风险日志
     * @param string $msg
     */
    public function lm($msg);

    /**
     * 打印低风险日志
     *
     * @param string $msg
     */
    public function ll($msg);

    /**
     * 打印警告日志
     *
     * @param string $msg
     */
    public function la($msg);

    /**
     * 打印无关日志
     *
     * @param string $msg
     */
    public function lo($msg);
}