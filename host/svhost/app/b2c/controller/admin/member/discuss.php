<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_member_discuss extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member';

    function index(){
        $member_comments = $this->app->model('member_comments');
        $member_comments->set_type('discuss');
        $this->finder('b2c_mdl_member_comments',array(
        'title'=>'评论列表',
        'use_buildin_recycle'=>true,
        //'actions'=>array(
         //   array('label'=>'评论设置','href'=>'index.php?app=b2c&ctl=admin_member_discuss&act=setting')
          //  ),
        ));

    }
    function setting(){
        $member_comments = kernel::single('b2c_message_disask');
        echo '<div class="tabs-wrap"><ul><li class="tab"><span><a href="index.php?app=b2c&ctl=admin_member_gask&act=setting">咨询设置</a></span></li><li class="tab current"><span><a href="index.php?app=b2c&ctl=admin_member_discuss&act=setting">评论设置</a></span></li><li class="tab"><span><a href="index.php?app=b2c&ctl=admin_member_shopbbs&act=setting">留言设置</a></span></li></ul></div>';
      
        $aOut = $member_comments->get_setting('discuss');
          if(!$aOut['verifyCode']['discuss']){
            $aOut['verifyCode']['discuss']='off';
        }
        $aOut['aSwitch']['discuss'] = array('on'=>__('开启'), 'off'=>__('关闭'));
        $aOut['aPower']['discuss'] = array('null'=>__('非会员可发表评论'), 'member'=>__('注册会员可发表评论'), 'buyer'=>__('只有购买过此商品的会员才可发表评论（并且订单状态为已支付状态）'));
         $aOut['verifyLCode']['discuss'] = array('on'=>__('开启'), 'off'=>__('关闭'));
        $this->pagedata['setting']= $aOut;
        $this->page('admin/member/discuss_setting.html');
    }
    
    function to_setting(){
        $this->begin('index.php?app=b2c&ctl=admin_member_discuss&act=setting');
        $member_comments = kernel::single('b2c_message_disask');
        $aOut = $member_comments->to_setting('discuss',$_POST);
        $this->end('success',__('设置成功'));
    }

#回复评论
    function to_reply(){
      $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
      $comment_id = $_POST['comment_id'];
      $comment = $_POST['reply_content'];
      if($comment_id&&$comment){
         $member_comments = kernel::single('b2c_message_disask');
         $sdf = $member_comments->dump($comment_id);
         if($this->app->getConf('comment.display.discuss') == 'reply'){
            $aData = $sdf;
            $aData['display'] = 'true';
            $member_comments->save($aData);
         }
         $sdf['comment_id']= '';
         $sdf['for_comment_id'] = $comment_id;
         $sdf['object_type'] = "discuss";
         $sdf['author_id'] = null;
         $sdf['author'] = '管理员';
         $sdf['title'] = '';
         $sdf['contact'] = '';
         $sdf['display'] = 'true';
         $sdf['time'] = time();
         $sdf['comment'] = $comment;
         if($member_comments->send($sdf,'discuss')){
            $this->end(true,__('操作成功'));
         }
         else{
            $this->end(false,__('操作失败'));
         }
      }
      else{
         $this->end(false,__('内容不能为空'));
      }
    }
    
    
    
 
}
