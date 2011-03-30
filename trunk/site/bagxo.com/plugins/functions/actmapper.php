<?php
/**
 * shopex前台url地址生成扩展程序
 *     parse用来分析地址
 *     getLink方法用来产生地址
 *  对应系统配置项: system.seo.mklink / system.seo.parselink
 */
class actmapper{

    var $area = '';

    function actmapper(){
        $system = &$GLOBALS['system'];
        $this->basepath = $system->request['base_url'];
        $this->seoEmuFile = 'html';
    }

    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @return    mixed     Description
    * @access    private
    * @see        ??
    */
    function parse($query){
        if($pos = strpos($query,'.')){
            $type = substr($query,$pos+1);
            if($position = strpos($type,'&')){
                $type = substr($type,0,$position);
            }
            if($position = strpos($type,'?')){
                $type = substr($type,0,$position);
            }
            $query = substr($query,0,$pos);
        }

        $args = explode('-',$query);
        $act = 'index';


        if(($ctl = array_shift($args)) && $ctl!='index'){
            if(count($args)>0 && !is_numeric($args[count($args)-1])){
                $act = array_pop($args);
            }
        }
        foreach($args as $k=>$v){
            $args[$k] = str_replace(';jh;','-',$v);
            $args[$k] = str_replace(';dian;','.',$args[$k]);
            $args[$k] = str_replace(';xie;','/',$args[$k]);
            $args[$k] = str_replace(';xie;','%2F',$args[$k]);
        }

        return array('controller'=>$ctl,'method'=>$act,'args'=>$args,'type'=>$type);

    }


    function appendUrl($url){
        return $this->basepath.$url;
    }

    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @access    
    * @see        ??
    */
    function getLink($controller,$method,$args=null,$extname=null){

        if($controller=='index') return '';
        $array = array($controller);

        $use_arg = 0;
        if(is_array($args) && (count($args)>1 || (count($args)==1 && $args[0]))){
            $use_arg = 1;
            foreach($args as $k=>$arg){
                $args[$k] = str_replace('-',';jh;',$arg);
                $args[$k] = str_replace('/',';xie;',$args[$k]);
                $args[$k] = str_replace('%2F',';xie;',$args[$k]);
                $args[$k] = str_replace('.',';dian;',$args[$k]);
            }
            $array = array_merge(array($controller),$args);
        }

        if($method!='index' || ($use_arg && !is_numeric(array_pop($args)))){
            $array[] = urlencode($method);
        }
        return implode('-',$array).'.'.($extname?$extname:$this->seoEmuFile);
    }

}
?>
