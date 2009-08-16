<?php
class cct_member extends ctl_member{

	 function orderdetail($order_id){
        $objOrder = $this->system->loadModel('trading/order');
        $aOrder = $objOrder->load($order_id);
        $this->_verifyMember($aOrder['member_id']);
        $this->pagedata['orderlogs'] = $objOrder->getLogs($order_id);
        
        if(!$aOrder||$this->member['member_id']!=$aOrder['member_id']){
            $this->system->error(404);
            exit;
        }
        if($aOrder['member_id']){
            $member = $this->system->loadModel('member/member');
            $aMember = $member->getFieldById($aOrder['member_id'], array('email'));
            $aOrder['receiver']['email'] = $aMember['email'];
        }
        $this->pagedata['order'] = $aOrder;

        $gItems = $objOrder->getItemList($order_id);
        foreach($gItems as $key => $item){
            $gItems[$key]['addon'] = unserialize($item['addon']);
            if($item['minfo'] && unserialize($item['minfo'])){
                $gItems[$key]['minfo'] = unserialize($item['minfo']);
            }else{
                $gItems[$key]['minfo'] = array();
            }
        }
				#				
				$this->pagedata['order']['ismarked'] = $objOrder->isMarked($order_id);;
				#
        $this->pagedata['order']['items'] = $gItems;
        $this->pagedata['order']['giftItems'] = $objOrder->getGiftItemList($order_id);
        //----查找物流公司相关信息
        /*
        $corp=$this->system->loadModel('trading/delivery');
        $cinfo=$corp->getCorpInfoByShipId($this->pagedata['order']['shipping']['id']);
        $corp=array('name'=>$cinfo['name'],'website'=>$cinfo['website']);
        $this->pagedata['order']['corp']=$corp;*/
        //----
        
        $oMsg = $this->system->loadModel('resources/message');
        $orderMsg = $oMsg->getOrderMessage($order_id);
        $this->pagedata['ordermsg'] = $orderMsg;

        $this->_output();
    }

	function delOrderMsg( $orderId, $msgType = 0 ){
			$timeHours = array();
			for($i=0;$i<24;$i++){
					$v = ($i<10)?'0'.$i:$i;
					$timeHours[$v] = $v;
			}
			$timeMins = array();
			for($i=0;$i<60;$i++){
					$v = ($i<10)?'0'.$i:$i;
					$timeMins[$v] = $v;
			}
			$this->pagedata['orderId'] = $orderId;
			$this->pagedata['msgType'] = $msgType;
			$this->pagedata['timeHours'] = $timeHours;
			$this->pagedata['timeMins'] = $timeMins;
			$this->_output();
	}

	function toDelOrderMsg(){
        $this->begin($this->system->mkUrl('member','orders'));
        $oOrder = $this->system->loadModel('trading/order');
        $data = array();
        $data['rel_order'] = $_POST['msg']['orderid'];
        $data['date_line'] = time();
        $data['msg_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['msg_from'] = $this->member['uname'];
        $data['from_id'] = $this->member['uid'];
        $this->end($oOrder->delOrderMsg($data),'订单已经删除');
  }
}
?>
