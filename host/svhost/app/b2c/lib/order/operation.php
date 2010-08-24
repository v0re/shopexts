<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * 定义order操作的抽象类
 * 主要实现接口ectools_interface_order_operaction的freezeGoods和unfreezeGoods的方法
 * ectools payment operation version 0.1
 */
abstract class b2c_order_operation implements b2c_interface_order_operation
{
    // 对应的应用对象
    protected $app;
    
    // 对象实体
    protected $model;
    
    public function freezeGoods($order_id)
    {
        $is_freeze = false;
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $this->app->model('orders')->dump($order_id, 'order_id,status,pay_status,ship_status', $subsdf);
        $storage_enable = $this->app->getConf('site.storage.enabled');        
           
        $objGoods = $this->app->model('goods');
        foreach($sdf_order['order_objects'] as $k => $v)
        {
            foreach ($v['order_items'] as $arrItem)
            {
                if ($storage_enable != 'true')
                    if ($arrItem['item_type'] != 'gift')
                        $is_freeze = $objGoods->freez($arrItem['products']['goods_id'], $arrItem['products']['product_id'], $arrItem['quantity']);
                    else
                    {
                        $this->gift_goods_freez($arrItem['goods_id'], $arrItem['quantity']);
                    }
                else
                {
                    $this->gift_goods_freez($arrItem['goods_id'], $arrItem['quantity']);
                }
            }
        }        
        
        return $is_freeze;
    }
    
    public function unfreezeGoods($order_id)
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
                    if ($arrItem['item_type'] != 'gift')
                        $is_unfreeze = $objGoods->unfreez($arrItem['products']['goods_id'], $arrItem['products']['product_id'], $arrItem['quantity']);
                    else
                    {
                        $this->gift_goods_unfreez($arrItem['goods_id'], $arrItem['quantity']);
                    }
                else
                {
                    $this->gift_goods_unfreez($arrItem['goods_id'], $arrItem['quantity']);
                }
            }
        }       
        
        return $is_unfreeze;
    }
    
    public function minus_stock(&$arrData)
    {
        $storage_enable = $this->app->getConf('site.storage.enabled');
        
        if ($storage_enable != 'true')
        {
            $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
            $arrStatus = $obj_checkorder->checkOrderFreez('delivery', $arrData['order_id']);
            // 裁剪库存
            $products = $this->app->model('products');
            $objMath = kernel::single('ectools_math');
        
            $update_data = array();
            if ($arrData['item_type'] != 'gift')
            {
                $tmp = $products->dump($arrData['product_id'],'*');
                
                if (!is_null($tmp['store']) || $tmp['store'] === '')
                {
                    if ($arrStatus['store'])
                        $update_data['store'] = $objMath->number_minus(array($tmp['store'], $arrData['number']));
                    if ($arrStatus['unfreez'])
                        $update_data['freez'] = $objMath->number_minus(array($tmp['freez'], $arrData['number']));
                        
                    $update_data['product_id'] = $tmp['product_id'];
                    
                    if ($products->save($update_data)) 
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return true;
                }
            }
        }
        
        if ($arrData['item_type'] == 'gift')
        {
            $app_gift = app::get('gift');
            if ($app_gift->is_installed())
            {
                $obj_gift_goods = $app_gift->model('goods');
                $arr_gift_goods = $obj_gift_goods->dump($arrData['goods_id'], '*');
                if (!is_null($arr_gift_goods['store']) || $arr_gift_goods['store'] === '')
                {
                    if ($arrStatus['store'])
                    {
                        $arr_gift_goods['store'] = $objMath->number_minus(array($arr_gift_goods['store'], $arrData['number']));
                        
                        $obj_gift_goods->save($arr_gift_goods);
                    }
                    if ($arrStatus['unfreez'])
                    {
                        $obj_gift_goods->unfreez($arrData['goods_id'], $arrData['number']);
                    }                        
                }
            }
        }
        
        return true;
    }
    
    public function restore_stock(&$arrData)
    {
        $storage_enable = $this->app->getConf('site.storage.enabled');
        
        if ($storage_enable != 'true')
        {
            //更新库存
            $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
            $arrStatus = $obj_checkorder->checkOrderFreez('reship', $arrData['order_id']);
            
            if ($arrStatus['unstore'])
            {
                $update_data = array();
                $products = $this->app->model('products');
                $objMath = kernel::single('ectools_math');
                
                $tmp = $products->dump($arrData['product_id'],'*');
                if (!is_null($tmp['store']) || $tmp['store'] === '')
                {
                    $update_data['store'] = $objMath->number_plus(array($tmp['store'], $arrData['number']));
                    $update_data['product_id'] = $tmp['product_id'];
                    
                    if ($products->save($update_data))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return true;
                }
            }
        }
        else
        {
            return true;
        }
        
        return false;
    }
    
    private function gift_goods_freez($goods_id, $quantity)
    {
        $app_gift = app::get('gift');
        if ($app_gift->is_installed())
        {
            $obj_gift_goods = $app_gift->model('goods');
            $obj_gift_goods->freez($goods_id, $quantity);
        }
    }
    
    private function gift_goods_unfreez($goods_id, $quantity)
    {
        $app_gift = app::get('gift');
        if ($app_gift->is_installed())
        {
            $obj_gift_goods = $app_gift->model('goods');
            $obj_gift_goods->unfreez($goods_id, $quantity);
        }
    }
}
