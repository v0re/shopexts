<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_specification extends desktop_controller{

    var $workground = 'b2c_ctl_admin_goods';

    function index(){
        $this->finder('b2c_mdl_specification',array(
            'title'=>'商品规格',
            'use_buildin_set_tag' => false
        ));
    }

    function add(){
        $this->page('admin/goods/specification/detail.html');
    }

    function save(){
        $this->begin('index.php?app=b2c&ctl=admin_specification&act=index');
        $oSpec = &$this->app->model('specification');
        if(!$_POST['spec']['spec_value']){
            $this->end(false,'请输入规格值');
            exit;
        }
        foreach( $_POST['spec']['spec_value'] as $specValue ){
            if( $specValue['spec_value'] == '' ){
                $this->end(false,'规格值不能为空');
                exit;
            }
        }
        $this->end($oSpec->save($_POST['spec']),'操作成功');
    }

    function edit( $specId ){
        $oSpec = &$this->app->model('specification');
        $subsdf = array(
            'spec_value'=>array('*')
        );
        $this->pagedata['spec'] = $oSpec->dump($specId,'*',$subsdf);
        $this->page('admin/goods/specification/detail.html');
    }

    function check_spec_value_id(){
        $oSpecIndex = &$this->app->model('goods_spec_index');
        if( !$oSpecIndex->dump($_POST) )
            echo "can";
        else
            echo __("该规格值已绑定商品");
    }

    function selSpecDialog($typeId = 0) {
        $aSpec = array();
        if($typeId){
            //$aSpec = $objSpec->getListByTypeId($typeId);
        }else{
            $oSpec = &$this->app->model('specification');
            $aSpec = $oSpec->getList('spec_id,spec_name,spec_memo',null,0,-1);
        }
        $this->pagedata['specs'] = $aSpec;
        $this->display('admin/goods/specification/spec_select.html');
    }

    function previewSpec(){
        $oSpec = &$this->app->model('specification');
        $this->pagedata['spec'] = $oSpec->dump( $_POST['spec_id'], '*',array('spec_value'=>array('*')));
        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->display('admin/goods/specification/spec_value_preview.html');
    }

    function edit_default_pic(){
        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->pagedata['spec_image_height'] = $this->app->getConf('spec.image.height');
        $this->pagedata['spec_image_width'] = $this->app->getConf('spec.image.width');
        $this->display('admin/goods/specification/spec_default_pic.html');
    }

    function save_default_pic(){
        $this->begin('index.php?app=b2c&ctl=admin_specification&act=index');
        foreach( $_POST['set'] as $k => $v ){
            $this->app->setConf($k,$v);
        }
        $this->end(true,'保存成功');
    }

}
