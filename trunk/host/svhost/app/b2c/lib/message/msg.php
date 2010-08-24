<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_message_msg extends b2c_message_comment{
    
    function __construct(&$app){
         
        $this->app = $app;
        $this->type = 'msg';
        parent::__construct($app);

    }
    
      function send($aData){
          if($aData['to_type'] == 'admin'){
              $to_id = 1;
              $to_uname = '管理员';
          }
          else{
              $to_id = $aData['to_id'];
              $to_uname = $aData['msg_to'];
          }
          $data = array(
                    'comment_id' => $aData['comment_id']?$aData['comment_id']:'',
                    'author_id' => $aData['member_id'],
                    'author' => $aData['uname'],
                    'contact'=>htmlspecialchars($aData['contact']),
                    'object_type' => 'msg',
                    'to_id' =>  $to_id,
                    'to_uname' => $to_uname,
                    'title' =>htmlspecialchars($aData['subject']),
                    'comment' => htmlspecialchars($aData['comment']),
                    'time' => time(),
                    'ip'=>$aData['ip'],
                    'has_sent'=>$aData['has_sent'],
                        );
        if($this->save($data)){
            return true;
        }
        else{
            return false;
        }
  }
      
      function to_reply($aData){
        $sdf = $this->dump($aData['comment_id']);
        $sdf['comment_id']= '';
        $sdf['for_comment_id'] = $aData['comment_id'];
        $sdf['object_type'] = "msg";
        $sdf['to_id'] = $sdf['author_id'];
        $sdf['to_uname'] = $sdf['author'];
        $sdf['author_id'] = 1;
        $sdf['author'] = '管理员';
        $sdf['title'] = htmlspecialchars($aData['title']);
        $sdf['contact'] = '';
        $sdf['display'] = 'true';
        $sdf['lastreply'] = time();
        $sdf['comment'] = htmlspecialchars($aData['reply_content']);
        if($this->save($sdf)){
            return true;
        }
        else{
            return false;
        }
    }
   
}