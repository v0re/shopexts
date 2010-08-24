<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_dlytype extends dbeav_model{
    var $has_many = array(
        
    );


    public function getRegionById($parent_id){
        $sql='select r.region_id,r.p_region_id,r.local_name,count(p.region_id) as child_count from sdb_b2c_regions as r
                left join sdb_b2c_regions as p on r.region_id=p.p_region_id
                where r.p_region_id'.($parent_id?('='.intval($parent_id)):' is null').' and r.package=\''.$this->app->getConf('system.location').'\'
                group by(r.region_id)
                order by r.ordernum asc,r.region_id';

        return $this->db->select($sql);
    }
    
    public function save(&$data,$mustUpdate = null){
        if($data['dt_useexp']==0)
        {
            //如果未使用公式则使用默认
            $data['dt_expressions'] = "{{w-0}-0.4}*{{{".$data['firstunit']."-w}-0.4}+1}*".$data['firstprice']."+ {{w-".$data['firstunit']."}-0.6}*[(w-".$data['firstunit'].")/".$data['continueunit']."]*".$data['continueprice']."*".$data['dt_discount']."";
        }
        
        if ($data['protect']) 
            $data['protect_rate'] = $data['protect_rate']/100;
        
        $data['ordernum'] = intval($data['ordernum']);
        
        if($data['area_fee_conf'] && is_array($data['area_fee_conf']))
        {
            foreach ($data['area_fee_conf'] as $key=>$value)
            {
                if ($value['dt_useexp']==0)
                {//如果未使用公式则使用默认
                    $data['area_fee_conf'][$key]['dt_expressions'] = "{{w-0}-0.4}*{{{".$data['firstunit']."-w}-0.4}+1}*".$value['firstprice']."+ {{w-".$data['firstunit']."}-0.6}*[(w-".$data['firstunit'].")/".$data['continueunit']."]*".$value['continueprice']."*".$value['dt_discount']."";
                }
                else
                {
                    $data['area_fee_conf'][$key]['dt_expressions'] = $data['area_fee_conf'][$key]['expressions'];
                }
            }
        }
        
        $return = parent::save($data,$mustUpdate);
        
        return $return;
    }
}
