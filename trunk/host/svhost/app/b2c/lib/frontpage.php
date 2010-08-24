<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_frontpage extends site_controller{
    //todo
    function __construct(&$app){
        parent::__construct($app);     
    } 

    function verify_member(){
        kernel::single('base_session')->start();
        if($this->app->member_id = $_SESSION['account'][pam_account::get_account_type($this->app->app_id)]){
            if(!isset($_COOKIE['UNAME'])){
                $this->bind_member($this->app->member_id);
            }
            return true;
        }
        else{
            $this->redirect(array('app'=>'b2c', 'ctl'=>'site_passport', 'act'=>'error'));
        }
        
    }
    
    function bind_member($member_id){
        $obj_member = &$this->app->model('members');
        $data = $obj_member->dump($member_id,'*',array(':account@pam'=>array('*')));
        $secstr = $obj_member->gen_secret_str($data['member_id']);
        $this->cookie_path = '/';
        $this->set_cookie('MEMBER',$secstr,NULL);
        $this->set_cookie('loginName',$data['pam_account']['login_name'],NULL);
        $this->set_cookie('UNAME',$data['pam_account']['login_name'],NULL);
        $this->set_cookie('MLV',$data['member_lv']['member_group_id'],NULL);
        $this->set_cookie('CUR',$data['currency'],NULL);
        $this->set_cookie('LANG',$data['lang'],NULL);
        $this->set_cookie('S[MEMBER]',$member_id,NULL);
    }
    
    public function _check_verify_member($member_id=0)
    {
        if (isset($member_id) && $member_id)
        {
            $arr_member = $this->get_current_member();
            if ($member_id != $arr_member['member_id'])
            {
                $this->begin();
                $this->end(false,  __('订单无效！'), $this->gen_url(array('app'=>'site','ctl'=>'default','act'=>'index')));
            }
            else
            {
                return true;
            }
        }
        
        return false;
    }
    
    public function get_current_member()
    {
        
        if($this->member) return $this->member;
        
        kernel::single('base_session')->start();
        $this->app->member_id = $_SESSION['account'][pam_account::get_account_type($this->app->app_id)];
        
         #获取会员基本信息
        $obj_member = &$this->app->model('members');
        $member_sdf = $obj_member->dump($this->app->member_id,"*",array(':account@pam'=>array('*'))); 
        if( !empty($member_sdf) ) {
            $this->member['member_id'] = $member_sdf['pam_account']['account_id'];
            $this->member['uname'] =  $member_sdf['pam_account']['login_name'];
            $this->member['name'] = $member_sdf['contact']['name'];
            $this->member['sex'] =  $member_sdf['profile']['gender'];
            $this->member['point'] = $member_sdf['score']['total'];
            $this->member['email'] = $member_sdf['contact']['email'];
            $this->member['member_lv'] = $member_sdf['member_lv']['member_group_id'];
            #获取会员等级
            $obj_mem_lv = &$this->app->model('member_lv');
            $levels = $obj_mem_lv->dump($member_sdf['member_lv']['member_group_id']);
            if($levels['disabled']=='false'){
                $this->member['levelname'] = $levels['name'];
            }
        }
        kernel::single('base_session')->close(false);
        return $this->member;
    }
    
    function set_cookie($name,$value,$expire=false,$path=null){
        if(!$this->cookie_path){
            $this->cookie_path = substr(PHP_SELF, 0, strrpos(PHP_SELF, '/')).'/';
            $this->cookie_life =  $this->app->getConf('system.cookie.life');
        }
        $this->cookie_life = $this->cookie_life > 0 ? $this->cookie_life : 315360000;
        $expire = $expire === false ? time()+$this->cookie_life : $expire;
        setcookie($name,$value,$expire,$this->cookie_path);
        $_COOKIE[$name] = $value;
    }
    
    function check_login(){
        if($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]){
            return true;
        }
        else{
            return false;
        }
    }

    function setSeo($app,$act,$args=null){
        $seo = kernel::single('site_seo_base')->get_seo_conf($app,$act,$args);
        $this->title = $seo['seo_title'];
        $this->keywords = $seo['seo_keywords'];
        $this->content = $seo['seo_content'];
        $this->nofollow = $seo['seo_nofollow'];
        $this->noindex = $seo['seo_noindex'];
    }//End Function


    
    
}
