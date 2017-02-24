<?php
class mredis
{
    private static $instance = null;

    public $redis = null;

    public $host  = null;

    public $port  = null;

    function __construct ()
    {    	
    	$CI = & get_instance();    	
    	$CI->config->load('redis');
    	$tmp = $CI->config->item('redis');    	
        $this->host = $tmp['host'];
        $this->port = $tmp['port'];
        $this->pwd =  $tmp['password'];

	    $this->redis = new redis();      
	    $this->redis->connect($this->host, $this->port); 
	    $this->auth($this->pwd); 
        // $this->redis->select(1);

    }
    public function auth($passwd){
        return $this->redis->auth($passwd);
    }

    /**
     * 设置值
     *
     * @param string $key
     *            KEY名称
     * @param string|array $value
     *            获取得到的数据
     * @param int $timeOut
     *            时间
     */
    public function set ($key, $value, $timeOut = 0)
    {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0)
            $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }
    public function geInfo(){
        $this->redis->info();
    }
    /**
     * 通过KEY获取数据
     *
     * @param string $key
     *            KEY名称
     */
    public function get ($key)
    {
        $result = $this->redis->get($key);
        return json_decode($result, TRUE);
    }
	
	/**
     * 通过KEY获取数据
     *
     * @param string $key
     *            KEY名称
     */
    public function mget ($key = array())
    {
        return $this->redis->mget($key);
    }
    
    public function getInfo(){
       return $this->redis->info();
    }
    /**
     * 删除一条数据
     *
     * @param string $key
     *            KEY名称
     */
    public function delete ($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * 清空数据
     */
    public function flushAll ()
    {
        return $this->redis->flushAll();
    }

    /**
     * 数据入队列
     *
     * @param string $key
     *            KEY名称
     * @param string|array $value
     *            获取得到的数据
     * @param bool $right
     *            是否从右边开始入
     */
    public function push ($key, $value, $right = true)
    {
        $value = json_encode($value);
        return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush(
                $key, $value);
    }

    /**
     * 数据出队列
     *
     * @param string $key
     *            KEY名称
     * @param bool $left
     *            是否从左边开始出数据
     */
    public function pop ($key, $left = true)
    {
        $val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
        return json_decode($val);
    }

    /**
     * 数据自增
     *
     * @param string $key
     *            KEY名称
     */
    public function increment ($key)
    {
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     *
     * @param string $key
     *            KEY名称
     */
    public function decrement ($key)
    {
        return $this->redis->decr($key);
    }

    /**
     * key是否存在，存在返回ture
     *
     * @param string $key
     *            KEY名称
     */
    public function exists ($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     * @param string|array $value
     *            获取得到的数据
     * @param int $timeOut
     *            时间
     */
    public function hset ($flag, $key, $value, $timeOut = 0)
    {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->hset($flag, $key, $value);
        return $retRes;
    }

    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     * @param string|array $value
     *            获取得到的数据
     * @param int $timeOut
     *            时间
     */
    public function hmset ($flag, $value, $timeOut = 0)
    {
        if(is_array($value) && !empty($value)){
            $retRes = $this->redis->HMSET($flag, $value);
            return $retRes;
        }
        return false;
    }
    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string|array $field
     *            获取得到的数据
     * @param int $timeOut
     *            时间
     */
    public function hmget ($flag, $field, $timeOut = 0)
    {
        $retRes = $this->redis->HMGET($flag, $field);
        return $retRes;
    }
    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     */
    public function hget ($flag, $key)
    {
        $result = $this->redis->get($flag, $key);
        return json_decode($result, TRUE);
    }

    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     */
    public function hhget ($flag, $key)
    {
        return $this->redis->hget($flag, $key);
    }

    /**
     * 设置值
     * 返回名称为h的hash中元素个数
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     */
    public function hlen ($flag)
    {
        return $this->redis->hlen($flag);
    }
    public function lrange ($key)
    {
        return $this->redis->lrange($key,0,-1);
    }
     public function lrem ($key,$value,$count=1)
    {
        return $this->redis->lrem($key,$value,$count);
    }
    /**
     * 设置值
     * 删除名称为h的hash中键为key1的域
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     */
    public function hdel ($flag, $key)
    {
        return $this->redis->hdel($flag, $key);
    }

    /**
     * 设置值
     * 返回名称为key的hash中所有键
     *
     * @param string $flag
     *            hash名称
     */
    public function hkeys ($flag)
    {
        return $this->redis->hkeys($flag);
    }

    /**
     * 设置值
     * 返回名称为h的hash中所有键对应的value
     *
     * @param string $flag
     *            hash名称
     */
    public function hvals ($flag)
    {
        return $this->redis->hvals($flag);
    }

    /**
     * 设置值
     * 返回名称为h的hash中所有的键（field）及其对应的value
     *
     * @param string $flag
     *            hash名称
     */
    public function hgetall ($flag)
    {
        return $this->redis->hgetAll($flag);
    }

    /**
     * 设置值
     * 名称为h的hash中是否存在键名字为a的域
     *
     * @param string $flag
     *            hash名称
     *            
     */
    public function hexists ($flag, $key)
    {
        return $this->redis->hexists($flag, $key);
    }

    /**
     * 设置值
     *
     * @param string $key
     *            KEY名称
     * @param string $value
     *            获取得到的数据
     */
    public function sadd ($key, $value)
    {
        return $this->redis->sadd($key, $value);
    }

    /**
     * 删除一条数据
     *
     * @param string $key
     *            KEY名称
     * @param $value 值            
     */
    public function srem ($key, $value)
    {
        return $this->redis->srem($key, $value);
    }

    /**
     * value是否存在key，存在返回ture
     *
     * @param string $key
     *            KEY名称
     */
    public function sismember ($key, $value)
    {
        return $this->redis->sismember($key, $value);
    }

    /**
     * 返回名称为key的set的元素个数
     *
     * @param string $key
     *            名称
     */
    public function scard ($key)
    {
        return $this->redis->scard($key);
    }

    /**
     * 返回名称为key的set的所有元素
     *
     * @param string $key
     *            名称
     */
    public function smembers ($key)
    {
        return $this->redis->smembers($key);
    }

    /**
     * ******** zadd operation**********
     */
    /**
     * 返回名称为key的set的所有元素
     *
     * @param string $key
     *            名称
     */
    public function zadd ($key, $score, $value)
    {
        return $this->redis->zadd($key, $score, $value);
    }
    
    /*
     * 对指定元素索引值的增减,改变元素排列次序
     */
    public function zincrby ($key, $score, $value)
    {
        return $this->redis->zincrby($key, $score, $value);
    }
    
    /*
     * 移除指定元素
     */
    public function zrem ($key, $value)
    {
        return $this->redis->zrem($key, $value);
    }
    
    /*
     * zrange 按位置次序返回表中指定区间的元素 
     * $redis->zrange(‘zset1′,0,1); //返回位置0和1之间(两个)的元素
     * $redis->zrange(‘zset1′,0,-1);//返回位置0和倒数第一个元素之间的元素(相当于所有元素)
     */
    public function zrange ($key, $score, $value)
    {
        return $this->redis->zrange($key, $score, $value);
    }
    
    /*
     * zrangebyscore/zrevrangebyscore 按顺序/降序返回表中指定索引区间的元素
     * $redis->zadd(‘zset1′,3,’ef’); $redis->zadd(‘zset1′,5,’gh’);
     * $redis->zrangebyscore(‘zset1′,2,9); //返回索引值2-9之间的元素 array(‘ef’,'gh’)
     * //参数形式 $redis->zrangebyscore(‘zset1′,2,9,’withscores’);
     * //返回索引值2-9之间的元素并包含索引值 array(array(‘ef’,3),array(‘gh’,5))
     * $redis->zrangebyscore(‘zset1′,2,9,array(‘withscores’
     * =>true,’limit’=>array(1, 2))); //返回索引值2-9之间的元素,’withscores’
     * =>true表示包含索引值; ‘limit’=>array(1,
     * 2),表示最多返回2条,结果为array(array(‘ef’,3),array(‘gh’,5))
     */
    public function zrangebyscore ($key, $score, $value)
    {
        return $this->redis->zrangebyscore($key, $score, $value);
    }
    
    
    /*
     * zunionstore/zinterstore 将多个表的并集/交集存入另一个表中
     * $redis->zunionstore(‘zset3′,array(‘zset1′,’zset2′,’zset0′));
     * //将’zset1′,’zset2′,’zset0′的并集存入’zset3′ //其它参数
     * $redis->zunionstore(‘zset3′,array(‘zset1′,’zset2′),array(‘weights’ =>
     * array(5,0)));//weights参数表示权重，其中表示并集后值大于5的元素排在前，大于0的排在后
     * $redis->zunionstore(‘zset3′,array(‘zset1′,’zset2′),array(‘aggregate’ =>
     * ‘max’));//’aggregate’ => ‘max’或’min’表示并集后相同的元素是取大值或是取小值
     */
    public function zunionstore ($key, $array1, $array2)
    {
        return $this->redis->zunionstore($key, $array1, $array2);
    }
    
    /*
     * zcount 统计一个索引区间的元素个数 
     * $redis->zcount(‘zset1′,3,5);//2
     * $redis->zcount(‘zset1′,’(3′,5));//’(3′表示索引值在3-5之间但不含3,同理也可以使用’(5′表示上限为5但不含5
     */
    public function zcount ($key, $array1)
    {
        return $this->redis->zcount($key, $array1);
    }
    
    /*
     * zcard 统计元素个数 $redis->zcard(‘zset1′);//4
     */
    public function zcard ($key)
    {
        return $this->redis->zcard($key);
    }
    
    /*
     * zscore 查询元素的索引 $redis->zscore(‘zset1′,’ef’);//3
     */
    public function zscore ($key, $value)
    {
        return $this->redis->zscore($key, $value);
    }
    
    /*
     * //zremrangebyscore 删除一个索引区间的元素 $redis->zremrangebyscore(‘zset1′,0,2);
     * //删除索引在0-2之间的元素(‘ab’,'cd’),返回删除元素个数2
     */
    public function zremrangebyscore ($key, $value1, $value2)
    {
        return $this->redis->zremrangebyscore($key, $value1, $value2);
    }
    
    /*
     * zrank/zrevrank 返回元素所在表顺序/降序的位置(不是索引)
     * $redis->zrank(‘zset1′,’ef’);//返回0,因为它是第一个元素;zrevrank则返回1(最后一个)
     */
    public function zrank ($key, $value)
    {
        return $this->redis->zrank($key, $value);
    }
    
    public function setnx ($key, $value)
    {
        return $this->redis->setnx($key, $value);
    }
    /*
     * zremrangebyrank 删除表中指定位置区间的元素 $redis->zremrangebyrank(‘zset1′,0,10);
     * //删除位置为0-10的元素,返回删除的元素个数2
     */
    public function zremrangebyrank ($key, $value1, $value2)
    {
        return $this->redis->zremrangebyrank($key, $value1, $value2);
    }
    
    public function lpoplpush($key,$start,$end){
    	$result = $this->redis->lrange($key,$start,$end);
    	return $result;

    }
    
    public function llen($key){
    	return  $this->redis->llen($key);
    }
    public function ttl($key){
    	return  $this->redis->ttl($key);
    }
	 public function expireAt($key,$expiretime){
    	return  $this->redis->expireAt($key,$expiretime);
    }
    public function expire($key,$expiretime){
        return  $this->redis->expire($key,$expiretime);
    }
}
?>
