<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_dlycorp{

    var $detail_basic = '编辑配送公司';
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function detail_basic($id){
        $dly_corp = $this->app->model('dlycorp');
        if($_POST){
            $_POST['corp_id'] = $id;
            $result = $dly_corp->save($_POST);
            //echo $result;
        }
        else
        {
            $render = $this->app->render();
            $row = $dly_corp->dump($id);
            $render->pagedata['dlycrop'] = $row;
            
            return $render->fetch("admin/delivery/dlycrop_edit.html", $this->app->app_id);
        }
    }

}
