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
 * Tencent mind xml
 *
 * @package sdk.src.framework.fileparser.xml
 */
class TMXml extends TMAbstractXml
{
	/**
	 * XML文件内容
	 * @var string
	 */
    private $xmlString;

    /**
     * Construction function
     *
     * @param  string $string     the xml string
     * @param  boolean $isValidated   是否进行加载时的xml的DTD校验
     * @param  string $version  the XML version
     * @param  string $encoding the xml encoding
     * @throws TMXmlException
     */
    public function __construct($string, $isValidated = true, $version = '1.0', $encoding = 'utf-8')
    {
        $this->doc = new DOMDocument ( $version, $encoding );
        $this->xmlString = $string;
        if(!$this->loadXML($isValidated))
        {
            throw new TMXmlException(TMExceptionConstants::xmlError(TMExceptionConstants::XML_ERROR_LOAD));
        }
    }

    /**
     * The document load xml file
     *
     * @param  boolean $isValidated   是否进行加载时的xml的DTD校验
     * @return boolean $result        true or false
     */
    public function loadXML($isValidated = true)
    {
        $this->doc->validateOnParse = $isValidated;
        return $this->doc->loadXML($this->xmlString);
    }

    /**
     * save document object to xml file
     *
     * @return void
     * @throws TMXmlException
     */
    public function saveXML()
    {
        $result = $this->doc->saveXML();
        if($result === false)
        {
            throw new TMXmlException(TMExceptionConstants::xmlError(TMExceptionConstants::XML_ERROR_SAVE_DOM));
        }
        $this->xmlString = $result;
    }

    /**
     * Parse xml to array
     *
     * @return array $array    the xml array
     * @throws TMXmlException
     */
    public function parseXmlToArray()
    {
        $result = simplexml_load_string( $this->xmlString,null,LIBXML_NOCDATA );
        if ($result === false)
        {
            throw new TMXmlException(TMExceptionConstants::xmlError(TMExceptionConstants::XML_ERROR_PARSE_ARRAY));
        }
        $array = ( array ) $result;
        foreach ( $array as $key => $item )
        {
            $array [$key] = $this->convertStructToArray ( ( array ) $item );
        }
        return $array;
    }

    /**
     * Get Xml string
     *
     * @return string $xmlString   the xml string
     */
    public function getXMLString()
    {
        return $this->xmlString;
    }
}