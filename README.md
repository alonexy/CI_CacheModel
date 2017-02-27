# CI_CacheModel
CI框架请求式缓存model

```
本扩展类 基于 ci 2.1.4  
修改核心代码包cache不兼容5.4问题
提供代码请求实例   
提供session重写类  支持redis存储session


全局引用

修改  config/autoload.php     67行左右

/*
| -------------------------------------------------------------------
|  Auto-load Libraries
| -------------------------------------------------------------------
| These are the classes located in the system/libraries folder
| or in your application/libraries folder.
|
| Prototype:
|
|	$autoload['libraries'] = array('database', 'session', 'xmlrpc');
*/

$autoload['libraries'] = array('mredis','database','session','auth','form_validation');   //增加mredis  &&  session重写


注意：  本model结合redis使用
```
