<?php
/**
 * ctl_olist 
 * 
 * @uses adminPage
 * @package 
 * @version $Id: ctl.brand.php 1912 2008-04-24 08:21:17Z alex $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com> 
 * @license Commercial
 */
include_once('objectPage.php');
class ctl_brand extends objectPage{

    var $name='品牌';
    var $workground = 'goods';
    var $actionView = 'product/brand/finder_action.html';
    var $object = 'goods/brand';
    var $editMode = true;
    
    var $disableGridEditCols = "brand_id,s_brand_id,brand_logo";
    var $disableColumnEditCols = "brand_id,s_brand_id,brand_logo";
    var $disableGridShowCols = "brand_id,s_brand_id,brand_logo";

    function detail($brand_id){
        $this->path[] = array('text'=>'商品品牌编辑');
        $brand = $this->system->loadModel('goods/brand');
        $this->pagedata['brandInfo'] = $brand->getFieldById($brand_id, array('*'));
        if(empty($this->pagedata['brandInfo']['brand_url'])) $this->pagedata['brandInfo']['brand_url'] = 'http://';
        
        foreach($brand->getBrandTypes($brand_id) as $row){
            $aType[$row['type_id']] = 1;
        }
        $this->pagedata['brandInfo']['type'] = $aType;
        $this->pagedata['type'] = $brand->getDefinedType();
        $oGtype = &$this->system->loadModel('goods/gtype');
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();
        
        $this->setView('product/brand/detail.html');
        $this->output();
    }
    
    function addNew(){
        $this->path[] = array('text'=>'商品品牌新增');
        $this->pagedata['brandInfo']['brand_url'] = 'http://';
        $brand = $this->system->loadModel('goods/brand');
        $this->pagedata['type'] = $brand->getDefinedType();
        $this->pagedata['brandInfo']['type'][$this->pagedata['type']['default']['type_id']] = 1;
        $oGtype = &$this->system->loadModel('goods/gtype');
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();
        $this->page('product/brand/detail.html');
    }
    function active(){
        parent::active();
        $brand = $this->system->loadModel('goods/brand');
        $brand->brand2json();
    }
     function recycle(){
        parent::recycle();
        $brand = $this->system->loadModel('goods/brand');
        $brand->brand2json();
    }
    function save(){
    
        if($_POST['brand_id'])
            $this->begin('index.php?ctl=goods/brand&act=detail&p[0]='.$_POST['brand_id']);
        else
            $this->begin('index.php?ctl=goods/brand&act=index');
        $brand = $this->system->loadModel('goods/brand');
        
        $this->end($brand->save($_POST['brand_id'],$_POST),__('品牌保存成功'));
    }
    
    function getCheckboxList(){
        $brand = $this->system->loadModel('goods/brand');
        $this->pagedata['checkboxList'] = $brand->getList('brand_id,brand_name');
        $this->setView('product/brand/checkbox_list.html');
        $this->output();
    }
}
?>
