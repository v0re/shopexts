<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_goods_type extends dbeav_model{
    var $has_many = array(
        'brand' => 'type_brand:replace',
        'spec' => 'goods_type_spec:replace',
        'props' => 'goods_type_props:contrast'
    );

    function checkDefined(){
        return $this->count(array('is_def'=>'false'));
    }

    function getDefault(){
        return $this->getList('*',array('is_def'=>'true'));
    }

    function getSpec($id,$fm=0){
        $sql="select spec_id,spec_style from sdb_b2c_goods_type_spec where type_id=".intval($id);
        $row = $this->db->select($sql);

        if ($row){
            foreach($row as $key => $val){
                if ($fm){
                    if($val['spec_style']<>'disabled'){
                        $attachment=array(
                            "spec_style"=>$val['spec_style']
                        );
                        $tmpRow[$val['spec_id']]=$this->getSpecName($val['spec_id'],$attachment);
                    }
                }
                else{
                    $attachment=array(
                        "spec_style"=>$val['spec_style']
                    );
                    $tmpRow[$val['spec_id']]=$this->getSpecName($val['spec_id'],$attachment);
                }
            }

            return $tmpRow;
        }
        else
            return false;
    }
    function getSpecName($spec_id,$args){
        $sql="select spec_name,spec_type from sdb_b2c_specification where spec_id=".intval($spec_id);
        $snRow=$this->db->selectrow($sql);
        $tmpRow['name']=$snRow['spec_name'];
        $tmpRow['spec_type'] = $snRow['spec_type'];
        $tmpRow['spec_memo'] = $snRow['spec_memo'];
        if (is_array($args)){
            foreach($args as $k => $v){
                $tmpRow[$k] = $v;
            }
        }
        $row=$this->getSpecValue($spec_id);
        $tmpRow['spec_value']=$row;
        $tmpRow['type'] = 'spec';
        return $tmpRow;
    }

    function getSpecValue($spec_id){
        $sql="select spec_value,spec_value_id,spec_image from sdb_b2c_spec_values where spec_id=".intval($spec_id)." order by p_order,spec_value_id";
        $svRow=$this->db->select($sql);
        if ($svRow){
            foreach($svRow as $key => $val){
                $tmpRow[$val['spec_value_id']]=array(
                        "spec_value"=>$val['spec_value'],
                        "spec_image"=>$val['spec_image']
                );
            }
        }
        return $tmpRow;
    }

    function save( &$data,$mustUpdate =null ){
        $opv = &$this->app->model('goods_type_props_value');
        if ($data['props'])
        {
            foreach( $data['props'] as $k => $v ){
                $v['goods_p'] = $k;
                if( $v['options'] ){
                    foreach( $v['options'] as $vk => $vv ){
                        if( $v['props_id'] )
                            $aPropsValueId = $opv->dump( array('props_id'=>$v['props_id'],'name'=>$vv),'props_value_id' );
                        if( $aPropsValueId['props_value_id'] )
                            $data['props'][$k]['props_value'][$vk]['props_value_id'] = $aPropsValueId['props_value_id'];
                        $data['props'][$k]['props_value'][$vk]['name'] = $vv;
                        $data['props'][$k]['props_value'][$vk]['alias'] = $v['optionAlias'][$vk];
                    }
                }
                unset( $data['props'][$k]['options'] );
            }
        }
        return parent::save($data,$mustUpdate);
    }

    function dump($filter,$field = '*',$subSdf = null){
        $subSdf = array_merge( (array)$subSdf,array('props'=>array('*',array('props_value'=>array('*')))) );
        $rs = parent::dump($filter,$field,$subSdf);
        $props = array();
        if( $rs['props'] ){
            foreach( $rs['props'] as $k => $v ){
                $props[$v['goods_p']] = $v;
                if( $v['props_value'] )
                    foreach( $v['props_value'] as $vk => $vv ){
                        $props[$v['goods_p']]['options'][$vv['props_value_id']] = $vv['name'];
                        $props[$v['goods_p']]['optionAlias'][$vv['props_value_id']] = $vv['alias'];
                    }
                unset( $props[$v['goods_p']]['props_value'] );
            }
            unset( $rs['props'] );
            $rs['props'] = $props;
        }
        return $rs;
    }
    function pre_recycle($rows){
        foreach($rows as $v){
            $type_ids[] = $v['type_id'];
        }
        $o = &$this->app->model('goods');
        $rows = $o->getList('*',array('type_id'=>$type_ids),0,1);
        if( $rows ){
            $this->recycle_msg = '类型已被商品使用';
            return false;
        }
        return true;
    }
}
