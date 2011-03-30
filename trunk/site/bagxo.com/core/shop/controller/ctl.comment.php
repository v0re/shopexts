<?php
class ctl_comment extends shopPage{

    var $noCache = true;
    function ctl_comment(&$system){
        parent::shopPage($system);
        $this->_verifyMember(false);
    }

    function commentlist($goodsid, $item, $nPage=1){
        $objGoods = $this->system->loadModel('trading/goods');
        $this->pagedata['goods'] = $objGoods->getFieldById($goodsid);
        $this->title = $this->pagedata['goods']['name'];

        $this->pagedata['goods']['setting']['mktprice'] = $this->system->getConf('site.market_price');
        $this->pagedata['goods']['setting']['saveprice'] = $this->system->getConf('site.save_price');
        $this->pagedata['goods']['setting']['buytarget'] = $this->system->getConf('site.buy.target');

        $switchStatus = $this->system->getConf('comment.switch.'.$item);
        if($switchStatus == 'on'){
            $objComment= $this->system->loadModel('comment/comment');
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
            $this->system->error(404);
            exit;
        }

        switch($item){
            case 'ask':
            $this->path[]=array('title'=>'商品咨询');
            $pagetitle = __('商品咨询');
            break;
            case 'discuss':
            $this->path[]=array('title'=>'商品评论');
            $pagetitle = __('商品评论');
            break;
            case 'buy':
            $this->path[]=array('title'=>'商品经验');
            $pagetitle = __('商品经验');
            break;
        }
        $this->pagedata['commentData'] = $aComment['data'];
        $this->pagedata['comment']['total'] = $aComment['total'];
        $this->pagedata['comment']['pagetitle'] = $pagetitle;
        $this->pagedata['comment']['item'] = $item;
        $this->pagedata['pager'] = array(
                'current'=> $nPage,
                'total'=> $aComment['page'],
                'link'=> $this->system->mkUrl('comment','commentlist', array($goodsid,$item,($tmp = time()))),
                'token'=> $tmp);
        
        $this->output();
    }
    
    //发表评论
    function toComment($goodsid, $item){
        if ($this->system->getConf('comment.verifyCode.'.$item)=="on"){
            if (md5($_POST[$item.'verifyCode'])<>$_COOKIE[strtoupper($item)."_RANDOM_CODE"]){
                if ($item=="ask")
                    $stp="咨询";
                elseif($item=="discuss")
                    $stp="评论";
                $this->splash('failed','back',$stp.'验证码录入错误，请重新输入');
            }
        }
        $objComment = $this->system->loadModel('comment/comment');
        if(!$objComment->toValidate($item, $goodsid, $this->member, $message)){
            $this->splash('failed', 'back',  $message);
        }else{
            $aData['title'] = $_POST['title'];
            $aData['comment'] = $_POST['comment'];
            $aData['goods_id'] = $goodsid;
            $aData['object_type'] = $item;
            $aData['author_id'] = $this->member['member_id'];
            $aData['author'] = ($this->member['member_id'] ? $this->member['uname'] : __('Anonymity '));
            $oLv = $this->system->loadModel('member/level');
            $aLevel = $oLv->getFieldById($GLOBALS['runtime']['member_lv'], array('name'));
            $aData['levelname'] = $aLevel['name'];
            $aData['contact'] = ($_POST['contact']=='' ? $this->member['email'] : $_POST['contact']);
            $aData['time'] = time();
            $aData['lastreply'] = 0;
            $aData['ip'] = remote_addr();
            $aData['display'] = ($this->system->getConf('comment.display.'.$item)=='soon' ? 'true' : 'false');
            $objComment->toComment($aData, $item, $message);
            $this->splash('success', $this->system->mkUrl('product','index', array($goodsid)), $message);
        }
    }

    function reply($comment_id, $item){
        $objComment = $this->system->loadModel('comment/comment');
        $aComment = $objComment->getFieldById($comment_id, array('*'));
        $aComment['reply'] = $objComment->getCommentsReply(array($comment_id), true);
        
        $this->pagedata['comment'] = $aComment;
        $this->output();
    }

    //客户回复评论
    function toReply($comment_id){
        $objComment = $this->system->loadModel('comment/comment');
        $aComment = $objComment->getFieldById($comment_id, array('*'));
        if(!$objComment->toValidate($aComment['object_type'], $aComment['goods_id'], $this->member, $message)){
            $this->splash('failed', 'back', $message);
        }else{
            $aData['comment'] = $_POST['comment'];
            $aData['goods_id'] = $aComment['goods_id'];
            $aData['for_comment_id'] = $comment_id;
            $aData['author_id'] = $this->member['member_id'];
            $aData['mem_read_status'] = ($this->member['member_id']==$aComment['author_id'] ? 'false' : 'true');
            $aData['object_type'] = $aComment['object_type'];
            $aData['author'] = ($this->member['member_id'] ? $this->member['uname'] : __('Anonymity '));
            $objLevel = $this->system->loadModel('member/level');
            $aLevel = $objLevel->getFieldById($GLOBALS['runtime']['member_lv'], array('name'));
            $aData['levelname'] = $aLevel['name'];
            $aData['contact'] = ($_POST['contact']=='' ? $this->member['email'] : $_POST['contact']);
            $aData['time'] = time();
            $aData['lastreply'] = time();
            $aData['reply_name'] = $aData['author'];
            $aData['ip'] = remote_addr();
            $aData['display'] = ($this->system->getConf('comment.display.'.$aComment['object_type'])=='soon' ? 'true' : 'false');
            $objComment->toReply($aData);
            
            $this->splash('success', $this->system->mkUrl('product','index', array($aComment['goods_id'])),  __('reply success!'));
        }
    }
}
?>