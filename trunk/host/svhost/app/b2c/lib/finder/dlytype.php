<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_dlytype{

    var $column_control = '编辑';
    function column_control($row){
        return '<a href="index.php?app=b2c&ctl=admin_dlytype&act=showEdit&p[0]='.$row['dt_id'].'" target="_blank">编辑</a>';
    }

}
