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
class ftp_storage{

private $conn;

//构造函数，建立FTP连接，登陆FTP
 function ftp_storage(){

//建立FTP连接
          $this->conn = ftp_connect(FTP_SERVER) or die ("不能连接");
//登陆FTP
          ftp_login($this->conn, FTP_UNAME, FTP_PASSWD) or die("不能登陆");
 }
 

function save($file,&$url,$type,$addons){

        $id = $this->_get_ident($file,$type,$addons,$url,$path);
        $upload = ftp_put($this->conn, $path, $file, FTP_BINARY);
        //ftp_close($conn_id);
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
	   $upload = ftp_put($this->conn, $path, $file, FTP_BINARY);
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
        if(!is_dir($this->conn,$dir)){        //判断目录时候存在，不存在则新建目录
            //if(!ftp_mkdir($this->conn,$dir)){
               // return false;
			   ftp_mkdir($this->conn,$dir);
            //}
        }

        return $uri;
    }
	
  
}
?>
