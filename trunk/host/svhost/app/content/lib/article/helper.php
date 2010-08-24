<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_article_helper 
{

    function function_header(){
        $ret='<base href="'.app::get('content')->base_url().'"/>';
        $path = app::get('desktop')->res_url;
        /*
        if( constant('DEBUG_CSS')){
            $ret.= '<link rel="stylesheet" href="'.$path.'/framework.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.$path.'/shop.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.$path.'/widgets.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.$path.'/widgets_edit.css" type="text/css" />';
        }elseif( constant('GZIP_CSS')){
            $ret.= '<link rel="stylesheet" href="'.$path.'/style.zcss" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.$path.'/widgets_edit.css" type="text/css" />';
        }else{
            */
            //$ret.= '<link rel="stylesheet" href="'.$path.'/style.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.app::get('site')->res_url.'/widgets.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="'.app::get('site')->res_url.'/widgets_edit.css" type="text/css" />';
        //}

        //if( constant('DEBUG_JS')){
            $ret.= '<script src="'.$path.'/js/moo.js"></script>
                    <script src="'.$path.'/js/moomore.js"></script>
                    <script src="'.$path.'/js/mooadapter.js"></script>
                    <script src="'.$path.'/js/jstools.js"></script>
                    <script src="'.app::get('site')->res_url.'/js/dragdropplus.js"></script>
                    <script src="'.app::get('site')->res_url.'/js/shopwidgets.js"></script>';
        /*
        }elseif( constant('GZIP_JS')){
            $ret.= '<script src="'.$tmp_path.'/js/package/tools.jgz"></script>
                     <script src="'.$tmp_path.'/js/package/widgetsedit.jgz"></script>';
        }else{
            $ret.= '<script src="'.$tmp_path.'/js/package/tools.js"></script>
                     <script src="'.$tmp_path.'/js/package/widgetsedit.js"></script>';
        */
        //}
        return $ret;
    }

    function function_footer(){
       return "<div id='drag_operate_box' class='drag_operate_box' style='visibility:hidden;'>
       <div class='drag_handle_box'>
             <table cellpadding='0' cellspacing='0' width='100%'>
                                           <tr>
                                           <td><span class='dhb_title'>标题</span></td>
                                           <td width='40'><span class='dhb_edit'>编辑</span></td>
                                           <td width='40'><span class='dhb_del'>删除</span></td>
                                           </tr>
              </table>
              </div>
          </div>

          <div id='drag_ghost_box' class='drag_ghost_box' style='visibility:hidden'>

          </div>";
    }

}//End Class
