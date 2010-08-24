<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_api_callback_shoprelation implements b2c_api_callback_interface_app
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * 回调接口
     * @param array return array
     */
    public function callback($result)
    {
        if (isset($_POST) && $_POST)
        {
            $obj_shop = &$this->app->model('shop');
            
            $arr_shop = $obj_shop->dump(array('shop_id' => $result['shop_id']));
            
            if ($_POST['node_id'])
            {
                $arr_shop['node_id'] = $_POST['node_id'];
                $arr_shop['node_type'] = $_POST['node_type'];
                $arr_shop['status'] = $_POST['status'];
                
                $obj_shop->save($arr_shop);
            }
            
        }
    }
}