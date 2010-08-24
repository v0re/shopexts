<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_task 
{
    function post_install() 
    {
        kernel::log('Initial themes');
        $themes = kernel::single('site_theme_install')->check_install();
    }//End Function
}//End Class
