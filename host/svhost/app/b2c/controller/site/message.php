<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_site_message extends b2c_frontpage{

    function index($nPage=1){
        $objMessage = kernel::single('b2c_message_message');
        if(!isset($_COOKIE['UNAME'])){
           $this->pagedata['nomember'] = 'on';
        }
        $aData = $objMessage->getList('*',array('display' => 'true'));
        foreach($aData as $key=>$reply){
            $aData[$key]['reply'] = $objMessage->get_reply($reply['comment_id']);
        }
        $member_data = $this->get_current_member();
        if($member_data['member_id']){
            $this->pagedata['login'] = 'YES';
        }
        else{
            $this->pagedata['login'] = 'NO';
        }
        $this->pagedata['msg'] = $aData;
        $this->pagedata['msgshow'] = $this->app->getConf('message_verifyCode')?$this->app->getConf('message_verifyCode'):"on";
        $this->page('site/message/index.html');
       //$this->output();

    }

    function sendMsgToOpt(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_message','act'=>'index'));
        $msgshow = $this->app->getConf('message_verifyCode')?$this->app->getConf('message_verifyCode'):"on";
        if($msgshow === "on"){
            if(!base_vcode::verify('MESSAGEVCODE',intval($_POST['verifyCode']))){
                 $this->splash('failed',$url,__('验证码填写错误'));
            }
        }
        if($this->app->getConf('system.message.open') == "on"){
            $_POST['display'] = "true";
        }
        else{
            $_POST['display'] = "false";
        }
        $member_data = $this->get_current_member();
        $objMessage = kernel::single('b2c_message_message');
        $_POST['ip'] = $_SERVER["REMOTE_ADDR"];
         if($objMessage->send($_POST,$member_data)){
             $this->splash('success',$url,__('发表成功！'));
         }
         else{
             $this->splash('failed',$url,__('发表失败！'));
         }
    }
    
    function verifyCode(){
        $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key('MESSAGEVCODE');
        $vcode->display();
    }
}
