<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_refund extends b2c_api_rpc_request
{
    /**
     * 私有构造方法，不能直接实例化，只能通过调用getInstance静态方法被构造
     * @params null
     * @return null
     */
    public function __construct($app)
    {  
        parent::__construct($app);
    }
    
    /**
     * 退款单发送矩阵请求
     * @param array 数组值
     * @return null
     */
    public function send_request(&$sdf)
    {
        $obj_members = $this->app->model('members');
        $arrPams = $obj_members->dump($sdf['member_id'], '*', array(':account@pam' => array('*')));
            
        $arr_data = array();
        $arr_data['tid'] = $sdf['order_id'];
        $arr_data['refund_id'] = $sdf['refund_id'];
        $arr_data['buyer_bank'] = $sdf['bank'];
        $arr_data['buyer_account'] = $sdf['account'];
        $arr_data['buyer_name'] = $arrPams['pam_account']['login_name'];
        $arr_data['refund_fee'] = $sdf['money'];
        $arr_data['currency'] = $sdf['currency'];
        $arr_data['currency_fee'] = $sdf['cur_money'];
        $arr_data['pay_type'] = $sdf['pay_type'];
        $arr_data['payway_name'] = $sdf['pay_name']; 
        $arr_data['seller_account'] = $sdf['pay_account'];
        $arr_data['t_ready'] = date('Y-m-d H:i:s', $sdf['t_begin']);
        $arr_data['t_sent'] = date('Y-m-d H:i:s', $sdf['t_payed']);
        $arr_data['t_received'] = date('Y-m-d H:i:s', $sdf['t_confirm']);
        $arr_data['status'] = $sdf['status'] == 'succ' ? 'SUCC' : 'PROGRESS';
        $arr_data['memo'] = $sdf['memo'];
        $arr_data['outer_no'] = $sdf['trade_no'];
        
        $arr_callback = array(
            'class' => 'b2c_api_callback_app', 
            'method' => 'callback',
            'params' => array(
                'method' => 'store.trade.refund.add',
                'tid' => $arr_data['tid'],
            ),
        );
        
        //$rst = $this->app->matrix()->call('store.trade.refund.add', $arr_data);
        parent::request('store.trade.refund.add', $arr_data, $arr_callback, 'Order Refund', 1);
    }
}

?>
