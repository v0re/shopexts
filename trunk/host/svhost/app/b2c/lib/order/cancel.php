<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_cancel extends b2c_api_rpc_request
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
     * 最终的克隆方法，禁止克隆本类实例，克隆是抛出异常。
     * @params null
     * @return null
     */
    final public function __clone()
    {
        trigger_error("此类对象不能被克隆！", E_USER_ERROR);
    }
    
    /**
     * 订单取消
     * @params array - 订单数据
     * @params object - 控制器
     * @params string - 支付单生成的记录
     * @return boolean - 成功与否
     */
    public function generate($sdf, &$controller=null, &$msg='')
    {
        $is_save = false;
        $is_unfreeze = true;
        
        $order = $controller->app->model('orders');
        $sdf_order = $order->dump($sdf['order_id'], '*');
        
        //更新库存
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        $arrStatus = $obj_checkorder->checkOrderFreez('cancel', $sdf['order_id']);

        if ($arrStatus['unfreez'])
        {
            $is_unfreeze = $this->unfreezeGoods($sdf['order_id']);
        }
        
        //$obj_api_order = kernel::service("api.b2c.order");
        $sdf_order['status'] = 'dead';        
        $is_save = $order->save($sdf_order);
        $this->request($sdf_order['order_id']);
        
        
        // 更新退款日志结果
        if ($is_save && $is_unfreeze)
        {
            $objorder_log = $this->app->model('order_log');            
            $sdf_order_log = array(
                'rel_id' => $sdf['order_id'],
                'op_id' => $sdf['op_id'],
                'op_name' => $sdf['opname'],
                'alttime' => time(),
                'bill_type' => 'order',
                'behavior' => 'cancel',
                'result' => 'SUCCESS',
                'log_text' => '订单取消',
            );
            $log_id = $objorder_log->save($sdf_order_log);
        }
        
        $aUpdate['order_id'] = $sdf['order_id'];
        if ($sdf_order['member_id'])
        {
            $member = $this->app->model('members');
            $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
        }
        $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
        $order->fireEvent("cancel", $aUpdate, $sdf_order['member_id']);
        
        return ($is_save && $is_unfreeze);
    }
    
    private function unfreezeGoods($order_id)
    {
        $is_unfreeze = true;
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $this->app->model('orders')->dump($order_id, 'order_id,status,pay_status,ship_status', $subsdf);
        $storage_enable = $this->app->getConf('site.storage.enabled');
        
        $objGoods = &$this->app->model('goods');
        foreach($sdf_order['order_objects'] as $k => $v)
        {
            foreach ($v['order_items'] as $arrItem)
            {
                if ($storage_enable != 'true')
                    $is_unfreeze = $objGoods->unfreez($arrItem['products']['goods_id'], $arrItem['products']['product_id'], $arrItem['quantity']);
            }
        }       
        
        return $is_unfreeze;
    }
    
    /**
     * 订单取消事件埋点
     * @param array sdf
     * @return boolean success or failure
     */
    protected function request(&$sdf)
    {
        // 回朔待续...
        $arr_data['tid'] = $sdf;
        $arr_data['status'] = 'TRADE_CLOSED';
        
        $arr_callback = array(
            'class' => 'b2c_api_callback_app', 
            'method' => 'callback',
            'params' => array(
                'method' => 'store.trade.status.update',
                'tid' => $sdf,
            ),
        );
        
        //$rst = $this->app->matrix()->call('store.trade.status.update', $arr_data);
        parent::request('store.trade.status.update', $arr_data, $arr_callback, 'Order Cancel', 1);
        
        return true;
    }
}
