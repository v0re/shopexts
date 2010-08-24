<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_goods_type extends desktop_controller{

    var $workground = 'b2c_ctl_admin_goods';

    function index(){
        $this->finder('b2c_mdl_goods_type',array('title'=>'商品类型','base_filter'=>array('is_def'=>'false')));
    }

    function add(){
        $this->page('admin/goods/goods_type/add_type.html');
    }

    function set($typeId = 0){
        if( $typeId ){
            $oType = &$this->app->model('goods_type');
            $this->pagedata['gtype'] = $oType->dump($typeId,'type_id,is_physical,setting');
        }else{
            $this->pagedata['gtype'] = array(
                'is_physical' => 1,
                'setting' => array('use_brand' => 1,'use_props' => 1,)
            );
        }
        $this->page('admin/goods/goods_type/edit_type_set.html');
    }

    function edit(){
        $gtype = $_POST['gtype'];
        if($gtype['type_id']){
            $oType = &$this->app->model('goods_type');
            $subsdf = array(
                'spec'=>array('*',array('spec:specification'=>array('spec_name,spec_memo'))),
                'brand'=>array('brand_id')
            );
            $gtype = array_merge($oType->dump($gtype['type_id'],'*',$subsdf),$gtype );
        }
        $this->pagedata['gtype'] = $gtype;
        
        $oBrand = &$this->app->model('brand');
        $this->pagedata['brands'] = $oBrand->getList('brand_id,brand_name',null,0,-1);

        $this->page('admin/goods/goods_type/edit_type_edit.html'); 
    }

    function check_type(){
        $oGtype = &$this->app->model('goods_type');
        $typeId = current( (array)$oGtype->dump( array( 'name'=>$_POST['name'],'type_id' ) ) );
        if( $typeId && $_POST['id'] != $typeId )
            echo 'false';
        else
            echo 'true';
        
    }

    function save(){
        $gtype = &$_POST['gtype'];

        $oGtype = &$this->app->model('goods_type');
        $this->begin('index.php?app=b2c&ctl=admin_goods_type&act=index');
       
        $typeId = current( (array)$oGtype->dump( array( 'name'=>$gtype['name'],'type_id' ) ) );
        if( $typeId && $gtype['type_id'] != $typeId ){ 
            trigger_error(__('类型名称已存在'),E_USER_ERROR);
            $this->end(false,__('类型名称已存在'));
        }

        //品牌
        if(!$gtype['brand']) $gtype['brand'] = null;
        //属性
        $this->_preparedProps($gtype);
        //参数
        $this->_preparedParams($gtype);
        //必填参数
        $this->_preparedMinfo($gtype);
        //规格
        $this->_preparedSpec($gtype);
        $this->end($oGtype->save($gtype),'操作成功');    
    }


    function _preparedProps(&$gtype){
        if( !$gtype['props'] )return;
        $searchType = array(
            '0' => array('type' => 'input', 'search' => 'input'),
            '1' => array('type' => 'input', 'search' => 'disabled'),
            '2' => array('type' => 'select', 'search' => 'nav'),
            '3' => array('type' => 'select', 'search' => 'select'),
            '4' => array('type' => 'select', 'search' => 'disabled'),
        );
        $props = array();
        $inputIndex = 21;
        $selectIndex = 1;
        
        $oProps = &$this->app->model('goods_type_props');
        $oPropsValue = &$this->app->model('goods_type_props_value');
        foreach( $gtype['props'] as $aProps ){
            if( !$aProps['name'] )
                continue;
            if( $gtype['type_id'] ){
                $propsId = $oProps->dump(array('type_id'=>$gtype['type_id'],'name'=>$aProps['name']),'props_id');
                if( $propsId['props_id'] )
                    $aProps['props_id'] = $propsId['props_id'];
            }
            $aProps = array_merge( $aProps,$searchType[$aProps['type']] );
            if( !$aProps['options'] ){
                unset($aProps['options']);
            }else{
                foreach( ($aProps['options'] = explode(',',$aProps['options'])) as $opk => $opv ){
                    $opv = explode('|',$opv);
                    $aProps['options'][$opk] = $opv[0];
                    unset($opv[0]);
                    $aProps['optionAlias'][$opk] = implode('|',(array)$opv);
                }
            }
            if( $aProps['type'] == 'input' ){
                $propskey = $inputIndex++;
            }else{
                $propskey = $selectIndex++;
            }
            $aProps['goods_p'] = $propskey;
            $props[$propskey] = $aProps;
        }
        $gtype['props'] = $props;
        $props = null; 
    }

    /*
    function _preparedProps(&$gtype){
        if( !$gtype['props'] )return;
        $searchType = array(
            '0' => array('type' => 'input', 'search' => 'input'),
            '1' => array('type' => 'input', 'search' => 'disabled'),
            '2' => array('type' => 'select', 'search' => 'nav'),
            '3' => array('type' => 'select', 'search' => 'select'),
            '4' => array('type' => 'select', 'search' => 'disabled'),
        );
        $props = array();
        $inputIndex = 21;
        $selectIndex = 1;
        $oProps = &$this->app->model('goods_type_props');
        $oPropsValue = &$this->app->model('goods_type_props_value');
        foreach( $gtype['props'] as $aProps ){
            if( !$aProps['name'] )
                continue;
            if( $gtype['type_id'] ){
                $propsId = $oProps->dump(array('type_id'=>$gtype['type_id'],'name'=>$aProps['name']),'props_id');
                if( $propsId['props_id'] )
                    $aProps['props_id'] = $propsId['props_id'];
            }
            $aProps = array_merge( $aProps,$searchType[$aProps['type']] );
            if( !$aProps['options'] ){
                unset($aProps['options']);
            }else{
                $tmpProps = array();
                foreach( ($aProps['options'] = explode(',',$aProps['options'])) as $opk => $opv ){
                    $opv = explode('|',$opv);
                    $aProps['props_value'][$opk] = array(
                        'name'=>array_shift( $opv ),
                        'alias'=>implode( '|',(array)$opv ),
                        'props_id' => $aProps['props_id']
                    );
                    if( $aProps['props_id'] )
                        $aPropsValueId = $oPropsValue->dump( array('props_id'=>$aProps['props_id'],'name'=>$aProps['props_value'][$opk]['name']),'props_value_id' );
                    if( $aPropsValueId['props_value_id'] )
                        $aProps['props_value'][$opk]['props_value_id'] = $aPropsValueId;
                }
//                unset($aProps['options']);
            }
            if( $aProps['type'] == 'input' ){
                $propskey = $inputIndex++;
            }else{
                $propskey = $selectIndex++;
            }
            $props[$propskey] = $aProps;
        }
        $gtype['props'] = $props;
        $props = null; 
    }
    */

    function _preparedParams(&$gtype){
        if( !$gtype['params'] ){
            $gtype['params'] = array();
            return;
        }
        $params = array();
        foreach( $gtype['params'] as $aParams ){
            $paramsItem = array();
            foreach( $aParams['name'] as $piKey => $piName ){
                $paramsItem[$piName] = $aParams['alias'][$piKey];
            }
            $params[$aParams['group']] = $paramsItem;
        }
        $gtype['params'] = $params;
        $params = null;
    }

    function _preparedMinfo(&$gtype){
        if(!$gtype['minfo'])return;
        $minfo = $gtype['minfo'];
        foreach( $minfo as $minfoKey => $aMinfo ){
            if( !trim( $aMinfo['label'] ) ){
                unset( $gtype['minfo'][$minfoKey] );
                continue;
            }
            if( !trim($aMinfo['name']) )
                $gtype['minfo'][$minfoKey]['name'] = 'M'.md5($aMinfo['label']);
            if( $aMinfo['type'] == 'select' )
                $gtype['minfo'][$minfoKey]['options'] = explode(',',$aMinfo['options']);
            else
                unset( $gtype['minfo'][$minfoKey]['options'] );
        }
        $gtype['minfo'] = array_values( $gtype['minfo'] );
    }

    function _preparedSpec(&$gtype){
        if(!$gtype['spec'])return;
        $spec = array();
        foreach( $gtype['spec']['spec_id'] as $k => $aSpec ){
            $spec[] = array(
                'spec_id'=>$aSpec,
                'spec_style' => $gtype['spec']['spec_type'][$k]
            );
        }
        $gtype['spec'] = $spec;
        $spec = null;
    }

}
