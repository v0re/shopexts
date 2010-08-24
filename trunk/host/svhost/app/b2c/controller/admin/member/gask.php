<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_member_gask extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member';

    function index(){  
        $member_comments = $this->app->model('member_comments');
        $member_comments->set_type('ask');
        $this->finder('b2c_mdl_member_comments',array(
        'title'=>'咨询列表',
        'use_buildin_recycle'=>true,
       // 'actions'=>array(
       //     array('label'=>'咨询设置','href'=>'index.php?app=b2c&ctl=admin_member_gask&act=setting')
       //     )
        ));

    }
  
    function setting(){
        $member_comments = kernel::single('b2c_message_disask');
        echo '<div class="tabs-wrap"><ul><li class="tab current"><span><a href="index.php?app=b2c&ctl=admin_member_gask&act=setting">咨询设置</a></span></li><span><li class="tab"><a href="index.php?app=b2c&ctl=admin_member_discuss&act=setting">评论设置</a></span></li><li class="tab"><span><a href="index.php?app=b2c&ctl=admin_member_shopbbs&act=setting">留言设置</a></span></li></ul></div>';
      
        $aOut = $member_comments->get_setting('ask');
         if(!$aOut['verifyCode']['ask']){
            $aOut['verifyCode']['ask']='off';
        }
        $aOut['aSwitch']['ask'] = array('on'=>__('开启'), 'off'=>__('关闭'));
        $aOut['aPower']['ask'] = array('null'=>__('所有顾客都可咨询'), 'member'=>__('只有注册会员才能咨询'));
        $aOut['verifyLCode']['ask'] = array('on'=>__('开启'), 'off'=>__('关闭'));
        $this->pagedata['setting']= $aOut;
        $this->page('admin/member/gask_setting.html');
    }
    
    function to_setting(){
        $this->begin('index.php?app=b2c&ctl=admin_member_gask&act=setting');
        $member_comments = kernel::single('b2c_message_disask');
        $aOut = $member_comments->to_setting('ask',$_POST);
        $this->end('success',__('设置成功'));
    }
    #回复咨询
    function to_reply(){
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        #$this->begin("javascript:finderGroup['411c04.refresh()']");
        $comment_id = $_POST['comment_id'];
        $comment = $_POST['reply_content'];
        if($comment_id&&$comment){
            $member_comments = kernel::single('b2c_message_disask');
            $sdf = $member_comments->dump($comment_id);
            if($this->app->getConf('comment.display.ask') == 'reply'){
                $aData = $sdf;
                $aData['display'] = 'true';
                $member_comments->save($aData);
            }
            $sdf['comment_id']= '';
            $sdf['for_comment_id'] = $comment_id;
            $sdf['object_type'] = "ask";
            $sdf['author_id'] = null;
            $sdf['author'] = '管理员';
            $sdf['title'] = '';
            $sdf['contact'] = '';
            $sdf['display'] = 'true';
            $sdf['lastreply'] = time();
            $sdf['reply_name'] = '管理员';
            $sdf['comment'] = $comment;
            if($member_comments->send($sdf,'ask')){
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
    
 #设置显示
    function to_display($comment_id,$comment_display){ 
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
         if($comment_display=="true"){
             $comment_display = "false";
         }
         else{
             $comment_display = "true";
         }
         $member_comments = kernel::single('b2c_message_disask');
         $sdf = $member_comments->dump($comment_id);
         $sdf['display'] = $comment_display;
         if($member_comments->save($sdf)){
             $this->end(true,__('操作成功'));
         }
         else{
             $this->end(fasle,__('操作失败'));
         }       
    }
    
   
    
    #删除
    function delete_message($comment_id){
      $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
      $member_comment = kernel::single('b2c_message_disask');
      $reply = $member_comment->get_reply($comment_id);
      $aComId = array();
      $aComId[] = $comment_id;
      foreach($reply as $v){
         $aComId[] = $v['comment_id'];
      }
      if($member_comment->delete(array('comment_id' => $aComId))){
         $this->end(true,__('操作成功'));
      }
      else{
         $this->end(fasle,__('操作失败'));
      }
    }
    
    function delete_reply($comment_id){
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        $member_comment = kernel::single('b2c_message_disask');
        if($member_comment->delete(array('comment_id' => $comment_id))){
            $this->end(true,__('操作成功'));
        }
        else{
            $this->end(fasle,__('操作失败'));
        }
    }
}
