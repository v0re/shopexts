<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class site_finder_link
{
    
    public $column_tools='操作';
    public $column_tools_width='80';
    public function column_tools($row){
       return '<a target="dialog::{title:\'链接编辑\', width:600, height:400}" href="index.php?app=site&ctl=admin_link&act=edit&link_id='.$row['link_id'].'">编辑</a>';
    }
}//End Class

