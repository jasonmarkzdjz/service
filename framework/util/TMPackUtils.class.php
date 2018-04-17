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
 * TMPackUtils
 * php pack 封装类
 *
 * @package sdk.src.framework.util
 */
class TMPackUtils {

    /**
     * 将char数组转换成字符串
     * @param array $array char数组
     */
    private static function chars2str($array){
        $ret = array();
        foreach ($array as $ele){
            $ret[]= chr($ele);
        }
        return implode('', $ret);
    }

    /**
     * 编码一个无符号32位整数
     * @param int $int 无符号32位整数
     */
    public static function encodeUInt32($int){
        $tmpStr = base_convert($int, 10, 16);
        $len = strlen($tmpStr);
        if($len > 8)
        {
            $tmpStr = substr($tmpStr, -8, 8);
        }else if($len < 8){
            $tmpStr = str_pad($tmpStr, 8,'0',STR_PAD_LEFT);
        }

        return pack('H8', $tmpStr);
    }

    /**
     * 解码一个无符号32位整数
     * @param string $string 编码字符串
     * @param boolean $mod 是否将原始字符串截断
     */
    public static function decodeUInt32(&$string,$mod=true)
    {
        $len = strlen($string);
        if($len<4)
        {
            throw new TMParameterException("need 4 bytes , $len bytes provide");
        }
        $aa = unpack('H8', $string);
        if($mod){
            $string = substr($string, 4);
        }
        return strval(hexdec($aa[1]));
    }

    /**
     * 编码一个符号32位整数
     * @param int $int 有符号32位整数
     */
    public static function encodeInt32($int){
        $tmpStr = dechex($int);
        $len = strlen($tmpStr);
        if($len > 8)
        {
            $tmpStr = substr($tmpStr, -8, 8);
        }else if($len < 8){
            $tmpStr = str_pad($tmpStr, 8,'0',STR_PAD_LEFT);
        }

        return pack('H8', $tmpStr);
    }

    /**
     * 解码一个符号32位整数
     * @param string $string 编码字符串
     * @param boolean $mod 是否将原始字符串截断
     */
    public static function decodeInt32(&$string,$mod=true)
    {
        $len = strlen($string);
        if($len<4)
        {
            throw new TMParameterException("need 4 bytes , $len bytes provide");
        }
        $aa = unpack('H8', $string);
        if($mod){
            $string = substr($string, 4);
        }

        $x = hexdec($aa[1]);
        if ($x > (float)2147483647) {
            $x -= (float)"4294967296";
            return intval($x);
        }else{
            return intval($x);
        }

        /*
         return sprintf("%d", hexdec($aa[1]));
         */
    }

    /**
     * 编码一个无符号16位整数
     * @param int $int 无符号16位整数
     */
    public static function encodeUInt16($int){
        return pack('n',$int);
    }

    /**
     * 解码一个无符号16位整数
     * @param string $string 编码字符串
     * @param boolean $mod 是否将原始字符串截断
     */
    public static function decodeUInt16(&$string,$mod=true)
    {
        $len = strlen($string);
        if($len<2)
        {
            throw new TMParameterException("need 2 bytes , $len bytes provide");
        }
        $ret = unpack('n', $string);
        if($mod){
            $string = substr($string, 2);
        }
        return $ret[1];
    }

    /**
     * 编码一个无符号8位char
     * @param string $char 字符
     */
    public static function encodeUChar($char){
        return pack('C',$char);
    }

    /**
     * 解码一个无符号8位char
     * @param string $string 编码字符串
     * @param boolean $mod 是否将原始字符串截断
     */
    public static function decodeUChar(&$string,$mod=true){
        $len = strlen($string);
        if($len<1)
        {
            throw new TMParameterException("need 1 bytes , $len bytes provide");
        }
        $ret = unpack('C', $string);
        if($mod){
            $string = substr($string, 1);
        }
        return $ret[1];
    }

    /**
     * 编码一个lv形式的字符串
     * @param string $string 字符串
     */
    public static function encodeLVString($string)
    {
        $length = strlen($string);
        return pack('na*',$length,$string);
    }

    /**
     * 解码一个lv形式的字符串
     * @param string $string 编码字符串
     * @param boolean $mod 是否将原始字符串截断
     */
    public static function decodeLVString(&$string,$mod=true)
    {
        $length = self::decodeUInt16($string);
        $len = strlen($string);
        if($len<$length)
        {
            throw new TMParameterException("need $length bytes , $len bytes provide");
        }
        if($length>0){
            $ret = unpack('C'.$length, $string);
            if($mod){
                $string = substr($string, $length);
            }
            return self::chars2str($ret);
        }
        return '';
    }

    /**
     * 编码一个lv形式的字符串-L长度为8
     * @param string $string 字符串
     */
    public static function encodeCLVString($string)
    {
    	$length = strlen($string);
    	return pack('ca*',$length,$string);
    }

    /**
     * 普通字符串转16进制字符串
     * @param str $str 字符串
     */
    public static function str2Hex($str)
    {
        $len = strlen($str);
        $result = '';
        for($i=0;$i<$len;$i++)
        {
            $result.=dechex(ord($str[$i]));
        }
        return $result;
    }

    /**
     * 16进制字符串转普通字符串
     * @param str $str 16进制字符串
     */
    public static function hex2Str($str)
    {
        $bin = "";
        $i = 0;
        do {
            $bin .= chr(hexdec($str{$i}.$str{($i + 1)}));
            $i += 2;
        } while ($i < strlen($str));
        return $bin;
    }

    /**
     * 编码一个字符串 - 将字符串转化为32位的16进制字符串
     * @param string $str 字符串
     */
    public static function encodeH32($str){
        $hex=self::str2Hex($str);
        return pack("H32",str_pad($hex,32,'0',STR_PAD_RIGHT));
    }

    /**
     * 解码一个字符串 - 将32位16进制字符串转化为普通字符串
     * @param string $str 32位16进制字符串
     * @param boolean $mod 是否将原始字符串截断
     */
    public static function decodeH32(&$str, $mod=true){
        $len = strlen($str);
        if($len < 16)
        {
            throw new TMParameterException("need 16 bytes , $len bytes provide");
        }
        $string = unpack("H32", $str);
        $string = rtrim($string[1], '0');
        if(strlen($string)%2 != 0){
            $string = $string.'0';
        }
        if($mod){
            $str = substr($str, 16);
        }
        return self::hex2Str($string);
    }
}
