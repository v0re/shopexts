<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_storage_filesystem implements base_interface_storager{
    function base_storage_filesystem(){
        switch($_POST['_f_type']){
            case "public":
                $this->f_dir = ROOT_DIR.'/public/files';
                $this->base_dir = 'public/files';
                break;
            case "private":
                $this->f_dir = ROOT_DIR.'/data/private';
                $this->base_dir = 'data/private';
                $this->is_private = true;
                break;
            default:
                $this->f_dir = ROOT_DIR.'/public/images';
                $this->base_dir = 'public/images';
        }
    }

    function save($file,&$url,$type,$addons,$ext_name=""){
        if($this->is_private){
            $ext_name = '.php';
        }
        $id = $this->_get_ident($file,$type,$addons,$url,$path,$ext_name);
        if($path && copy($file,$path)){
            @chmod($path,0644);
            return $id;
        }else{
            return false;
        }
    }

    function replace($file,$id){
        $path = $this->f_dir.$id;
        $dir = dirname($path);

        if(file_exists($path)){
            if(!unlink($path)){
                return false;
            }
        }elseif(!is_dir($dir)){
            if(!$this->mkdir_p($dir)){
                return false;
            }
        }
        if($path && rename($file,$path)){
            return $id;
        }else{
            return false;
        }
    }


    function _get_ident($file,$type,$addons,&$url,&$path,$ext_name=""){
        $ident = md5(rand(0,9999).microtime());
        $dir = '/'.$ident{0}.$ident{1}.'/'.$ident{2}.$ident{3}.'/'.$ident{4}.$ident{5}.'/'.substr($ident,6);
        $addons = implode('-',$addons);
        $s_file = basename($addons?$addons:$file);
        $uri = $dir.substr(md5(($addons?$addons:$file).microtime()),0,16).substr($s_file,strrpos($s_file,'.'));
        if($ext_name) {
            if(strrpos($uri,".")) $uri = substr($uri,0,strrpos($uri,".")).$ext_name;
            else $uri .= $ext_name;
        }
        $path = $this->f_dir.$uri;
        $url = $this->base_dir.$uri;
        if(file_exists($path) && !unlink($path)){
            return false;
        }

        $dir = dirname($path);
        if(!is_dir($dir)){
            if(!$this->mkdir_p($dir)){
                return false;
            }
        }

        return $uri;
    }

    function remove($id){
        if($id && file_exists($this->f_dir.'/'.$id)){
            return @unlink($this->f_dir.'/'.$id);
         }else{
            return true;
        }
    }

    function getFile($id,$type){
        if($id && file_exists($this->f_dir.'/'.$id)){
            return $this->f_dir.'/'.$id;
         }else{
            return false;
        }
    }
    
    function mkdir_p($dir,$dirmode=0755){
        $path = explode('/',str_replace('\\','/',$dir));
        $depth = count($path);
        for($i=$depth;$i>0;$i--){
            if(file_exists(implode('/',array_slice($path,0,$i)))){
                break;
            }
        }
        for($i;$i<$depth;$i++){
            if($d= implode('/',array_slice($path,0,$i+1))){
                mkdir($d,$dirmode);
            }
        }
        return is_dir($dir);
    }


    /*function store($file,$ident,$size=''){
        $ident = $ident{0}.$ident{1}.'/'.$ident{2}.$ident{3}.'/'.$ident{4}.$ident{5}.'/'.substr($ident,6);

        if($size){
            $ident = substr($ident,0,-10).$size.substr($ident,-4,4);
        }

        $filename = $this->f_dir.'/'.$ident;

        $dir = dirname($filename);
        if(!is_dir($dir)){
            utils::mkdir_p($dir);
        }
        if(file_exists($filename)){
            unlink($filename);
        }
        rename($file,$filename);
        return array($ident,'images/'.$ident);
    }

    function delete($ident){
        unlink($this->f_dir.'/'.$ident);
        foreach(array('L','M','S') as $size){
            unlink($this->f_dir.'/'.substr($ident,0,-10).$size.substr($ident,-4,4));
        }
    }

    function fetch($ident){
        return $this->f_dir.'/'.$ident;
    }
    */
}
