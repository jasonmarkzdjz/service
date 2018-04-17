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
 * TMJsonpResultEncoder.class.php
 *
 * @package sdk.src.framework.result
 */
class TMJsonpResultEncoder extends TMResultEncoder {

    /**
     *
     * callback参数的key
     * @var string
     */
    protected static $callbackParamKey = 'callback';

    /**
     *
     * 设置callback的参数名
     * @param string $key 设置的参数名字
     */
    public static function setCallbackParamKey($key)
    {
        self::$callbackParamKey = $key;
    }

    /**
     *
     * 获得callback的参数名
     */
    public static function getCallbackParamKey()
    {
        return self::$callbackParamKey;
    }

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

        $request = TMDispatcher::getInstance()->getRequest();
        $callback = $request->getParameter(self::$callbackParamKey, '');
        if (!preg_match('/^[0-9a-zA-Z_]+$/i', $callback)) {
            $callback = 'callback';
        }
        if (empty($callback)) {
            $callback = 'callback';
        }

        return $callback . '(' . json_encode($arr) . ')';
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