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

class TMRegisterTree {
    /**
     * 
     * @var array 变量树
     */
    protected static $tree = array();
    
    /**
     * 通过key val注册
     * @param string $key key
     * @param mix $val value
     */
    public static function set($key,$val){
        self::$tree[$key] = $val;
    }
    
    /**
     * 当一个参数get($key)时，按$key取出
     * 也可以放入多个get($key1,$key2,$key3) 返回的是 $array[$key1][$key2][$key3]的值
     * @return mix 当值不存在时，返回为null 
     */
    public static function get(){
        $keys = func_get_args();
        if(empty($keys)){
            return null;
        }else if(is_array($keys)){
            $t = self::$tree;
            foreach($keys as $key){
                if(!isset($t[$key]))
                {
                    return null;
                }
                $t = $t[$key];
            }
            return $t;
        }else{
            return self::$tree[$keys];
        }
    }
    
    /**
     * 将数组形态数据设置进Tree中
     * @param array $array 
     */
    public static function setArray($array){
        if(is_array($array)){
            foreach($array as $key=>$val){
                self::$tree[$key] = $val;
            }
        }
    }
    
    /**
     * 覆盖整个Tree来赋值
     * @param array $tree 
     */
    public static function setAll($tree){
        self::$tree = $tree;
    }
    
    /**
     * 取出所有注册的值
     * @return array 
     */
    public static function getAll(){
        return self::$tree;
    } 
}