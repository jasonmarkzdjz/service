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
 * TMCrontabManager
 * crontab管理类
 *
 * @package sdk.src.framework.crontab
 */
class TMCrontabManager {
    /**
     *
     * @var TMCrontabManager 单例实例
     */
    private static $instance = null;

    /**
     *
     * 用于文件锁的路径地址
     * @deprecated
     * @var string
     */
    private $touchName;

    /**
     * 构造函数
     */
    protected function __construct()
    {
        TMPHPEventHandler::registerPHPHandleFunction();
    }

    /**
     * 获取单例函数
     * @return TMCrontabManager
     */
    public function getInstance(){
        if(self::$instance === NULL)
        {
            self::$instance = new TMCrontabManager();
        }

        return self::$instance;
    }

    /**
     * Run one crontab php script
     * @param $crontabName 脚本类名
     * @param $env 环境类型
     */
    public function runCrontab($crontabName, $params = null, $env = null)
    {
        $this->initCrontabRunEnv($env);
        $this->initTaeEnv();
        $this->loadCrontabHandler($crontabName, $params);
    }

    /**
     * Init crontab run env
     * @param string $env 环境类型
     */
    protected function initCrontabRunEnv($env = null)
    {
        if($env !== NULL)
        {
            TMEnvConfig::setEnv($env);
        }

        TMAbstractLogger::setIsRunInCli(true);
        TMConfig::initialize();
    }

    /**
     * Init Tae Env
     */
    protected function initTaeEnv()
    {
        TMTaeInitFilter::taeInit();
    }

    /**
     * 加载crontab脚本运行处理类
     * @param string $crontabName 脚本类名
     */
    protected function loadCrontabHandler($crontabName, $params = null)
    {
        $crontabClassName = $crontabName."Handler";
        if(!class_exists($crontabClassName))
        {
            throw new TMParameterException("Handler class '$crontabClassName' doesn't exist");
        }

        if(!in_array("TMCrontabHandler", class_parents($crontabClassName)))
        {
            throw new TMParameterException("Handler class '$crontabClassName' doesn't extend from TMCrontabHandler");
        }

        //检查当前脚本是否正在运行
        /*
        $this->touchName = ROOT_PATH."log/crontab.{$crontabName}.run.fd";
        if(is_file($this->touchName)){
            exit;
        }*/

        exec("pgrep -fl \"".$_SERVER["PHP_SELF"]." crontab:run ".$crontabName."\"", $array);
        if(!empty($array) && count($array) >= 2)
        {
            foreach($array as $key => $value)
            {
                if(preg_match("/^\d+ \/bin\/sh -c/", $value))
                {
                    unset($array[$key]);
                }
            }

            if(count($array) >= 2)
            {
                TMPHPEventHandler::setRunSuccessStatus();
                exit;
            }
        }
        /*
        declare(ticks=1);
        pcntl_signal(SIGTERM, array($this, "signalHandle"));
        pcntl_signal(SIGINT,  array($this, "signalHandle"));
        */
        try{
            //touch($this->touchName);
            //进行SQM监测

            $handler = new $crontabClassName($crontabName);

            if(!empty($params)){
                $handler->handle($params);
            }else{
                $handler->handle();
            }
            //unlink($this->touchName);
        }catch(TMException $te)
        {
            //TODO 处理crontab逻辑异常的情况
            //unlink($this->touchName);
        }

        TMPHPEventHandler::setRunSuccessStatus();
    }

    /**
     *
     * 处理中断信号
     * @deprecated
     * @param $signo 信号量
     */
    public function signalHandle($signo)
    {
        if($signo == SIGTERM || $signo == SIGINT)
        {
            unlink($this->touchName);
            exit;
        }
    }

}
