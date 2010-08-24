<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_finder_indexs 
{
    function __construct(&$app) 
    {
        $this->app = $app;
    }//End Function
    
    public $column_edit='编辑';
    public $column_edit_width='40';
    public function column_edit($row){
        return '<a href="index.php?app=content&ctl=admin_article_detail&act=edit&article_id=' . $row['article_id'] . '" target="_blank" >编辑</a>';
    }

    public $column_preview='预览';
    public $column_preview_width='40';
    public function column_preview($row){
        return '<a href="' .  app::get('site')->router()->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'index', 'arg0'=>$row['article_id'])) . '" target="_blank" >预览</a>';
    }
}//End Class
