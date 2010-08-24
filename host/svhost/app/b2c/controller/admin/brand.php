<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_brand extends desktop_controller{

    var $workground = 'b2c.workground.goods';

    function index(){
        $this->finder('b2c_mdl_brand',array(
            'title'=>'商品品牌',
            'actions'=>array(
                array('label'=>'添加商品品牌','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_brand&act=create','target'=>'_blank'),
            )
            ));
    }

    function getCheckboxList(){
        $brand = &$this->app->model('brand');
        $this->pagedata['checkboxList'] = $brand->getList('brand_id,brand_name',null,0,-1);
        $this->page('admin/goods/brand/checkbox_list.html');
    }

    function create(){
        $oGtype = &$this->app->Model('goods_type');
        $objBrand = &$this->app->model('brand');
        $this->pagedata['type'] = $objBrand->getDefinedType();
        $this->pagedata['brandInfo']['type'][$this->pagedata['type']['default']['type_id']] = 1;
        $this->pagedata['gtype']['status'] = $oGtype->checkDefined();
        $this->singlepage('admin/goods/brand/detail.html');
    }

    function save(){

        $this->begin('index.php?app=b2c&ctl=admin_brand&act=index');
        $_POST['ordernum'] = intval( $_POST['ordernum'] );
        $objBrand = &$this->app->model('brand');
        $data = $this->_preparegtype($_POST);
        $this->end($objBrand->save($data),__('品牌保存成功'));

    }

    function edit($brand_id){
        $this->path[] = array('text'=>__('商品品牌编辑'));
        $objBrand = &$this->app->model('brand');
        $this->pagedata['brandInfo'] = $objBrand->dump($brand_id);
        if(empty($this->pagedata['brandInfo']['brand_url'])) $this->pagedata['brandInfo']['brand_url'] = 'http://';

        foreach($objBrand->getBrandTypes($brand_id) as $row){
            $aType[$row['type_id']] = 1;
        }

        $this->pagedata['seo']=$seo_info;
        $this->pagedata['brandInfo']['type'] = $aType;
        $this->pagedata['type'] = $objBrand->getDefinedType();
        $objGtype = &$this->app->model('goods_type');
        $this->pagedata['gtype']['status'] = $objGtype->checkDefined();

        $this->singlepage('admin/goods/brand/detail.html');
    }
    function _preparegtype($data){
        if(is_array($data['gtype'])){
            foreach($data['gtype'] as $key=>$val){
                $pdata = array('type_id'=>$val);
                $result[] = $pdata;
            }
        }
        $data['gtype'] = $result;
        return $data;
    }


}
