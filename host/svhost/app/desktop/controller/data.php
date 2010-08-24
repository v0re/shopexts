<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_data extends desktop_controller{

    function index(){
        $router = app::get('desktop')->router();
        $info = array(
            'backup' => array(
                            'title'=>'数据备份', 'url' => $router->gen_url(array('app'=>'desktop', 'ctl'=>'backup', 'act'=>'index'))
                        ),
            'comeback' => array(
                            'title'=>'数据还原', 'url' => $router->gen_url(array('app'=>'desktop', 'ctl'=>'comeback', 'act'=>'index'))
                        ),
        );
        $html.='<h2 class="head-title">数据管理</h2>';
        $html.='<div class="tabs-wrap"><ul>';
        foreach($info as $controller => $info) {
            $html.='<li class="tab '. $this->pagedata[$controller] .'"><span><a href='.$info['url'].'>'.$info['title'].'</a></span></li>';
        }
        $html.='</ul></div>';

        $this->pagedata['_PAGE_CONTENT'] = $html;

        $this->page();

    }


}
