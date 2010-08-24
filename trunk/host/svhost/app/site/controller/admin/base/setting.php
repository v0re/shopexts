<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */


class site_ctl_admin_base_setting extends site_admin_controller
{

    /*
     * workground
     * @var string
     */
    var $workground = 'site_ctl_admin_base_setting';

    public function index()
    {
        $all_settings = array(
            '基本信息' => array(
                'site.name',
                'system.foot_edit',
            ),
            '高级设置' => array(
                'base.site_params_separator',
                'base.enable_site_uri_expanded',
                'base.site_uri_expanded_name',
                'base.check_uri_expanded_name',
            ),
        );
        $html = kernel::single('site_base_setting')->process($all_settings);
        $this->pagedata['_PAGE_CONTENT'] = $html;
        $this->page();
    }//End Function

}//End Class
