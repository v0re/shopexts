<?php
require_once('shopObject.php');

class mdl_adminroles extends shopObject{

    var $idColumn = 'role_id'; //表示id的列 
    var $textColumn = 'role_name';
    var $defaultCols = 'role_name,role_memo';
    var $adminCtl = 'trading/delivery_centers';
    var $defaultOrder = array('role_id','desc');
    var $tableName = 'sdb_admin_roles';

    function getAllActions(){
        return array(
            '1'=>'商品',
            '2'=>'订单',
            '3'=>'会员',
            '4'=>'营销推广',
            '5'=>'页面管理',
            '6'=>'统计报表',
            '7'=>'商店配置',
            '8'=>'工具箱'
        );
    }

    function rolemap(){
        return array(
            'goods'=>1,
            'order'=>2,
            'member'=>3,
            'sale'=>4,
            'site'=>5,
            'analytics'=>6,
            'setting'=>7,
            'tools'=>8
        );
    }


    function getColumns(){
        return array(
            'role_id'=>array('label'=>'角色id','class'=>'span-2'),
            'role_name'=>array('label'=>'角色名称','class'=>'span-4','required'=>1),
            'role_memo'=>array('label'=>'角色备注','class'=>'span-5'),
        );
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

    function update($data,$filter){
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

}
?>
