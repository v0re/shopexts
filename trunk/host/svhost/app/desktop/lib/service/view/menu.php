<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_service_view_menu{
    function function_menu(){
        //$html[] = "<a href='index.php?ctl=shoprelation&act=index&p[0]=apply'>网店邻居</a>";
        $html[] = "<a href='index.php?ctl=adminpanel'>控制面板</a>";
        $html[] = "<a href='index.php?app=desktop&ctl=appmgr&act=index'>应用管理</a>";
        $html[] = "<a href='index.php?app=desktop&ctl=default&act=alertpages&goto=".urlencode('index.php?app=desktop&ctl=recycle&act=index&nobuttion=1')."' target='_blank'>回收站</a>";
        $html[] = "<a href='index.php?ctl=dashboard&act=index'>桌面</a>";
        return $html;
    }
}