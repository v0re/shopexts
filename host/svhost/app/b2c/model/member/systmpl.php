<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_mdl_member_systmpl extends dbeav_model{
    
     function __construct($app){
        parent::__construct($app);
    }
    function fetch($tplname,$data=null){
        $aTmpl = explode(':',$tplname);
        $render = $this->app->render();
        foreach($data as $key=>$val){
            $render->pagedata[$key] = $val;
        }
        if(count($aTmpl) != 1){
            return $render->fetch('admin/'.$aTmpl[0].'/'.$aTmpl[1].'.html');
        }
        else{
            return $render->fetch($tplname.'.html');
        }
        
    }

    function getTitle($ident){
        $row = $this->db->select('select title,path from sdb_sitemaps where action=\'page:'.$ident.'\'');
        if($row[0]['path']){
            $row[0]['path']=substr($row[0]['path'],0,strlen($row[0]['path'])-1);
            $parentRow=$this->db->select('select title,action as link from sdb_sitemaps where node_id in ('.$row[0]['path'].')');
            $parentRow[]=array('title'=>$row[0]['title'],'link'=>$row[0]['action']);
            return $parentRow;
        }

        return $row;
    }

    function _file($name){
        if($p = strpos($name,':')){
            $type = substr($name,0,$p);
            $name=substr($name,$p+1);
            if($type=='messenger'){
                #return PLUGIN_DIR.'/messenger/'.$name.'.html';
                return ROOT_DIR.'/app/b2c/view/admin/messenger/'.$name.'.html';
            }
        }
        else{
            return ROOT_DIR.'/app/b2c/view/'.$name.'.html';
        }
    }

    function get($name){
           $aRet = $this->getList('*',array('active'=>'true','tmpl_name'=>$name));
           if($aRet){
            return $aRet[0]['content'];
        }else{
            return file_get_contents($this->_file($name));
        }
    }

    function clear($name){
        $sdf = $this->dump($name);
        $sdf['edittime'] = time();
        $sdf['active'] = 'false';
        return $this->save($sdf);
    }

    function tpl_src($matches){
        return '<{'.html_entity_decode($matches[1]).'}>';
    }

    function set($name,$body){
        file_put_contents($this->_file($name),$body);
        $body = str_replace(array('&lt;{','}&gt;'),array('<{','}>'),$body);
        $body = preg_replace_callback('/<{(.+?)}>/',array(&$this,'tpl_src'),$body);
        $sdf['tmpl_name'] = $name;
        $sdf['edittime'] = time();
        $sdf['active'] = 'true';
        $sdf['content'] = $body;
        $rs = $this->save($sdf);
        return $rs;
    }
}
