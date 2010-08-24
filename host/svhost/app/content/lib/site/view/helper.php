<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_site_view_helper 
{

    function function_header($params, &$smarty)
    {
        if($smarty->app->app_id !='content') return '';

        $smarty->pagedata['TITLE'] = &$smarty->pagedata['title'];
        $smarty->pagedata['KEYWORDS'] = &$smarty->pagedata['keywords'];
        $smarty->pagedata['DESCRIPTION'] = &$smarty->pagedata['description'];
        //$
        return $smarty->fetch('site/common/header.html', app::get('content')->app_id);
    }


/**
    function function_footer($params, &$smarty)
    {
        return ;
    }
//*/

}//End Class
