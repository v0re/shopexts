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
        if($upload)
	{
        return $id;
	}
	else {
		return false;
	}
}



 

function remove($ident){
$ret = ftp_delete($this->conn,FTP_SAVEDIR.$ident);
      return $ret;
    }
	 
function save_upload($file,&$url,$type,$addons){

       $id = $this->_get_ident($file,$type,$addons,$url,$path);
	   $upload = ftp_put($this->conn, $path, $file, FTP_BINARY);
          if($upload)
	{
        return $id;
	}
	else {
		return false;
	}
     
    }

function getFile($id){

        if($id && file_exists(FTP_REFURL.'/'.$id)){
         return FTP_REFURL.'/'.$id;
			
         }else{
            return true;
        }
    }


 function _get_ident($file,$type,$addons,&$url,&$path){

         $addons = implode('-',$addons);
        $dir = '/'.$type.'/'.date('Ymd').'/';

        $uri = $dir.substr(md5(($addons?$addons:$file).microtime()),0,16).ext_name(basename($addons?$addons:$file));
		
        $path = FTP_SAVEDIR.$uri;
       
        $url=FTP_REFURL.$uri;
        if(file_exists($path) && !unlink($path)){
            return false;
        }


        $dir = dirname($path);

		$d=explode('/',$dir);




 for ($i=1;$i<=count($d)-1;$i++)       //判断目录是否存在，不存在则建立目录
        {
            $ftp_path.="/".$d[$i];
            if(!@ftp_chdir($this->conn,$ftp_path)){
                @ftp_chdir($this->conn,"/");
                if(!@ftp_mkdir($this->conn,$ftp_path)){
                    return false;
                }
            } 
        }
		
        return $uri;
    }
	
  
}
?>
