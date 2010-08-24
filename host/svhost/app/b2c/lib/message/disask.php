<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_message_disask extends b2c_message_comment{

    function __construct(&$app){
         
        $this->app = $app;
        parent::__construct($app);
     }
     
     #插入评论/咨询
  function send($aData,$item){
        $sdf['for_comment_id'] = $aData['for_comment_id']?$aData['for_comment_id']:0;
        if($sdf['for_comment_id']){
            $aRes = $this->dump($sdf['for_comment_id']);
            $aRes['lastreply'] = time();
            $aRes['reply_name'] = $aData['author'];
            $this->save($aRes);
        }
      $sdf['type_id'] = $aData['goods_id'];
      $sdf['object_type'] = $item;
      $sdf['author_id'] = $aData['author_id'];
      $sdf['author'] = $aData['author'];
      $sdf['contact'] = htmlspecialchars($aData['contact']);
      $sdf['title'] = htmlspecialchars($aData['title']);
      $sdf['comment'] = htmlspecialchars($aData['comment']);
      $sdf['time'] = $aData['time'];
      $sdf['lastreply'] = $aData['lastreply'];
      $sdf['ip'] = $aData['ip'];
      $sdf['display'] = $aData['display'];
      if($this->save($sdf)){
          return true;
      }
      else{
          return false;
      }
  }
     

  function get_message($comment_id){
          $aData = $this->getList('*',array('object_type' => $this->type,'for_comment_id' => 0,'comment_id' => $comment_id));
          return $aData[0];
  }
  
  ////读取商品评论回复列表
   function getCommentsReply($aId, $display=false){
        if($display)
        {
            $aData = $this->getList('*',array('for_comment_id' => $aId,'display' => 'true'));
        }
        return $aData;
    }
    
    function get_member_comments($member_id,$page){
        $list_listnum = intval($this->app->getConf('comment.list.listnum'));
        $this->objComment->type = array('ask','discuss');
        $params = $this->getList('*',array('for_comment_id' => 0,'author_id' => $member_id,'display' => 'true'));
        $count = count($params);
        $maxPage = ceil($count / $list_listnum);
        if($page > $maxPage) $page = $maxPage;
        $start = ($page-1) * $list_listnum;
        $start = $start<0 ? 0 : $start; 
        $params['data'] = $this->getList('*',array('for_comment_id' => 0,'author_id' => $member_id,'display' => 'true'),$start,$list_listnum);
        #print_r($params['data']);exit;
        foreach($params['data'] as $key=>$v){
            $params['data'][$key]['items'] = $this->get_reply($v['comment_id']);
        }
        $params['page'] = $maxPage;
        return $params;
    }
    
    //咨询评论回复记录数
    function calc_unread_disask($member_id){
        $this->objComment->type = array('ask','discuss');
        $aData = $this->getList('comment_id',array('author_id' => $member_id));
        $i = 0;
        foreach($aData as $v){
            $row = $this->getList('comment_id',array('for_comment_id' => $v['comment_id']));
            if($row){
                $i++;
            }
        }
        return $i;    
    }
    function getGoodsIndexComments($gid,$item){
        $index_listnum = intval($this->app->getConf('comment.index.listnum'));
        $this->objComment->type = $item;
        $aData = $this->getList('*',array('for_comment_id' => 0,'type_id'=>$gid,'display'=>'true'));
        $count = count($aData);
        $data = array();
        foreach($aData as $key=>$val){
            if($key<$index_listnum){
                $data[] = $val;
            }
            else{
                break;
            }
        }
        $result['total'] = $count;
        $result['data'] = $data;
        return $result;        
    }
    
    function getGoodsCommentList($gid,$item,$page=1){
        $list_listnum = intval($this->app->getConf('comment.list.listnum'));
        $this->objComment->type = $item;
        $aData = $this->getList('*',array('for_comment_id' => 0,'type_id'=>$gid,'display'=>'true'));
        $count = count($aData);
        $maxPage = ceil($count / $list_listnum);
        if($page > $maxPage) $page = $maxPage;
        $start = ($page-1) * $list_listnum;
        $start = $start<0 ? 0 : $start;
        $data = $this->getList('*',array('for_comment_id' => 0,'type_id'=>$gid,'display'=>'true'),$start,$list_listnum);
        $result['total'] = $count;
        $result['page'] = $maxPage;
        $result['data'] = $data;
        return $result;        
    }
    
     function toValidate($item, $gid, $memInfo, &$message){
        if($this->app->getConf('comment.switch.'.$item) != 'on'){
            $this->_response->set_http_response_code(404);
            return false;
            exit;
        }

        if($this->app->getConf('comment.power.'.$item) != 'null' && !isset($memInfo['member_id'])){
            $message = __('非会员不能发表!');
            return false;
            exit;
        }

        if($this->app->getConf('comment.power.'.$item) == 'buyer' && $memInfo['member_id']){
            $this->db = kernel::database();
            $aRet = $this->db->selectrow('SELECT count(*) AS countRows FROM sdb_b2c_order_items i
                        LEFT JOIN sdb_b2c_orders o ON i.order_id = o.order_id
                        LEFT JOIN sdb_b2c_products p ON i.product_id = p.product_id
                        WHERE o.member_id='.intval($memInfo['member_id']).' AND p.goods_id='.intval($gid).' AND (o.pay_status>"0" OR o.ship_status>"0")');
            if($aRet['countRows'] == 0){
                $message = __('未购买过该商品不能发表!');
                return false;
                exit;
            }
        }
        return true;
    }
    
     #获取设置
   function get_setting($item){
        $aOut['switch'][$item] = $this->app->getConf('comment.switch.'.$item);
        $aOut['display'][$item] = $this->app->getConf('comment.display.'.$item);
        $aOut['power'][$item] = $this->app->getConf('comment.power.'.$item);
        $aOut['null_notice'][$item] = $this->app->getConf('comment.null_notice.'.$item);
        $aOut['submit_display_notice'][$item] = $this->app->getConf('comment.submit_display_notice.'.$item);
        $aOut['submit_hidden_notice'][$item] = $this->app->getConf('comment.submit_hidden_notice.'.$item);

        $aOut['index'] = intval($this->app->getConf('comment.index.listnum'));
        $aOut['list'] = intval($this->app->getConf('comment.list.listnum'));
        $aOut['verifyCode'][$item] = $this->app->getConf('comment.verifyCode.'.$item);
        return $aOut;
    }

#设置

   function to_setting($item,$aData){
        $this->app->setConf('comment.switch.'.$item, $aData['switch'][$item]);
        $this->app->setConf('comment.display.'.$item, $aData['display'][$item]);
        $this->app->setConf('comment.power.'.$item, $aData['power'][$item]);
        $this->app->setConf('comment.null_notice.'.$item, $aData['null_notice'][$item]);
        $this->app->setConf('comment.submit_display_notice.'.$item, $aData['submit_display_notice'][$item]);
        $this->app->setConf('comment.submit_hidden_notice.'.$item, $aData['submit_hidden_notice'][$item]);

        $this->app->setConf('comment.index.listnum', $aData['indexnum']);
        $this->app->setConf('comment.list.listnum', $aData['listnum']);
        $this->app->setConf('comment.verifyCode.'.$item, $aData['verifyCode'][$item]);
   }
}