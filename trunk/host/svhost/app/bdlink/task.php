<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class bdlink_task 
{
    function post_install()
    {
        kernel::log('Initial bdlink');
        kernel::single('base_initial', 'bdlink')->init();
    }//End Function
}//End Class
