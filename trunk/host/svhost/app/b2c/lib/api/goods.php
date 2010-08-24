<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_api_goods
{
    /**
     * 公开构造方法
     * @params app object
     * @return null
     */
    public function __construct($app)
    {        
        $this->app = $app;
    }
    
    /**
     * 取到所有货品的bn和store
     * @param null
     * @return string json
     */
    public function getAllProductsStore()
    {
        $obj_products = $this->app->model('products');
        $arr_products = $obj_products->getList('bn,store');
        
        return $arr_products;
    }
}