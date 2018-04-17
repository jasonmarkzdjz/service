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
 * TMJsonResultEncoder.class.php
 *
 * @package sdk.src.framework.result
 */
class TMJsonResultEncoder extends TMResultEncoder {
    /**
     *
     * 格式化返回值
     * @param int $code
     * @param string $message
     * @param mixed $data 如果返回里不包含数据则为NULL
     * @return 不同格式的返回值
     */
    private function formatResult($code, $message, $data = NULL) {
        $arr = array ('code' => $code, 'message' => $message);
        if (!is_null($data)) {
            $arr['data'] = is_array($data) ? $data : array($data);
        }

        return json_encode($arr);
    }

    /**
     * JSON格式成功返回
     * @see ApiResultEncoder::success()
     */
    public function success($message, $data) {
        return $this->formatResult(TMResultEncoder::RET_SUCCESS, $message, $data);
    }

    /**
     * JSON格式错误返回
     */
    protected function error($code, $message) {
        return $this->formatResult($code, $message);
    }
}