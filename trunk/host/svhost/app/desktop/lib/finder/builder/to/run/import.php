<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_to_run_import {

    function run(&$cursor_id,$params){
        if(!empty($params['sdfdata']['store'])){
            $params['sdfdata']['product'][0]['store'] = $params['sdfdata']['store'];
        }
        app::get($params['app'])->model($params['mdl'])->save($params['sdfdata']);
        return 0;
    }
}
