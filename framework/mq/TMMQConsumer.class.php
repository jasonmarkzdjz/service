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
 * TMMQConsumer
 * 消费者进程启动器
 *
 * @package sdk.src.framework.mq
 * @author  ianzhang <ianzhang@tencent.com>
 * @version $Id: TMMQConsumer.class.php 2547 2013-01-17 09:48:04Z ianzhang $
 */
class TMMQConsumer {

    /**
     *
     * 消费者进程休眠时间
     * @var int
     */
    const WAIT_TIME = 3;

    /**
     *
     * 消费者进程的个数上限
     * @var int
     */
    const CLEAR_COUNT = 10000;

    /**
     *
     * 队列长度
     * @var int
     */
    const QUEUE_LENGTH = 100000;

    /**
     *
     * 队列的名字
     * @var string
     */
    private $queue;

    /**
     *
     * 消费者处理类名
     * @var string
     */
    private $handleClass;

    /**
     *
     * 标记是否要退出
     * @var boolean
     */
    private $quit;

    /**
     * 日志记录类
     * @var TMLog
     */
    private $loger;

    /**
     *
     * 构造函数
     * @param string $queue 队列名字
     * @param string $handleClass 处理类名字
     */
    public function __construct($queue, $handleClass)
    {
        $this->queue = $queue;
        $this->handleClass = $handleClass;
        $this->quit = false;
        $this->loger = new TMLog(ROOT_PATH.'log/mq/'.$queue.'.frame.log',true,false);

        $log = new TMLog(ROOT_PATH.'log/mq/'.$queue.'.system.log',true,false);
        TMException::addLogger($log);

        TMMysqlAdapter::setAllowReconnect(true);
    }

    /**
     * 获取是否要退出的状态
     * @return the $quit
     */
    public function getQuit() {
        return $this->quit;
    }

    /**
     * 设置是否要退出
     * @param boolean $quit 是否要退出的状态
     */
    public function setQuit($quit) {
        $this->quit = $quit;
    }

    /**
     *
     * 设置进程准备退出标志
     * @param string $signo 进程信号量
     */
    public function doQuit($signo)
    {
        $this->log("receive signo $signo");
        $this->setQuit(true);
    }

    /**
     *
     * 记录日志
     * @param string $str 日志
     */
    private function log($str)
    {
        echo $str."\n";
        $this->loger->lm($str);
    }

    /**
     *
     * 初始化消费者进程
     */
    public function init()
    {
        $this->log('starting consumer');
        //init queue
        $mq = TMMQClient::getInstance();
        $mq->changeQueueLength(self::QUEUE_LENGTH, $this->queue);

        $this->log('initing queue client success');
        //init processor
        $class = $this->handleClass;
        $listener = new $class($this->queue);
        $this->log('processor inited');
        //fork
        $pid = pcntl_fork();
        if($pid!=0)//parent
        {
            exit;
        }
        //child
        //set priority
        pcntl_setpriority(1);
        // setup signal handlers
        pcntl_signal(SIGTERM, array($this,'doQuit'));
        $this->log('signal handler installed');
        //while loop
        $this->log('starting main loop');
        $count = 0;
        while (true) {
            if($this->quit)
            {
                break;
            }
            $ret = $mq->getMessage($this->queue);
            if(empty($ret))
            {
                sleep(self::WAIT_TIME);
            }else{
                try{
                    $listener->process($ret);
                    $count++;
                    if($count>self::CLEAR_COUNT)
                    {
                        unset($listener);
                        $listener = new $class($this->queue);
                    }
                }catch (TMException $e)
                {
                    $this->log('error ocured :'.$e->getMessage());
                    continue;
                }
            }
        }//end of while loop

        //something to do before quit
        $this->log("normal quit");
    }
}