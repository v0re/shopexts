<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_site_comment extends b2c_frontpage{

    var $noCache = true;
    function __construct(&$app){
         parent::__construct($app);

    }
    
    //评论验证码
    
    function gen_dissvcode(){
        $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key('DISSVCODE');
        $vcode->display();
    }
    
    //咨询验证码
    function gen_askvcode(){
        $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key('ASKVCODE');
        $vcode->display();
    }
    
    function commentlist($goodsid, $item, $nPage=1){#exit;
        $objGoods = &$this->app->Model('goods');
        $this->pagedata['goods'] = $objGoods->getList('*',array('goods_id'=>$goodsid));
        $this->pagedata['goods'] = $this->pagedata['goods'][0];
        $this->title = $this->pagedata['goods']['name'];
        $member_data = $this->get_current_member();
        if($member_data['member_id']){
            $this->pagedata['login'] = "YES";
        }
        else{
            $this->pagedata['login'] = "NO";
        }
        $this->pagedata['goods']['setting']['mktprice'] = $this->app->getConf('site.market_price');
        $this->pagedata['goods']['setting']['saveprice'] = $this->app->getConf('site.save_price');
        $this->pagedata['goods']['setting']['buytarget'] = $this->app->getConf('site.buy.target');
        $switchStatus = $this->app->getConf('comment.switch.'.$item);
        if($switchStatus == 'on'){
            $objComment= kernel::single('b2c_message_disask');
            $aComment = $objComment->getGoodsCommentList($goodsid, $item, $nPage);
            $aId = array();
            foreach($aComment['data'] as $rows){
                $aId[] = $rows['comment_id'];
            }
            if(count($aId)) $aReply = $objComment->getCommentsReply($aId, true);
            reset($aComment['data']);
            foreach($aComment['data'] as $key => $rows){
                foreach($aReply as $rkey => $rrows){
                    if($rows['comment_id'] == $rrows['for_comment_id']){
                        $aComment['data'][$key]['items'][] = $aReply[$rkey];
                    }
                }
                reset($aReply);
            }
        }else{
            $this->_response->set_http_response_code(404);
            exit;
        }

        switch($item){
            case 'ask':
            $this->pagedata['askshow'] = $this->app->getConf('comment.verifyCode.ask');
            $this->path[]=array('title'=>__('商品咨询'));
            $pagetitle = __('商品咨询');
            break;
            case 'discuss':
            $this->pagedata['discussshow'] = $this->app->getConf('comment.verifyCode.discuss');
            $this->path[]=array('title'=>__('商品评论'));
            $pagetitle = __('商品评论');
            break;
            case 'buy':
            $this->path[]=array('title'=>__('商品经验'));
            $pagetitle = __('商品经验');
            break;
        }

        $this->pagedata['commentData'] = $aComment['data'];
        $this->pagedata['comment']['total'] = $aComment['total'];
        $this->pagedata['comment']['pagetitle'] = $pagetitle;
        $this->pagedata['comment']['item'] = $item;
        if($item === 'ask'){
            $this->pagedata['title'] ='[咨询]'.$this->pagedata['goods']['name'];
        }
        else{
            $this->pagedata['title'] = '[评论]'.$this->pagedata['goods']['name'];
        }
        $this->pagedata['pager'] = array(
                'current'=> $nPage,
                'total'=> $aComment['page'],
                'link'=> $this->gen_url(array('app'=>'b2c', 'ctl'=>'site_comment','full'=>1,'act'=>'commentlist','args'=>array($goodsid,$item,($tmp = time())))),                 'token'=> $tmp);
        $this->page('site/comment/commentlist.html');
    }

    //发表评论
    function toComment($goodsid, $item){
        $member_data = $this->get_current_member();
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'index','arg'=>$goodsid));
             // $this->begin($url);
        $objComment = $this->app->model('member_comments');
        if ($this->app->getConf('comment.verifyCode.'.$item)=="on"){
            if($item =="ask"){
                if(!base_vcode::verify('ASKVCODE',intval($_POST['askverifyCode']))){
                 $this->splash('failed',$url,__('验证码填写错误'));
            }
            }
            if($item =="discuss"){
                if(!base_vcode::verify('DISSVCODE',intval($_POST['discussverifyCode']))){
                 $this->splash('failed',$url,__('验证码填写错误'));
            }
            }
            /*
            if (md5($_POST[$item.'verifyCode'])<>$_COOKIE[strtoupper($item)."_RANDOM_CODE"]){
                if ($item=="ask")
                    $stp=__("咨询");
                elseif($item=="discuss")
                    $stp=__("评论");
                #$this->splash('failed','back',$stp.__('验证码录入错误，请重新输入'));
                echo "验证码有错误";
            }*/
        }
        $objComment = kernel::single('b2c_message_disask');
        if(!$objComment->toValidate($item, $goodsid, $member_data, $message)){
            $this->splash('failed', 'back',  $message);
            //$this->end(true,__('您已经退出系统'),$url);

        }else{
            $aData['title'] = $_POST['title'];
            $aData['comment'] = $_POST['comment'];
            $aData['goods_id'] = $goodsid;
            $aData['object_type'] = $item;
            $aData['author_id'] = $member_data['member_id'] ? $member_data['member_id']:0;
            $aData['author'] = ($member_data['uname'] ? $member_data['uname'] : __('非会员顾客'));
            $aData['contact'] = ($_POST['contact']=='' ? $member_data['email'] : $_POST['contact']);
            $aData['time'] = time();
            $aData['lastreply'] = 0;
            $aData['ip'] = $_SERVER["REMOTE_ADDR"];
            $aData['display'] = ($this->app->getConf('comment.display.'.$item)=='soon' ? 'true' : 'false');
            if($this->app->getConf('comment.display.'.$item) == 'soon'){
            $msg = $this->app->getConf('comment.submit_display_notice.'.$item);
            }else{
            $msg = $this->app->getConf('comment.submit_hidden_notice.'.$item);
            }
            if($objComment->send($aData, $item, $message)){
                 $this->splash('success',$url,__($msg));

            }
            else{
               $this->splash('failed','back',__('发表失败！'));
        }

        }
    }
    function csplash($goodsid){
        $this->splash('success', $this->app->mkUrl('product','index', array($goodsid)),'提交成功！');
    }
    function reply($comment_id){
        $objComment = kernel::single('b2c_message_disask');
        $aComment = $objComment->dump($comment_id);
        $aComment['reply'] = $objComment->get_reply($comment_id);
        $this->pagedata['comment'] = $aComment;
        if($_COOKIE['UNAME']){
            $this->pagedata['login'] = "YES";
        }
        else{
            $this->pagedata['login'] = "NO";
        }
        $this->page('site/product/reply.html');
    }

    //客户回复评论
    function toReply($comment_id){
        $member_data = $this->get_current_member();
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_comment','act'=>'reply','arg'=>$comment_id));
        $objComment = kernel::single('b2c_message_disask');
        $aComment = $objComment->dump($comment_id);
        if(!$objComment->toValidate($aComment['object_type'], $aComment['goods_id'], $member_data, $message)){
            $this->splash('failed', 'back', $message);
            #echo "未开启";
        }else{
            $aData['comment'] = $_POST['comment'];
            $aData['type_id'] = $aComment['type_id'];
            $aData['for_comment_id'] = $comment_id;
            $aData['author_id'] = $member_data['member_id'] ? $member_data['member_id']:0;
            $aData['mem_read_status'] = ($this->member['member_id']==$aComment['author_id'] ? 'false' : 'true');
            $aData['object_type'] = $aComment['object_type'];
            $aData['author'] = ($member_data['uname'] ? $member_data['uname'] : __('非会员顾客'));
            $aData['contact'] = ($_POST['contact']=='' ? $member_data['email'] : $_POST['contact']);
            $aData['time'] = time();
            $aData['lastreply'] = time();
            $aData['reply_name'] = $aData['author'];
            $aData['display'] = ($this->app->getConf('comment.display.'.$aComment['object_type'])=='soon' ? 'true' : 'false');
            if($objComment->send($aData,$aComment['object_type'])){ $this->splash('success',$url,__('发表成功！'));}
            else{
                  $this->splash('failed',$url,__('发表失败！'));
            }
        }
    }
}
?>
