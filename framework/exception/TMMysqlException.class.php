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
 * The Mysql exception class
 *
 * @package sdk.src.framework.exception
 */
class TMMysqlException extends TMException
{
    /**
     * 构造函数
     *
     * @param string $message
     * @param int $code
     * @return void
     */
    public function __construct($message, $code = TMExceptionConstants::EXCEPTION_MYSQL_CODE)
    {
        $this->logException($message);
        if(!self::$needShowRealMsg){
            $message = "系统繁忙";
        }
        parent::__construct ( $message, $code );
    }

    /**
     * 异常处理，输出显示给用户
     *
     * @param string $tpl
     * @return string
     */
    public function handle($tpl = '')
    {
        if(self::$needShowRealMsg)
        {
            echo "<pre>".$this->getTraceAsString()."</pre>";
        }
        return $this->output($tpl, $this->getMessage());
    }
}