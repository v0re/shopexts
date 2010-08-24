<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_member_msgbox extends desktop_controller{

    var $workground = 'b2c.workground.member';

    function index(){
        
          $member_comments = $this->app->model('member_comments');
          $member_comments->set_type('msgtoadmin');
          $this->finder('b2c_mdl_member_comments',array(
          'title'=>'站内消息',
            'use_buildin_recycle'=>true,
            'finder_aliasname'=>'msgbox',
            'finder_cols'=>'author,title,comment,time',
          ));

    }
    
   function to_reply(){
      $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
      $comment_id = $_POST['comment_id'];
      $comment = $_POST['reply_content'];
      if($comment_id&&$comment){
         $member_comments = kernel::single('b2c_message_msg');
         if($member_comments->to_reply($_POST)){
            $this->end(true,__('回复成功')); 
         }
         else{
             $this->end(false,__('回复失败')); 
         }
      }
      else{
         $this->end(false,__('内容不能为空'));
      }
  } 
  
 
}
