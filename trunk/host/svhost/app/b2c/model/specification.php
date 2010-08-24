<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_specification extends dbeav_model{
    var $has_many = array(
        'spec_value' => 'spec_values:contrast'
    );

    function getSpecIdByAll($spec){
        $sql = 'SELECT s.spec_id from sdb_b2c_specification s '
            .'left join sdb_b2c_spec_values v on s.spec_id = v.spec_id '
            .'where s.spec_name = "'.$spec['spec_name'].'" and v.spec_value in ("'.implode('","',$spec['option']).'") '
            .' group by v.spec_id having count(*) = '.count($spec['option']);
        return $this->db->select($sql);
    }

    function getSpecValuesByAll($spec){
        $rs = array();
        $i = 0;
        $oSpecValue = &$this->app->model('spec_values');
        foreach( $spec['option'] as $specValue ){
            $rs[$specValue] = $oSpecValue->dump(array('spec_value'=>$specValue,'spec_id'=>$spec['spec_id']),'spec_value_id');
            $rs[$specValue]['spec_value'] = $specValue;
            $rs[$specValue]['private_spec_value_id'] = time().(++$i);
            $rs[$specValue]['spec_image'] = '';
            $rs[$specValue]['spec_goods_images'] = '';
        }
        return $rs;
    }

    function pre_recycle($rows){
        foreach($rows as $v){
            $spec_ids[] = $v['spec_id'];
        }
        $o = &$this->app->model('goods_spec_index');
        $rows = $o->getList('*',array('spec_id'=>$spec_ids));
        if( $rows[0] ){
            $this->recycle_msg = '规格已被商品使用';
            return false;
        }
        return true;
    }

    function save(&$data,$mustUpdate = null){
        if( $data['spec_value'] ){
            $i = 1;
            foreach( $data['spec_value'] as $k => $v ){
                $data['spec_value'][$k]['p_order'] = $i++;
            }
        }
        return parent::save($data,$mustUpdate);
    }

    function dump($filter,$field = '*',$subSdf = null){
        $rs = parent::dump($filter,$field,$subSdf);
        if( $rs['spec_value'] ){
            $tSpecValue = current( $rs['spec_value'] );
            if( $tSpecValue['p_order'] && $tSpecValue['spec_value_id'] ){
                $specValue = array();
                foreach( $rs['spec_value'] as $k => $v ){
                    $specValue[$v['p_order']] = $v;
                }
                ksort($specValue);
                $rs['spec_value'] = array();
                foreach( $specValue as $vk => $vv ){
                    $rs['spec_value'][$vv['spec_value_id']] = $vv;
                }
            }
        }
        return $rs;
    }

    function delete($filter){
        $o = &$this->app->model('goods_spec_index');
        if( $o->dump($filter) ){
            $this->recycle_msg = '规格已被商品使用';
            return false;
        }
        $o = &$this->app->model('spec_values');
        $o->delete($filter);
        return parent::delete($filter);;
    }
}
