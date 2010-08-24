<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class express_finder_print_tmpl
{
     var $column_edit = '操作';
    function column_edit($row){
        $html = "<a href=index.php?app=express&ctl=admin_delivery_printer&act=edit_tmpl&p[0]=".$row['prt_tmpl_id'].">编辑</a> ";
        $html.= "<a href=index.php?app=express&ctl=admin_delivery_printer&act=add_same&p[0]=".$row['prt_tmpl_id'].">添加相似单据</a> ";
        $html.= "<a target='download' href=index.php?app=express&ctl=admin_delivery_printer&act=download&p[0]=".$row['prt_tmpl_id'].">下载模板</a> ";
       return $html;
    }  
    
    function column_edit_width(){
        return "100px";
    }
}