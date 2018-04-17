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
 * 配置文件的常量
 *
 * @package sdk.src.framework.exception
 */
class TMExceptionConstants{
    //code从0 - 99，超过99之后用10000以上进行表示，这样和其他系统进行区分
    const EXCEPTION_DEFAULT_CODE = 0;
    const EXCEPTION_404_CODE = 1;
    const EXCEPTION_CACHE_CODE = 2;
    const EXCEPTION_CONFIG_CODE = 3;
    const EXCEPTION_DAO_CODE = 4;
    const EXCEPTION_FILE_CODE = 5;
    const EXCEPTION_EVENT_CODE = 6;
    const EXCEPTION_LOG_CODE = 7;
    const EXCEPTION_MYSQL_CODE = 8;
    const EXCEPTION_REMOTE_CODE = 9;
    const EXCEPTION_UPLOAD_CODE = 10;
    const EXCEPTION_VIEW_CODE = 11;
    const EXCEPTION_VOTE_CODE = 12;
    const EXCEPTION_XML_CODE = 13;
    const EXCEPTION_NO_LOGIN_CODE = 14;
    const EXCEPTION_PARAMETER_CODE = 15;
    const EXCEPTION_DATA_OBJECT = 16;
    const EXCEPTION_BUILDER = 17;
    const EXCEPTION_BUSINESS = 18;
    const EXCEPTION_SYSTEM = 19;
    const EXCEPTION_SEND_CODE = 20;
    const EXCEPTION_AUTHUTILS_CODE = 21;
    const EXCEPTION_TAE_SUP = 22;       // 后端TAE支撑平台的异常码
    const EXCEPTION_LOG_FILE_CODE = 23;  //单独用于日志文件异常的错误码
    const EXCEPTION_WAP_CODE = 24;
    const EXCEPTION_COMMAND_CODE = 25;
    const EXCEPTION_COMMAND_ARGUMENTS_CODE = 26;
    const EXCEPTION_ASYN_TRACK_CODE = 27;
    const EXCEPTION_COMPONENT_CODE = 28;
    const EXCEPTION_COMPONENT_COMMONUPLOAD_CODE = 29; // 通用上传组件错误
    const EXCEPTION_COMPONENT_ITIL_CODE = 30; // itil静态化组件错误
    const EXCEPTION_COMPONENT_AUTOSTATIC_CODE = 31; // 自动静态化组件错误
    const EXCEPTION_SAC_CODE = 32; // Sac Service错误
    const EXCEPTION_RANK_CODE = 33; // Rank Service异常
    const EXCEPTION_UDS_CODE = 34; //uds 服务异常
    const EXCEPTION_MAP_CODE = 35; //地图服务异常
    const EXCEPTION_EXCHANGE_CODE = 36; //兑换码服务异常

    //解析xml错误
    const XML_ERROR_SET_DOM = 1;
    const XML_ERROR_SET_NODE = 2;
    const XML_ERROR_LOAD = 3;
    const XML_ERROR_SAVE_DOM = 4;
    const XML_ERROR_PARSE_ARRAY = 5;

    /**
     * 错误信息
     * @var array
     * @access public
     */    
    static $_xmlErrors = array (1 => '设置的Dom Document的类型不正确', 2 => 'Dom Node的类型不正确', 3 => '加载XML失败', 4 => '保存xml的DOM结构失败', 5 => 'Xml解析错误' );

    /**
     * 显示xml错误提示
     *
     * @param int $mixed
     * @return string
     */
    public static function xmlError($mixed) {
        return self::$_xmlErrors [$mixed];
    }

    /**
     * 返回xml错误提示数组
     *
     * @return array
     */
    public static function xmlErrors() {
        return self::$_xmlErrors;
    }
}