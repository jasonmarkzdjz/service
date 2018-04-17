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
 * TMResult.class.php
 *
 */
abstract class TMResult {
    public function __call($method, $args) {
        if ('fail' === $method) {
            if (is_object($args[0])) {
                return $this->exception($args[0]);
            }
            else if (isset($args[1])) {
                return $this->error($args[0], $args[1]);
            }
        }
    }
    /**
     *
     * 返回成功信息
     * @param string $message
     * @param mixed $data
     * @return mixed 不同格式的成功返回
     */
    abstract function success($message, $data);

    /**
     *
     * 返回错误信息
     * @param int $code
     * @param string $message
     * @return mixed 不同格式的错误返回
     */
    abstract protected function error($code, $message);

    /**
     *
     * 处理Exception
     * @param TMException $exception
     * @return mixed 不同格式的异常返回
     */
    abstract protected function exception(TMException $exception);
}