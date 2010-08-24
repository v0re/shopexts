<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_member_shopbbs extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member';

    function index(){
        $member_comments = $this->app->model('member_comments');
        $member_comments->set_type('message');
        $this->finder('b2c_mdl_member_comments',array(
        'title'=>'留言列表',
        'use_buildin_recycle'=>true,
        'finder_aliasname'=>'shopbbs',
        'finder_cols'=>'author,title,comment,time',
      //  'actions'=>array(
      //      array('label'=>'留言设置','href'=>'index.php?app=b2c&ctl=admin_member_shopbbs&act=setting')
      //      )
        ));

    }

  function to_reply(){
   $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
   $comment_id = $_POST['comment_id'];
   $comment = $_POST['reply_content'];
   if($comment_id&&$comment){
      $member_comments = kernel::single('b2c_message_message');
      if($this->app->getConf('system.message.open') == "off"){
         $_POST['display'] = "true";
      }
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
  
  function delete_reply($msg_id){
   $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
   $member_msg = kernel::single('b2c_message_message');
   if($member_msg->delete(array('comment_id' => $msg_id))){
      $this->end(true,__('删除成功')); 
   }
   else{
      $this->end(false,__('删除失败'));
   }
}
 
 function setting(){
      echo '<div class="tabs-wrap"><ul><li class="tab"><span><a href="index.php?app=b2c&ctl=admin_member_gask&act=setting">咨询设置</a></span></li><li class="tab"><span><a href="index.php?app=b2c&ctl=admin_member_discuss&act=setting">评论设置</a></span></li><li class="tab current"><span><a href="index.php?app=b2c&ctl=admin_member_shopbbs&act=setting">留言设置</a></span></li></ul></div>';
     if($_POST){
         $this->app->setConf('system.message.open',$_POST['system_message_open']);
         $this->app->setConf('message_verifyCode',$_POST['verifyCode']['msg']);
     }
     $this->pagedata['setting'] = $this->app->getConf('system.message.open');
     $this->pagedata['verifyCode'] = $this->app->getConf('message_verifyCode')?$this->app->getConf('message_verifyCode'):"on";
     $this->page('admin/member/setting.html');
 }
    
    

}
