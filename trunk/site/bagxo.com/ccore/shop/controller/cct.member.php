<?php
class cct_member extends ctl_member{

    function cct_member(&$system){
        parent::shopPage($system);
        $this->_verifyMember(true);
        $this->header .= '<meta name="robots" content="noindex,noarchive,nofollow" />';
        $this->title='My Account';
        $action = $this->system->request['action']['method'];
        $this->_tmpl = $action.'.html';
        $this->map = array(
            array('label'=>'交易记录',
                  'items'=>array(   
				      array('label'=>'My Account Home','link'=>'index'),                                             
                      array('label'=>'Order Status & History','link'=>'orders'),
					  array('label'=>'My coupon','link'=>'coupon'),
					  array('label'=>'Exchange coupons','link'=>'couponExchange')
                                  )
                ),
  
   
            array('label'=>'个人设置',
                  'items'=>array(
				   array('label'=>'Shipping Address','link'=>'receiver'),
				   array('label'=>'Change password','link'=>'security'),
                   array('label'=>'Personal Profile','link'=>'setting'),                 
                      )
                ),
				
            array('label'=>'收藏夹',
                  'items'=>array(
				      array('label'=>'Sharing & Quires','link'=>'comment'),
                      array('label'=>'Wish List','link'=>'favorite'),
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
        if( ($this->system->getConf('site.is_open_return_product') === false) || ($this->system->getConf('site.is_open_return_product') === "false") ){
            unset($this->map[6]);
        }

        $this->_action = $action;
    }

  

}
?>
