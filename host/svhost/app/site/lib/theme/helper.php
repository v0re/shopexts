<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_theme_helper 
{
    function function_header(){
        $ret='<base href="'.app::get('site')->base_url().'"/>';
        $path = app::get('site')->res_url;
        
            $ret.= '<link rel="stylesheet" href="'.kernel::base_url(1).'/app/b2c/statics/framework.css" type="text/css" />';
			$ret.='<link rel="stylesheet" href="'.$path.'/widgets.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.$path.'/widgets_edit.css" type="text/css" />';
            $ret.= '<script src="'.$path.'/js/mootools.js"></script>
		<script src="'.$path.'/js/jstools.js"></script>
        <script src="'.$path.'/js/dragdropplus.js"></script>
        <script src="'.$path.'/js/shopwidgets.js"></script>';
        
        if($theme_info=(app::get('site')->getConf('site.theme_'.app::get('site')->getConf('current_theme').'_color'))){
            $theme_color_href=kernel::base_url().'/themes/'.app::get('site')->getConf('current_theme').'/'.$theme_info;
            $ret.="<script>
            window.addEvent('domready',function(){
                new Element('link',{href:'".$theme_color_href."',type:'text/css',rel:'stylesheet'}).injectBottom(document.head);
             });
            </script>";
        }
        $ret .= '<script>window.onload = function(){(parent.loadedPart[1])++;}</script>';
        return $ret;
    }

    function function_footer(){
        return '<div id="drag_operate_box" class="drag_operate_box" style="visibility:hidden;">
            <div class="drag_handle_box">
            <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
            <td><span class="dhb_title">标题</span></td>
            <td width="40"><span class="dhb_edit">编辑</span></td>
            <td width="40"><span class="dhb_del">删除</span></td>
            </tr>
            </table>
            </div>
            </div>

            <div id="drag_ghost_box" class="drag_ghost_box" style="visibility:hidden">
            </div>';
    }


}//End Class
