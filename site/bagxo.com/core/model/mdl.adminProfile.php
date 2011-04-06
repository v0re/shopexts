<?php
/**
 * mdl_adminProfile
 *
 * @uses modelFactory
 * @package
 * @version $Id: mdl.adminProfile.php 1867 2008-04-23 04:00:24Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author LeoLee <leoleegood@zovatech.com>
 * @license Commercial
 */
class mdl_adminProfile extends modelFactory{

    function load($opid=null){
        if(!is_null($opid))
            $this->opid = $opid;
        else
            $opid = $this->opid;
            
        $aCfg = $this->db->selectrow('SELECT username,name,super FROM sdb_operators WHERE op_id='.intval($this->opid));
        $this->loginName = $aCfg['username'];
        $this->name = $aCfg['name'];
        $this->is_super = $aCfg['super'];
    }

    /**
     * getSetting
     *
     * @access public
     * @return void
     */
    function setting(){
        return $this->_setting;
    }

    function getMenu($part=null,$is_supper=false){
        include('adminSchema.php');

        $role = &$this->system->loadModel('admin/adminroles');
        $opt = $role->rolemap();
        $op = &$this->system->loadModel('admin/operator');

        if($part){
            if(!$is_supper){
                foreach($menu[$part]['items'] as $k=>$v){
                    if($v['super_only']){
                        unset($menu[$part]['items'][$k]);
                    }
                }
            }
            return $menu[$part]['items'];
        }else{
            if(!$this->is_super){
                $allow_wground = $op->getActions($this->opid);
                foreach($menu as $k=>$v){
                    if(!isset($allow_wground[$opt[$k]])){
                        unset($menu[$k]);
                    }
                }
            }
            return $menu;
        }
    }

    /**
     * get
     *
     * @param mixed $string
     * @access public
     * @return void
     */
    function get($string){
        if(!isset($this->_setting)){
            $row = $this->db->selectrow("SELECT config FROM sdb_operators WHERE op_id=".intval($this->opid));
            $this->_setting = unserialize($row['config']);
        }
        return $this->_setting[$string];
    }

    /**
     * set
     *
     * @param mixed $string
     * @param mixed $value
     * @access public
     * @return void
     */
    function set($key,$value=null){
        define('Setting_Modified',true);

        //支持两种风格的设置
        //
        // 1. $this->op->set('note',$this->in['note']);
        // 2. $this->op->set(
        //       array(
        //         'note'=>$this->in['note'],
        //         'abc'=>'bcd'
        //         ));
        //

        if(is_array($key) && !$value){
            $this->_setting = array_merge($this->_setting,$key);
        }else{
            $this->_setting[$key]=$value;
        }
        register_shutdown_function(array(&$this,'save'));
    }

    function save(){
        global $system;
        $this->system = &$system;
        $this->db = &$system->database();
        $rs = $this->db->exec("SELECT config FROM sdb_operators WHERE op_id=".intval($this->opid));
        $sql = $this->db->GetUpdateSql($rs,array('config'=>$this->_setting));
        return (!$sql || $this->db->exec($sql));
    }

    function __sleep(){
        unset($this->db);
        unset($this->system);
        return( array_keys( get_object_vars( $this ) ) );
    }
    
    function __wakeup(){
        global $system;
        $this->system = &$system;
        $this->db = &$system->database();
    }
}
?>
