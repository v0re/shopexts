<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class gift_task 
{
    function post_install()
    {
        kernel::log('Initial gift');
        kernel::single('base_initial', 'gift')->init();
    }//End Function
}//End Class
