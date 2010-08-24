<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class image_finder_image{

    var $detail_basic = '图片详细信息';
    var $column_img = '图片';
    function __construct($app){
        $this->app = $app;
    }

    function detail_basic($image_id){
        $app = app::get('image');

        $render = $app->render();

        $image = $this->app->model('image');
        $image_info = $image->dump($image_id);
        $allsize = app::get('image')->getConf('image.default.set');

        $render->pagedata['allsize'] = $allsize;
        $render->pagedata['image'] = $image_info;
     
    
        return $render->fetch('finder/image.html');
    }
    function column_img($row){
        $set = $this->app->getConf('image.set');
        if(!$set)
            $set = app::get('image')->getConf('image.default.set');

        $obj = app::get('image')->model('image');
        $row = $obj->dump($row['image_id']);

        if($set){
            if($set['S']['width']){
                $width = 'width='.(($row['width']>$set['S']['width'])?$set['S']['width']:$row['width']);
            }
        }
        if($row['storage']=='network'){
            return '<a href="'.$row['ident'].'" target="_blank"><img src="'.$row['ident'].'" '.$width.' /></a>';
        }
        return '<a href="'.(base_storager::image_path($row['image_id'])).'" target="_blank"><img src="'.(base_storager::image_path($row['image_id'],'s')).'" '.$width.' /></a>';
    }
}
