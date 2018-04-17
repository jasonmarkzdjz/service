<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2012 BY  TENCENT  CORPORATION.  ALL RIGHTS
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
 * Create this object, you could use it to make your two-way cryption code (encode and decode)
 *
 * @package sdk.src.framework.db
 */
class TMCryption
{
    /**
     * The mask chars array that used to calculate encoding
     * 
     * @var array
     */
    private $mMaskArray;

    /**
     * Initialize the Key String, and
     *
     * @param  String key You'd better to use the chars which are not used in the encoded string as keys, avoids conflicts existed.
     */
    public function __construct($key='$|$||||$')
    {
        $this->mMaskArray  = $this->stringToArray($key);
    }

    /**
     * Encode the string
     *
     * @param string $str     The string need to be encoded
     * @return String    The string has been encoded
     */
    public function encryption($str)
    {
        if(empty($str))
        {
            return false;
        }

        $arr_input = $this->stringToArray($str);
        $endIndex = count($this->mMaskArray)-1;
        $cryptStr = "";
        $index = 0;
        foreach($arr_input as $char)
        {
            $cryptStr .= ord($char);
            if($index > $endIndex)
            {
                $index = 0;
            }
            $cryptStr .= ord($this->mMaskArray[$index]);
            $index ++;
        }
        return $cryptStr;
    }

    /**
     * Decode the string
     *
     * @param  $cryptStr     The encoded string.
     * @return String    The string has been decoded.
     */
    public function decryption($cryptStr){
        if (empty($cryptStr))
        {
            return false;
        }

        $startIndex = 0;
        $tempStr= "";
        $bound = strlen($cryptStr) -1;
        $keyIndex = 0;

        while($startIndex <= $bound)
        {
            $splitCode = "".ord($this->mMaskArray[$keyIndex]);
            $endIndex = strpos($cryptStr,$splitCode,$startIndex);

            if ($endIndex == false)
            {
                return false;
            }

            $str = substr($cryptStr,$startIndex,$endIndex-$startIndex);
            $tempStr .= chr($str);

            $startIndex = $endIndex+strlen($splitCode);
            if($keyIndex>=count($this->mMaskArray)-1)
            {
                $keyIndex=0;
            }
            else
            {
                $keyIndex ++;
            }
        }
        return $tempStr;
    }

    /**
     * change string into Array
     *
     * @param  String  $str 
     * @return Array
     */
    private function stringToArray($str)
    {
        $str = "".$str;
        $arr_return = array();
        for ($i = 0; $i<strlen($str);$i++)
        {
            $arr_return[] = $str[$i];
        }
        return $arr_return;
    }
}