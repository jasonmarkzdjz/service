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
 * The util class
 *
 * @package sdk.src.framework.util
 */
class TMUtil
{
    /**
     * @var array    用于生成随机兑换码
     * @access private
     */
    private static $charSet = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y',
                                            'a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y');

    /**
     * 用于生成随机兑换码的设置函数
     * @param array $array 用于生成随机兑换码的字符集
     */
    public static  function setCharSet($array) {
        self::$charSet = $array;
    }

    /**
     * 用于生成随机兑换码的取长度函数
     * @param boolean $isLowerCase 是否允许出现小写字母
     */
    private static function getCharLength($isLowerCase) {
        if($isLowerCase){
            $arrayIndex = count(self::$charSet);
        }else{
            $arrayIndex = 0;
            foreach (self::$charSet as $char) {
                if (($char >= '0' && $char <= '9') || ($char >= 'A' && $char <= 'Z')) {
                    $arrayIndex++;
                }
            }
        }
        $arrayIndex--;
        return $arrayIndex;
    }
    /**
     * Generate the random string
     *
     * @param  int $length                     Code的随机位长度。   The length of generated string
     * @param  string $prefix                  Code的固定位内容。
     * @param  boolean $isLowerCase     是否允许Code中出现小写字母。
     * @return random string
     */
    public static function getRandomString($length,$prefix="",$isLowerCase=false)
    {
        $returnString = $prefix;
        for($i = 0; $i < $length; $i ++)
        {
            //mt_srand((double)microtime()*1000000);
            $arrayIndex = self::getCharLength($isLowerCase);
            $randASC = self::$charSet[mt_rand(0,$arrayIndex)];
            $returnString .=$randASC;
        }
        return $returnString;
    }

    /**
     * 得到当前运行的环境
     *
     * @return string
     */
    public static function getServerType()
    {
        return $_ENV['SERVER_TYPE'];
    }

    /**
     * Generate the random code, then  write into the files.
     *
     * @param string $path            路径、及文件名前部分
     * @param int $allCount        Code总数量
     * @param int $perFileCount    每个文件的Code存放数量限制
     * @param int $length          Code的随机位长度。
     * @param string $prefix           Code的固定位内容。
     * @param boolean $isLowerCase     是否允许Code中出现小写字母。
     * @return void
     */
    public static function generateRandomCode($path,$allCount,$perFileCount=200000,$length=11,$prefix="",$isLowerCase=false)
    {
        $arrayIndex = self::getCharLength($isLowerCase);

        $filecount = ceil($allCount/$perFileCount);
        $index = 0;
        for($i=1;$i<=$filecount;$i++)
        {
            if($index > $arrayIndex)
            {
                $index = 0;
            }

            $prefix_p = $prefix.self::$charSet[$index];
            if($perFileCount*$i<=$allCount)
            {
                $recordCount = $perFileCount;
            }
            else
            {
                $recordCount = $allCount-$perFileCount*($i-1);
            }

            $string ="";
            $file = new  TMFile($path.$prefix."_".date("Y-m-d")."_".$i.".txt","w+");
            for($j=0;$j<$recordCount;$j++)
            {
                $string .= self::getRandomString($length,$prefix_p)."\n";
            }
            $file->insert($string);
            $index ++;
        }
    }

    /**
     * Get the file name string suffix
     *
     * @param  string $fileName     the file name
     * @return string $pix          example(".jpg")
     */
    public static function getSuffix($fileName)
    {
        $pix = strtolower ( strrchr ( $fileName, '.' ) );
        return $pix;
    }

    /**
     * Returns an array value for a path
     *
     * @param array  $values   The values to search
     * @param string $name     The token name
     * @param array  $default  Default if not found
     *
     * @return array
     */
    public static function getArrayValueForPath($values, $name, $default = null)
    {
        if (false === $offset = strpos ( $name, '[' ))
        {
            return isset ( $values [$name] ) ? $values [$name] : $default;
        }

        if (! isset ( $values [substr ( $name, 0, $offset )] ))
        {
            return $default;
        }

        $array = $values [substr ( $name, 0, $offset )];

        while ( false !== $pos = strpos ( $name, '[', $offset ) )
        {
            $end = strpos ( $name, ']', $pos );
            if ($end == $pos + 1)
            {
                // reached a []
                if (! is_array ( $array ))
                {
                    return $default;
                }
                break;
            }
            else if (! isset ( $array [substr ( $name, $pos + 1, $end - $pos - 1 )] ))
            {
                return $default;
            }
            else if (is_array ( $array ))
            {
                $array = $array [substr ( $name, $pos + 1, $end - $pos - 1 )];
                $offset = $end;
            }
            else
            {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Strip slashes recursively from array
     *
     * @param  array $value  the value to strip
     * @return array clean value with slashes stripped
     */
    public static function stripslashesDeep($value)
    {
        return is_array ( $value ) ? array_map ( array ('TMUtil', 'stripslashesDeep' ), $value ) : stripslashes ( $value );
    }

    /**
     * Filter text recursively from array
     *
     * @access public
     * @param  mixed $value     the value to filter text
     * @return mixed  $result   clean value with filtered
     */
    public static function filterTextDeep($value)
    {
        return is_array($value)?array_map(array("TMUtil", "filterTextDeep"), $value) : TMFilterUtils::filterText($value,false);
    }

    /**
     * 处理query字符串，例如a=b&c=d
     *
     * @param  string $string     处理源字符串
     * @return array $resultArray       结果数组
     */
    public static function handleQueryString($string)
    {
        $tmpArray = explode("&", $string);
        $resutlArray = array();
        foreach($tmpArray as $tmp)
        {
            $tmpArray2 = explode("=",$tmp);
            if(isset($tmpArray2[1]))
            {
                $resutlArray[$tmpArray2[0]] = $tmpArray2[1];
            }else{
                $resutlArray[$tmpArray2[0]] = "";
            }
        }

        return $resutlArray;
    }

    /**
     * Get client ip address
     *
     * @return string $ip    the client ip address
     */
    public static function getClientIp()
    {
        if (isset ( $_SERVER ['HTTP_QVIA'] ))
        {
            $ip = qvia2ip ( $_SERVER ['HTTP_QVIA'] );
            if ($ip)
            {
                return $ip;
            }
        }

        if (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) and ! empty ( $_SERVER ['HTTP_CLIENT_IP'] ))
        {
            return TMFilterUtils::filterIp ( $_SERVER ['HTTP_CLIENT_IP'] );
        }
        if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) and ! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))
        {
            $ip = strtok ( $_SERVER ['HTTP_X_FORWARDED_FOR'], ',' );
            do
            {
                $ip = ip2long ( $ip );

                //-------------------
                // skip private ip ranges
                //-------------------
                // 10.0.0.0 - 10.255.255.255
                // 172.16.0.0 - 172.31.255.255
                // 192.168.0.0 - 192.168.255.255
                // 127.0.0.1, 255.255.255.255, 0.0.0.0
                //-------------------
                if (! (($ip == 0) or ($ip == 0xFFFFFFFF) or ($ip == 0x7F000001) or (($ip >= 0x0A000000) and ($ip <= 0x0AFFFFFF)) or
                (($ip >= 0xC0A8FFFF) and ($ip <= 0xC0A80000)) or (($ip >= 0xAC1FFFFF) and ($ip <= 0xAC100000))))
                {
                    return long2ip ( $ip );
                }
            }
            while ( $ip = strtok ( ',' ) );
        }
        if (isset ( $_SERVER ['HTTP_PROXY_USER'] ) and ! empty ( $_SERVER ['HTTP_PROXY_USER'] ))
        {
            return TMFilterUtils::filterIp ( $_SERVER ['HTTP_PROXY_USER'] );
        }
        if (isset ( $_SERVER ['REMOTE_ADDR'] ) and ! empty ( $_SERVER ['REMOTE_ADDR'] ))
        {
            return TMFilterUtils::filterIp ( $_SERVER ['REMOTE_ADDR'] );
        }
        else
        {
            return "0.0.0.0";
        }
    }

    /**
     * 获得本地服务器IP地址
     * 在web下一般$_SERVER['SERVER_ADDR']和apache_getenv('SERVER_ADDR')变量都能获得本地服务器IP
     * 在cli下能扫描/etc/sysconfig/network下面的文件找出IP地址
     *
     * @return string　本地服务器IP
     */
    static public function getServerIP() {
        $local_ip = '0.0.0.0';
        if ($_SERVER['SERVER_ADDR']) {
            $local_ip = $_SERVER['SERVER_ADDR'];
        } elseif (function_exists('apache_getenv')) {
            $local_ip = apache_getenv('SERVER_ADDR');
        } elseif (substr(PHP_OS, 0, 3) != 'WIN') {
            $dir = '/etc/sysconfig/network/';
            foreach(new RecursiveDirectoryIterator($dir) as $spl_file) {
                if (preg_match('/ifcfg-eth\d+/is', $spl_file->getFileName())) {
                    if (preg_match('/IPADDR=\'([\d.]+)\'/i', file_get_contents($spl_file->getPathname()), $match)) {
                        $local_ip = $match[1];
                        break;
                    }
                }
            }
        }
        return $local_ip;
    }

    /**
     * Get all page for the table
     *
     * @param  int $allCount     the count of all records
     * @param  int $countOnePage the count of one page
     * @return int $allPage      the count of all page
     */
    public static function getAllPage($allCount, $countOnePage)
    {
        if ($allCount == 0)
        {
            $allPage = 0;
        }
        else if($countOnePage == 0)
        {
            $allPage = 0;
        }
        else
        {
            if ($allCount % $countOnePage != 0)
            {
                $allPage = intval ( $allCount / $countOnePage ) + 1;
            }
            else
            {
                $allPage = $allCount / $countOnePage;
            }
        }

        if($allPage == 0)
        {
            $allPage = 1;
        }
        return $allPage;
    }

    /**
     * Get the length of string
     *
     * @param  string $str        the string
     * @return string $length     the string' text length
     */
    public static function getStringLength($str)
    {
        $start = 0;
        $len = strlen ( $str );
        $r = array ();
        $n = 0;
        $m = 0;
        for($i = 0; $i < $len; $i ++)
        {
            $x = substr ( $str, $i, 1 );
            $a = base_convert ( ord ( $x ), 10, 2 );
            $a = substr ( '00000000' . $a, - 8 );
            if ($n < $start)
            {
                if (substr ( $a, 0, 1 ) == 0)
                {
                }
                elseif (substr ( $a, 0, 3 ) == 110)
                {
                    $i += 1;
                }
                elseif (substr ( $a, 0, 4 ) == 1110)
                {
                    $i += 2;
                }
                $n ++;
            }
            else
            {
                if (substr ( $a, 0, 1 ) == 0)
                {
                    $r [] = substr ( $str, $i, 1 );
                }
                elseif (substr ( $a, 0, 3 ) == 110)
                {
                    $r [] = substr ( $str, $i, 2 );
                    $i += 1;
                }
                elseif (substr ( $a, 0, 4 ) == 1110)
                {
                    $r [] = substr ( $str, $i, 3 );
                    $i += 2;
                }
                else
                {
                    $r [] = '';
                }
            }
        }

        return count($r);
    }

    /**
     * Get short text
     *
     * @param  string $str     the origin string
     * @param  int    $lenth   the number n1%3 = 0 because utf
     * @param  string $etc     the short tail
     * @return string $str     the changed string
     */
    public static function getShortText($str, $lenth = 80, $etc = '...')
    {
        $start = 0;
        $len = strlen ( $str );
        $r = array ();
        $n = 0;
        $m = 0;
        for($i = 0; $i < $len; $i ++)
        {
            $x = substr ( $str, $i, 1 );
            $a = base_convert ( ord ( $x ), 10, 2 );
            $a = substr ( '00000000' . $a, - 8 );
            if ($n < $start)
            {
                if (substr ( $a, 0, 1 ) == 0)
                {
                }
                elseif (substr ( $a, 0, 3 ) == 110)
                {
                    $i += 1;
                }
                elseif (substr ( $a, 0, 4 ) == 1110)
                {
                    $i += 2;
                }
                $n ++;
            }
            else
            {
                if (substr ( $a, 0, 1 ) == 0)
                {
                    $r [] = substr ( $str, $i, 1 );
                }
                elseif (substr ( $a, 0, 3 ) == 110)
                {
                    $r [] = substr ( $str, $i, 2 );
                    $i += 1;
                }
                elseif (substr ( $a, 0, 4 ) == 1110)
                {
                    $r [] = substr ( $str, $i, 3 );
                    $i += 2;
                }
                else
                {
                    $r [] = '';
                }
                if (++ $m >= $lenth)
                {
                    break;
                }
            }
        }
        $trunstr = join ( '', $r );
        if (strlen ( $trunstr ) < $len){
            return $trunstr . $etc;
        }
        else{
            return $trunstr;
        }
    }

    /**
     * buildWhereString
     * 生成sql中的where 子句
     *
     * @param  array $input     where子句的键值对数组
     * @param  array $arrayStringColumn   将数字作为字符串处理的数据库key数组
     * @return string    sql中的where子句，如果参数不为数组则返回false
     */
    public static function buildWhereString($input, array $arrayStringColumn)
    {
        if(is_array($input))
        {
            $updateString = "1 ";
            foreach ($input as $key => $field)
            {
                if (!empty($arrayStringColumn))
                {
                    if (in_array($key, $arrayStringColumn))
                    {
                        $field = strval($field);
                    }
                    else if (is_numeric($field))
                    {
                        $field = intval($field);
                    }

                    if (is_string($field))
                    {
                        $updateString .= "and ".$key . " = '" . TMFilterUtils::filterSqlParameter($field) . "' ";
                    }
                    else
                    {
                        $updateString .= "and ".$key . " = " . $field . " ";
                    }
                }
            }

            return $updateString;
        }else{
            return "";
        }
    }

    /**
     * 处理curl发送参数的格式化
     *
     * @param array $array 要处理的参数键值对数组
     * @param string $connetChar 分割符号
     * @return string $result
     */
    public static function handleParameter($array, $connetChar='&')
    {
        $result = "";
        $i = 0;
        foreach($array as $key => $value)
        {
            $value = str_replace($connetChar, "", $value);
            if ($i == 0)
            {
                $result = $result.$key."=".$value;
            }
            else
            {
                $result = $result.$connetChar.$key."=".$value;
            }
            $i++;
        }
        return $result;
    }

    /**
     * 是否当前php运行在Web环境
     * @return boolean
     */
    public static function isPHPRunInWeb()
    {
        $sapiName = php_sapi_name();
        if($sapiName == "cli")
        {
            return false;
        }else{
            return true;
        }
    }

    /**
     *
     * 自定义数组合并递归函数，使用方法和标准的array_merge_recursive相同，不同的是可以进行数组相同元素的覆盖
     *
     */
    public static function arrayMergeRecursiveSimple()
    {
        if (func_num_args() < 2) {
            trigger_error(__CLASS__.":".__FUNCTION__ .' needs two or more array arguments', E_USER_ERROR);
            return;
        }
        $arrays = func_get_args();
        $merged = array();
        while ($arrays) {
            $array = array_shift($arrays);
            if (!is_array($array)) {
                trigger_error(__CLASS__.":".__FUNCTION__ .' encountered a non array argument', E_USER_ERROR);
                return;
            }
            if (!$array){
                continue;
            }
            foreach ($array as $key => $value)
            {
                //删除了原有只对于字符串处理的逻辑

                if ($key[0] == "_" && $key[1] == "_")
                {
                    $key = substr($key, 2);
                    $merged[$key] = $value;
                }
                else if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])){
                    $merged[$key] = self::arrayMergeRecursiveSimple($merged[$key], $value);
                }
                else{
                    $merged[$key] = $value;
                }
            }
        }
        return $merged;
    }

    /**
     * 是否是内网IP地址
     * @param string $ip 需要检查的IP地址，如果不传默认使用当前客户端地址
     *
     * @throws TMException 如果不是内网IP地址就抛异常
     * @return boolean 如果正确返回true
     */
    static public function isInternalIP($ip = '') {
        empty($ip) && ($ip = TMUtil::getClientIp());
        // 先用正则校验IP地址格式
        if (!preg_match("/^[\d]{1,3}(?:\.[\d]{1,3}){3}$/", $ip)) {
            throw new TMException("不是合法的IP地址");
        }

        // 判断IP地址是否为空或不是由4个IP段构成
        $ip_array = explode('.', $ip);
        if (empty($ip_array) or 4 != count($ip_array)) {
            throw new TMException("不是合法的IP地址");
        }

        // 判断是否是内网地址
        $intranetip_prefix_array = array(
                '10',
                '172',
                '192',
                '169'
        );
        if (!in_array($ip_array[0], $intranetip_prefix_array)) {
            return false;
        }

        // 判断掩码是否正确
        foreach($ip_array as $ip_sec) {
            if (255 != ($ip_sec | 255)) {
                throw new TMException("不是合法的IP地址");
            }
        }
        return true;
    }
    /**
     * 检查目标文件夹是否存在，如果不存在则自动创建该目录
     *
     * @access      public
     * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
     *
     * @return      bool
     */
    function make_dir($folder)
    {
        $reval = false;

        if (!file_exists($folder))
        {
            /* 如果目录不存在则尝试创建该目录 */
            @umask(0);

            /* 将目录路径拆分成数组 */
            preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

            /* 如果第一个字符为/则当作物理路径处理 */
            $base = ($atmp[0][0] == '/') ? '/' : '';

            /* 遍历包含路径信息的数组 */
            foreach ($atmp[1] AS $val)
            {
                if ('' != $val)
                {
                    $base .= $val;

                    if ('..' == $val || '.' == $val)
                    {
                        /* 如果目录为.或者..则直接补/继续下一个循环 */
                        $base .= '/';

                        continue;
                    }
                }
                else
                {
                    continue;
                }

                $base .= '/';

                if (!file_exists($base))
                {
                    /* 尝试创建目录，如果创建失败则继续循环 */
                    if (@mkdir(rtrim($base, '/'), 0777))
                    {
                        @chmod($base, 0777);
                        $reval = true;
                    }
                }
            }
        }
        else
        {
            /* 路径已经存在。返回该路径是不是一个目录 */
            $reval = is_dir($folder);
        }
        clearstatcache();

        return $reval;
    }
}
