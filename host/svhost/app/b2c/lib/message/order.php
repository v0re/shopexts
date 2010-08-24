<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_message_order extends b2c_message_comment{
    
    public function __construct(&$app)
    {         
        $this->app = $app;
        $this->type = 'order';
        parent::__construct($app);
    }
    
    public function send($aData)
    {
        if($_POST['msg']['msgType'] == 1)
        {
            $data['title'] = __('订单 ').$_POST['msg']['orderid'].__(' 付款通知，请核实');
            $data['comment'] = __('我已经于 ').$_POST['msg']['paydate'][0].' '.$_POST['msg']['paydate'][1].':'.$_POST['msg']['paydate'][2].__(' 通过 ').$_POST['msg']['payments'].__(' 支付 ').$_POST['msg']['paymoney'].__(' 元，订单号码：').$_POST['msg']['orderid'].__(' ，请尽快核实。').__("\n备注：").htmlspecialchars($_POST['msg']['message']);
        }
        else
        {
            $data['title'] = $_POST['msg']['subject'];
            $data['comment'] = htmlspecialchars($_POST['msg']['message']);
        }
        
        $data['order_id'] = $_POST['msg']['orderid'];
        $data['object_type'] = 'order';
        $data['author_id'] = $aData['author_id'];        
        $data['author'] = $aData['author'];
        $data['time'] = time();
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        
        if($aData['to_type'] == 'admin'){
            $data['to_id'] = 1;
            $data['to_uname'] = '管理员';
        }
        else{
            $data['to_id'] = $aData['to_id'];
            $data['to_uname'] = $aData['to_uname'];
            $data['for_comment_id'] = $_POST['msg']['msgid'];
        }
        
        if($this->save($data))
        {
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
        $sdf['title'] = $aData['title'];
        $sdf['contact'] = '';
        $sdf['display'] = 'true';
        $sdf['lastreply'] = time();
        $sdf['comment'] = $aData['reply_content'];
        if($this->save($sdf)){
            return true;
        }
        else{
            return false;
        }
    }
   
}