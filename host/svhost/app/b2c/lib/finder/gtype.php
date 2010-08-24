<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_gtype{

    var $actions = array(
            array('label'=>'添加商品类型','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_goods_type&act=add','target'=>'dialog::{ title:\'添加商品类型\', width:800, height:470}')
        );

    var $column_control = '类型操作';
    function column_control($row){
        return '<a href=\'index.php?app=b2c&ctl=admin_goods_type&act=set&p[0]='.$row['type_id'].'\'" target="dialog::{ title:\'编辑商品类型\', width:800, height:470}">编辑</a>';
    }


}
