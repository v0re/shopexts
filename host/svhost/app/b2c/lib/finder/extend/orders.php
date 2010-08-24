<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_extend_orders{
    function get_extend_colums(){
            $db['orders']=array (
              'columns' => 
              array (
                'payment' => 
                array (
                  'type' => 'table:payment_cfgs@ectools',
                  'required' => true,
                  'default' => 0,
                  'label' => '支付方式',
                  'width' => 75,
                  'editable' => true,
                  'filtertype' => 'yes',
                  'filterdefault' => true,
                  'in_list' => true,
                  'default_in_list' => true,
                )));
        return $db;
    }
}

