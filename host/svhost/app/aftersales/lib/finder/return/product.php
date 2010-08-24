<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class aftersales_finder_return_product
{
    public $detail_basic = '基本信息';
    
    private $arr_status = array();
     
    public function __construct($app)
    {
        $this->app = $app;
        $this->arr_status = array(
            '1' => '申请中',
            '2' => '审核中',
            '3' => '接受申请',
            '4' => '完成',
            '5' => '拒绝',
        );
    }
    
    public function detail_basic($return_id)
    {
        $render = $this->app->render();
        $obj_return_product = $this->app->model('return_product');
        $arr_return_product = $obj_return_product->dump($return_id);
        if ($arr_return_product['comment'])
            $arr_return_product['comment'] = unserialize($arr_return_product['comment']);
        if ($arr_return_product['product_data'])
            $arr_return_product['product_data'] = unserialize($arr_return_product['product_data']);
        if ($arr_return_product['status'])
        {
            $arr_return_product['status'] = $this->arr_status[$arr_return_product['status']];
        }
        $render->pagedata['info'] = $arr_return_product;
        
        return $render->fetch('admin/return_product/detail.html');
    }
}