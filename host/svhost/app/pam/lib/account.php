<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class pam_account{

    function __construct($type){
        $this->type = $type;
        $this->session = kernel::single('base_session')->start();
    }

    function is_valid(){
        return $_SESSION['account'][$this->type];
    }
    
    function is_exists($login_name){
        if(app::get('pam')->model('account')->getList('account_id',array('account_type'=>$this->type,'login_name'=>$login_name)))
            return true;
        else
            return false;
    }

    function update($module,$module_uid, $auth_data){
        if($module!='pam_passport_basic'){
            $auth_model = app::get('pam')->model('auth');
            if($row = $auth_model->getlist('*',array(
                    'module_uid'=>$module_uid,
                    'module'=>$module,
                ),0,1)){
                $auth_model->update(array('data'=>$auth_data),array(
                    'module_uid'=>$module_uid,
                    'module'=>$module,
                ));
                $account_id = $row[0]['account_id'];
            }else{
                $data = array(
                        'account_type'=>$this->type,
                        'createtime'=>time(),
                    );
                $account_id = app::get('pam')->model('account')->insert($data);

                $data = array(
                    'account_id'=>$account_id,
                    'module_uid'=>$module_uid,
                    'module'=>$module,
                    'data'=>$auth_data,
                );
                $auth_model->insert($data);
            }
        }else{
            $account_id = $module_uid;
        } 

        $_SESSION['account'][$this->type] = $account_id;
        return true;
    }

    static function register_account_type($app_id,$type,$name){
        $account_types = app::get('pam')->getConf('account_type');
        $account_types[$app_id] = array('name' => $name, 'type' => $type);
        app::get('pam')->setConf('account_type',$account_types);
    }

    static function unregister_account_type($app_id){
        $account_types = app::get('pam')->getConf('account_type');
        unset($account_types[$app_id]);
        app::get('pam')->setConf('account_type',$account_types);
    }

    static function get_account_type($app_id = 'b2c') 
    {
        $aType = app::get('pam')->getConf('account_type');
        //todo
        return $aType[$app_id]['type'];
        //return 'member';
    }//End Function
    

}
