<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_finder_menus
{
    public $addon_cols='app,ctl,act,custom_url,params';

    public $column_preview = '预览';
    public $column_preview_width = 40;
    public $column_control = '编辑';
    public function column_preview($row) 
    {
        if($row[$this->col_prefix . 'custom_url']){

            return '<a href="' . $row[$this->col_prefix . 'custom_url'] . '" target="_blank">进入</a>';
        }else{
            
            $params = array('app'=>$row[$this->col_prefix . 'app'], 'ctl'=>$row[$this->col_prefix . 'ctl'], 'act'=>$row[$this->col_prefix . 'act']);
            if($row[$this->col_prefix.'params']){
                $params = (array)$params + (array)array('args'=>$row[$this->col_prefix.'params']);
            }
            return '<a href="' . kernel::single('site_router')->gen_url($params) . '" target="_blank">进入</a>';
        }
    }//End Function


    function column_control($row){
        return '<a href="index.php?app=site&ctl=admin_menu&act=detail_edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['id'].'"  target="dialog::{frameable:true, title:\'添加菜单\', width:400, height:280}">编辑</a>';
    }


}//End Class
