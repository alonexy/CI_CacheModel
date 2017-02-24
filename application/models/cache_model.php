<?php

/**
 * 缓存模型
 *
 * @author passkey<hpl1124@126.com>
 * @version $Id: cache_model.php 0000 2014-06-04 15:44:30Z passkey $
 */
class Cache_model extends MY_Model {

    /**
     * 缓存开关
     *
     * @var boolean
     */
    private $cache_enable = true;

    /**
     * 缓存实例
     *
     * @var object
     */
    private $cache_instance = null;

    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct();
        if ($this->cache_enable) {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $this->cache_instance = $this->cache->redis;
        }

    }

    /**
     * 获取城市
     *
     * @return array
     */
    public function getArealist() {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = "arealist";
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('home/home');
                $value = $this->home->operArea();
                $this->cache_instance->save($key, $value, 3600);
            }
        } else {
            $this->load->model('home/home');
            $value = $this->home->operArea();
        }
        return $value;
    }
    /*
    *stock  缓存
    */
    public function Cache_stock($key='stock',$r_type = 2,
                                $symbol = [],
                                $market = [],
                                $stime  = '',
                                $etime  = '',
                                $query  = [],
                                $q_type = 0,
                                $desc   = 0,
                                $num    = 0,
                                $page   = 1) {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = REDIS_ROOT_PATH.':'.WSTOCK_PATH.':'.$key;
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->stock($r_type,$symbol, $market, $stime, $etime, $query, $q_type, $desc, $num, $page);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        } else {
            $this->load->model('Wstock_model');
            $value = $this->Wstock_model->stock($r_type,$symbol,$market,$stime,$etime, $query,$q_type,$desc, $num,$page);
        }
        if (!empty($value[ 'errcode' ]))
        {
            if($value[ 'errcode' ] == 4003)
            {
                $sleep_time = rand(1,3);
                sleep($sleep_time);
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->stock($r_type,$symbol, $market, $stime, $etime, $query, $q_type, $desc, $num, $page);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }

        }
        if(empty($value) || $value == null || $value == 'null')
        {
            $value = $this->Wstock_model->stock($r_type,$symbol, $market, $stime, $etime, $query, $q_type, $desc, $num, $page);
            $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
        }
        return $value;
    }

    /**
     * kline  缓存
     */
    public function Cache_kline($key='kline_',$r_type=2,
                                $symbol   ,
                                $return_t = 0,
                                $stime    = '',
                                $etime    = '',
                                $qt_type  = 1,
                                $d_type   = 1,
                                $fq       = 0,
                                $fqDate   = [],
                                $board_w  = 0,
                                $board_h  = 0,
                                $buy      = '',
                                $sale     = '',
                                $remark   = '',
                                $coordinate= 0,
                                $q_type  = 0,
                                $num     = 0,
                                $desc    = 0,
                                $retime  = 1) {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = REDIS_ROOT_PATH.':'.WSTOCK_PATH.':'.$key;
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->kline($r_type,$symbol,$return_t,$stime,$etime,$qt_type,$d_type,$fq,$fqDate,$board_w,$board_h,$buy,$sale,$remark,$coordinate,$q_type,$num,$desc);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME * $retime);
            }
        } else {
            $this->load->model('Wstock_model');
            $value = $this->Wstock_model->kline($r_type,$symbol,$return_t,$stime,$etime,$qt_type,$d_type,$fq,$fqDate,$board_w,$board_h,$buy,$sale,$remark,$coordinate,$q_type,$num,$desc);
        }
        if (!empty($value[ 'errcode' ]))
        {
            if($value[ 'errcode' ] == 4003)
            {
                $sleep_time = rand(1,3);
                sleep($sleep_time);
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->kline($r_type,$symbol,$return_t,$stime,$etime,$qt_type,$d_type,$fq,$fqDate,$board_w,$board_h,$buy,$sale,$remark,$coordinate,$q_type,$num,$desc);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME * $retime);
            }

        }
        return $value;
    }

    /**
     * thetimetrend 缓存
     */
    public function Cache_thetimetrend($key='thetimetrend_',$r_type    = 2,
                                       $symbol    = '',
                                       $mean_type = 0,
                                       $board_w   = 0,
                                       $board_h   = 0,
                                       $date      = '',
                                       $bgcolor   = 0 ) {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = REDIS_ROOT_PATH.':'.WSTOCK_PATH.':'.$key;
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->thetimetrend($r_type, $symbol, $mean_type, $board_w, $board_h, $date, $bgcolor);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        } else {
            $this->load->model('Wstock_model');
            $value = $this->Wstock_model->thetimetrend($r_type, $symbol, $mean_type, $board_w, $board_h, $date, $bgcolor);
        }
        if (!empty($value[ 'errcode' ]))
        {
            if($value[ 'errcode' ] == 4003)
            {
                $sleep_time = rand(1,3);
                sleep($sleep_time);
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->thetimetrend($r_type, $symbol, $mean_type, $board_w, $board_h, $date, $bgcolor);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        }
        return $value;

    }

    /**
     * @details  缓存
     */
    public function Cache_details($key='details_',$r_type=2,$symbol='',$stime='',$etime='',$query=[],$num=0)
    {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = REDIS_ROOT_PATH.':'.WSTOCK_PATH.':'.$key;
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->details($r_type,$symbol,$stime,$etime,$query,$num);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        } else {
            $this->load->model('Wstock_model');
            $value = $this->Wstock_model->details($r_type,$symbol,$stime,$etime,$query,$num);
        }
        if (!empty($value[ 'errcode' ]))
        {
            if($value[ 'errcode' ] == 4003)
            {
                $sleep_time = rand(1,3);
                sleep($sleep_time);
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->details($r_type,$symbol,$stime,$etime,$query,$num);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        }
        return $value;
    }
    /**
     * @finance  缓存
     */
    public function Cache_finance($key='finance_',$r_type = 2,
                                  $symbol = [],
                                  $stime  = '',
                                  $etime  = '',
                                  $query  = [],
                                  $q_type = 0,
                                  $desc   = 0,
                                  $num    = 0,
                                  $page   = 1)
    {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = REDIS_ROOT_PATH.':'.WSTOCK_PATH.':'.$key;
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->finance($r_type,$symbol, $stime, $etime, $query, $q_type, $desc, $num, $page);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        } else {
            $this->load->model('Wstock_model');
            $value = $this->Wstock_model->finance($r_type,$symbol, $stime, $etime, $query, $q_type, $desc, $num, $page);
        }
        return $value;
    }

    /**
     * @splits_m  缓存
     */
    public function Cache_splits_m($key='splits_m_',$r_type = 2,
                                   $symbol = [],
                                   $stime  = '',
                                   $etime  = '',
                                   $q_type = 0,
                                   $desc   = 0,
                                   $num    = 0,
                                   $page   = 1)
    {
        //判断是否开启缓存
        if ($this->cache_enable && $this->cache_instance->is_supported()) {
            $key = REDIS_ROOT_PATH.':'.WSTOCK_PATH.':'.$key;
            $value = $this->cache_instance->get($key);

            if (empty($value)) { //没有缓存
                $this->load->model('Wstock_model');
                $value = $this->Wstock_model->splits_m($r_type,$symbol, $stime, $etime, $q_type, $desc, $num, $page);
                $this->cache_instance->save($key, $value, WSTOCK_DATA_TIME);
            }
        } else {
            $this->load->model('Wstock_model');
            $value = $this->Wstock_model->splits_m($r_type,$symbol, $stime, $etime, $q_type, $desc, $num, $page);
        }
        return $value;
    }
    //end+++
}
