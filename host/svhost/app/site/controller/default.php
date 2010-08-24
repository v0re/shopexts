<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_ctl_default extends site_controller{

    function index(){
        $this->pagedata['headers'][] = '<title>' . app::get('site')->getConf('page.default_title') . ' ' . app::get('site')->getConf('site.name') . '</title>';
        $this->pagedata['headers'][] = '<meta name="keywords" content="' . app::get('site')->getConf('page.default_keywords'). '" />';
        $this->pagedata['headers'][] = '<meta name="description" content="' . app::get('site')->getConf('page.default_description'). '" />';
        $this->set_tmpl('index');
        $this->page('index.html');
    }
    
    public function page404() 
    {
        die('404:找不到此页面');
    }//End Function

    public function page503() 
    {
        die('503:服务不可用');
    }//End Function
}
