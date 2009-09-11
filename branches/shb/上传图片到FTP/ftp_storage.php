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
define(__FTP_SERVER__,"124.74.193.214");
define(__FTP_UNAME__,"webmaster@shop25457.p02.shopex.cn");
define(__FTP_PASSWD__,"82ab48583ad5");
define(__FTP_DIR__,"/ftp_storage");
/*
define(__FTP_SERVER__,"192.168.0.114");
define(__FTP_UNAME__,"hjx");
define(__FTP_PASSWD__,"hjxisking");
define(__FTP_DIR__,"/shopex/");
 */
class ftp_storage{

 /* function save($path='',$url,$id){

        $system = &$GLOBALS['system'];
        
        $fp = fopen(BASE_DIR.$path, 'rb');
   // error_log(print_r($id."-----------",true),3,__FILE__.'b.log');
        $id = $path;
        $url = "http://" . __FTP_SERVER__ . "/" . $path;
        
        $ftp_path = __FTP_DIR__ . "/" . $path;
        $file = $ftp_path;
          
        // connect to the server
      $conn = ftp_connect("124.74.193.214") or die ("Cannot initiate connection to host");

// send access parameters

     ftp_login($conn, "webmaster@shop25457.p02.shopex.cn", "82ab48583ad5") or die("Cannot login");


        /*$d=split("/", $ftp_path);
        $ftp_path="";

        if(substr(__FTP_DIR__,0,1) == "/"){
            $i = 1;
        }
        else{
            $i = 0;
        }
        
        for ($i;$i<count($d)-1;$i++)
        {
            $ftp_path.="/".$d[$i];
            if(!@ftp_chdir($conn_id,$ftp_path)){
                @ftp_chdir($conn_id,"/");
                if(!@ftp_mkdir($conn_id,$ftp_path)){
                    return false;
                }
            } 
        }

        // try to upload the file
        ftp_fput($conn_id, $file, $fp, FTP_BINARY);
        
        $o = $system->loadModel('goods/gimage');
        foreach($o->defaultImages as $tag){
            $ext = $o->getImageExt(BASE_DIR.$path);
            $otherimage = substr(BASE_DIR.$path,0,strlen(BASE_DIR.$path)-strlen($ext)) . "_" . $tag . $ext;
            if(file_exists($otherimage)){
                $fp = fopen($otherimage,'rb');
                ftp_fput($conn_id, "ftp_storage/".basename($otherimage), $fp, FTP_BINARY);
                fclose($fp);
                @unlink($otherimage);
            }
        }
        @unlink(BASE_DIR.$path);
        
        // close the connection and the file handler
        ftp_close($conn_id);
        fclose($fp);

        return true;
    }
*/

function save($file,&$url,$type,$addons){



	// $id = $this->_get_ident($file,$type,$addons,$url,$path);

       
	
		$conn = ftp_connect(FTP_SERVER) or die ("不能连接");

// send access parameters

       ftp_login($conn, FTP_UNAME, FTP_PASSWD) or die("不能登陆");
  
// perform file upload
//ftp_chdir($conn, $destDir); 

     $upload = ftp_put($conn, FTP_SAVEDIR.$addons[0], $file, FTP_BINARY);
 //$id = $this->_get_ident($file,$type,$addons,$url,$path);
//error_log(print_r($id,true),3,__FILE__.'id.log');
     return FTP_REFURL.$addons[0];
    }




    function remove($ident){
        $conn_id = ftp_connect(__FTP_SERVER__);
        $login_result = ftp_login($conn_id, __FTP_UNAME__,__FTP_PASSWD__);
        $ret = ftp_delete($conn_id,__FTP_DIR__ . "/" .$ident);
        ftp_close($conn_id);
        return $ret;
    }
	 
   function save_upload($file,&$url,$type,$addons){

        //$id = $this->_get_ident($file,$type,$addons,$url,$path);
        $conn = ftp_connect(FTP_SERVER) or die ("不能连接");

// send access parameters

       ftp_login($conn, FTP_UNAME, FTP_PASSWD) or die("不能登陆");

// perform file upload
//ftp_chdir($conn, $destDir); 

      $upload = ftp_put($conn, FTP_SAVEDIR.$addons[0], $file, FTP_BINARY);
      //  if($path && move_uploaded_file($file,$path)){
          //  @chmod($path,0644);
			 
            return FTP_REFURL.$addons[0];
     //   }else{
            return false;
       // }
    }

	  function getFile($id){
        if($id && file_exists(MEDIA_DIR.'/'.$id)){
            //return MEDIA_DIR.'/'.$id;
			return $id;
         }else{
            return true;
        }
    }



	
  
}
?>
