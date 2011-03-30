<?php
/**
 * ctl_comment 
 * 
 * @uses adminPage
 * @package 
 * @version $Id: ctl.comment.php 1867 2008-04-23 04:00:24Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Liujy <ever@shopex.cn> 
 * @license Commercial
 */
class ctl_comment extends adminPage{

    var $workground = 'goods';
    var $aItems = array('ask','discuss');
    
    function toSetting(){
        foreach($this->aItems as $item){
            $this->system->setConf('comment.switch.'.$item, $this->in['switch'][$item]);
            $this->system->setConf('comment.display.'.$item, $this->in['display'][$item]);
            $this->system->setConf('comment.power.'.$item, $this->in['power'][$item]);
            $this->system->setConf('comment.null_notice.'.$item, $this->in['null_notice'][$item]);
            $this->system->setConf('comment.submit_display_notice.'.$item, $this->in['submit_display_notice'][$item]);
            $this->system->setConf('comment.submit_hidden_notice.'.$item, $this->in['submit_hidden_notice'][$item]);
        }
        $this->system->setConf('comment.index.listnum', $this->in['indexnum']);
        $this->system->setConf('comment.list.listnum', $this->in['listnum']);
        
        $this->splash('success','index.php?ctl=goods/comment&act=setting',__('保存成功!'));
    }
    
    function toRemove($comment_id){
        $objComment = $this->system->loadModel('comment/comment');
        $aComment = array();
        if(intval($comment_id) > 0){
            $aComment[] = intval($comment_id);
        }else{
            if(count($this->in['items']['items']) > 0) $aComment = $this->in['items']['items'];
        }
        
        foreach($aComment as $id){
            $objComment->toRemove($id);
        }
        unset($aComment);
        echo __('删除成功!');
        $this->index();
    }
    
    function toDisplay($comment_id, $status='false'){
        $objComment = $this->system->loadModel('comment/comment');
        if(intval($comment_id) > 0){
            if($status == 'true'){
                $status = 'false';
            }else{
                $status = 'true';
            }
            $objComment->toDisplay(intval($comment_id), $status);
            echo __('操作成功!');
        }else{
            echo __('操作失败: 传入参数丢失!');
        }
        $this->index();
    }
    
    function toReply($comment_id){
        $objComment = $this->system->loadModel('comment/comment');
        $aComment = $objComment->getFieldById($comment_id, array('*'));
        
        $aData['comment'] = $this->in['reply_content'];
        $aData['for_comment_id'] = $comment_id;
        $aData['goods_id'] = $aComment['goods_id'];
        $aData['object_type'] = $aComment['object_type'];
        $aData['author_id'] = $this->op->op_id;
        $aData['author'] = __(' BagxO ');
        $aData['time'] = time();
        $aData['lastreply'] = time();
        $aData['display'] = 'true';
        $objComment->toReply($aData);
        echo __('回复成功!');
        $this->index();
    }
    
    function setIndexOrder(){
        $objComment = $this->system->loadModel('comment/comment');
        if(count($_POST['comment_id']) > 0)
            foreach($_POST['comment_id'] as $id){
                $objComment->setIndexOrder($id);
            }
        unset($this->in);
        echo __('操作成功!');
    }
}
?>