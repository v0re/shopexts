<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class gift_finder_goods {

    function __construct(&$app) 
    {
        $this->app = $app;
        $this->router = app::get('site')->router();
    }//End 
    
    public $column_edit='编辑';
    public $column_edit_width='80';
    public function column_edit($row){
        return '<a href="index.php?app=gift&ctl=admin_gift&act=edit&gift_id=' . $row['goods_id'] . '" target="_blank" >编辑</a>&nbsp;&nbsp;'.
              '<a href="'. $this->router->gen_url(array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'index', 'arg0'=>$row['goods_id'])) . '" target="_blank" >预览</a>';
    }




}
