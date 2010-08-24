<?php

class b2c_site_sitemaps {
    
    public function __construct( $app ) {
        $this->app = $app;
    }
    
    /*
     *
     * return array
     * array(
     *  array(
     *      'url' => '...........'
     *      ),
     *  array(
     *      'url' => '...........'
     *      )
     * )
     */
    
    public function get_arr_maps() {
        $router = app::get('site')->router();
        $tmp = array();
        
        
        //货品
        $arr = $this->app->model('products')->getList('*');
        foreach( (array)$arr as $row ) {
            $tmp[] = array(
                    'url' => $router->gen_url(array('app'=>'b2c', 'ctl'=>'site_product', 'act'=>'index', 'arg0'=>$row['product_id'], 'full'=>true) ),
                );
        }
        
        //品牌
        $arr = $this->app->model('brand')->getList( '*' );
        foreach( (array)$arr as $row ) {
            $tmp[] = array(
                    'url' => $router->gen_url(array('app'=>'b2c', 'ctl'=>'site_brand', 'act'=>'index', 'arg0'=>$row['brand_id'], 'full'=>true) ),
                );
        }
        
        //分类
        $arr = $this->app->model('goods_cat')->getList( '*' );
        foreach( (array)$arr as $row ) {
            $tmp[] = array(
                    'url' => $router->gen_url(array('app'=>'b2c', 'ctl'=>'site_gallery', 'act'=>'index', 'arg0'=>$row['cat_id'], 'full'=>true) ),
                );
        }
        
        return $tmp;
    }
}