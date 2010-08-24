<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_message_comment{
    
    var $objComment;
    var $type;
    function __construct(&$app){
        $this->app = &$app;
        $this->objComment = $this->app->model('member_comments');
        $this->objComment->type = $this->type;
    }
     function save($aData){
         if($this->objComment->save($aData)){
             return true;
         }
         else{
             return false;
         }
         
     }
     
     function dump($id){
         $aData = $this->getList('*', array('comment_id' => $id,'for_comment_id' => 'all'));
         return $aData[0];
     }

     function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderby=null){
        $aData = $this->objComment->getList($cols='*', $filter, $offset, $limit, $orderby);
        return $aData;
      }
      function count($filter=array()){
        $aData = $this->objComment->count($filter);
        return $aData;
      }
      function delete($filter=null){
          if($this->objComment->delete($filter)){
              return true;
          }
          else{
              return false;
          }
      }
      
      function get_reply($comment_id){
        $aData = $this->getList('*',array('for_comment_id' => $comment_id));
        return $aData;
  }
  
      function setReaded($comment_id){
        $sdf = $this->dump($comment_id);
        $sdf['mem_read_status'] = 'true';
        $this->save($sdf);
    }
    
}