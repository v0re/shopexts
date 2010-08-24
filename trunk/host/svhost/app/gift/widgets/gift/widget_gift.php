<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_gift(&$setting,&$smarty){
    //$o=$system->loadModel('content/article');
    //$giftCount = $giftCount ? $giftCount : 10;
    $gift = app::get('gift')->model('goods');
    $result=$gift->getList('*', array('goods_type'=>'gift', 'marketable'=>'true'), 0, ($setting['giftnum']?$setting['giftnum']:5) );

//    $oGift->getGiftList(($page-1)*$pageLimit,$pageLimit,$giftCount,$filter)

    /*
    $result = $gift->getList('gift_id,name,thumbnail_pic',array('shop_iffb'=>1,'disabled'=>'false','ifrecommend'=>1,'storage_ifenough'=>1,'time_ifvalid'=>1),0,$setting['giftnum'],$count);
    */
    return $result;
    
}
?>
