<?php

class content_site_sitemaps {
    
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
        $arr = $this->app->model('article_indexs')->getList('*');
        $tmp = array();
       
        foreach( (array)$arr as $row ) {
            $tmp[] = array(
                    'url' => app::get('site')->router()->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'index', 'arg0'=>$row['article_id'], 'full'=>true) ),
                );
        }
        
        return $tmp;
    }
}