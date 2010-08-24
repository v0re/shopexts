<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_products extends dbeav_model{
    var $has_many = array(
        'price/member_lv_price' => 'goods_lv_price:contrast',
        );
    function getRealStore( $pId ){
        $data = $this->dump($pId,'store,freez');
        if( $data[$pId] === null )
            return null;
        return $data['store'] - $data['freez'];
    }

    function checkStore($pId, $quantity){
        $realQuantity = $this->getRealStore($pId);
        if(!is_null($realQuantity)){
            if($realQuantity < $num){
                return false;
            }
        }
        return true;

    }

    function getRealMkt($price){
        if($this->app->getConf('site.show_mark_price')=='true'){
            $math = $this->app->getConf('site.market_price');
            $rate = $this->app->getConf('site.market_rate');
            if($math == 1)
               return $price = $price*$rate;
            if($math == 2)
               return $price = $price+$rate;
        }else{
            return $price;
        }
    }

    function save(&$data,$mustUpdate = null){
        if (isset($data['spec_desc']) && $data['spec_desc'] && is_array($data['spec_desc']) && isset($data['spec_desc']['spec_value']) && $data['spec_desc']['spec_value'])
        {
            $data['spec_info'] = implode('、', (array)$data['spec_desc']['spec_value']);
        }
        if( $data['price']['member_lv_price'] )
            foreach( $data['price']['member_lv_price'] as $k => $v ){
                $data['price']['member_lv_price'][$k]['goods_id'] = $data['goods_id'];
            }

        return parent::save($data,$mustUpdate);
    }

    function dump($filter,$field = '*',$subSdf = null){
        $data = parent::dump($filter,$field,$subSdf);
        if( !isset($this->site_member_lv_id ) ){
            $ctlGoods = new b2c_ctl_site_product($this->app);
            $siteMember = $ctlGoods->get_current_member();
            $this->site_member_lv_id = $siteMember['member_lv'];
        }
        if (isset($data['price']) && $data['price'] && is_array($data['price']) && isset($data['price']['member_lv_price']) && $data['price']['member_lv_price'] && is_array($data['price']['member_lv_price']))
        {
            if( array_key_exists( 'member_lv_price', $data['price'] ) && array_key_exists( $this->site_member_lv_id, $data['price']['member_lv_price'] ) ){
                $data['price']['price']['current_price'] = $data['price']['member_lv_price'][$this->site_member_lv_id]['price'];
            }else{
                $data['price']['price']['current_price'] = $data['price']['price']['price'];
            }
        }
        return $data;
    }

/*    function getList($cols='*',$filter=array(),$start=0,$limit=-1,$orderType=null){
        return kernel::service('b2c_goods_list')->goods_list($cols,$filter,$start=0,$limit=-1,$orderType);
    }*/

    function _dump_depends_goods_lv_price(&$data,&$redata,$filter,$subSdfKey,$subSdfVal){
        $oMlv = &$this->app->model('member_lv');
        $memLvId = $oMlv->getList('member_lv_id','',0,-1);
        foreach( $memLvId as $aMemLvId )
            $idArray[] = array( 'level_id'=>$aMemLvId['member_lv_id'],'product_id'=>$data['product_id'] );
        $subObj = &$this->app->model('goods_lv_price');
        //$idArray = $subObj->getList( implode(',',(array)$subObj->idColumn), $filter,0,-1 );
        foreach( (array)$idArray as $aIdArray ){
            $subDump = $subObj->dump($aIdArray,$subSdfVal[0],$subSdfVal[1]);
            if( $this->has_many[$subSdfKey] ){
                switch( count($aIdArray) ){
                    case 1:
                        eval('$redata["'.implode( '"]["', explode('/',$subSdfKey) ).'"][current($aIdArray)] = $subDump;');
                        break;
                    case 2:
                        eval('$redata["'.implode( '"]["', explode('/',$subSdfKey) ).'"][current(array_diff_assoc($aIdArray,$filter))] = $subDump;');
                        break;
                    default:
                        eval('$redata["'.implode( '"]["', explode('/',$subSdfKey) ).'"][] = $subDump;');
                        break;
                }
            }else{
                eval('$redata["'.implode( '"]["', explode('/',$subSdfKey) ).'"] = $subDump;');
            }
        }
    }

}
