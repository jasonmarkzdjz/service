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
 * TMResultCreator.class.php
 *
 * @package sdk.src.framework.result
 */
class TMResultCreator {
    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_JSONP = 'jsonp';
    const TYPE_PNG = 'png';

    protected static $currentType;

    /**
     * @var ApiResult
     */
    private static $encoder;

    /**
     *
     * 创建不同格式的返回值 编码器
     * @param string $type
     */
    public static function getEncoder($paramType = self::TYPE_JSON) {
        $type = is_null(self::$currentType) ? $paramType : self::$currentType;
        if (is_null(self::$encoder)) {
            switch ($type) {
                case TMResultCreator::TYPE_JSON :
                    self::$encoder = new TMResultCodeFormatter(new TMJsonResultEncoder());
                    break;
                case TMResultCreator::TYPE_JSONP :
                    self::$encoder = new TMResultCodeFormatter(new TMJsonpResultEncoder());
                    break;
                case TMResultCreator::TYPE_XML:
                    self::$encoder = new TMResultCodeFormatter(new TMXmlResultEncoder());
                    break;
                default:
                    self::$encoder = new TMResultCodeFormatter(new TMJsonResultEncoder());
                    break;
            }
        }

        return self::$encoder;
    }

    public static function getType() {
        return self::$currentType;
    }

    public static function setType($type) {
        self::$currentType = $type;
    }
}