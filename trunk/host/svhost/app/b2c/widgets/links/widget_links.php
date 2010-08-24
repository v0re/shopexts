<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_links(&$setting,&$system){
    $oML = app::get('site')->model('link');
    $results = $oML->getList('*', array('hidden'=>'false'),0,$setting['limit']?$setting['limit']:10,$c);
    return $results;
}
?>
