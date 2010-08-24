<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_site_view_helper 
{

    function function_header($params, &$smarty)
    {
        $url = kernel::base_url();
        if($smarty->app->app_id !='b2c') return '<link rel="stylesheet" href="'.$url.'/app/b2c/statics/shop.css"'.' type="text/css" /><link rel="stylesheet" href="'.$url.'/app/b2c/statics/widgets.css"'.' type="text/css" />';
        $shop['url']['shipping'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'shipping'));
        $shop['url']['total'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'total'));
        $shop['url']['region'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_tools','act'=>'selRegion'));
        $shop['url']['payment'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'payment'));
        $shop['url']['diff'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'diff'));
        $smarty->pagedata['shopDefine'] = json_encode($shop);
        $smarty->pagedata['TITLE'] = &$smarty->title;
        $smarty->pagedata['KEYWORDS'] = &$smarty->keywords;
        $smarty->pagedata['DESCRIPTION'] = &$smarty->content;
        $smarty->pagedata['NOFOLLOW'] = &$smarty->nofollow;
        $smarty->pagedata['NOINDEX'] = &$smarty->noindex;
        return $smarty->fetch('site/common/header.html', app::get('b2c')->app_id);
    }



    function function_footer($params, &$smarty)
    {
        $smarty->pagedata['app'] = app::get('b2c')->getConf('site.login_type');   
        #if($smarty->app->app_id =='b2c') return '';

        $data['shopDefine'] = json_encode($shop);

        $html= $smarty->fetch('site/common/footer.html',app::get('b2c')->app_id);
    
        return $html;
    }

}//结束