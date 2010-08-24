<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_view_helper 
{

    public function function_header($params, &$smarty) 
    {
        $headers = $smarty->pagedata['headers']; 
        if(is_array($headers)){
            foreach($headers AS $header){
                $html .= $header;
            }
        }else{
            $html .= $headers;
        }
        $services = kernel::servicelist("site_view_helper");
        foreach($services AS $service){
            if(method_exists($service, 'function_header'))
                $html .= $service->function_header($params, $smarty);
        }
        return $html . '<style id="site_widgets_style"><%site_widgets_css%></style><script type="text/javascript">(function(){var widgets_style = document.getElementById(\'site_widgets_style\');var head = document.getElementsByTagName(\'head\')[0];head.appendChild(widgets_style);})();</script>';
    }//End Function

    public function function_footer($params, &$smarty) 
    {
        $footers = $smarty->pagedata['footers']; 
        if(is_array($footers)){
            foreach($footers AS $footer){
                $html .= $footer;
            }
        }else{
            $html .= $footers;
        }
        $services = kernel::servicelist("site_view_helper");
        foreach($services AS $service){
            if(method_exists($service, 'function_footer'))
                $html .= $service->function_footer($params, $smarty);
        }
        
        $html .= app::get('site')->getConf('system.foot_edit');
        
        return $html;
    }//End Function

    public function function_template_filter($params, &$smarty) 
    {
        
        if($params['type']){
            $render = kernel::single('base_render');
            $theme = kernel::single('site_theme_base')->get_default();
            $obj = kernel::single('site_theme_tmpl');
            $theme_list = $obj->get_edit_list($theme);
            $render->pagedata['list'] = $theme_list[$params['type']];
            unset($params['type']);
            $render->pagedata['selected'] = $params['selected'];
            unset($params['selected']);
            if(is_array($params)){
                foreach($params AS $k=>$v){
                    $ext .= sprintf(' %s="%s"', $k, $v);
                }
            }
            $render->pagedata['ext'] = $ext;
            return $render->fetch('admin/theme/tmpl/template_filter.html', app::get('site')->app_id);
        }else{
            return '';
        }
    }//End Function

}//End Class
