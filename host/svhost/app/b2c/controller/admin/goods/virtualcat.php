<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_goods_virtualcat extends desktop_controller{

    function index(){
        $objCat = &$this->app->model('goods_virtual_cat');
        if($objCat->checkTreeSize()){
            $this->pagedata['hidenplus']=true;
        }
        $tree = $objCat->get_virtualcat_list();
        $this->pagedata['tree_number']=count($tree);
        if(is_array($tree)){
            foreach($tree as $k=>$v){
               parse_str($v['filter'],$filter);
               $link = array();
               foreach($filter as $n=>$f){
                   if($n=='pricefrom'&&!$f){
                       $link[$n] = array('v'=>0,'t'=>$v['cat_name']);
                   }
                   if($f){
                       $link[$n] = array('v'=>$f,'t'=>$v['cat_name']);
                   }
                   $data[$n] = $f;
               }
               $tree[$k]['link'] = $link;
               $tree[$k]['filter'] = urlencode(serialize($data));
            }
        }
        $this->pagedata['tree']=&$tree;
        $depath=array_fill(0,$objCat->get_virtualcat_depth(),'1');
        $this->pagedata['depath']=$depath;
        $this->page('admin/goods/virtualcat/map.html');

    }
    function addNew($id=0){
        $this->begin('index.php?app=b2c&ctl=admin_goods_virtualcat&act=index');
        $this->path[] = array('text'=>__('商品虚拟分类新增'));
        $vobjCat = &$this->app->Model('goods_virtual_cat');
        $objCat = &$this->app->Model('goods_cat');
        $aCat = $vobjCat->get_virtualcat_list(true);
        $aCatNull[] = array('cat_id'=>0,'cat_name'=>__('----无----'),'step'=>1);
        if(empty($aCat)){
            $aCat = $aCatNull;
        }else{
            $aCat = array_merge($aCatNull, $aCat);
        }
        $this->pagedata['catList'] = $aCat;
        $this->pagedata['gtypes'] = $objCat->getTypeList();
        $oGtype = &$this->app->Model('goods_type');
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();

        if($id){
            $aCat = $vobjCat->dump($id);
            $this->pagedata['cat']['parent_id'] = $aCat['virtual_cat_id'];
            $this->pagedata['cat']['type_id'] = $aCat['type_id'];
        }else{
            $aTmp = $oGtype->getDefault();
            $this->pagedata['cat']['type_id'] = $aTmp[0]['type_id'];
        }
        $this->pagedata['cat']['p_order'] = 0;
        $this->page('admin/goods/virtualcat/info.html');
    }

    function doAdd(){
        $this->begin('index.php?app=b2c&ctl=admin_goods_virtualcat&act=index');
        $objCat = &$this->app->Model('goods_virtual_cat');
        $cat = $_POST['cat'];
        $cat['virtualcat_template'] = $_POST['virtualcat_template'];
        $cat['filter'] = $_POST['adjunct']['items'][0];
        if($objCat->addNew($cat)){
            $this->end(true,__('保存成功'));
        }else{
            $this->end(false,__('保存失败'));

        }
    }
    function doImport(){

        if(!is_array($_POST['cat']) || empty($_POST['cat'])){
            $this->splash('failed', 'index.php?ctl=goods/virtualcat&act=import', __('请选择商品分类源节点'));
        }else{
            $this->begin('index.php?app=b2c&ctl=admin_goods_virtualcat&act=index');
            foreach($_POST['cat'] as $key=>$v){
                if($v){
                    $search[]=$v;
                }
            }
            $objCat = &$this->system->loadModel('goods/virtualcat');
            $this->end($objCat->doImport($search,$_POST['vCat_id'],$_POST['defaultfilter']),__('保存成功'));
        }
    }


    function getGoodsCatById($cat_id=0){
        $vobjCat = &$this->app->Model('goods_virtual_cat');
        echo json_encode($vobjCat->getGoodsCatById($cat_id));

    }


    function edit($catid){
        $this->path[] = array('text'=>__('商品虚拟分类编辑'));
        $vobjCat = &$this->app->Model('goods_virtual_cat');
        $objCat = &$this->app->Model('goods_cat');
        $aCat = $vobjCat->dump($catid);
        $this->pagedata['virtual_cat_name'] = $aCat['virtual_cat_name'];
        $aCat['addon'] = unserialize($aCat['addon']);
        $filter = $aCat['filter'];
        unset($aCat['filter']);
        $aCat['filter']['items'] = $filter;
        $this->pagedata['cat'] = $aCat;
        $aCat = $vobjCat->get_virtualcat_list(true);
        $aCatNull[] = array('cat_id'=>0,'cat_name'=>__('----无----'),'step'=>1);
        $aCat = array_merge($aCatNull, $aCat);
        $this->pagedata['catList'] = $aCat;
        $this->pagedata['gtypes'] = $objCat->getTypeList();
        $oGtype = &$this->app->Model('goods_type');
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();

        $this->page('admin/goods/virtualcat/info.html');
    }

    function getChildCat($pid){
        $pid = $_POST['pid'];
        $objType = &$this->app->Model('goods_virtual_cat');
        $this->pagedata['tree']=$objType->getTreeList($pid, false);
        $this->page('admin/goods/virtualcat/info.html');
    }


    function toRemove($id){
        $this->begin('index.php?app=b2c&ctl=admin_goods_virtualcat&act=index');
        $objType = &$this->app->Model('goods_virtual_cat');
        $this->end($objType->toRemove($id),__('分类删除成功'));
    }

    function update(){
        $this->begin('index.php?app=b2c&ctl=admin_goods_virtualcat&act=index');
        $objType = &$this->app->Model('goods_virtual_cat');
        $this->end($objType->updateOrder($_POST['p_order']), __('更新成功'));
    }
}
?>