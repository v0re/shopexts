<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_member_lv extends dbeav_model{    
        
    function save($aData){
        $default_lv_id = $this->get_default_lv();
        /*
        if( $aData['member_lv_id'] && $default_lv_id && $aData['member_lv_id'] != $default_lv_id ){
            $this->unset_default_lv($default_lv_id);
        }*/
        return parent::save($aData);
    }
    
    function get_level(){
        $rows = $this->getList("*");
        return  $rows ? $rows : array() ;
    }
    
    function get_default_lv(){
        $ret = $this->getList('member_lv_id',array('default_lv'=>1));
        return $ret[0]['member_lv_id'];
    }

    
    function unset_default_lv($default_lv_id){
        $sdf['member_lv_id'] = $default_lv_id;
        $sdf['default_lv'] = 0;
        $this->save($sdf);
    }
    
    function validate(&$data,&$msg){
       $fag = 1;
       if($data['name']==''){
             $msg = __('等级名称不能为空！');
             $fag = 0;
        } 
        $ret = $this->getList('member_lv_id',array('name'=>$data['name']));
        $member_lv_id = $ret[0]['member_lv_id'];
        $lv = $this->getList('*',array('default_lv'=>1));
        if(isset($data['point'])){
            $data['point'] = intval($data['point']);
            $filter = array('point' => $data['point']);
            $levelSwitch = "积分";
        }
        elseif(isset($data['experience'])){
            $data['experience'] = intval($data['experience']);
            $filter = array('experience' => $data['experience']);
            $levelSwitch = "经验值";
        }
        $exist = $this->getList('*',$filter);
        $default_lv = $lv[0]['name'];
        if($exist && ($exist[0]['member_lv_id'] != $data['member_lv_id'])){
            $msg = __('已存在'.$levelSwitch.'相同的会员等级');
            $fag = 0;
        }
        if( $member_lv_id && $member_lv_id != $data['member_lv_id']){
             $msg = __('同名会员等级存在！');
             $fag = 0;
        }
        if(($data['default_lv'] == 1 && $default_lv)&&$data['member_lv_id'] !=$lv[0]['member_lv_id']){          
             $msg = $default_lv.__('  已是默认等级，请先取消！！');
             $fag = 0;
        }
        if($data['dis_count'] < 0 or $data['dis_count'] > 1){
             $msg = __('会员折扣率不是有效值！');
             $fag = 0;
        }      
        if($data['point'] < 0 || $data['experience'] < 0){
            $msg = __($levelSwitch.'不能为负！');
            $fag = 0;
        }  
        if($data['dis_count'] == 0){
            $data['dis_count'] = "0.0";
        }
        return $fag;    
    }
    
     function pre_recycle($data){
       $members = $this->app->model('members');
       foreach($data as $val){
          $aData = $members->getList('member_id',array('member_lv_id' => $val['member_lv_id']));
          if($aData){
              $this->recycle_msg = '该等级下存在会员,不能删除';
               return false;
           }
          
       }
       return true;
   }
    
        
}
