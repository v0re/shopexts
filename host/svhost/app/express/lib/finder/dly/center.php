<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class express_finder_dly_center
{
    var $column_editbutton = '操作';
    
    public function column_editbutton($row)
    {
        return '<a href="index.php?app=express&ctl=admin_delivery_center&act=showEdit&p[0]='.$row['dly_center_id'].'">编辑</a>';
    }
}