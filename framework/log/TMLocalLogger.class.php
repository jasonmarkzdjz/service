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
 * TMLocalLogger
 * 日志程序
 *
 * @package sdk.src.framework.log
 */
class TMLocalLogger extends TMAbstractLogger implements TMLogInterface{

    /**
     *
     * 保存日志的数组
     * @var array
     */
    protected static $logRecords = array();

    /**
     *
     * 文件操作对象
     * @var TMFile
     */
    protected $file;

    /**
     *
     * 日志路径
     * @var string
     */
    protected $path;

    /**
     *
     * 高级错误日志
     * @var string
     */
    const statusHigh   = "H";

    /**
     *
     * 中级错误日志
     * @var string
     */
    const statusMiddle = "M";

    /**
     *
     * 低级错误日志
     * @var string
     */
    const statusLow    = "L";

    /**
     *
     * 告警错误日志
     * @var string
     */
    const statusAlert  = "A";

    /**
     *
     * 字体颜色对应map
     * @var array
     */
    protected static $colorFront   = array(
            'black'  => 30,
            'red'    => 31,
            'green'  => 32,
            'yellow' => 33,
            'blue'   => 34,
            'purple' => 35,
            'darkgreen' => 36,
            'white'  => 37,
    );

    /**
     *
     * 背景颜色对应map
     * @var array
     */
    protected static $colorBg   = array(
            'black'   => 40,
            'red'     => 41,
            'green'   => 42,
            'yellow'  => 43,
            'blue'    => 44,
            'purple'  => 45,
            'darkgreen' => 46,
            'white'   => 47,
    );

    /**
     *
     * 表示日志记录错误是否要抛出异常
     * @var boolean
     */
    protected static $needThrowException = false;

    /**
     *
     * 返回是否要抛出异常的状态
     * @return boolean
     */
    public static function getNeedThrowException()
    {
        return self::$needThrowException;
    }

    /**
     *
     * 设置日志记录错误是否要抛出异常
     * @param boolean $needThrowException
     */
    public static function setNeedThrowException($needThrowException)
    {
        self::$needThrowException = $needThrowException;
    }

    /**
     * 构造函数
     * @param string $path   日志地址
     * @param boolean $multiple 是否要多文件分割
     * @param mixed $splitRequest 不需要按照请求分离文件则传入FALSE
     * @return void
     */
    public function __construct($path = null, $multiple = true, $splitRequest = null) {
        if(empty($path))
        {
            $path = TMConfig::get("error_log", "path");
            if(empty($path))
            {
                $path = "log/error_log";
            }
        }
        if($path[0] != "/")
        {
            $path = ROOT_PATH.$path;
        }

        if($splitRequest === null){
            $tmpSplitRequest = TMConfig::get("error_log", "split_request");

            if(isset($tmpSplitRequest)){
                $splitRequest = $tmpSplitRequest;
            }else{
                $splitRequest = true;
            }
        }

        if(!self::$isRunInCli && !(isset($splitRequest) && $splitRequest == false))
        {
            $dispatcher = TMDispatcher::getInstance();
            $componentName = $dispatcher->getComponent();
            $controllerName = $dispatcher->getController();
            $actionName = $dispatcher->getAction();

            if(!empty($componentName)){
                $path = $path."_{$componentName}";
            }

            if(!empty($controllerName)){
                $path = $path."_{$controllerName}";
            }

            if(!empty($actionName))
            {
                $path = $path."_{$actionName}";
            }
        }else if(self::$isRunInCli)
        {
            $path = $path."_crontab";
        }

        $logSize = 33554432; //32M
        $logSize = (TMConfig::get("error_log", "size") == null) ? $logSize : TMConfig::get("error_log", "size");

        $this->createBackUpFile($path, $multiple, $logSize);
    }

    /**
     * 创建备份文件
     *
     * @param string  $path    日志文件的路径（完整路径）
     * @param boolean $multiple 支持分文件存储
     * @param int     $logSize 如果支持分文件，该值为分文件的临界值
     */
    protected function createBackUpFile($path, $multiple = true, $logSize = 134217728)
    {
        $this->path = $path;
        if(is_file($path) && $multiple === true){
            $filesize = @filesize($path);
            if (!empty($filesize) && $filesize > $logSize)
            {
                $lockServ = new LockService();
                if ($lockServ->lock($path.'_log_file_path', 10))
                {
                    @unlink($path."_php_4");
                    if(@rename($path."_php_3", $path."_php_4") === TRUE)
                    {
                        @chmod($path."_php_4", 0777);
                    }
                    if(@rename($path."_php_2", $path."_php_3") === TRUE)
                    {
                        @chmod($path."_php_3", 0777);
                    }
                    if(@rename($path, $path."_php_2") === TRUE)
                    {
                        @chmod($path."_php_2", 0777);
                    }
                    @unlink($path);
                    $lockServ->unlock($path.'_log_file_path');
                }
            }

            if(is_file($path."_2")){
                $filesize = @filesize($path."_2");
                if (!empty($filesize) && $filesize > $logSize)
                {
                    $lockServ = new LockService();
                    if ($lockServ->lock($path.'_2_log_file_path', 10))
                    {
                        @unlink($path."_2_php_4");
                        if(@rename($path."_2_php_3", $path."_2_php_4") === TRUE)
                        {
                            @chmod($path."_2_php_4", 0777);
                        }
                        if(@rename($path."_2_php_2", $path."_2_php_3") === TRUE)
                        {
                            @chmod($path."_2_php_3", 0777);
                        }
                        if(@rename($path."_2", $path."_2_php_2") === TRUE)
                        {
                            @chmod($path."_2_php_2", 0777);
                        }
                        @unlink($path."_2");
                        $lockServ->unlock($path.'_2_log_file_path');
                    }
                }
            }

            if(is_file($path."_2_3")){
                $filesize = @filesize($path."_2_3");
                if (!empty($filesize) && $filesize > $logSize)
                {
                    $lockServ = new LockService();
                    if ($lockServ->lock($path.'_2_3_log_file_path', 10))
                    {
                        @unlink($path."_2_3_php_4");
                        if(@rename($path."_2_3_php_3", $path."_2_3_php_4") === TRUE)
                        {
                            @chmod($path."_2_3_php_4", 0777);
                        }
                        if(@rename($path."_2_3_php_2", $path."_2_3_php_3") === TRUE)
                        {
                            @chmod($path."_2_3_php_3", 0777);
                        }
                        if(@rename($path."_2_3", $path."_2_3_php_2") === TRUE)
                        {
                            @chmod($path."_2_3_php_2", 0777);
                        }
                        @unlink($path."_2_3");
                        $lockServ->unlock($path.'_2_3_log_file_path');
                    }
                }
            }
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {

    }

    /**
     * Log High priority （高优先级日志）
     *
     * @param string $msg     the log message
     */
    public function lh($msg)
    {
        $this->formatWrite($msg,self::statusHigh);
    }

    /**
     * Log Middle priority （中优先级日志）
     *
     * @param string $msg     the log message
     */
    public function lm($msg)
    {
        $this->formatWrite($msg,self::statusMiddle);
    }

    /**
     * Log Low priority （低优先级日志）
     *
     * @param string $msg     the log message
     */
    public function ll($msg)
    {
        $this->formatWrite($msg,self::statusLow);
    }

    /**
     * 记录Alert级别的日志
     * @param string $msg
     */
    public function la($msg)
    {
        $this->formatWrite($msg,self::statusAlert);
    }

    /**
     * 记录无关日志
     * @param string $msg
     */
    public function lo($msg)
    {
        $this->formatWrite($msg,'',self::$colorFront['darkgreen']);
    }

    /**
     * handleFormatWrite
     * 格式化输出
     * echo chr(033).'[31;47;1mThis is a very important infomation.'.chr(033).'[0;0;0m'.chr(0x0a);
     *
     * @param string $msg      输出的log信息内容
     * @param string $status   输出标志位信息
     * @param string $colorFront 字体颜色
     * @param string $colorBg 背景颜色
     */
    protected function handleFormatWrite($msg, $status="", $colorFront=null, $colorBg=null)
    {
        // 设置控制字符
        $cntlChar = chr(033);

        // 前景色：$colorFront
        // 背景色：$colorBg

        // 校验前景色。如果没有设置正确的前景色，默认不高亮
        $colorFront = (int)$colorFront;
        $colorFront = in_array($colorFront,self::$colorFront) ? $colorFront : 0;
        // 校验背景色。如果没有设置正确的背景色，默认不高亮
        $colorBg = (int)$colorBg;
        $colorBg = in_array($colorBg,self::$colorBg) ? $colorBg : 0;

        // 有级别的日志记录，根据级别标志位来决定颜色。
        $status = strtoupper($status);
        switch($status) {
            case self::statusMiddle:
                $colorFront = self::$colorFront['yellow'];
                break;
            case self::statusLow:
                $colorFront = self::$colorFront['green'];
                break;
            case self::statusAlert:
            case self::statusHigh:
                $colorFront = self::$colorFront['red'];
                break;
        }

        // 写入日志
        try
        {
            //记录写日志的次数
            TaeMonitorService::attrReport(TaeConstants::TNM_ATTR_LOG);

            //上报写日志到SQM中
            TaeSQMService::attrReport(TaeSQMService::ATTR_LOG_TOUCH);

            if(!isset($this->file)){
                $this->file = new TMFile($this->path, "ab+");
            }

            $date = date("H:i:s Ymd");
            if (!empty($status))
            {
                $val = $cntlChar."[{$colorBg};{$colorFront};1m[".$date."] <".$status.">{$cntlChar}[0;0;0m ".$msg.".\n";
            }
            else
            {
                $val = $cntlChar."[{$colorBg};{$colorFront};1m[".$date."]{$cntlChar}[0;0;0m ".$msg.".\n";
            }
            if(empty($this->file)){
                throw new TMFileException ( "Failed to open the log file! Can't open the file");
            }
            $this->file->insert($val);
        }
        catch(TMException $te)
        {
            TaeMonitorService::attrReport(TaeConstants::TNM_ATTR_LOG_FAILURE);
            TaeSQMService::attrReport(TaeSQMService::ATTR_LOG_TOUCH_EXCEPTION);
            TaeLogService::logTxt(TaeLogService::ERROR_LEVEL, "taesdk_tmn", "TNM_ATTR_LOG_FAILURE", $te->getMessage());
            if(self::$needThrowException)
            {
                throw $te;
            }
        }
    }

    /**
     * formatWrite
     * 格式化输出
     * echo chr(033).'[31;47;1mThis is a very important infomation.'.chr(033).'[0;0;0m'.chr(0x0a);
     *
     * @param string $msg      输出的log信息内容
     * @param string $status   输出标志位信息
     * @param string $colorFront 字体颜色
     * @param string $colorBg 背景颜色
     */
    protected function formatWrite($msg, $status="", $colorFront=null, $colorBg=null)
    {
        $this->handleFormatWrite($msg, $status, $colorFront, $colorBg);
    }
}
?>
