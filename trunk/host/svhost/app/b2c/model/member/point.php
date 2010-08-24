<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_member_point extends dbeav_model{
    //type: 1.订单得积分,2.消费积分,3.无分类
    function getHistoryReason() {

        $aHistoryReason = array(
                            'order_pay_use' => array(
                                                    'describe' => __('订单消费积分'),
                                                    'type' => 1,
                                                    'related_id' => 'sdb_b2c_mall_orders',
                                                ),
                            'order_pay_get' => array(
                                                    'describe' => __('订单获得积分.'),
                                                    'type' => 2,
                                                    'related_id' => 'sdb_b2c_mall_orders',
                                                ),
                            'order_refund_use' => array(
                                                    'describe' => __('退还订单消费积分'),
                                                    'type' => 1,
                                                    'related_id' => 'sdb_b2c_mall_orders',
                                                ),
                            'order_refund_get' => array(
                                                    'describe' => __('扣掉订单所得积分'),
                                                    'type' => 2,
                                                    'related_id' => 'sdb_b2c_mall_orders',
                                                ),
                            'order_cancel_refund_consume_gift' => array(
                                                    'describe' => __('Score deduction for gifts refunded for order cancelling.'),
                                                    'type' => 1,
                                                    'related_id' => 'sdb_b2c_mall_orders',
                                                ),
                            'exchange_coupon' => array(
                                                    'describe' => __('兑换优惠券'),
                                                    'type' => 3,
                                                    'related_id' => '',
                                                ),
                            'operator_adjust' => array(
                                                    'describe' => __('管理员改变积分.'),
                                                    'type' => 3,
                                                    'related_id' => '',
                                                ),
                            'consume_gift' => array(
                                                    'describe' => __('积分换赠品.'),
                                                    'type' => 3,
                                                    'related_id' => 'sdb_b2c_mall_orders',
                                                ),
                            'fire_event' => array(
                                                      'describe' => __('网店机器人触发事件'),
                                                      'type' => 3,
                                                      'related_id' =>'',
                                                ),
            );
        return $aHistoryReason;
    }   
    
    //检查用户积分是否足够
    function _chgPoint($userId, $nCheckPoint) {
        if ($nCheckPoint<0) {
            $nPoint = $this->getMemberPoint($userId);
            if ($nPoint >= abs($nCheckPoint)) {
                return true;
            }else{
                return false;
            }
        }else {
            return true;
        }
    }
    ##管理员扣除积分
    function adj_amount($nMemberId,$pointInfo,&$msg){
        $objMember = &$this->app->model('members');
        $row = $objMember->dump($nMemberId,'*');
        $falg = 1;
        if($pointInfo['modify_point']<0){
            if(!($this->app->getConf('site.level_point'))){
                $falg = 0;
            }
            if($row['score']['total']<-$pointInfo['modify_point']){
                $msg =  "积分扣除超过会员已有积分";
                return false;
            }
        }
        $newValue = $row['score']['total'] + $pointInfo['modify_point'];
        $sdf_member = $objMember->dump($nMemberId,'*');
        $sdf_member['score']['total'] = $newValue;
        if(($this->app->getConf('site.level_switch') == 0) && $falg == 1){
        $sdf_member['member_lv']['member_group_id'] = $this->member_lv_chk($newValue);
        }
        $objMember->save($sdf_member);
        $point = $pointInfo['modify_point'];
        $reasons = $this->getHistoryReason();
        $reason = $reasons['operator_adjust'];
        $remark = $pointInfo['modify_remark'];
        $sdf_point = array(
                          'member_id'=>$nMemberId,
                          'point'=>$newValue,
                          'addtime'=>time(),
                          'expiretime'=>time() ,
                          'reason'=>$reason['describe'],
                          'remark'=>$remark,
                          'type'=>$reason['related_id'],
                          'type'=>$reason['type'],
                          'operator'=>$operator
                                 );
        if($this->insert($sdf_point)){
            $msg = "修改成功";
            return true;
        }
        else{
            $msg = "修改失败";
            return false;
        }

    }
    
    ###通用改变积分方法
    function change_point($nMemberId,$point,&$msg,$reason_type,$type=0){
        $objMember = &$this->app->model('members');
        $row = $objMember->dump($nMemberId,'*');
        $falg = 1;
        if($point<0){
            if(!($this->app->getConf('site.level_point'))){
                $falg = 0;
            }
            if($row['score']['total']<-$point){
                $msg = "积分扣除超过会员已有积分";return false;
            }
        }
        $newValue = $row['score']['total'] + $point;
        $sdf_member = $objMember->dump($nMemberId,'*');
        $sdf_member['score']['total'] = $newValue;
        if(($this->app->getConf('site.level_switch')== 0) && $falg == 1){
            $sdf_member['member_lv']['member_group_id'] = $this->member_lv_chk($newValue);
        }
        $objMember->save($sdf_member);
        $reasons = $this->getHistoryReason();
        $reason = $reasons[$reason_type];
        $remark = $pointInfo['modify_remark'];
        $sdf_point = array(
                          'member_id'=>$nMemberId,
                          'point'=>$newValue,
                          'addtime'=>time(),
                          'expiretime'=>time() ,
                          'reason'=>$reason['describe'],
                          'type'=>$type
                                 );
       if($this->insert($sdf_point)){
            $msg = "修改成功";
            return true;
        }
        else{
            $msg = "修改失败";
            return false;
        }
        
    }
    
    
    ###根据积分修改会员等级
    
    function member_lv_chk($score){
        $objmember_lv = $this->app->model('member_lv');
        $sdf_lv = $objmember_lv->getList('*');
        $member_lv_id = $objmember_lv->get_default_lv();
        foreach($sdf_lv as $sdf){
         if($score>=$sdf['point']) {$member_lv_id = $sdf['member_lv_id'];
          }
          else{
              
          }
        }
        return $member_lv_id;
    }
    
    
    
    
}
