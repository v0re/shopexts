<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_colset{
    function run($task,$ctl){
        $user_id = $ctl->user->user_id;
        foreach($task as $key=>$v){
            app::get('desktop')->setConf('colwith.'.$key.'.'.$user_id,$v);
        }

    }
}
