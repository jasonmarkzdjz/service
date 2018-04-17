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
 * 排行服务
 *
 * @package sdk.src.framework.tae.service
 */
class RankService {
    /**
     * 服务实例
     * @var RankService
     */
    private static $instance;

    /**
     * Redis对象
     * @var Redis
     */
    private $redis;

    /**
     * 初始化，获得redis连接
     */
    private function __construct() {
        $this->redis = TMPHPRedisClientFactory::getClient();
    }

    /**
     * 获得排行榜服务实例
     * @return RankService
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 将用户积分加入指定的排行榜
     * @param string $rankName 排行名
     * @param int $score 用户积分
     * @param string $uin 用户UIN
     * @return Ambigous <multitype:number, multitype:number string >
     */
    public function setScore($rankName, $score, $uin) {
        $params = array($score, $uin);

        return $this->callRedis('zAdd', $rankName, $params);
    }

    /**
     * 获取排行榜成员总数
     * @param string $rankName 排行名
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function getTotalMembers($rankName) {
        return $this->callRedis('zCard', $rankName);
    }

    /**
     * 获取某段积分区间内的成员（及分数）
     * @param string $rankName 排行名
     * @param int|string $start 分数区间起始值，若设置无穷小则填'-inf'
     * @param int|string $end 分数区间结束值，若设置无穷大则填'+inf'
     * @param array $options 设置是否同时返回积分名及limit，offset;
     *      array('withscores' => TRUE, //若设置为TRUE表示同时返回积分值
     *      'limit' => array($offset, $count)) //设置限制返回数量及偏移值，若设置则返回从$offset开始的$count个成员
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function getMembersByScoreRange($rankName, $start, $end, $options = array()) {
        $params = array($start, $end, $options);

        return $this->callRedis('zRangeByScore', $rankName, $params);
    }

    /**
     * 给某个用户增加积分
     * @param string $rankName 排行名
     * @param int|float $addScore 增加积分值
     * @param string $uin 用户UIN
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function addScore($rankName, $addScore, $uin) {
        $params = array($addScore, $uin);

        return $this->callRedis('zIncrBy', $rankName, $params);
    }

    /**
     * 根据当前分数获取用户排名
     * @param string $rankName 排行名
     * @param int|float $score 当前积分
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function getRankByScore($rankName, $score) {
        $start = $score;
        $end = '+inf';
        $params = array($start, $end);

        return $this->callRedis('zCount', $rankName, $params);
    }

    /**
     * 获取前N个用户
     * @param string $rankName 排行名
     * @param int $num 前N个用户
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function getTopN($rankName, $num) {
        $params = array(0, $num - 1, true);

        return $this->callRedis('zRevRange', $rankName, $params);
    }

    /**
     * 获取某个用户排名
     * @param string $rankName 排行名
     * @param string $uin 用户UIN
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function getRank($rankName, $uin) {
        $params = array($uin);
        $ret = $this->callRedis('zRevRank', $rankName, $params);
        if (0 == $ret['retcode']) {
            ++$ret['data']; //Redis返回的结果从0开始，实际排名需要加1
        }
        return $ret;
    }

    /**
     * 获取某个用户分数
     * @param string $rankName
     * @param string $uin
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function getScore($rankName, $uin) {
        $params = array($uin);

        return $this->callRedis('zScore', $rankName, $params);
    }

    /**
     * 从排行中删除某个用户
     * @param string $rankName
     * @param string $uin
     * @return Ambigous <multitype:number, multitype:number string unknown >
     */
    public function removeMember($rankName, $uin) {
        $params = array($uin);

        return $this->callRedis('zRem', $rankName, $params);
    }

    /**
     * 拼装排行榜的key，通过活动号+前缀区分
     * @param string $name
     * @return string
     */
    private static function formatKey($name) {
        return TMConfig::get('tams_id') . '_tae_rank_' . $name;
    }

    /**
     * 使用反射统一调用Redis方法
     * @param string $method 方法名
     * @param string $rankName 排行名称
     * @param string $params 方法参数
     * @return multitype:number string
     */
    private function callRedis($method, $rankName, $params = array()) {
        $result = array();
        $key = self::formatKey($rankName);
        array_unshift($params, $key);
        try {
            $reflectObj = new ReflectionObject($this->redis);
            if ($reflectObj->hasMethod($method)) {
                $reflectMethod = $reflectObj->getMethod($method);
                $ret = $reflectMethod->invokeArgs($this->redis, $params);
                if (FALSE !== $ret) {
                    $result = $ret;
                }
                else {
                    throw new TMRankException("Logic error.");
                }
            }
            else {
                throw new TMRankException('Method does not exist');
            }
        }
        catch (ReflectionException $re) {
            throw new TMRankException('Method params mismatch');
        }
        catch (RedisException $re) {
            throw new TMRankException('Failed to connect to server');
        }

        return $result;
    }
}