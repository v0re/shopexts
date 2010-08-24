<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_mdl_member_advance extends dbeav_model{

      function check_account($member_id,&$errMsg,$money){
          $objMember = &$this->app->model('members');
        $row= $objMember->dump($member_id,'advance');
        if($row){
              // print_r($row);exit;
            if(count($row)>0){
               if($money > $row['advance']['total']){
                    $errMsg .= __('预存款帐户余额不足');
                    return 0;
                }else{
                    return $row;
                }
            }else{
                $errMsg .= __('预存款帐户不存在');
                return false;
            }
        }else{
            $errMsg .= __('查询预存款帐户失败');
            return false;
        }
        
      }
    /**
     * add 预存款充值
     *
     * @param mixed $member_id
     * @param mixed $money
     * @param mixed $message
     * @access public
     * @return void
     */
    function add($member_id,$money,$message,&$errMsg, $payment_id='', $order_id='' ,$paymenthod='' ,$memo='',$type=0){
       if($money){
           $advance = $this->get($member_id);
            if($advance < 0){
                $errMsg .= __('更新预存款账户失败');
                return false;
            }
        $total = $advance + $money;
        $member['advance'] = $total;  
        $adjmember = &$this->app->model('members');
        $result = $adjmember->update($member,array('member_id'=>$member_id));    
        $member_advance = $this->get($member_id);
                    $data = array(
                        'member_id'=>$member_id,
                        'money'=>$money,
                        'message'=>$message,
                        'mtime'=>time(),
                        'payment_id'=>$payment_id,
                        'order_id'=>$order_id,
                        'paymethod'=>$paymenthod,
                        'memo'=>$memo,
                        'import_money'=>$money,
                        'explode_money'=>0,
                        'member_advance'=>$member_advance,
                        'shop_advance'=>$member_advance,
              );
            if($this->save($data)){                
                if (!$type){
                    $data['member_id']=$member_id;
                    #$this->fireEvent('member/account:changeadvance',$data,$member_id);
                }
                return true;
            }else{
                $errMsg .= __('更新预存款帐户失败');
                return false;
            }
        }else{
            
            return false;
        }
    }
    

    /**
     * deduct 扣除预存款
     *
     * @param mixed $member_id
     * @param mixed $money
     * @param mixed $message
     * @access public
     * @return void
     */
    function deduct($member_id,$money,$message,&$errMsg, $payment_id='', $order_id='' ,$paymethod='' ,$memo=''){

        if($row = $this->check_account($member_id,$errMsg,$money)){
        $advance = $this->get($member_id);
        $total = $advance - $money;
        $member['advance'] = $total;  
        $adjmember = &$this->app->model('members');
        $result = $adjmember->update($member,array('member_id'=>$member_id));
        $member_advance = $this->get($member_id);
            $data['member_id'] = $member_id;
            $data['advance']['total'] = $row['advance']['total'] + $money;
             if($data['advance']['total'] < 0){
                $errMsg .= __('更新预存款账户失败');
                return false;
            }
                $data = array(
                        'member_id'=>$member_id,
                        'money'=>$money,
                        'message'=>$message,
                        'mtime'=>time(),
                        'payment_id'=>$payment_id,
                        'order_id'=>$order_id,
                        'paymethod'=>$paymenthod,
                        'memo'=>$memo,
                        'import_money'=>0,
                        'explode_money'=>$money,
                        'member_advance'=>$member_advance,
                        'shop_advance'=>$member_advance,
              );
            if($this->save($data
                )){         
                return true;
            }else{
                $errMsg .= __('更新预存款帐户失败');
                return false;
            }
        }else{
            echo $errMsg;
            return false;
        }
        
    }

    /**
     * getListByMemId 取得现有预存款充值记录
     *
     * @param mixed $member_id
     * @access public
     * @return void
     */
    function get_list_bymemId($member_id){
        return $this->getList('*',array('member_id'=>$member_id));
    }
    /**
     * get 取得现有预存款
     *
     * @param mixed $member_id
     * @access public
     * @return void
     */
    function get($member_id){
        $member = &$this->app->model('members');
        $result = $member->dump($member_id);
        $advance=$result['advance']['total'];
        return $advance;
    }
    
    function adj_amount($nMemberId,$aAdvanceInfo){
         $advance = $aAdvanceInfo['modify_advance'];
         $memo = $aAdvanceInfo['modify_memo'];
         
         $operator = substr($advance,0,1);
         $operand = substr($advance,0);
         if($operator == '-' && is_numeric($operand) ){
             $message = __('管理员后台扣款');
           return $this->deduct($nMemberId,-$advance,$message,$errMsg, $payment_id='', $order_id='' ,$paymethod='' ,$memo);
         }elseif(is_numeric($operand)){
             $message = __('管理员代充值');
           return $this->add($nMemberId,$advance,$message,$errMsg, $payment_id='', $order_id='' ,$paymethod='' ,$memo,$type=0);
         }
    }
 
    
}
?>
