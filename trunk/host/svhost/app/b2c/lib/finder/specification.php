<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_specification{

    var $actions = array(
        array('label'=>'添加规格','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_specification&act=add','target'=>'dialog::{title:\'添加规格\', width:800, height:420}'),
        array('label'=>'默认规格图设置','href'=>'index.php?app=b2c&ctl=admin_specification&act=edit_default_pic'),
    );

    var $column_control = '规格操作';
    function column_control($row){
        return '<a href=\'index.php?app=b2c&ctl=admin_specification&act=edit&p[0]='.$row['spec_id'].'\'" target="dialog::{title:\'编辑规格\', width:800, height:420}">编辑</a>';
    }

}
