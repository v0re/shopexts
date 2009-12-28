<?php
class cct_member extends ctl_member{
	function cct_member(&$system){
		parent::ctl_member($system);
		$this->map = array(
            array('label'=>'咨询报价',
                  'items'=>array(
                      array('label'=>'报价列表','link'=>'askpric'),
                      )
                ),
            array('label'=>'交易记录',
                  'items'=>array(                                                
                      array('label'=>'我的订单','link'=>'orders'),
                      array('label'=>'我的积分','link'=>'pointHistory'),
                      array('label'=>'积分兑换优惠券','link'=>'couponExchange'),
                      array('label'=>'我的优惠券','link'=>'coupon')
                      )
                ),
            array('label'=>'收藏夹',
                  'items'=>array(
                      array('label'=>'商品收藏','link'=>'favorite'),
                      array('label'=>'缺货登记','link'=>'notify'),
                      )
                ),
            array('label'=>'商品留言',
                  'items'=>array(
                      array('label'=>'评论与咨询','link'=>'comment'),
                      )
                ),

            array('label'=>'个人设置',
                  'items'=>array(
                      array('label'=>'个人信息','link'=>'setting'),
                      array('label'=>'修改密码','link'=>'security'),
                      array('label'=>'收货地址','link'=>'receiver'),
                      )
                ),
            array('label'=>'预存款',
                  'items'=>array(
                      array('label'=>'我的预存款','link'=>'balance'),
                      array('label'=>'预存款充值','link'=>'deposit'),
                      )
                ),
            array('label'=>"站内消息(".$this->member['unreadmsg'].")",
                  'items'=>array(
                      array('label'=>'发送消息','link'=>'send'),
                      array('label'=>__('收件箱'),'link'=>'inbox'),
                      array('label'=>'草稿箱','link'=>'outbox'),
                      array('label'=>'发件箱','link'=>'track'),
                      array('label'=>'给管理员发消息','link'=>'message'),
                      //    array('label'=>'搜索短消息','link'=>'review'),
                      //    array('label'=>'导出短消息','link'=>'review'),
                      //    array('label'=>'忽略列表','link'=>'review'),
                      )
                ),
            array('label'=>'售后服务',
                  'items'=>array(
                      array('label'=>'申请售后服务','link'=>'return_policy')
                      )
                ),

//              array('label'=>'商业合作',
//                'items'=>array(
//                      array('label'=>'合作方式','link'=>'partner'),
//                      array('label'=>'申请成为代理','link'=>'agent'),
//                      array('label'=>'文档与协议','link'=>'shared'),
//                      array('label'=>'佣金结算','link'=>'commission'),
//                  )
//                ),
            );
	}

    function ajaxAddAsk($nGid){
        if(!$this->member['member_id']){
            echo '<script>alert('.__('未登陆').');</script>';
            exit;
        }
        if($nGid){
            $oMem = $this->system->loadModel('member/member');
            $oMem->addAsk($this->member['member_id'],$nGid);
        }
    }

    function askPric($nPage=1){
        $oMem = $this->system->loadModel('member/member');
        $aData = $oMem->getAskPric($this->member['member_id'],$nPage-1);
        $this->pagedata['favorite'] = $aData['data'];
        $this->pagination($nPage,$aData['page'],'askPric');
        $setting['buytarget'] = $this->system->getConf('site.buy.target');
        $this->pagedata['setting'] = $setting;
        $this->_output();
    }
}
?>
