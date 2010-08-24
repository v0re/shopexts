<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * b2c aftersales interactor with center
 * shopex team
 * dev@shopex.cn
 */
class b2c_api_aftersale
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
        $this->app = app::get('aftersales');
        $this->app_b2c = $app;
    }
    
    /**
     * 售后服务单创建
     * @param array sdf
     * @param string message
     * @return boolean success or failure
     */
    public function create(&$sdf, &$thisObj)
    {
        $obj_return_product = $this->app->model('return_product');
        
        $return_id = $obj_return_product->gen_id();
        $arr_product_data = json_decode($sdf['return_product_items'], true);
        $str_product_data = serialize($arr_product_data);
        
        $arr_data = array(
            'order_id' => $sdf['order_bn'],
            'return_bn' => $sdf['return_bn'],
            'return_id' => $return_id,
            'title' => $sdf['title'],
            'content' => $sdf['content'],
            'comment' => $sdf['comment'],
            'status' => $sdf['status'],
            'product_data' => $str_product_data,
            'member_id' => $sdf['member_id'],
            'add_time' => $sdf['add_time'],
        );
        
        $obj_return_product->save($arr_data);
        
        return array('return_id' => $return_id);
    }
    
    /**
     * 售后服务单修改
     * @param array sdf
     * @param string message
     * @return boolean sucess of failure
     */
    public function update(&$sdf, &$msg='')
    {
        $obj_return_product = $this->app->model('return_product');
        
        $arr_data = $obj_return_product->dump(array('order_bn'=>$sdf['order_bn'],'return_bn'=>$sdf['return_bn']));
        if ($arr_data)
        {
            if ($sdf['return_product_items'])
            {
                $arr_product_data = json_decode($sdf['return_product_items']);
                $str_product_data = serialize($arr_product_data);
            }
            else
            {
                $str_product_data = "";
            }
            
            $arr_data['order_id'] = $sdf['order_bn'];
            $arr_data['return_bn'] = $sdf['return_bn'];
            if ($sdf['title'])
                $arr_data['title'] = $sdf['title'];
            if ($sdf['content'])
                $arr_data['content'] = $sdf['content'];
            if ($sdf['comment'])
                $arr_data['comment'] = $sdf['comment'];
            if ($sdf['status'])
                $arr_data['status'] = $sdf['status'];
            if ($str_product_data)
                $arr_data['product_data'] = $str_product_data;
            if ($sdf['member_id'])
                $arr_data['member_id'] = $sdf['member_id'];
            if ($sdf['add_time'])
                $arr_data['add_time'] = $sdf['add_time'];
            
            $obj_return_product->save($arr_data);
        }
        else
        {
            $thisObj->send_user_error(__('售后服务单不存在！'), array());
        }
    }
}