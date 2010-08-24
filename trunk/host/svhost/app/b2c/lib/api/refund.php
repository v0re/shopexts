<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * ectools refund interactor with center
 * shopex team
 * dev@shopex.cn
 */
class b2c_api_refund
{
    /**
     * app object
     */
    public $app;
    
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = app::get('ectools');
        $this->app_b2c = $app;
    }
    
    /**
     * 退款单创建
     * @param array sdf
     * @return boolean success or failure
     */
    public function create(&$sdf, $thisObj)
    {
        // 退款单创建是和中心的交互
        $is_payed = false; 
        $obj_refund = $this->app->model('refunds');
        $obj_order = $this->app_b2c->model('orders');
        $objMath = kernel::single('ectools_math');        
        
        $refund_id = $sdf['refund_id'] = $obj_refund->gen_id();
        $refundArr = array(
            'refund_id' => $sdf['refund_id'],
            'refund_bn' => $sdf['refund_bn'],
            'order_id' => $sdf['order_bn'],
            'account' => $sdf['account'],
            'bank' => $sdf['bank'],
            'pay_account' => $sdf['pay_account'] ? $sdf['pay_account'] : '付款帐号',
            'currency' => $sdf['currency'],
            'money' => $sdf['money'],
            'paycost' => $sdf['paycost'],
            'cur_money' => $sdf['cur_money'],
            'pay_type' => $sdf['pay_type'],
            'pay_app_id' => $sdf['pay_name'],
            'pay_name' => $sdf['pay_name'],
            'pay_ver' => '1.0',
            'op_id' => '0',
            'ip' => $sdf['ip'],
            't_begin' => $sdf['t_begin'],
            't_payed' => $sdf['t_payed'],
            't_confirm' => $sdf['t_confirm'],
            'status' => $sdf['status'],
            'trade_no' => $sdf['trade_no'],
            'memo' => $sdf['memo'],
            'return_url' => '',
            'orders' => array(
                array(
                    'rel_id' => $sdf['order_bn'],
                    'bill_type' => 'refunds',
                    'pay_object' => 'order',
                    'bill_id' => $sdf['refund_id'],
                    'money' => $sdf['money'],
                )
            )
        );
        
        $is_save = $obj_refund->save($refundArr);
        
        if ($is_save)
        {
            // 修改订单状态.
            $arr_order = $obj_order->dump($sdf['order_bn']);
            $arr_order['order_id'] = $sdf['order_bn'];
            if ($objMath->number_minus(array($arr_order['payed'], $sdf['money'])) <= 0 && $sdf['status'] == 'succ')
            {
                $arr_order['pay_status'] = '5';
                $arr_order['payed'] = 0;
            }
            else
            {
                $arr_order['pay_status'] = '4';
                $arr_order['payed'] = $objMath->number_minus(array($arr_order['payed'], $sdf['money']));
            }            
            $arr_order['payed'] = $objMath->number_plus(array($arr_order['payed'], $sdf['money']));
            $is_payed = $obj_order->save($arr_order);
            
            $objOrderLog = $this->app->model("order_log");                
            $sdf_order_log = array(
                'rel_id' => $sdf['order_bn'],
                'op_id' => "1",
                'op_name' => "admin",
                'alttime' => time(),
                'bill_type' => 'order',
                'behavior' => 'refunds',
                'result' => 'SUCCESS',
                'log_text' => '订单' . $sdf['order_bn'] . '退款' . $sdf['money'],
            );
                
            $log_id = $objOrderLog->save($sdf_order_log);
            
            $aUpdate['order_id'] = $sdf['order_bn'];
            if ($arr_order['member_id'])
            {
                $member = $this->app->model('members');
                $arr_member = $member->dump($arr_order['member_id'], '*', array(':account@pam'=>'*'));
            }
            $aUpdate['email'] = (!$arr_order['member_id']) ? $arr_order['consignee']['email'] : $arr_member['contact']['email'];
            $aUpdate['pay_status'] = ($arr_order['pay_status'] == '5') ? 'REFUND_ALL' : 'REFUND_PART'; 
                                    
            $obj_order->fireEvent('refund', $aUpdate, $arr_order['member_id']);
        }
        
        if (!$is_save || !$is_payed)
        {
            $thisObj->send_user_error(__('退款单生成失败！'), array());
        }
        
        return array('refund_id' => $refund_id);
    }
    
    /**
     * 退款单修改
     * @param array sdf
     * @return boolean sucess of failure
     */
    public function update(&$sdf, $thisObj)
    {
        // 退款单修改是和中心的交互
        $objRefunds = $this->app->model('refunds');        
        $arr_refunds = $this->dump(array('refund_bn' => $sdf['refund_bn']));
        
        if (isset($arr_refunds) && $arr_refunds)
        {
            $arr_refunds['account'] = $sdf['account'] ? $sdf['account'] : '';
            $arr_refunds['bank'] = $sdf['bank'] ? $sdf['bank'] : '';
            $arr_refunds['pay_account'] = $sdf['pay_account'] ? $sdf['pay_account'] : '';
            $arr_refunds['op_id'] = $sdf['op_id'] ? $sdf['op_id'] : '';
            $arr_refunds['ip'] = $sdf['ip'] ? $sdf['ip'] : '';
            $arr_refunds['t_payed'] = $sdf['t_sent'] ? $sdf['t_sent'] : '';
            $arr_refunds['t_confirm'] = $sdf['t_received'] ? $sdf['t_received'] : '';
            $arr_refunds['status'] = $sdf['status'] ? $sdf['status'] : '';
            $arr_refunds['trade_no'] = $sdf['trade_no'] ? $sdf['trade_no'] : '';
            $arr_refunds['memo'] = $sdf['memo'] ? $sdf['memo'] : '';
            
            $is_save = $objRefunds->save($arr_refunds);
            
            if ($is_save)
                return true;
            else
            {
                $thisObj->send_user_error(__('支付单修改失败！'), array());
            }
        }
        else
        {
            $thisObj->send_user_error(__('退款单不存在！'), array());
        }
    }
}