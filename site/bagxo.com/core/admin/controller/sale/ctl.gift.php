<?php
include_once('objectPage.php');
class ctl_gift extends objectPage{

    var $name = '赠品';
    var $object = 'trading/gift';
    var $actionView = 'sale/gift/finder_action.html'; //默认的动作html模板,可以为null
    var $filterView = 'sale/gift/finder_filter.html'; //默认的过滤器html,可以为null
    var $workground = 'sale';
    var $actions= array(
                'showAddGift'=>'编辑',

            );
    var $editMode = true;
    var $batchEdit = false;
    var $disableGridEditCols = "gift_id,insert_time,update_time,thumbnail_pic,small_pic,big_pic,gift_describe,limit_level,freez";
    var $disableColumnEditCols = "gift_id,insert_time,update_time,thumbnail_pic,small_pic,big_pic,gift_describe,limit_level,freez";
    var $disableGridShowCols = "gift_id,insert_time,update_time,thumbnail_pic,small_pic,big_pic,gift_describe,limit_level,freez";    


    function getTypeList() {
        $this->page('sale/giftcat/list.html',true);
    }

    function addGift(){
        $this->begin('index.php?ctl=sale/gift&act=index');
        $oGift = $this->system->loadModel('trading/gift');
        $this->end($oGift->saveGift($_POST),__('赠品添加成功'));
    }

    function addType(){
        $oGiftCat = $this->system->loadModel('trading/giftcat');
        if(!$oGiftCat->addType($this->in)){
            //todo 出错信息
        }
        $this->splash('success','index.php?ctl=sale/giftcat&act=index');
    }

    function showAddType($catId){
        $this->path[] = array('text'=>'赠品分类内容页');
        $oGiftCat = $this->system->loadModel('trading/giftcat');
        if ($catId) {
            $this->pagedata['giftcat'] = $oGiftCat->getTypeById($catId);
        } else {
            $this->pagedata['giftcat']['shop_iffb'] = 0;
            $this->pagedata['giftcat']['orderlist'] = $oGiftCat->getInitOrder();
        }
        $this->page('sale/giftcat/addType.html');
    }

    function showAddGift($giftId=null) {
        $this->path[] = array('text'=>'赠品内容页');
        $oGift = $this->system->loadModel('trading/gift');
        $oMember = $this->system->loadModel('member/member');
        $this->pagedata['catList'] = $oGift->getTypeArr();
        if(count($this->pagedata['catList'])<1){
            $this->splash('failed','index.php?ctl=sale/gift&act=showAddType','缺少赠品类别，无法添加赠品。转到添加赠品类别');
        }

        $aMemberLevelList = $oMember->getLevelList(false);
        foreach ($aMemberLevelList as $k => $v) {
            $aTmpMList[$v['member_lv_id']] = $v['name'];
        }
        $this->pagedata['mLev'] = $aTmpMList;

        if ($giftId) {
            $this->pagedata['gift'] = $oGift->getGiftById($giftId);
            $this->pagedata['gift']['limit_end_time'] = dateFormat($this->pagedata['gift']['limit_end_time']);
            $this->pagedata['gift']['limit_start_time'] = dateFormat($this->pagedata['gift']['limit_start_time']);
            $this->pagedata['gift']['mLev'] = explode(',', $this->pagedata['gift']['limit_level']);
        } else {
            $this->pagedata['gift']['giftcat_id'] = $aType[0][0]['giftcat_id'];
            $this->pagedata['gift']['shop_iffb'] = 1;
            $this->pagedata['gift']['ifrecommend'] = 1;
            $this->pagedata['gift']['limit_num'] = 1;
            $this->pagedata['gift']['orderlist'] = $oGift->getInitOrder();
        }

        $this->page('sale/gift/addGift.html');
    }

    function delGift(){
        $oGift = $this->system->loadModel('trading/gift');
        $giftIds = $oGift->finderResult($_POST['items']);
        if ($oGift->delGift($giftIds,$msg)) {
            $this->splash('success', 'index.php?ctl=sale/gift&act=index');
        } else {
            $this->splash('failed', 'index.php?ctl=sale/gift&act=index', $msg);
        }
    }

    function delType(){
        $oGiftCat = $this->system->loadModel('trading/giftcat');
        $giftCatIds = $oGiftCat->finderResult($_POST['items']);
    
        if ($oGiftCat->delType($giftCatIds,$msg)) {
            $this->splash('success', 'index.php?ctl=sale/giftcat&act=index');
        } else {
            $this->splash('failed', 'index.php?ctl=sale/giftcat&act=index', $msg);
        }
    }

}
?>
