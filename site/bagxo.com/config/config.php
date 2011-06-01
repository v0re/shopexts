<?php
/**
 * 网店配置模板
 *
 * 版本 $Id: config.sample.php 20959 2009-03-11 13:26:53Z flaboy $
 * 配置参数讨论专贴 http://www.shopex.cn/bbs/thread-61957-1-1.html
 */


// ** 数据库配置 ** //
define('DB_USER', 'root');     // 数据库用户名
define('DB_PASSWORD', 'shopex'); // 数据库密码
define('DB_NAME', 'bagxocom');    // 数据库名
define('DB_HOST', '127.0.0.1');    // 数据库服务器 -- 99% 的情况下您不需要修改此参数
define('DEBUG_CSS',true);

// You can have multiple installations in one database if you give each a unique prefix
define ('STORE_KEY', '2b748c4a4f2707524bd29b600e202be9'); //密钥
define('DB_PREFIX', 'sdb_');
define ('LANG', '');

define ('WITHOUT_CACHE',false);

/* 以下为调优参数 */
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('DEBUG_CODE',false);
define('DEBUG_JS',false);
define('BASE_DIR', dirname(__FILE__).'/../');
define('CORE_DIR', BASE_DIR.'/core');
define('HOME_DIR', BASE_DIR.'/home'); //您可以更改这个目录的位置来获得更高的安全性
define('PLUGIN_DIR', BASE_DIR.'/plugins');
define('THEME_DIR', BASE_DIR.'/themes');
define('MEDIA_DIR', BASE_DIR.'/images');
define('PUBLIC_DIR', BASE_DIR.'/public');  //同一主机共享文件
define('CERT_DIR', BASE_DIR.'/cert');
define('DB_OLDVERSION',false); //mysql版本小于4.1.1时，设置为true
define('DEFAULT_LOCAL','mainland');
define('SECACHE_SIZE','15M'); //缓存大小,最大不能超过1G
//define('TEMPLATE_MODE','database');
define("MAIL_LOG",false);
define('DEFAULT_INDEX','');
define('SERVER_TIMEZONE','8'); //服务器时区
//define('APP_ROOT_PHP','index.php'); //iis 5
define('WITHOUT_FLOCK',false); //当你使用iis+isapi时，修改成true
@ini_set('memory_limit','256M');
define('WITHOUT_GZIP',false);
//前台禁ip
//define('BLACKLIST','10.0.0.0/24 192.168.0.1/24');

//数据库集群.
//define('DB_SLAVE_NAME',DB_NAME);
//define('DB_SLAVE_USER',DB_USER);
//define('DB_SLAVE_PASSWORD',DB_PASSWORD);
//define('DB_SLAVE_HOST',DB_HOST);


//确定服务器支持htaccess文件时，可以打开下面两个参数获得加速。
//define ('GZIP_CSS',true);
//define ('GZIP_JS',true);

//可以选择缓存方式apc 或者 memcached
//define('CACHE_METHOD','cacheApc');
//======================================
//define('CACHE_METHOD','memcached');
//======================================
//define('CACHE_METHOD','cachedir'); //使用单个文件存放，稳定，但无法控制文件大小


/* 日志 */
//define('LOG_LEVEL',E_ERROR);
//define('LOG_FILE',HOME_DIR.'/logs/{date}/{ip}.php');    //按日期分目录，每个ip一个日志文件。扩展名是php防止下载。
//define('LOG_HEAD_TEXT','<'.'?php exit()?'.'>');    //log文件头部放上exit()保证无法下载。
//define('LOG_FORMAT',"{gmt}\t{request}\t{code}");

//======================================
//define('WITH_MEMCACHE',true);
//define('MEMCACHED_HOST','192.168.0.230');
//define('MEMCACHED_PORT','11211');
//======================================

//define('DISABLE_SYS_CALL',1); //禁止运行安装
//define('THEME_STORAGE','db'); //使用数据库存放改动过的模板
define('CUSTOM_CORE_DIR', BASE_DIR.'/ccore');
@ini_set('max_execution_time','300');