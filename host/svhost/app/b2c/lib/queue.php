<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

  class b2c_queue{
      function send_mail(&$cursor_id,$params){
          $obj_emailconf = kernel::single('desktop_email_emailconf');
        $aTmp = $obj_emailconf->get_emailConfig();
        $acceptor =  $params['acceptor'];    //收件人邮箱
        $aTmp['shopname'] = app::get('b2c')->getConf('system.shopname');
        $subject = $params['title'];
        $body = $params['body'];
        $email = kernel::single('desktop_email_email');
        $email->ready($aTmp);
        $res = $email->send($acceptor,$subject,$body,$aTmp);
        }
        
        
    function send_msg(&$cursor_id,$params){
        $obj_memmsg = kernel::single('b2c_message_msg');
        $aData = $params['data'];
        $aData['member_id'] = 1;
        $aData['uname'] = '管理员';
        $aData['to_id'] = $params['member_id'];
        $aData['msg_to'] = $params['name'];
        $aData['subject'] = $aData['title']; 
        $aData['comment'] = $aData['content'];
        $aData['has_sent'] = 'true';
        $obj_memmsg->send($aData);
    }
      
    ##发到货通知邮件
    function goods_notify(&$cursor_id,$params){
        $obj_emailconf = kernel::single('desktop_email_emailconf');
        $aTmp = $obj_emailconf->get_emailConfig();
        $acceptor = $params['acceptor'];     //收件人邮箱
        $aTmp['shopname'] = app::get('b2c')->getConf('system.shopname');
        $subject = $params['title'];
        #$subject = "biaoti";
        $body = $params['body'];
        #$body = "内容";
        $email = kernel::single('desktop_email_email');
        $email->ready($aTmp);
        $res = $email->send($acceptor,$subject,$body,$aTmp);
        $member_goods = app::get('b2c')->model('member_goods');
        $aData = $member_goods->getList('gnotify_id',array('product_id' => $params['product_id']));
        foreach($aData as $data){
            $sdf = $member_goods->dump($data['gnotify_id']);
            $sdf['status'] = "send";
            $sdf['send_time'] = time();
            $member_goods->save($sdf);
        }
    } 
  }
?>
