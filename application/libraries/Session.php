<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 11:30 2015/5/5
 * @class  session 重写类
 * @author Alonexy
 * @author email 961610358@qq.com
 */

class MY_Session
{
	static public $_redis = null;
	static public $_times = null;
	private $_flash_key = 'flash'; 

	/**
	 * @构造函数
	 * @param void
	 * @return void
	 */
	public function __construct()
	{
		$CI = & get_instance(); 
		$CI->load->library('mredis'); 
		self::$_times = time();
		self::$_redis = new mredis();
		//echo class_exists('mredis');
		//self::$_redis->connect(SESS_REDIS_HOST, SESS_REDIS_PORT);
		$this->funSessInit();
	}

	/*
	 * @初始化Session信息
	 * @param  void
	 * @return void
	 */
	public function funSessInit()
	{
		//重写session配置文件
		ini_set('session.gc_maxlifetime', SESS_PERIOD);		//服务端生存周期
		ini_set('session.cookie_lifetime', 0);	//客户端的生存周期
		ini_set('session.name', SESS_COOKIE_NAME);		//SESSION在客户端的名称
		ini_set('session.user_trans_sid',0);			//不使用POST/GET提交
		ini_set('session.user_cookies',1);			//使用cookie保存SESSION数据
		ini_set('session.cookie_path','/');			//使用根路径保存
		ini_set('session.cookie_domain', SESS_COOKIE_DOMAIN);	//保存的域名

		session_module_name('user');

		//定义操作函数
		session_set_save_handler(
				array(__CLASS__, 'open'), 
				array(__CLASS__, 'close'), 
				array(__CLASS__, 'read'), 
				array(__CLASS__, 'write'), 
				array(__CLASS__, 'destroy'), 
				array(__CLASS__, 'gc')
				);
		session_start();
		$sess_id = session_id();
		if(!$sess_id){
			$sess_id = $_COOKIE[SESS_COOKIE_NAME];
			session_id($sess_id);
			session_start();
		}
	}

	/*
	 * @打开操作
	 * @param  void
	 * @return void
	 */
	public function open()
	{
		return true;
	}

	/**
	 * @读取SESSION信息
	 * @param string $key
	 * @return string || false
	 */
	public function read($key)
	{
		$sess_key = SESS_PREFIX . $key;
		//是否在有效期内
		$end_time = self::$_redis->ttl($sess_key);
		if($end_time != -1){
			return self::$_redis->get($sess_key);
		}
	}

	/**
	 * @关闭操作
	 * @param  void
	 * @return void
	 */
	public function close()
	{
		return true;
	}

	/**
	 * @写cookie到redis
	 * @param string key
	 * @param object $val
	 * @return bool
	 */
	public function write($key, $val)
	{
		$sess_key  = SESS_PREFIX . $key;
		$sess_data = self::$_redis->get($sess_key);
		$expire_time = self::$_times + SESS_PERIOD;
		if(empty($sess_data) && !empty($val)){
			self::$_redis->set($sess_key, $val);
			self::$_redis->expireAt($sess_key, $expire_time);
		}else{
			$ttl = self::$_redis->ttl($sess_key);
			if($ttl != -1 && ($ttl < self::$_times)){
				self::$_redis->set($sess_key, $val);
				self::$_redis->expireAt($sess_key, $expire_time);
			}
		}

		return true;
	}

	/**
	 * @删除SESSION
	 * @param string $key
	 * @return bool
	 */
	public function destroy($key)
	{
		$sess_key = SESS_PREFIX . $key;
		return self::$_redis->delete($sess_key);
	}

	/**
	 * @SESSION的垃圾回收
	 * @param string $life_time
	 * @return bool
	 */
	public function gc($life_time)
	{
		return true;
	}

	/*---------------网站功能性函数-----------------*/
	public function userdata($item, $subitem=null)
	{
		if($subitem){
			if($subitem == 'session_id'){
				return session_id();
			}else{
				if(isset($_SESSION[$item])){
					if(is_array($_SESSION[$item])) return (!isset($_SESSION[$item][$subitem])) ? false : $_SESSION[$item][$subitem];
					if(is_object($_SESSION[$item])) return (!isset($_SESSION[$item]->$subitem)) ? false : $_SESSION[$item]->$subitem;
					return false;
				}
			}
		}

		// this item is not in an array
		else {
			if($item == 'session_id'){ //added for backward-compatibility
				return session_id();
			} else {
				return ( ! isset($_SESSION[$item])) ? false : $_SESSION[$item];
			}
		}

	}

	/**
	 * Sets session attributes to the given values
	 */
	function set_userdata($newdata = array(), $newval = '')
	{
		if(is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$_SESSION[$key] = $val;
			}
		}
	}

	function unset_userdata($newdata = array())
	{
		if(is_string($newdata)){
			$newdata = array($newdata => '');
		}
		if(count($newdata)>0){
			foreach ($newdata as $key => $val){
				unset($_SESSION[$key]);
			}
		}
	}

	/**
	 * PRIVATE: Internal method - removes "flash" session marked as 'old'
	 */
	function _flashdata_sweep()
	{
		foreach ($_SESSION as $name => $value)
		{
			$parts = explode(':old:', $name);
			if (is_array($parts) && count($parts) == 2 && $parts[0] == $this->_flash_key)
			{
				$this->unset_userdata($name);
			}
		}
	}

	/**
	 * PRIVATE: Internal method - marks "flash" session attributes as 'old'
	 */
	function _flashdata_mark()
	{
		foreach ($_SESSION as $name => $value)
		{
			$parts = explode(':new:', $name);
			if (is_array($parts) && count($parts) == 2)
			{
				$new_name = $this->_flash_key.':old:'.$parts[1];
				$this->set_userdata($new_name, $value);
				$this->unset_userdata($name);
			}
		}
	}

	/**
	 * Keeps existing "flash" data available to next request.
	 */
	function keep_flashdata($key)
	{
		$old_flash_key = $this->_flash_key.':old:'.$key;
		$value = $this->userdata($old_flash_key);

		$new_flash_key = $this->_flash_key.':new:'.$key;
		$this->set_userdata($new_flash_key, $value);
	}

	/**
	 * Returns "flash" data for the given key.
	 */
	function flashdata($key)
	{
		$flash_key = $this->_flash_key.':old:'.$key;
		return $this->userdata($flash_key);
	}

	/**
	 * Sets "flash" data which will be available only in next request (then it will
	 * be deleted from session). You can use it to implement "Save succeeded" messages
	 * after redirect.
	 */
	function set_flashdata($key, $value)
	{
		$flash_key = $this->_flash_key.':new:'.$key;
		$this->set_userdata($flash_key, $value);
	}

	/**
	 * Returns all session data
	 */    
	function all_userdata()
	{
		if (isset($_SESSION['session_id'])) { //added for backward-compatibility
			$_SESSION['session_id'] = session_id();
		}
		return $_SESSION;
	}

}

