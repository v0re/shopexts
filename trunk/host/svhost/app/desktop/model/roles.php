<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class desktop_mdl_roles extends dbeav_model{

    function getAllActions(){
        $actions = array(
            '1'=>__('商品'),
            '2'=>__('订单'),
            '3'=>__('会员'),
            '4'=>__('营销推广'),
            '5'=>__('页面管理'),
            '6'=>__('统计报表'),
            '7'=>__('商店配置'),
            '8'=>__('工具箱'),
        );
        if($this->app->getConf('certificate.distribute')){
            $actions['29'] = __('采购中心');
        }
        
        return $actions;
    }

    function rolemap(){
        $map = array(
            'goods'=>1,
            'order'=>2,
            'member'=>3,
            'sale'=>4,
            'site'=>5,
            'analytics'=>6,
            'setting'=>7,
            'tools'=>8,
        );
        if($this->app->getConf('certificate.distribute')){
            $map['distribution'] = 29;
        }
        
        return $map;
    }


    function getColumns(){
        $ret = array('_cmd'=>array('label'=>__('操作'),'width'=>75,'html'=>'admin/roles_cmd.html'));
        return array_merge($ret,parent::getColumns());
    }

    function instance($role_id){
        $role = parent::instance($role_id);
        if($role){
            $rows = $this->db->select('select * from sdb_lnk_acts where role_id='.intval($role_id));
            foreach($rows as $r){
                $role['actions'][] = $r['action_id'];
            }
        }
        return $role;
    }

    function updatebak($data,$filter){
        $c = parent::update($data,$filter);

        if($filter['role_id']){
            $role_id = array();
            foreach($this->getList('role_id',$filter) as $r){
                $role_id[] = $r['role_id'];
            }
        }else{
            $role_id = $filter['role_id'];
        }

        if(count($role_id)==1){
            $rows = $this->db->select('select action_id from sdb_lnk_acts where role_id in ('.implode(',',$role_id).')');
            $in_db = array();
            foreach($rows as $r){
                $in_db[] = $r['action_id'];
            }
            $data['actions'] = $data['actions']?$data['actions']:array();
            $to_add = array_diff($data['actions'],$in_db);
            $to_del = array_diff($in_db,$data['actions']);

            if(count($to_add)>0){
                $sql = 'INSERT INTO `sdb_lnk_acts` (`role_id`,`action_id`) VALUES ';
                foreach($to_add as $action_id){
                    $actions[] = "({$role_id[0]},$action_id)";
                }
                $sql .= implode($actions,',').';';
                $a = $this->db->exec($sql);
            }

            if(count($to_del)>0){
                $this->db->exec('delete from sdb_lnk_acts where action_id in ('.implode(',',$to_del).') and role_id='.intval($role_id[0]));
            }
        }else{

        }

        return $c;
    }

    function insert($data){
        $role_id = parent::insert($data);
        if($role_id && is_array($data['actions'])){
            $sql = 'INSERT INTO `sdb_lnk_acts` (`role_id`,`action_id`) VALUES ';
            foreach($data['actions'] as $action_id){
                $actions[] = "($role_id,$action_id)";
            }
            $sql .= implode($actions,',').';';
            $a = $this->db->exec($sql);
        }
        return $role_id;
    }
   ####检查工作组名称
   function check_gname($name){
      # $result = $this->db->select("select * from sdb_desktop_roles where role_name='$name'");
    $result = $this->getList('role_id',array('role_name'=>$name));
       if($result){
           
           return $result[0]['role_id'];
       }
       else{
           return false;
       }
   }
   
   function validate($aData,&$msg){
        if($aData['role_name']==''){
        $msg = __("工作组名称不能为空");
        return false;
        }
        if(!$aData['workground']){
        $msg = __("请至少选择一个权限");
        return false;
        }
        $opctl = &$this->app->model('roles');
        $result = $opctl->check_gname($aData['role_name']);
        if($result){
        $msg = __("该名称已经存在");
        return false;    
         }
         return true;
     }
}
?>
