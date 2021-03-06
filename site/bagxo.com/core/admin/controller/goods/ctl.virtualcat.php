<?php
include_once('objectPage.php');
class ctl_virtualcat extends objectPage{

    var $name='商品虚拟分类';
    var $workground = 'goods';
    var $object = 'goods/virtualcat';
    var $actionView = 'product/finder_action.html';
    var $filterView = 'product/finder_filter.html';
    var $allowImport = true;
    var $ioType = 'goods';
    function addNew($id=0){
        $this->path[] = array('text'=>'商品虚拟分类新增');
        $vobjCat = $this->system->loadModel('goods/virtualcat');
        $objCat = $this->system->loadModel('goods/productCat');
        $aCat = $vobjCat->get_virtualcat_list(true);
        $aCatNull[] = array('cat_id'=>0,'cat_name'=>'----无----','step'=>1);
        if(empty($aCat)){
            $aCat = $aCatNull;
        }else{
            $aCat = array_merge($aCatNull, $aCat);
        }
        $this->pagedata['catList'] = $aCat;
        $this->pagedata['gtypes'] = $objCat->getTypeList();
        $oGtype = &$this->system->loadModel('goods/gtype');
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();
        if($id){
            $aCat = $this->model->instance($id);
            $this->pagedata['cat']['parent_id'] = $aCat['virtual_cat_id'];
            $this->pagedata['cat']['type_id'] = $aCat['type_id'];
        }else{
            $aTmp = $oGtype->getDefault();
            $this->pagedata['cat']['type_id'] = $aTmp[0]['type_id'];
        }
        $this->pagedata['cat']['p_order'] = 0;
        
        $this->page('product/virtualcat/info.html');
    }

    function doAdd(){
        $objCat = $this->system->loadModel('goods/virtualcat');
        $objCat->addNew($_POST['cat']);
        $this->splash('success','index.php?ctl=goods/virtualcat&act=index','保存成功');
    }
    function doImport(){
        if(!is_array($_POST['cat']) || empty($_POST['cat'])){
            $this->splash('failed', 'index.php?ctl=goods/virtualcat&act=import', __('请选择商品分类源节点'));
        }else{
            $this->begin('index.php?ctl=goods/virtualcat&act=index');
            foreach($_POST['cat'] as $key=>$v){
                if($v){
                    $search[]=$v;
                }
            }
            $objCat = $this->system->loadModel('goods/virtualcat');
            $this->end($objCat->doImport($search,$_POST['vCat_id'],$_POST['defaultfilter']),__('保存成功'));
        }
    }
    function import(){
        $vobjCat = $this->system->loadModel('goods/virtualcat');
        $aCat = $vobjCat->get_virtualcat_list(true);
        $objCat = $this->system->loadModel('goods/productCat');
        $this->pagedata['tree']=$objCat->get_cat_list();
        
        $aCatNull[] = array('cat_id'=>0,'cat_name'=>'根目录','step'=>1);
        if(empty($aCat)){
            $aCat = $aCatNull;
        }else{
            $aCat = array_merge($aCatNull, $aCat);
        }
        $this->pagedata['catList'] = $aCat;
        $this->page('product/virtualcat/import.html');
    }
    function view($catid){
        $objCat = $this->system->loadModel('goods/productCat');
        if($views = $objCat->getTabs($catid)){
            $this->pagedata['views'] = $views;
        }
        $this->pagedata['params'] = array('cat_id'=>$catid);
        $this->pagedata['catid'] = $catid;
        $this->page('product/virtualcat/view.html');
    }

    function getGoodsCatById($cat_id=0){
        $vobjCat = $this->system->loadModel('goods/virtualcat');
        echo json_encode($vobjCat->getGoodsCatById($cat_id));

    }
    function getVirtualCatById($cat_id=0){
        $vobjCat = $this->system->loadModel('goods/virtualcat');
        echo json_encode($vobjCat->getVirtualCatById($cat_id));
    }

    function edit($catid){
        $this->path[] = array('text'=>'商品虚拟分类编辑');
        $vobjCat = $this->system->loadModel('goods/virtualcat');
        $objCat = $this->system->loadModel('goods/productCat');
        //$aCat = $vobjCat->getFieldById($catid);
        $aCat = $this->model->instance($catid);
        $this->pagedata['virtual_cat_name'] = $aCat['virtual_cat_name'];
        $aCat['addon'] = unserialize($aCat['addon']);
        $aCat['addon']['meta']['keywords'] = stripslashes($aCat['addon']['meta']['keywords']);
        $aCat['addon']['meta']['description'] = stripslashes($aCat['addon']['meta']['description']);
        $this->pagedata['cat'] = $aCat;
        $aCat = $vobjCat->get_virtualcat_list(true);
        $aCatNull[] = array('cat_id'=>0,'cat_name'=>'----无----','step'=>1);
        $aCat = array_merge($aCatNull, $aCat);
        $this->pagedata['catList'] = $aCat;
        $this->pagedata['gtypes'] = $objCat->getTypeList();
        $oGtype = &$this->system->loadModel('goods/gtype');
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();

        $this->page('product/virtualcat/info.html');
    }

    function addView($catid){
        $this->pagedata['params'] = array('cat_id'=>$catid);
        $this->pagedata['item'] = array('label'=>'New View '.mydate('H:i:s'));
        $this->setView('product/virtualcat/view_row.html');
        $this->output();
    }

    function saveView($catid){
        foreach($_POST['view'] as $k=>$v){
            if($v) $views[] = array('label'=>$v,'filter'=>$_POST['filter'][$k]);
        }
        $objCat = $this->system->loadModel('goods/productCat');
        $this->splash($objCat->setTabs($catid,$views),'index.php?ctl=goods/virtualcat&act=index');
    }

    function filter($catid){
        $this->pagedata['catid'] = $catid;
        $this->pagedata['params'] = array('cat_id'=>$catid);
        $this->page('product/virtualcat/filter.html');
    }

    function index(){
        $objCat = $this->system->loadModel('goods/virtualcat');
        if($objCat->checkTreeSize()){
            $this->pagedata['hidenplus']=true;
        }

        $tree=$objCat->get_virtualcat_list();
        $this->pagedata['tree_number']=count($tree);
        $this->pagedata['tree']=$tree;
        $depath=array_fill(0,$objCat->get_virtualcat_depth(),'1');
        $this->pagedata['depath']=$depath;
        $this->page('product/virtualcat/map.html');

    }

    function getChildCat($pid){
        $pid = $_POST['pid'];
        $objType=$this->system->loadModel('goods/virtualcat');
        $this->pagedata['tree']=$objType->getTreeList($pid, false);
        $this->setView('product/virtualcat/rows.html');
        $this->output();
    }


    function toRemove($id){
    
        $objType=$this->system->loadModel('goods/virtualcat');


        if($objType->toRemove($id)){
            $this->splash('success','index.php?ctl=goods/virtualcat&act=index','虚拟分类删除成功');
        }else{
            $this->splash('failed','index.php?ctl=goods/virtualcat&act=index','虚拟分类删除失败');
        }

    }

    function update(){
        $objType=$this->system->loadModel('goods/virtualcat');
        if($objType->updateOrder($_POST['p_order'])){
            $this->splash('success','index.php?ctl=goods/virtualcat&act=index','更新成功');
        }else{
            $this->splash('failed','index.php?ctl=goods/virtualcat&act=index','更新失败');
        }    
    }
}
?>