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
 * Tencent mind xml creator
 *
 * @package sdk.src.framework.fileparser.xml
 */
class TMXmlCreator {

    /**
     *
     * xml的版本号
     * @var string
     */
    private $version    = '1.0';

    /**
     *
     * 编码形式
     * @var string
     */
    private $encoding   = 'UTF-8';

    /**
     *
     * 根节点名字
     * @var string
     */
    private $root       = 'result';

    /**
     *
     * Xml编写器
     * @var XmlWriter
     */
    private $xml        = null;

    /**
     *
     * 构造函数
     */
    function __construct() {
        $this->xml = new XmlWriter();
    }

    /**
     *
     * 从数组转换成XML
     * @param array $data 解析的数据
     * @param boolean $eIsArray 判断是否在循环的递归数组中解析
     */
    function createFromArray(array $data, $eIsArray = false) {
        if(!$eIsArray){
            $this->xml->openMemory();
            $this->xml->startDocument($this->version, $this->encoding);
            $this->xml->startElement($this->root);
        }
        foreach($data as $key => $value){
            if (is_numeric($key)) {
                $key = 'item';
            }
            if(is_array($value)){
                $this->xml->startElement($key);
                $this->createFromArray($value, TRUE);
                $this->xml->endElement();
            } else {
                if (is_numeric($value)) {
                    $this->xml->writeElement($key, $value);
                } else {
                    $this->xml->startElement($key);
                    $this->xml->writeCData($value);
                    $this->xml->endElement();
                }
            }
        }
        if(!$eIsArray){
            $this->xml->endElement();
            return $this->xml->outputMemory(true);
        }
    }
}