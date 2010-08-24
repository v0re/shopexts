<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_member_comments extends dbeav_model{
    
   function __construct(&$app){
        $this->app = $app;

        parent::__construct($app);

    }

   function get_type(){

          return $type;

   }

   function set_type($type){
      $this->type = $type;
   }
   
    function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderby=null){  
        if($this->type == 'msgtoadmin' || $this->falg == 'msgtoadmin'){
             $this->type = 'msg';
             $filter['to_id'] = 1;
         }
         if($this->type){
            $filter['object_type'] = $this->type;
        }   
         if($filter['for_comment_id'] === 'all'){
             unset($filter['for_comment_id']);
         }
         else{
             $filter['for_comment_id'] = $filter['for_comment_id'] ? $filter['for_comment_id']:0;    
            }
         $aData = parent::getList($cols='*', $filter, $offset, $limit, $orderby);
         return $aData;
  }
  
  function count($filter=array()){
       if($this->type == 'msgtoadmin'){
             $this->type = 'msg';
             $this->falg = 'msgtoadmin';
             $filter['to_id'] = 1;
         }
         if($this->type){
            $filter['object_type'] = $this->type;
        }   
         if($filter['for_comment_id'] === 'all'){
             unset($filter['for_comment_id']);
         }
         else{
             $filter['for_comment_id'] = $filter['for_comment_id'] ? $filter['for_comment_id']:0;    
            }
           return parent::count($filter);     
  }
  /*设置管理员阅读状态*/
  function set_admin_readed($comment_id){
        $sdf = $this->dump($comment_id);
        $sdf['adm_read_status'] = 'true';
        $this->save($sdf);
  }  
}
