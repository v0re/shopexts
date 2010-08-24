<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_system_backup {
    private $header = "<?php exit; ?>";
    private $tar_ext = '.zip';
    
    
    
    function __construct() {
        exit;
    }
    
    
    function get($var='') {
        if(!$var) return false;
        return $this->$var;
    }
    
    function getList(){

        $dir = DATA_DIR.'/backup';
        if(!is_dir($dir))return false;
        $handle=opendir($dir);

        if ($handle = opendir($dir)) {
            $return = array();
            while (false !== ($file = readdir($handle))) {
                if(in_array($file, array('.', '..', '.svn'))) continue;
                if(is_file($dir.'/'.$file) && strtolower(substr($file, -(strpos(strrev($file),'.'))))=='php'){
                    $temp = array('name'=>str_replace('.php', $this->tar_ext, $file), 'size'=>filesize($dir .'/'. $file), 'time'=>filectime($dir .'/'. $file));
                    $return[] = $temp;
                }
            }
            krsort($return);
            closedir($handle);
        }
        return $return;
    }
    
    
    function start_backup_sdf($params,&$nexturl){
        
        set_time_limit(0);
        header("Content-type:text/html;charset=utf-8");
        $app = $params['appname'];
        $dirname = $params['dirname'];
        #$cols = $params['cols'];
        #$model = $params['model'];
        #$startid = $params['startid'];
        
        $oMysqlDump = kernel::single("desktop_system_mysqldumper");
        $oMysqlDump->setDroptables(true);
        //$backdir = DATA_DIR.'/backup';
        $oMysqlDump->tableid = $tableid;
        $oMysqlDump->startid = $startid;
        //is_dir($backdir) or mkdir($backdir, 0755, true);
        //is_dir($backdir .'/' . $dirname) or mkdir($backdir .'/' . $dirname, 0755, true);
        $app = $oMysqlDump->multi_dump_sdf( $app,$dirname );




        if($app){
            $nexturl = "index.php?app=desktop&ctl=backup&act=backup_sdf&appname=$app&dirname=$dirname";
        }else{
            exit('hell');
            $tar = kernel::single("base_tar");
            $dir = $backdir. '/' . $dirname;
            chdir($dir);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if($file{0}!='.') {
                        $tar->addFile($file);
                    }
                }
                
                closedir($handle);
            }
            $tar->filename = 'multibak_'.$dirname.$this->tar_ext;
            $tar->saveTar();
            @copy($dir . '/' . $tar->filename, $backdir.'/'.$tar->filename);
            
            

            $this->convertFile($backdir, $tar->filename, '.php');
            if(is_resource($tar->tar_file)) {
                fclose($tar->tar_file);
            }
            @unlink($tar->filename);
            @unlink($backdir.'/'.$tar->filename);
            $dir = $backdir. '/' . $dirname;
            chdir($dir);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if(is_file($file)) {
                        @unlink($file);
                    }
                }
                closedir($handle);
            }
            @rmdir($dir);
            
            return true;
        }
        
        return false;
    }
    
    
    

    public function download($file) {
        $dir = DATA_DIR.'/backup/';
        $file = str_replace($this->tar_ext, '.php', $file);
        if(!file_exists($dir . $file)){
            return false;
        }
        
        $etag = md5_file($dir . $file);
        header('Etag: '.$etag);
        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
            header('HTTP/1.1 304 Not Modified',true,304);
            exit(0);
        }else{
            set_time_limit(0);
            ob_end_clean();
            $content_size = filesize($dir . $file) - strlen($this->header);
            $filename = substr($file, 0, strpos($file, '.')) . $this->tar_ext;
            header("Content-Disposition: attachment; filename=\"$filename\"");        
            header("Content-Length: " . $content_size);

            $handle = fopen($dir . $file, "r");
            $flag = false;
            while($buffer = fread($handle,102400)){
                if(!$flag) $buffer = str_replace($this->header, '', $buffer);
                echo $buffer;
                $flag = true;
                flush();
            }
            fclose($handle);
            
            
        }
    }
    
    
    
    
    
    
    
    
    function recover($sTgz,&$vols,$fileid){
        $prefix = substr($sTgz,0,23);
        $sTmpDir = DATA_DIR.'/tmp/'.md5($sTgz).'/';
        $sTgz = str_replace($this->tar_ext, '.php', $sTgz);
        if($fileid==1){
            $rTar = kernel::single("base_tar");
            is_dir($sTmpDir) or mkdir($sTmpDir, 0755, true);
            $file = DATA_DIR.'/backup/'.$sTgz;
            
            $newFile = $this->convertFile(DATA_DIR.'/backup/', $sTgz, $this->tar_ext);

            if($rTar->openTAR($newFile, null)){
                $vols = count($rTar->files);
                if(!$rTar->files) return false;
                foreach($rTar->files as $id => $aFile) {
                    if(substr($aFile['name'],-4)=='.sdf'){
                        $sPath=$sTmpDir.$aFile['name'];
                        file_put_contents($sPath,$rTar->getContents($aFile));
                    }
                }
            }

            $rTar->closeTAR();
            @unlink($newFile);
            return $this->comeback($sTmpDir, 1, $vols);
        }else{
            return $this->comeback($sTmpDir, $fileid, $vols);
        }
        
    }


    function comeback($sDir, $fileid=1, $vols){
        
        if(!is_dir($sDir)) return false;
        chdir($sDir);
        if($rHandle=opendir($sDir)){
            while(false!==($sFile=readdir($rHandle))){
                if($sFile{0}=='.')continue;
                $handle=fopen($sFile,'r');
                $app = substr($sFile, 0, strpos($sFile, '.'));
                $model = substr($sFile, strpos($sFile, '.')+1, -(strpos(strrev($sFile), '.') + 1));
                $model = strpos($model, '.') ? substr($model, 0, strpos($model, '.')) : $model;
                if(!$app || !$model) return false;

                $model = app::get($app)->model($model);
                $str = null;
                while($sdf=$this->fgetline($handle)){
                    if(!is_array(@unserialize($sdf))){
                        $buffer .= $sdf;
                        $sdf = @unserialize($buffer);
                        if(!is_array($sdf)) continue;
                    }else{
                        $sdf = @unserialize($sdf);
                    }
                    
                    @$model->db_save($sdf);
                    $buffer = null;
                }
                fclose($handle);
                @unlink($sFile);
                break;
            }
            closedir($rHandle);
            @rmdir($sDir);
        }
        
    }


   /**
    * 文件类型转换
    */
    private function convertFile($dir, $file, $type='.php') {
        $dir = rtrim($dir, '/') . '/';
        $newFile = $dir . substr($file, 0, -(strpos(strrev($file), '.')+1)) . $type;
        
        $flag = true;
        if($type==$this->tar_ext) {
            $flag = false;
            //is_dir($dir .'/tmptar/') or mkdir($dir .'/tmptar/');
            //$newFile = $dir .'/tmptar/'. md5($newFile);
        }
        
        
        if(file_exists($newFile)) return $newFile;
        $handle = fopen($newFile, 'x');
        
        if($flag) {
            fwrite($handle, $this->header);
        }
        $src = fopen($dir . $file, 'r');

        while (!feof($src)) {
            $contents = fgets($src);
            if(!$flag && strpos($contents, $this->header)!==false) $contents = substr($contents, strlen($this->header));
            if($contents) {
                fwrite($handle, $contents);
            }
            $flag = true;
        }
        fclose($src);
        fclose($handle);
        return $newFile;
    }









    function fgetline($handle){
        $buffer = fgets($handle, 4096);
        if (!$buffer){
            return false;
        }
        if(( 4095 > strlen($buffer)) || ( 4095 == strlen($buffer) && "\n" == $buffer{4094} )){
            $line = $buffer;
        }else{
            $line = $buffer;
            while( 4095 == strlen($buffer) && "\n" != $buffer{4094} ){
                $buffer = fgets($handle,4096);
                $line.=$buffer;
            }
        }
        return $line;
    }

    function removeTgz($aTgz){
        foreach($aTgz as $sFile){
            $pathinfo = pathinfo($sFile);
            @unlink(DATA_DIR.'/backup/'.$pathinfo['filename'].'.php');
        }
        return true;
    }
    function __finish($sDir){
        $this->__removeDir($sDir);
        return $sDir;
    }
    function __removeDir($sDir){
        if($rHandle=opendir($sDir)){
            while(false!==($sItem=readdir($rHandle))){
                if ($sItem!='.' && $sItem!='..'){
                    if(is_dir($sDir.'/'.$sItem)){
                        $this->__removeDir($sDir.'/'.$sItem);
                    }else{
                        unlink($sDir.'/'.$sItem);
                    }
                }
            }
            closedir($rHandle);
            rmdir($sDir);
        }
    }
    //*/
}
