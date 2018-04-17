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
 * Tencent mind XML File
 *
 * @package sdk.src.framework.fileparser.xml
 */
class TMXmlFile extends TMAbstractXml
{
	/**
	 * XML路径
	 * @var string
	 */
    private $xmlPath;

    /**
     * Construction function
     *
     * @param  string $path     the file path
     * @param  string $version  the XML version
     * @param  string $encoding the xml encoding
     * @throws TMXmlException
     */
    public function __construct($path, $version = '1.0', $encoding = 'utf-8')
    {
        $this->doc = new DOMDocument ( $version, $encoding );
        $this->xmlPath = $path;
        if(!$this->loadXML())
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
        return $this->doc->load ($this->xmlPath);
    }

    /**
     * save document object to xml file
     *
     * @return $result  the number of bytes written or FALSE if an error occurred
     * @throws TMXmlException
     */
    public function saveXML()
    {
        $result = $this->doc->save($this->xmlPath );
        if($result == false)
        {
            throw new TMXmlException(TMExceptionConstants::xmlError(TMExceptionConstants::XML_ERROR_SAVE_DOM));
        }
        return $result;
    }

    /**
     * Parse xml file to array
     *
     * @return array $array    the xml array
     * @throws TMXmlException
     */
    public function parseXmlToArray()
    {
        $result = simplexml_load_file( $this->xmlPath,null,LIBXML_NOCDATA );
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
     * Get Xml path
     *
     * @return string xmlPath   the xml path
     */
    public function getPath()
    {
        return $this->xmlPath;
    }
}