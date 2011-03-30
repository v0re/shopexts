<?php
/**
 * ctl_gask
 *
 * @uses adminPage
 * @package
 * @version $Id$
 * @copyright 2003-2007 ShopEx
 * @author Liujy <ever@shopex.cn>
 * @license Commercial
 */
include_once('objectPage.php');
class ctl_gask extends objectPage{

    var $name='咨询';
    var $workground = 'member';
    var $object = 'comment/gask';
    var $actionView = 'member/gask/finder_action.html'; //默认的动作html模板,可以为null
    var $filterView = 'member/gask/finder_filter.html'; //默认的过滤器html,可以为null

    var $disableGridEditCols = "object_type";
    var $disableColumnEditCols = "object_type";
    var $disableGridShowCols = "object_type";

    function setting(){
        $this->path[] = array('text'=>'咨询设置');
        $comment = $this->system->loadModel('comment/comment');
        $aOut = $comment->getSetting('ask');
        if(!$aOut['verifyCode']['ask']){
            $aOut['verifyCode']['ask']='off';
        }
        $aOut['aSwitch']['ask'] = array('on'=>__('开启'), 'off'=>__('关闭'));
        $aOut['aPower']['ask'] = array('null'=>__('所有顾客都可咨询'), 'member'=>__('只有注册会员才能咨询'));
        $aOut['verifyLCode']['ask'] = array('on'=>__('开启'), 'off'=>__('关闭'));
        $this->pagedata['setting']= $aOut;
        $this->page('member/gask/setting.html');
    }

    function toSetting(){
        $comment = $this->system->loadModel('comment/comment');
        $comment->setSetting('ask', $_POST);
        $this->splash('success','index.php?ctl=member/gask&act=setting',__('保存成功!'));
    }

    function detail($comment_id){
        $Mem = $this->system->loadModel('member/member');
        $objComment = $this->system->loadModel('comment/comment');
        $aComment = $objComment->getCommentById($comment_id);
        $aComment['url']=$this->system->realUrl('product','index',array($aComment['goods_id']),null,$this->system->base_url());
        $this->pagedata['comment'] = $aComment;
        $this->pagedata['reply'] = $objComment->getCommentReply($comment_id);

        $this->setView('member/gask/detail.html');
        $data = $Mem->getMemIdByName($aComment['author']);

        $mem_id = $data[0]['member_id'];//
        $this->pagedata['reply'] = $objComment->getCommentReply($comment_id);
        $tree = $Mem->getContactObject($mem_id);
        $this->pagedata['tree'] = $tree;

        $objComment->setReaded($comment_id);
        $this->output();
    }

    function delete(){

        if($_REQUEST['f_id']){
            $this->begin('index.php?ctl=member/gask&act=detail&p[0]='.$_REQUEST['f_id']);
        }else{
            $this->begin('index.php?ctl=member/gask&act=index');
        }
        if($_POST['_finder']){
            parent::delete();
        }else{
            $objComment = $this->system->loadModel('comment/comment');
            if(is_array($_REQUEST['comment_id']))
                foreach($_REQUEST['comment_id'] as $id){
                    $objComment->toRemove($id);
                }
        }
        $this->end(true, __('操作成功!'));
    }

    function toDisplay($comment_id, $status='false', $f_id=0){
        $this->begin('index.php?ctl=member/gask&act=detail&p[0]='.($f_id?$f_id:$comment_id));
        $objComment = $this->system->loadModel('comment/comment');
        if(intval($comment_id) > 0){
            if($status == 'true'){
                $status = 'false';
            }else{
                $status = 'true';
            }
            $this->end($objComment->toDisplay(intval($comment_id), $status), __('操作成功!'));
        }else{
            $this->end(false, __('操作失败: 传入参数丢失!'));
        }
    }

    function toReply($comment_id){
        $this->begin('index.php?ctl=member/gask&act=detail&p[0]='.$comment_id);
        $objComment = $this->system->loadModel('comment/comment');
        $aComment = $objComment->getFieldById($comment_id, array('*'));
        $aData['comment'] = $this->in['reply_content'];
        $aData['for_comment_id'] = $comment_id;
        $aData['goods_id'] = $aComment['goods_id'];
        $aData['object_type'] = $aComment['object_type'];
        $aData['author_id'] = $this->op->op_id;
        $aData['author'] = __('BagXO').'['.($this->op->loginName?$this->op->loginName:$this->op->name).']';
        $aData['time'] = time();
        $aData['lastreply'] = time();
        $aData['display'] = 'true';
        $aData['ip'] = remote_addr();
        $this->end($objComment->toReply($aData), __('回复成功!'));
    }

    function setIndexOrder(){
        $objComment = $this->system->loadModel('comment/comment');
        if(count($_POST['comment_id']) > 0)
            foreach($_POST['comment_id'] as $id){
                $objComment->setIndexOrder($id);
            }
        unset($_POST);
        echo '操作成功!';
    }
}
?>
