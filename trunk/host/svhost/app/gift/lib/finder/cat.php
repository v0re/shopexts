<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class gift_finder_cat {

    function __construct(&$app) 
    {
        $this->app = $app;
    }//End 
    
    public $column_edit='编辑';
    public $column_edit_width='40';
    public function column_edit($row){
        return '<a href="index.php?app=gift&ctl=admin_gift_cat&act=edit&cat_id=' . $row['cat_id'] . '" >编辑</a>';
    }




}
