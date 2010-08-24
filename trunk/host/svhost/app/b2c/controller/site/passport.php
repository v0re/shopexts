<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * ctl_passport
 *
 * @uses b2c_frontpage
 * @package
 * @version $Id: ctl.passport.php 2035 2008-04-28 14:06:13Z alex $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */
class b2c_ctl_site_passport extends b2c_frontpage{
    function __construct(&$app){
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        kernel::single('base_session')->start();
    } 

    function index(){
        
        if($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]){
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'));
             $this->splash('success',$url,__('您已经是登陆状态，不需要重新登陆'));
        }
        if($this->mini_login()) return ;
        if(!isset($_SESSION['next_page'])){
            $_SESSION['next_page'] = $_SERVER['HTTP_REFERER'];
        }
        if(strpos($_SESSION['next_page'],'passport')) unset($_SESSION['next_page']);
         
        $this->gen_login_form();
        $this->set_tmpl('passport');
        $this->pagedata['valideCode'] = app::get('b2c')->getConf('site.register_valide');
        $this->page('site/passport/index.html');
    }

   
   function gen_vcode(){
        $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key('MEMBERVCODE');
        $vcode->display();
        
    }
    
    function login($mini=0){
        if(!isset($_SESSION['next_page'])){
            $_SESSION['next_page'] = $_SERVER['HTTP_REFERER'];
        }
        
        if(strpos($_SESSION['next_page'],'passport')) unset($_SESSION['next_page']);
        if($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]){
            $this->bind_member($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]);
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'));
            if($_GET['mini_passport']==1 || $mini){
                $this->_response->set_http_response_code(404);return;
            }else{
                $this->splash('success',$url,__('您已经是登陆状态，不需要重新登陆'));
            }
        } 
        $falg = false;
        if($_GET['mini_passport']==1 || $mini)  {
            $falg = true;
            $this->pagedata['mini_passport'] = 1;
        }
        
        $this->gen_login_form($_GET['mini_passport']);
        $this->set_tmpl('passport');
        if(!$mini)
            $this->page('site/passport/login.html', $falg);
    }
    
    
    private function mini_login() {
        if($_GET['mini_passport']==1) {
            $this->gen_login_form_mini();
            $this->pagedata["mini_passport"] = 1;
            //$this->page('site/passport/index.html', true);
            return true;
        }
        return false;
    }
    
    private function gen_login_form_mini(){
        
        $auth = pam_auth::instance(pam_account::get_account_type($this->app->app_id));
        #设置回调函数地址
        $auth->set_redirect_url(base64_encode($this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'post_login'))));
        foreach(kernel::servicelist('passport') as $k=>$passport){
            if($auth->is_module_valid($k)){
                $this->pagedata['passports'][] = array(
                        'name'=>$auth->get_name($k)?$auth->get_name($k):$passport->get_name(),
                        'html'=>$passport->get_login_form($auth,$singup_url),
                    );
            }
        }        
    }
    
    
    private function gen_login_form(){
        if($_SESSION['next_page']){
            $url = $_SESSION['next_page'];
        }
        else{
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'));
        }
        
        #var_dump($url);echo "<hr/>";
        unset($_SESSION['next_page']);
        $auth = pam_auth::instance(pam_account::get_account_type($this->app->app_id));
        $pagedata['singup_url'] = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'signup'));
        $pagedata['lost_url'] = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'lost'));
        $pagedata['loginName'] = $_COOKIE['loginName'];
        #设置回调函数地址
        $auth->set_redirect_url(base64_encode($this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'post_login','arg'=>base64_encode($url)))));
        #print_r(kernel::servicelist('passport'));exit;
        foreach(kernel::servicelist('passport') as $k=>$passport){
            if($auth->is_module_valid($k)){
                $this->pagedata['passports'][] = array(
                        'name'=>$auth->get_name($k)?$auth->get_name($k):$passport->get_name(),
                        'html'=>$passport->get_login_form($auth, 'b2c', 'site/passport/member-login.html', $pagedata),
                    );
            }
        } 
    }
    
    
    function post_login_mini(){    
        $member_id = $_SESSION['account'][pam_account::get_account_type($this->app->app_id)];
        if($member_id){
            $this->bind_member($member_id);
            $this->splash('success',$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index')),__('登陆成功'));
        }
        else{
            $this->splash('failed',$this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index')),__('登陆失败'));
        }
    }
    

    
    function post_login($url){ 
        $url = base64_decode($url);
        $mini = $_GET['mini'];
        $member_id = $_SESSION['account'][pam_account::get_account_type($this->app->app_id)];
        unset($_SESSION['next_page']);
        //exit($url);
        if($member_id){
            $obj_mem = $this->app->model('members');
            $member_point = $this->app->model('member_point');
            $sdf = $obj_mem->dump($member_id);
            if($this->app->getConf('site.level_switch')==1){
                $sdf['member_lv']['member_group_id'] = $obj_mem->member_lv_chk($sdf['experience']);
            }
            if($this->app->getConf('site.level_switch')==0 && $this->app->getConf('site.level_point') == 1){
                $sdf['member_lv']['member_group_id'] = $member_point->member_lv_chk($sdf['score']['total']);
            }
            $obj_mem->save($sdf);
            $this->bind_member($member_id);
            if($mini == 1){
                echo json_encode(array('status'=>'plugin_passport', 'url'=>$url));return;
            }
            else{
                $this->splash('success',$url,__('登陆成功'));
            }
        }
        else{
                $msg = $_SESSION['error'];
                unset($_SESSION['error']);
                if($mini == 1){
                    echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>$msg));return;
                }
                else{
                    $_SESSION['next_page'] = $url;
                    $this->splash('failed',$this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index')),__($msg));
                }
        }
    }

    function namecheck(){
        $obj_member=&$this->app->model('members');
        $name = trim($_POST['name']);
        if(strlen($name) < 3 ){
               echo '<span class="fontcolorRed">&nbsp;'.__('长度不能小于3').'</span>';
               exit;
        }
        if(!preg_match('/^([@\.]|[^\x00-\x2f^\x3a-\x40]){2,20}$/i', $name)){
            echo '<span class="fontcolorRed">&nbsp;'.__('用户名包含非法字符!').'</span>';exit;
        }
        else{    
            if(!$obj_member->is_exists($name)){
                echo '<span class="fontcolorGreen">&nbsp;'.__('可以使用').'</span>';exit;
            }else{
                echo '<span class="fontcolorRed">&nbsp;'.__('已经被占用').'</span>';exit;
            }   
        }     
    }
    
    function verifyCode(){
       $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key('LOGINVCODE');
        $vcode->display();
    }
    function verify(){
        $this->begin($this->gen_url('passport','login'));
        $member_model = &$this->app->model('members');
        $verifyCode = app::get('b2c')->getConf('site.register_valide');
        if($verifyCode == "true"){
        if(!base_vcode::verify('LOGINVCODE',intval($_POST['loginverifycode']))){
               $this->splash('failed',$this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index')),__('验证码错误'));
      
            }
        }
        $rows=app::get('pam')->model('account')->getList('account_id',array('account_type'=>'member','disabled' => 'false','login_name'=>$_POST['login'],'login_password'=>md5($_POST['passwd'])));
        if($rows){
            $_SESSION['account'][pam_account::get_account_type($this->app->app_id)] = $rows[0]['account_id'];
            $this->bind_member($rows[0]['account_id']);
            $this->end(true,__('登录成功，进入会员中心'),$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'))); 
        }else{
            $_SESSION['login_msg']=__('用户名或密码错误');
            $this->end(false,$_SESSION['login_msg'],$this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'login')));
        }
    }

    function __restore(){
        if($_SESSION['login_info']['post']){
            call_user_func_array(array(&$this,'redirect'),$_SESSION['login_info']['action']);
        }
    }

    function signup($url=null){
        $falg = false;
        if($_GET['mini_passport']==1)  {
            $falg = true;
            $this->pagedata['mini_passport'] = 1;
        }
        
        $this->pagedata['next_url'] = $url;
        $this->set_tmpl('passport');
        $this->pagedata['valideCode'] = app::get('b2c')->getConf('site.register_valide');
        $this->page("site/passport/signup.html", $falg);
        
    }

    function create($next_url=null){
        $mini = $_GET['mini'];
        $back_url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'signup'));
        if(!preg_match('/^([@\.]|[^\x00-\x2f^\x3a-\x40]){2,20}$/i', $_POST['pam_account']['login_name'])){
         if($mini!=1){
              $this->splash('failed',$back_url,__('用户名包含非法字符'));
         }
         else{
             echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'用户名包含非法字符'));return;
         }
        }
        
        $next_url = base64_decode($next_url);  
        $member_model = &$this->app->model('members'); 
        $valideCode = app::get('b2c')->getConf('site.register_valide');   
        if($valideCode =='true'){
               if(!base_vcode::verify('LOGINVCODE',intval($_POST['signupverifycode']))){
            if($mini!= 1){
                 $this->splash('failed',$back_url,__('验证码填写错误'));
             }
             else{
                 echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'验证码填写错误'));return;
            }
        }
        }
        if($_POST['license'] != 'agree'){
            if($mini!= 1){
            $this->splash('failed',$back_url,__('同意注册条款后才能注册'));
        }
        else{
            echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'同意注册条款后才能注册'));return;
        }
        }        

        $unamelen = strlen($_POST['pam_account']['login_name']);
        if($unamelen < 3){
            if($mini!= 1){
            $this->splash('failed',$back_url,__('长度不能小于3'));
        }
        else{
            echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'长度不能小于3'));return;
        }
        }
        if($member_model->is_exists($_POST['pam_account']['login_name'])){
            if($mini!= 1){
            $this->splash('failed',$back_url,__('该用户名已经存在'));
            }
            else{
            echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'该用户名已经存在'));return;
        }
        }
       if(!preg_match('/\S+@\S+/',$_POST['contact']['email'])){
          if($mini!= 1){
            $this->splash('failed',$back_url,__('邮件格式不正确'));
        }
        else{
            echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'邮件格式不正确'));return;
        }
        }
        $passwdlen = strlen($_POST['pam_account']['login_password']);
        if($passwdlen<4){
            if($mini!= 1){
           $this->splash('failed',$back_url,__('密码长度不能小于4'));
       }
            else{
            echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'密码长度不能小于4'));return;
        }
        }
        if($passwdLen>20){
           if($mini!= 1){
            $this->splash('failed',$back_url,__('密码长度不能大于20'));   
            }
            else{
            echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'密码长度不能大于20'));return;
        }
        }        
        if($_POST['pam_account']['login_password'] != $_POST['pam_account']['psw_confirm']){
            if($mini!= 1){
            $this->splash('failed',$back_url,__('输入的密码不一致'));
            }
            else{
           echo json_encode(array('status'=>'failed', 'url'=>'back', 'msg'=>'输入的密码不一致'));return;
            }
        }      
        
        $lv_model = &$this->app->model('member_lv');
        $_POST['member_lv']['member_group_id'] = $lv_model->get_default_lv();
        $arrDefCurrency = app::get('ectools')->model('currency')->getDefault();
        $_POST['currency'] = $arrDefCurrency['cur_code'];
        $_POST['pam_account']['login_password'] = md5(trim($_POST['pam_account']['login_password']));
        $_POST['pam_account']['login_name'] = strtolower($_POST['pam_account']['login_name']);
        $_POST['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
        $_POST['pam_account']['createtime'] = time();
        $_POST['reg_ip'] = base_request::get_remote_addr(); 
        $_POST['regtime'] = time();
        $_POST['contact']['email'] = htmlspecialchars($_POST['contact']['email']);
        if($member_model->save($_POST)){
            $member_id = $_POST['member_id'];
            $_SESSION['account'][pam_account::get_account_type($this->app->app_id)] = $member_id;
            $this->bind_member($member_id);
            foreach(kernel::servicelist('b2c_save_post_om') as $object) {
                $object->set_arr($member_id, 'member');
                $refer_url = $object->get_arr($member_id, 'member');
            }
            if($next_url){
                header("Location: ".$next_url);
            }
            else{
                $data['member_id'] = $member_id;
                $data['uname'] = $_POST['pam_account']['login_name'];
                $data['passwd'] = $_POST['pam_account']['psw_confirm'];
                $data['email'] = $_POST['contact']['email'];
                $data['refer_url'] = $refer_url ? $refer_url : '';
                $data['is_frontend'] = true;
                $obj_account=&$this->app->model('member_account');
                $obj_account->fireEvent('register',$data,$member_id);
             if($mini!= 1){
            $this->splash('success',$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'attr_page')),__('注册成功')); 
            }
            else{ 
            echo json_encode(array('status'=>'plugin_passport', 'url'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'attr_page'))));return;
            }
        }
            #$this->splash(null,$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'attr_page')),__('注册成功'));             
        }
    
        $this->splash('failed',$back_url,__('注册失败'));        
    }
    /*
    private function bind_member($member_id){
        $obj_member = &$this->app->model('members');
        $data = $obj_member->dump($member_id,'*',array(':account@pam'=>array('*')));
        $secstr = $obj_member->gen_secret_str($data['member_id']);
        $this->cookie_path = '/';
        $this->set_cookie('MEMBER',$secstr,NULL);
        $this->set_cookie('UNAME',$data['pam_account']['login_name'],NULL);
        $this->set_cookie('MLV',$data['member_lv']['member_group_id'],NULL);
        $this->set_cookie('CUR',$data['currency'],NULL);
        $this->set_cookie('LANG',$data['lang'],NULL);
    }
    */



    /*----------- 次要流程 ---------------*/

    function recover(){
        $obj_member = &$this->app->model('members');
        $rows = app::get('pam')->model('account')->getList('*',array('account_type'=>'member','login_name'=>$_POST['login']));
        $member_id = $rows[0]['account_id'];
        $this->pagedata['data']=$obj_member->dump($member_id);
        $this->pagedata['data']['login_name'] = $rows[0]['login_name'];
       # print_r($this->pagedata);exit;
        if(empty($member_id)){
            $this->splash('failed','back',__('该用户不存在！'));
        }
        if($this->pagedata['data']['disabled'] == "true"){
            $this->splash('failed','back',__('该用户已经放入回收站！'));
        }
        $this->set_tmpl('passport');
        $this->page("site/passport/recover.html");
    }

    function sendPSW(){
        $this->begin($this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index')));
        $rows = app::get('pam')->model('account')->getList('*',array('account_type'=>'member','login_name'=>$_POST['uname']));
        $member_id = $rows[0]['account_id'];
        $obj_member = &$this->app->model('members');
        $data = $obj_member->dump($member_id);
        if(($data['account']['pw_answer']!=$_POST['pw_answer']) || ($data['contact']['email']!=$_POST['email'])){
            $this->end(false,__('问题回答错误或当前账户的邮箱填写错误'),$this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index')));
        }
        if( $data['pam_account']['account_id'] < 1 ){
            $this->end(false,__('会员信息错误'),$this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index')));
        }
        $objRepass = $this->app->model('member_pwdlog');
        $secret = $objRepass->generate($data['pam_account']['account_id']);
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index'));
        $sdf = app::get('pam')->model('account')->dump($member_id);
        $new_password = $this->randomkeys(6);
        $sdf['login_password'] = md5($new_password);
        if(app::get('pam')->model('account')->save($sdf)){
          if($this->send_email($_POST['uname'],$data['contact']['email'],$new_password)){
            $this->end(true,__('密码变更邮件已经发送到'.$data['contact']['email'].'，请注意查收'),$url);
           }
          else{
          $this->end(fasle,__('发送失败，请与商家联系'),$url);
          }
        }
        else{
            $this->end(fasle,__('发送失败，请与商家联系'),$url);
        }
    }
    
    ####随机取6位字符数
    function randomkeys($length){
        
      $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';    //字符池
      for($i=0;$i<$length;$i++){
      $key .= $pattern{mt_rand(0,35)};    //生成php随机数
      }
      return $key;
      }
     
    function send_email($login_name,$user_email,$new_password){
        $obj_emailconf = kernel::single('desktop_email_emailconf');
        $aTmp = $obj_emailconf->get_emailConfig(); 
        $acceptor = $user_email;     //收件人邮箱
        $subject = __("来自[").$this->app->getConf('system.shopname').__("]网店的密码修改邮件");
        $body = __("尊敬的").$login_name.__(":您在").$this->app->getConf('system.shopname').__("网店的密码已经帮您找回，新的密码是:").$new_password.__(',请您立即登陆会员中心进行密码修改');
        $email = kernel::single('desktop_email_email');
        if ($email->ready($aTmp)){
        $res = $email->send($acceptor,$subject,$body,$aTmp);
        if ($res) {
            return true;            
        }
        else{
            return fasle;
        }
      }
      else{
          return false;
      }    
   
    }
    function lost(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'));
        if($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]){
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'));
             $this->splash('failed',$url,__('请先退出'));
        } 
        $this->set_tmpl('passport');
        $this->page("site/passport/lost.html");
    }
    
    function repass($secret){
        $this->begin($this->gen_url('passport','repass'));
        $objRepass = $this->app->model('member_pwdlog');
        if($objRepass->isValiad($secret)){
            $this->pagedata['secret'] = $secret;
           $this->set_tmpl('passport');
        $this->page("site/passport/repass.html");
        }else{
             $this->end(true,__('参数不正确，请重新申请密码取回'),$this->gen_url('passport','lost'));  
        }
    }
    
    function dorepass(){
        $this->begin($this->gen_url('passport','repass',array($_POST['secret'])));
        $passwdLen = strlen($_POST['password']);
       if($_POST['password'] != $_POST['repassword']){
           trigger_error(__('两次密码输入不一致，请重新输入新密码'),E_USER_ERROR);
        }elseif($passwdLen < 4){
            trigger_error(__('密码长度不能小于4'),E_USER_ERROR);
        }elseif($passwdLen > 20){
            trigger_error(__('密码长度不能大于20'),E_USER_ERROR);
        }
        
        $objRepass = $this->app->model('member_repass');
        if($objRepass->rePass($_POST)){
            $this->end(true,__('密码修改成功，请用新密码登录'),$this->gen_url('passport','index'));         
        }else{
             $this->end(false,__('密码修改失败，请重新申请密码取回'),$this->gen_url('passport','lost'));  
        }
    }
    
    function error(){
        $this->unset_member();
        $back_url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'signup'));
        $this->splash('failed',$back_url,__('本页需要会员才能进入，您未登陆或者已经超时'));
    }


  function logout(){
        $this->begin();
        $this->unset_member();
        $this->end(true,__('您已经退出系统'),app::get('site')->base_url(1));
    }
    
    function unset_member(){

        unset($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]);
        //exit('adfs');
        $this->app->member_id = 0;
        $this->cookie_path = '/';
        $this->set_cookie('MEMBER',null,time()-3600);
        $this->set_cookie('UNAME','',time()-3600);
        $this->set_cookie('MLV','',time()-3600);
        $this->set_cookie('CUR','',time()-3600);
        $this->set_cookie('LANG','',time()-3600);
        $this->set_cookie('S[MEMBER]','',time()-3600);
        foreach(kernel::servicelist('member_logout') as $service){
            $service->logout();
        }
    }   
}
