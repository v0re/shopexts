<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_goods_sto extends desktop_controller{
    /*
        %1 - id
        %2 - title
        $s - string
        $d - number
    */
    var $workground = 'b2c_ctl_admin_goods';

    function index(){
        $this->finder('b2c_mdl_goods_sto',array(
            'actions'=>array(array('label'=>'发送到货通知','submit'=>'index.php?app=b2c&ctl=admin_goods_sto&act=send')), 
            'use_buildin_recycle'=>true,
            ));
    }
    
     function send(){
        $this->begin('index.php?app=b2c&ctl=admin_goods_sto&act=index');
        $aGnotify = $_POST['gnotify_id'];
        $systmpl = $this->app->model('member_systmpl');
        $queue = app::get('base')->model('queue');
        $obj_product = app::get('b2c')->model('products');
        $member_goods = app::get('b2c')->model('member_goods');
        foreach( $aGnotify as $gnid){
        $data = $member_goods->dump($gnid);
        if($data['member_id']){
            $member_obj = $this->app->model('members');
            $member_sdf = $member_obj->dump($data['member_id'],'*',array(':account@pam'=>array('login_name')));
            $login_name = $member_sdf['pam_account']['login_name'];
        }
        else{
            $login_name = "顾客";
        }
       $goods = $obj_product->dump($data['product_id']);
       $obj['goods_name'] = $goods['name'];
       #$obj['goods_name'] = '测试商品';
       $obj['goods_id'] = $goods['goods_id'];
       $obj['username'] = $login_name;
       $obj['url'] = &app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','full'=>1,'act'=>'index','arg'=>$goods['goods_id']));
       $content = $systmpl->fetch('messenger:email/goods-notify',$obj);
       $queue_data = array(
        'queue_title'=>'到货通知'.$key,
        'start_time'=>time(),
        'params'=>array(
        'acceptor'=>$data['email'],
        'body' =>$content,
        'title' =>'到货通知',
        'product_id' => $data['product_id'],
        ),
        'worker'=>'b2c_queue.goods_notify',
    );
    if($data['member_id']){
        $aTmp['content'] =  $systmpl->fetch('messenger:msgbox/goods-notify',$obj);
        $aTmp['title'] ="商品到货通知";
        $data = array(
            'queue_title'=>'到货通知站内信',
            'start_time'=>time(),
            'params'=>array(
            'member_id'=>$data['member_id'],
            'data' =>$aTmp,
            'name' => $login_name,
            ),
            'worker'=>'b2c_queue.send_msg',
        );
        if(!$queue->insert($data)){
            $this->end(false,__('操作失败！'));
        }
    }
    if(!$queue->insert($queue_data)){
            $this->end(false,__('操作失败！'));
    }
        }
       $this->end(ture,__('操作成功！'));
    }
    
    
    

}
