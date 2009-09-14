<?php
/**
 * Storager:图片文件的存储方式
 * 本插件可用来将图片上传到指定的ftp服务器上
 *
 * 系统配置项：
 * system.storager.ftp.url  ->  访问地址前缀
 * system.storager.ftp.server  ->  访问地址前缀
 * system.storager.ftp.uname -> ftp用户名
 * system.storager.ftp.password  -> ftp密码
 * system.storager.ftp.dir  -> 上传路径
 * 
 */
/* define('WITH_STORAGER','ftp_storage');

define('FTP_SERVER',"124.74.193.214");
define('FTP_UNAME',"webmaster@shop25457.p02.shopex.cn");
define('FTP_PASSWD',"82ab48583ad5");
define('FTP_SAVEDIR',"/ftp_storage");
define('FTP_REFURL','http://shop25457.p02.shopex.cn/ftp_storage/');
*/
//define(__FTP_SERVER__,"124.74.193.214");
//define(__FTP_UNAME__,"webmaster@shop25457.p02.shopex.cn");
//define(__FTP_PASSWD__,"82ab48583ad5");
//define(__FTP_DIR__,"/ftp_storage");
/*
define(__FTP_SERVER__,"192.168.0.114");
define(__FTP_UNAME__,"hjx");
define(__FTP_PASSWD__,"hjxisking");
define(__FTP_DIR__,"/shopex/");
 */
class ftp_storage{

 

function save($file,&$url,$type,$addons){

//建立FTP连接
$conn = ftp_connect(FTP_SERVER) or die ("不能连接");


//登陆FTP
       ftp_login($conn, FTP_UNAME, FTP_PASSWD) or die("不能登陆");
  
$id = $this->_get_ident($file,$type,$addons,$url,$path);
// error_log(print_r($url,true),3,__FILE__.'URL.log');
  $dir=explode('/',$path);                                      //获取要产生目录的路径
  //error_log(print_r($dir,true),3,__FILE__.'dir.log');
 ftp_mkdir($conn,'/ftp_storage/images/goods/'.$dir[4].'/');    //创建新目录
  $upload = ftp_put($conn, $path, $file, FTP_BINARY);
    ftp_close($conn_id);
            return $id;
       
	}




    function remove($ident){
        $conn_id = ftp_connect(__FTP_SERVER__);
        $login_result = ftp_login($conn_id, __FTP_UNAME__,__FTP_PASSWD__);
        $ret = ftp_delete($conn_id,__FTP_DIR__ . "/" .$ident);
        ftp_close($conn_id);
        return $ret;
    }
	 
   function save_upload($file,&$url,$type,$addons){

        $id = $this->_get_ident($file,$type,$addons,$url,$path);
		
     //建立FTP连接
$conn = ftp_connect(FTP_SERVER) or die ("不能连接");


//登陆FTP
       ftp_login($conn, FTP_UNAME, FTP_PASSWD) or die("不能登陆");
	   $upload = ftp_put($conn, $path, $file, FTP_BINARY);
  //$url=FTP_REFURL.FTP_SAVEDIR.$addons[0];
     
			 
            return $id;
     
    }

	  function getFile($id){
        if($id && file_exists(FTP_REFURL.'images'.'/'.$id)){
            return FTP_REFURL.'images'.'/'.$id;
			//return FTP_REFURL.$id;
         }else{
            return true;
        }
    }


 function _get_ident($file,$type,$addons,&$url,&$path){

        $addons = implode('-',$addons);
        $dir = '/'.$type.'/'.date('Ymd').'/';

        $uri = $dir.substr(md5(($addons?$addons:$file).microtime()),0,16).ext_name(basename($addons?$addons:$file));
		error_log(print_r($uri,true),3,__FILE__.'uri.log');
        $path = FTP_SAVEDIR.'images'.$uri;
        //$url = 'images'.$uri;
        $url=FTP_REFURL.'images'.$uri;
        if(file_exists($path) && !unlink($path)){
            return false;
        }

        $dir = dirname($path);
        if(!is_dir($dir)){
            if(!ftp_mkdir($dir)){
                return false;
            }
        }

        return $uri;
    }
	
  
}
?>
