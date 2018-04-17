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
 * 表示404错误的Exception
 *
 * @package sdk.src.framework.exception
 */

class TM404Exception extends TMException
{
    /**
     * 构造函数
     * 
     * @param string $message
     * @param int $code
     * @return void
     */
    public function __construct($message, $code = TMExceptionConstants::EXCEPTION_404_CODE)
    {
        parent::__construct ( $message, $code );
    }

    /**
     * 异常处理
     * 
     * @param string $tpl
     * @return string
     */
    public function handle($tpl = 'error/404.php')
    {
        TMDispatcher::getInstance()->getResponse()->setStatusCode(404);
        if(self::$needShowRealMsg){
            echo "<pre>".$this->getMessage()."\n";
            echo $this->getTraceAsString()."</pre>";
        }
        return $this->output('error/404.php');
    }
}
