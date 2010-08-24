<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_finish extends b2c_api_rpc_request
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
     * 订单完成
     * @params array - 订单数据
     * @params object - 控制器
     * @params string - 支付单生成的记录
     * @return boolean - 成功与否
     */
    public function generate($sdf, &$controller=null, &$msg='')
    {
        $is_save = true;
        
        //todo:ever 什么状态下需要解冻
        $objOrders = $controller->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $objOrders->dump($sdf['order_id'], '*', $subsdf);
        
        $arr_data['status'] = 'finish';
        $arr_data['order_id'] = $sdf['order_id'];
        $objOrders->save($arr_data);
        $this->request($arr_data);
        
        // 更新退款日志结果        
        $objorder_log = $this->app->model('order_log');            
        $sdf_order_log = array(
            'rel_id' => $sdf['order_id'],
            'op_id' => $sdf['op_id'],
            'op_name' => $sdf['opname'],
            'alttime' => time(),
            'bill_type' => 'order',
            'behavior' => 'finish',
            'result' => 'SUCCESS',
            'log_text' => '订单完成',
        );
        $log_id = $objorder_log->save($sdf_order_log);
        
        return $is_save;
    }
    
    /**
     * 订单创建
     * @param array sdf
     * @return boolean success or failure
     */
    protected function request(&$sdf)
    {
        $arr_data['tid'] = $sdf['order_id'];
        $arr_data['status'] = 'TRADE_FINISHED';
        
        $arr_callback = array(
            'class' => 'b2c_api_callback_app', 
            'method' => 'callback',
            'params' => array(
                'method' => 'store.trade.status.update',
                'tid' => $arr_data['tid'],
            ),
        );
        
        // 回朔待续...
        //$rst = $this->app->matrix()->call('store.trade.status.update', $arr_data);
        parent::request('store.trade.status.update', $arr_data, $arr_callback, 'Order Finish', 1);
        
        return true;
    }
}
