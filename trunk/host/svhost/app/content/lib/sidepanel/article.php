<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_sidepanel_article 
{
    function __construct($app){
        $this->app = $app;
    }
    
    public function get_output(){
        $render = $this->app->render();
        return $render->fetch('admin/left-panel.html');
    }
}//End Class
