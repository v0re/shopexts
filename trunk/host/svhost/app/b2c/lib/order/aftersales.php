<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_aftersales extends b2c_api_rpc_request
{
    /**
     * 构造方法
     * @param object application object
     * @return null
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function generate(&$sdf, &$msg='')
    {
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $is_save = $obj_return_policy->save_return_product($sdf);
        
        if (!$is_save)
        {
            $msg = '数据保存失败！';
            
            return false;
        }        
        $this->request($sdf);
        
        return true;
    }
    
    /**
     * 提供外部调用的发送的接口（更新接口）
     * @param array request data array
     * @return null
     */
    public function send_update_request(&$sdf)
    {
        $this->request($sdf, 'store.trade.aftersale.status.update');
    }
    
    /**
     * 订单取消事件埋点
     * @param array sdf
     * @return boolean success or failure
     */
    protected function request(&$sdf, $method='')
    {
        $arr_data = array();
        $arr_data['tid'] = $sdf['order_id'];
        $arr_data['aftersale_id'] = $sdf['return_id'];
        if ($sdf['title'])
            $arr_data['title'] = $sdf['title'];
        if ($sdf['content'])
            $arr_data['content'] = $sdf['content'];
        $arr_data['messager'] = '';
        if ($sdf['add_time'])
            $arr_data['created'] = date('Y-m-d H:i:s', $sdf['add_time']);
        if ($sdf['comment'])
            $arr_data['memo'] = $sdf['comment'] ? $sdf['comment'] : '';
        if ($sdf['status'])
            $arr_data['status'] = $sdf['status'];
        if ($sdf['member_id'])
            $arr_data['buyer_id'] = $sdf['member_id'];
        if ($sdf['product_data'])
            $arr_product_data = unserialize($sdf['product_data']);
        
        if (isset($arr_product_data) && $arr_product_data)
        {
            foreach ($arr_product_data as $key=>&$items)
            {
                $arr_product_data[$key]['sku_bn'] = $items['bn'];
                unset($items['bn']);
                $arr_product_data[$key]['sku_name'] = $items['name'];
                unset($items['name']);
                $arr_product_data[$key]['number'] = $items['num'];
                unset($items['num']);
            }
        
            $arr_data['aftersale_items'] = json_encode($arr_product_data);
        }
        else
        {
            $arr_data['aftersale_items'] = "";
        }         
        
        $arr_callback = array(
            'class' => 'b2c_api_callback_app', 
            'method' => 'callback',
            'params' => array(
                'method' => (!$method) ? 'store.trade.aftersale.add' : $method,
                'tid' => $arr_data['tid'],
            ),
        );
        
        if (!$method)
            //$rst = $this->b2c_app->matrix()->call('store.trade.remarket.add', $arr_data);
            parent::request('store.trade.aftersale.add', $arr_data, $arr_callback, 'Aftersales add', 1);
        else
            //$rst = $this->b2c_app->matrix()->call($method, $arr_data);
            parent::request($method, $arr_data, $arr_callback, 'Aftersales update', 1);
    }
}