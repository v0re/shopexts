<?php
include_once('objectPage.php');
class ctl_package extends objectPage{

    var $name='捆绑商品';
    var $actionView = 'product/package/finder_action.html';
//    var $filterView = 'product/package/finder_filter.html';
    var $workground = 'sale';
    var $object = 'trading/package';
    var $actions= array(
                'showAddPackage'=>'编辑',
            );

    var $disableGridShowCols = "goods_id,goods_type";

    function addPackage(){
        $this->begin('index.php?ctl=goods/package&act=index');
        $oPackage = $this->system->loadModel('trading/package');
        $_POST['store'] = $_POST['p_store'];
        $this->end($oPackage->savePackage($_POST),__('保存成功'));
    }

    function showAddPackage($goodsId=null){
        $this->path[] = array('text'=>'捆绑商品内容页');
        $oPackage = $this->system->loadModel('trading/package');
        if($goodsId){
            $this->pagedata['package'] = $oPackage->getPackageById($goodsId);
            foreach($oPackage->getPackageProducts($goodsId) as $rows){
                $aId[] = $rows['product_id'];
                $aNum[$rows['product_id']] = array('num'=>$rows['pkgnum']);
                $bn[$rows['product_id']] = array('bn'=>$rows['bn']);
            }
            $this->pagedata['package']['products'] = $aId;
            $this->pagedata['package']['moreinfo'] = $aNum;
            $this->pagedata['package']['bn'] = $bn;
        }else{
            $this->pagedata['package']['marketable'] = 'true';
            $this->pagedata['package']['p_order'] = $oPackage->getInitOrder();
        }
    
        $this->pagedata['point_setting'] = $this->system->getConf('point.get_policy');

        $this->page('product/package/addPackage.html');
    }

    function delete(){
        $this->begin('index.php?ctl=goods/package&act=index');
        $oPackage = $this->system->loadModel('trading/package');
        $goodsIds = $oPackage->delPackage($_POST['goods_id']);
        $this->end_only();
    }
}
?>
