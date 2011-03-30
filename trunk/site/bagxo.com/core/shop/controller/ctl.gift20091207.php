<?php
class ctl_gift extends shopPage{

    function showTypeList($catId=0) {
        $oGift = $this->system->loadModel('trading/gift');
        $this->pagedata['giftType'] = $oGift->getTypeList('', true);
        $this->output();
    }

    function showList($catId=0,$page=1) {

        if($catId){
            $filter['gid']=$catId;
        }
        $this->path[]=array('title'=>'赠品');
        $pageLimit = 20;
        $oGift = $this->system->loadModel('trading/gift');
        $oGiftCat = $this->system->loadModel('trading/giftcat');

        if ($aGift = $oGift->getGiftList(($page-1)*$pageLimit,$pageLimit,$giftCount,$filter)) {

            $storager = $this->system->loadModel('system/storager');
            while (list($k,) = each($aGift)) {
                $aGift[$k]['image']['default'] = $storager->getUrl($aGift['image_default']);
                if ($oGift->isOnSale($aGift[$k], $GLOBALS['runtime']['member_lv'])){
                    $aGift[$k]['sale_status'] = 1;
                }else {
                    $aGift[$k]['sale_status'] = 0;
                }

            }
            if ($catId) {
                $this->title = $aGift[0]['cat'];
            }else{
                $this->title = '所有赠品';
            }
            $this->pagedata['giftList'] = $aGift;
        }else{
            trigger_error('查询失败',E_USER_NOTICE);
        }

        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($giftCount/$pageLimit),
            'link'=>$this->system->mkUrl('gift','showList',array($catId,($tmp = time()))),
            'token'=>$tmp);
        if($page > $this->pagedata['pager']['total']){
            trigger_error('查询数为空',E_USER_NOTICE);
        }
        $this->output();
    }

    function index($giftId) {
        $oGift = $this->system->loadModel('trading/gift');
        $oLev = $this->system->loadModel('member/level');
        $qerer=$oLev->getMLevel($aGift['limit_level']);
        $aGift = $oGift->getGiftById($giftId);
        if(!$aGift){
            trigger_error('找不到此赠品',E_USER_NOTICE);
        }
        if ($oGift->isOnSale($aGift, $GLOBALS['runtime']['member_lv'])){
            $aGift['sale_status'] = 1;
        }else {
            $aGift['sale_status'] = 0;
        }
        $this->title = $aGift['name'];

        $storager = $this->system->loadModel('system/storager');

        $aGift['image']['default'] = $storager->getUrl($aGift['image_default']);
        foreach(explode(',',$aGift['image_file']) as $id){
            $aImageFile[] = $storager->getUrl($id);
        }
        $aGift['image']['file'] = $aImageFile;
        $aGift['limit_level'] = $oLev->getMLevel($aGift['limit_level']);
        $this->pagedata['details'] = $aGift;
        $this->output();
    }
}
?>
