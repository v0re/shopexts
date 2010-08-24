<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_message_message extends b2c_message_comment{

    function __construct(&$app){
         
        $this->app = $app;
        $this->type = 'message';
        parent::__construct($app);

    }
    function send($aData,$member_data=null){
        $data = array(
                        'author_id' => $member_data['member_id'] ? $member_data['member_id']:0,
                        'author' => $member_data['uname']?$member_data['uname']:'游客',
                        'title' =>htmlspecialchars($aData['subject']), 
                        'comment' => htmlspecialchars($aData['message']),
                        'time' => time(),
                        'contact'=> $aData['contact'] ? htmlspecialchars($aData['contact']):$member_data['email'],
                        'object_type' => 'message',
                        'ip' => $aData['ip'],
                        'display' => $aData['display'],
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
        if($aData['display'] == "true"){
            $sdf['display'] = 'true';
        if(!($this->save($sdf))){
            return false; 
        }    
        }
        $sdf['comment_id']= '';
        $sdf['for_comment_id'] = $aData['comment_id'];
        $sdf['object_type'] = "message";
        $sdf['author_id'] = null;
        $sdf['author'] = '管理员';
        $sdf['title'] = strip_tags($aData['title']);
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