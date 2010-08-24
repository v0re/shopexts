<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_im(&$setting,&$app){

    if($setting['align']=='1'){
        $setting['plug']='<br>';
    }
    return $setting['im'];
}
?>
