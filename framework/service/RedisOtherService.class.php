<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 10/23/17
 * Time: 10:12 PM
 */

class RedisOtherService{

    /**
     * @author jason
     * @param 单利模式
     */
    private static $instance;

    private $redis;

    private function __construct()
    {
        $this->redis = TMPHPRedisClientFactory::getClient();
    }

    public static function getInstance(){
        if(empty(self::$instance)){
                self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * 使用反射统一调用Redis方法
     * @param string $method 方法名
     * @param string $rankName 排行名称
     * @param string $params 方法参数
     * @return multitype:number string
     */
    private function callRedis($method, $keyName, $params = array()) {
        $result = array();
        $key = self::formatKey($keyName);
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
    /**
     * 拼装incr的key，通过活动号+前缀区分
     * @param string $name
     * @return string
     */
    private static function formatKey($name) {
        return TMConfig::get('tams_id') . '_tae_key_' . $name;
    }
    /**
     *递增数列
     *@param string $key $increment 递增值
     *@return int
     */
    public function increment($key,$increment = 1){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->incrBy(self::formatKey($key),$increment);
    }

    /**
     *递减数列
     *@param string $key $increment 递减值
     *@return int
     */
    public function decrement($key,$increment = 1){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->decrBy(self::formatKey($key),$increment);
    }
    /**
     *获取递增数
     *@param string $key
     *@return int
     */
    public function getIncr($key){
        if(empty($this->redis)){
            return false;
        }
        return intval($this->redis->get(self::formatKey($key)));
    }
    /**
     *获取value
     *@param string $key
     *@return int
     */
    public function get($key){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->get(self::formatKey($key));
    }
    /**
     *redis缓存
     * @param $param 格式化的字符串  serialize(json_encode($param))$timeout 超时时间
     *@return string
     */
    public function cset($key,$param,$timeout = 0){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->setex(self::formatKey($key),$timeout,$param);
    }
    #-----hash----#
    /**
     *hash 设置不同的消息体的数量
     */
    public function setNoticMessage($key,$hashkey,$nums = 1){
        if(empty($this->redis)){
            return false;
        }
        $this->redis->hIncrBy(self::formatKey($key), $hashkey, $nums);
    }
    /**
     *获取不同消息类型数量
     */
    public function getNoticeMessage($key,$hashkey){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->hGet(self::formatKey($key),$hashkey);
    }
    /**
     *获取所有的消息类型的数量
     */
    public function getAllNoticeMessage($key){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->hGetAll(self::formatKey($key));
    }
    /**
     *
     *删除消息
     */
    public function delNoticeMessage($key,$hashkey){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->hDel(self::formatKey($key),$hashkey);
    }
    /**
     *set 没有排序的集合
     *
     */
    public function saddSet($key,$member){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->sAdd(self::formatKey($key),$member);
    }
    /**
     *
     *获取无排序的集合数据
     */
    public function getSmembers($key){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->sMembers(self::formatKey($key));
    }
    /**
     *获取无序集合的数量
     */
    public function getScardCount($key){
        if(empty($this->redis)){
            return false;
        }
        return intval($this->redis->sCard(self::formatKey($key)));
    }
    /**
     *移除set集合中的一个元素
     */
    public function sremMembers($key,$member){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->sRem(self::formatKey($key),$member);
    }
    /**
     *
     *获取两者的差异
     *
     */
    public function getSdiff($key,$keyn){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->sDiff(self::formatKey($key),$keyn);
    }
    /**
     *
     *获取两者数据的交集
     */
    public function getSsinter($key,$keyn){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->sInter(self::formatKey($key),$keyn);
    }
    /**
     *
     *获取集合的并集
     */
    public function getSunion($key,$keyn){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->sUnion(self::formatKey($key),$keyn);
    }

    /**
     *
     * list 集合
     */
    public function lpush($key,$member){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->lPush(self::formatKey($key),$member);
    }

    public function lrange($key,$start=0,$end=-1){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->lRange(self::formatKey($key),$start,$end);
    }
    public function lpushx($key,$member){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->lPushx(self::formatKey($key),$member);
    }
    /**
     *淡出元素
     *
     */
    public function lpop($key){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->lPop(self::formatKey($key));
    }

    public function llen($key){
        if(empty($this->redis)){
            return false;
        }
        return $this->redis->llen(self::formatKey($key));
    }
}