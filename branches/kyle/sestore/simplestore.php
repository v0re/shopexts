<?php
/**
 * simplestore 
 * 只可增加，不可减少和修改的hash表
 * 
 * @package 
 * @version $Id$
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@shopex.cn> 
 * @license Commercial
 */
if(!class_exists('simplestore')){
    class simplestore{

        var $version = '0.0.1';

        function simplestore(){        
            #默认索引容量是100,000行
            $this->isize = 256<<10;
            #索引入口地址
            $this->ihome = 64;
            #数据入口地址
            $this->dhome = $this->isize + $this->ihome;
        }

        function workat($file){
            $file = $file.".php";
            if(!file_exists($file)){
                $this->create($file);
            }else{
                $this->_rs = fopen($file,'rb+');
            }
        }

        function create($file){
            touch($file);
            $this->_rs = fopen($file,'rb+');
            fputs($this->_rs,"<?php exit(); ? >$this->version\n");
            #初始化索引
            fseek($this->_rs,$this->ihome);
            fputs($this->_rs,pack('V',$this->dhome)."\n");
            #初始化数据区
            fseek($this->_rs,$this->dhome);
            fputs($this->_rs,"^\n");
        }


        function close(){
            fclose($this->_rs);
        }

        function store(&$row){
            array_walk($row,array(&$this,_filter));
            $str = implode("\t",$row);
            $str .= "\n";
            fseek($this->_rs,1,SEEK_END);
            $offset = ftell($this->_rs);     
            fputs($this->_rs,$str);
            $this->_set_row_index($offset);
        }

        function fetch($rowno){
              $index = $this->_get_row_index();
              if($offset = $index[$rowno]){            
                   $str = $this->_get_line($offset);                  
                   return explode("\t",$str);
              }
              return false;
        }

        function tail($n=20){
            $index = $this->_get_row_index();
            $ret = array();
            for($i=0;$i<$n;$i++){
                if($offset = array_pop($index)){
                    $str = $this->_get_line($offset);
                    $ret[] = explode("\t",$str);
                }
            }

            return $ret;
        }

        function size(){
            return count( $this->_get_row_index());
        }

        function _set_row_index($offset){
            fseek($this->_rs,$this->ihome);
            $indexdesc = fgets($this->_rs);    
            $isize = strlen($indexdesc);
            if($isize + 5 < $this->isize){    
                fseek($this->_rs,$this->ihome + $isize - 1);
                fwrite($this->_rs,pack('V',$offset)."\n"); 
            }else{
                $this->trigger_error('index data overflow',E_USER_ERROR);
            }
        }

        function _get_row_index(){
            fseek($this->_rs,$this->ihome);
            $indexdesc = fgets($this->_rs);       
            #$indexdesc = substr($indexdesc,0,-1);   
            return unpack('V*',$indexdesc);
        }

        function _filter(&$value,$key){
            $value =str_replace(array("\n","\t"),array(".",","),$value);
        }

        function _get_line($offset){
            fseek($this->_rs,$offset);
            return fgets($this->_rs);             
        }

        function trigger_error($errstr,$errno){
            error_log($errstr,3,__FILE__.".fatal.log");
		    exit();
        }

    }
}
