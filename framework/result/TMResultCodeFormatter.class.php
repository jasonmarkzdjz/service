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
 * TMResultCodeFormatter.class.php
 */
class TMResultCodeFormatter extends TMResultDecorator {

    protected function error($code, $message) {
        $code = self::formatCode($code);

        return $this->result->error($code, $message);
    }

    protected function exception(TMException $exception) {
        $reflection = new ReflectionClass($exception);
        $code = self::formatCode($exception->getCode());

        $exception = $reflection->newInstance($exception->getMessage(), $code);

        return $this->result->exception($exception);
    }

    public function success($message, $data) {
        return $this->result->success($message, $data);
    }

    protected static function formatCode($code) {
        if(intval($code) < 100){
            return 900 + intval($code);
        }else{
            return $code;
        }
    }
}