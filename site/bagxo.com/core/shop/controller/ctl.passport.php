<?php
/**
 * ctl_passport 
 * 
 * @uses shopPage
 * @package 
 * @version $Id: ctl.passport.php 2035 2008-04-28 14:06:13Z alex $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com> 
 * @license Commercial
 */
class ctl_passport extends shopPage{
                                        
    var $noCache = true;

    function ctl_passport(&$system){
        parent::shopPage($system);
        $this->header .= "<meta name=\"robots\" content=\"noindex,noarchive,nofollow\" />\n";
        $this->pagedata['redirectInfo'] = '';
        if(isset($_POST['form'])){
            $form = get_magic_quotes_gpc()?stripcslashes($_POST['form']):$_POST['form'];
            $this->pagedata['redirectInfo'].='<input type="hidden" name="form" value="'.htmlspecialchars($form).'" />';
        }if(isset($_REQUEST['url']))
            $this->pagedata['redirectInfo'].='<input type="hidden" name="url" value="'.htmlspecialchars($_REQUEST['url']).'" />';
    }

    function verifyCode($type=''){
        $oVerifyCode = $this->system->loadModel('utility/verifyCode');
        $oVerifyCode->type = $type;
        $oVerifyCode->Output(60,16,true);
    }
    function namecheck(){
        $member=$this->system->loadModel('member/account');
        $name = trim($_POST['name']);
        if($member->check_uname($name,$message)){
                echo '<div class="valisuccess">&nbsp;<strong>'.$name.'</strong>&nbsp;is available</div>';
        }else{
                echo '<div class="valierror">&nbsp;<strong>'.$name.'</strong>&nbsp;'.$message.'</div>';
        }
    }
    function showValideCode($tp=''){
        switch ($tp){
            case "login":
                if($this->system->getConf('site.login_valide') == true || $this->system->getConf('site.login_valide') == 'true'){
                    $this->pagedata['valideCode'] = true;
                }
            break;
            case "signup":
                if($this->system->getConf('site.register_valide') == true || $this->system->getConf('site.register_valide') == 'true'){
                    $this->pagedata['valideCode'] = true;
                }
            break;
        }
        
    }
    function index($url){
        $this->pagedata['_MAIN_'] = 'passport/login.html';
        return $this->login($url);
        if(count($_POST['form'])>0 && !$this->member && !$this->system->getConf('security.guest.enabled',false)){
            $this->pagedata['mustMember'] = true;
        }
        
        $this->showValideCode();
        switch($url){
            case 'checkout':
                $options['url'] = $this->system->mkUrl("cart","checkout");
            break;
        }
        $this->pagedata['options'] = $options;
        $this->output();
    }

    function login($url,$error_msg=''){
        $passport = $this->system->loadModel('member/passport');
        if ($obj=$passport->function_judge('ServerClient')){
            $type = $obj->ServerClient('login');
        }
        $this->showValideCode('login');
        switch($url){
            case 'checkout':
                $options['url'] = $this->system->mkUrl("cart","checkout");
            break;
        }       
        $this->title = 'Sign';
        $this->pagedata['ref_url'] = str_replace(array('_',',','~'),array('+','/','='),base64_decode($url));
        $baseurl='http://'.$_SERVER['HTTP_HOST'].substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1);
       
       
        if(!strpos($baseurl.'?member',$_SERVER['HTTP_REFERER'])&&$url==""){
            $this->pagedata['ref_url'] = $_SERVER['HTTP_REFERER'];
        }
        if(($_SERVER['HTTP_REFERER']==$this->system->mkUrl("passport","login")&&$error_msg=="")||$_SERVER['HTTP_REFERER']==$this->system->mkUrl("passport","sendPSW")||$_SERVER['HTTP_REFERER'] ==$this->system->mkUrl("passport","signup")||$_SERVER['HTTP_REFERER']==""){
            $this->pagedata['ref_url'] = $this->system->mkUrl('member');
        }
        if($_SERVER['HTTP_REFERER']==$this->system->mkUrl("cart","checkout")&&$error_msg==""){
            $this->pagedata['ref_url'] = $this->system->mkUrl('cart','index');
        }
        if($error_msg!=''){
            $this->pagedata['err_msg'] =str_replace(array('_',',','~'),array('+','/','='),base64_decode($error_msg));
        }
        $this->pagedata['options'] = $options;
        $this->pagedata['forward'] = $_REQUEST['forward'];
        $this->pagedata['loginName'] = $_COOKIE['loginName'];
        $this->output();
    }

    function signup($url,$forward=''){
        $passport = $this->system->loadModel('member/passport');
        if ($obj=$passport->function_judge('ServerClient')){
            $type = $obj->ServerClient('signup');
        }
        $this->showValideCode('signup');
        switch($url){
            case 'checkout':
                $options['url'] = $this->system->mkUrl("cart","checkout");
            break;
        }
        $this->title = 'Register';
        $this->pagedata['options'] = $options;
        $this->pagedata['forward'] = $_REQUEST['forward'];
        $this->output();
    }

    function lost($url){
        $this->showValideCode();
        
        switch($url){
            case 'checkout':
                $options['url'] = $this->system->mkUrl("cart","checkout");
            break;
        }
        $this->pagedata['options'] = $options;
        $this->output();
    }

    function create(){ 
        $account = $this->system->loadModel('member/account');
        $passport=$this->system->loadModel('member/passport');
        if ($obj=$passport->function_judge('getPlugCookie')){
            if ($ck=$obj->getPlugCookie())
                $common = 1;
                if ($_REQUEST['plugUrl']){
                    $common=0;
                }
                elseif ($obj->getPlugCookie('Common')){
                    $common=0;
                }
            else
                $common = 1;
        }
        else
            $common = 1;
        if ($common){
            if($this->system->getConf('site.register_valide') == 'true' || $this->system->getConf('site.register_valide') == true){
                if($_COOKIE["S_RANDOM_CODE"]!=md5($_POST['signupverifycode'])){
                    $this->splash('failed','back','identifying code is not available, please modify');
                }
            }
            $passwdLen=strlen($_POST['passwd']);
            if($_POST['license']!='agree'){
                $this->splash('failed','back',__('We found that you do not fill in your details, you can at any time to perfect your details'));
            }elseif(!preg_match('/\S+@\S+/',$_POST['email'])){
                $this->splash('failed','back',__('The e-mail address entered is invalid. Please modify'));
            }elseif($passwdLen<4){
                $this->splash('failed','back',__('Sorry, your password must be between 4 and 20 characters long.'));
            }elseif($passwdLen>20){
                $this->splash('failed','back',__('Sorry, your password must be between 4 and 20 characters long.'));
            } 
            if($sCheck = $account->checkMember($_POST)){
    //            if($sCheck == 1)
                    $sError = __('The user name is not available');
    //            else
    //                $sError = __('该Email已经存在');
                $this->splash('failed','back',$sError);
                exit;
            }
            else{
                $pObj=$this->system->loadModel('member/passport');
                if ($obj=$pObj->function_judge('checkuserregister')){
                    $sRes = $obj->checkuserregister($_POST['uname'],$_POST['passwd'],$_POST['email'],$uid,$message);
                    if ($sRes){
                        if (!empty($message)){
                            $this->splash('failed','back',$message);
                            exit;
                        }
                        else{
                            if($_REQUEST['forward']) $url = $_REQUEST['forward'].DEFAULT_INDEX;
                            else $url = $this->system->request['base_url'].DEFAULT_INDEX;
                            if ($info = $account->createUserFromPluin($_POST,$message,$uid)){
                                $this->system->setCookie('MEMBER',$info['secstr'],null);
                                $this->system->setCookie('UNAME',$info['uname'],null);
                                $this->system->setCookie('MLV',$info['member_lv_id'],null);
                                $GLOBALS['runtime']['member_lv'] = $info['member_lv_id'];
                                $this->system->setCookie('CUR',$info['cur'],null);
                                $this->system->setCookie('LANG',$info['lang'],null);
                                $oPassport = $this->system->loadModel('member/passport');
                                $loginfo = $oPassport->login($info['member_id'],$url);
                                $this->header .=$loginfo;
                                if ($_POST['regType']=='buy'){
                                    $oCart = $this->system->loadModel('trading/cart');
                                    $cartCookie = $oCart->getCart();
                                    $oCart->checkMember($info);
                                    $oCart->save('all', $cartCookie);
                                    $this->system->setcookie($oCart->cookiesName,'');
                                    $this->splash('success',$this->system->mkUrl('cart','checkout'),__('Congratulations，registration succeeded'),'',1);
                                }
                            }
                            else
                                $this->splash('failed','back',$message);
                        }
                    }
                }
                else{
                    if($_REQUEST['forward']) $url = $_REQUEST['forward'].DEFAULT_INDEX;
                    else $url = $this->system->request['base_url'].DEFAULT_INDEX;
                    if($info = $account->create($_POST,$message)){
                        $this->system->setCookie('MEMBER',$info['secstr'],null);
                        $this->system->setCookie('UNAME',$info['uname'],null);
                        $this->system->setCookie('MLV',$info['member_lv_id'],null);
                        $GLOBALS['runtime']['member_lv'] = $info['member_lv_id'];
                        $this->system->setCookie('CUR',$info['cur'],null);
                        $this->system->setCookie('LANG',$info['lang'],null);

                        
                        if($_POST['regType'] == 'buy'){
                            $oCart = $this->system->loadModel('trading/cart');
                            $cartCookie = $oCart->getCart();
                            $oCart->checkMember($info);
                            $oCart->save('all', $cartCookie);
                            $this->system->setcookie($oCart->cookiesName,'');
                            $this->splash('success',$this->system->mkUrl('cart','checkout'),__('Congratulations，registration succeeded'),'',1);
                        }
                        else{
                            $oPassport = $this->system->loadModel('member/passport');
                            if ($opbj = $oPassport->function_judge('setPlugCookie'))
                                $opbj->setPlugCookie(0);
                            if ($oPassport->forward)
                                $oPassport->regist($info['member_id'],$url);
                            else{
                                if ($_POST['forward']){
                                    $url='';
                                } 
                                $oPassport->regist($info['member_id'],$url);
                            }
                        }
                    }else{
                        $this->splash('failed','back',$message);
                    }
                }
            }
        }
        else{
            if ($obj=$passport->function_judge('setPlugCookie'))
                $obj->setPlugCookie(0);
        }
      $oMem = $this->system->loadModel('member/member');
      $mematt = $this->system->loadModel('member/memberattr');
      //提取用户注册项
      $filter['attr_show'] = 'true';
      $tmpdate =$mematt->getList('*',$filter,0,-1,$count,array('attr_order','asc'));
     
      for($i=0;$i<count($tmpdate);$i++){
         if($tmpdate[$i]['attr_type'] == 'select'||$tmpdate[$i]['attr_type'] == 'checkbox'){
            $tmpdate[$i]['attr_option'] = unserialize($tmpdate[$i]['attr_option']);
         } 
      }
      $this->pagedata['tree'] = $tmpdate;
      $this->pagedata['plugUrl'] = $_REQUEST['plugUrl'];
      $this->output();
        
    }

    function recover(){
        $member=$this->system->loadModel('member/member');
        $this->pagedata['data']=$member->getMemberByUser($_POST['login']);
        if(empty($this->pagedata['data']['member_id'])){
            $this->splash('failed','back','This username is not yet registered with BagXO.com. You may try typing in a different username,registering a new account, or contacting us.');
        }
        $this->output();
    }

    function sendPSW(){
        $this->begin($this->system->mkUrl('passport','lost'));
        $member=$this->system->loadModel('member/member');
        $data=$member->getMemberByUser($_POST['uname']);
        if(($data['pw_answer']!=$_POST['pw_answer']) || ($data['email']!=$_POST['email'])){
            trigger_error('There is no shopper with that email address or answer. please try again',E_USER_ERROR);
        }

        if( $data['member_id'] < 1 ){
            trigger_error('会员信息错误',E_USER_ERROR);
        }

        $messenger = $this->system->loadModel('system/messenger');
        $passwd = substr(md5(print_r(microtime(),true)),0,6);
        $member->update(array('password'=>md5($passwd)),array('member_id'=>intval($data['member_id'])));
        $data['passwd'] = $passwd;
        $messenger->actionSend('lostPw',$data,$data['member_id']);

        $this->end(true,'Password E-Mailed',$this->system->mkUrl('passport','index'));
    }

    /*取消跳转后此方法已失效*/
    function error(){
        $url = $this->system->request['url_prefix'];
        $this->pagedata['nexturl'] = $url;
        $this->header.="\n<meta http-equiv=\"refresh\" content=\"3; url={$url}\">\n";
        $this->output();
    }

    function logout(){    //退出
        $passport = $this->system->loadModel('member/passport');
        if ($obj=$passport->function_judge('ServerClient')){
           $obj->ServerClient('logout');
        }
        $this->_verifyMember(false);
         $this->system->setCookie('MEMBER', '', time()-1000); 
         $this->system->setCookie('MLV', '', time()-1000); 
         $GLOBALS['runtime']['member_lv'] = -1;
         $this->system->setCookie('CART', '', time()-1000); 
         $this->system->setCookie('CART_COUNT', '', time()-1000); 
         $this->system->setCookie('UNAME', '', time()-1000); 
        if($_REQUEST['forward']) $url = $_REQUEST['forward'].DEFAULT_INDEX;
        else $url = $this->system->request['base_url'].DEFAULT_INDEX;
        if ($_REQUEST['forward'])
            $url='';
        $oPassport = $this->system->loadModel('member/passport');
        $logoutinfo = $oPassport->logout($this->member['member_id'],$url);
        if ($logoutinfo){
            $this->header.=$logoutinfo;
            $this->splash('success',$this->system->base_url().DEFAULT_INDEX,'Successful sign out, will return to home page...');
        }
        else{
            $baseurl='http://'.$_SERVER['HTTP_HOST'].substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1);

            if(!strpos($baseurl.'?member',$_SERVER['HTTP_REFERER'])){
                $this->nowredirect('success',$_SERVER['HTTP_REFERER'],'');
                exit;
            }
            $this->system->location($this->system->mkUrl('passport','login',array(base64_encode(str_replace(array('+','/','='),array('_',',','~'),$_SERVER['HTTP_REFERER'])))));
        }
    }

    function verify(){
        
        if($this->system->getConf('site.login_valide') == 'true' || $this->system->getConf('site.login_valide') == true){
            if($_COOKIE["L_RANDOM_CODE"]!=md5($_POST['loginverifycode'])){
                $this->nowredirect('failed',base64_encode(str_replace(array('+','/','='),array('_',',','~'),$_POST['ref_url'])),'identifying code, please modify');
            }
        }
        $memberObj = $this->system->loadModel('member/account');
        $this->system->setCookie('loginName',$_POST['login']);
        $pObj=$this->system->loadModel('member/passport');
        
        if ($obj=$pObj->function_judge('checkusername')){
            $uinfo=$obj->checkusername($_POST['login'],$_POST['passwd'],$_POST['forward']);
           
            if ((is_array($uinfo)&&intval($uinfo[0])>0)||$info = $memberObj->verifyLogin($_POST['login'],$_POST['passwd'],$message)){
                $this->checkusername($_POST['login'],$_POST['passwd'],$_POST['forward'],$uinfo[0],$uinfo[3]);
            }
            else{
                $this->nowredirect('failed',base64_encode(str_replace(array('+','/','='),array('_',',','~'),$_POST['ref_url'])),'The username or password is invalid/ please modify');
            }
        }
        $this->title = '';
        if($info = $memberObj->verifyLogin($_POST['login'],$_POST['passwd'],$message)){
            if($_REQUEST['forward']) $url = $_REQUEST['forward'].DEFAULT_INDEX;
            else $url = $_POST['ref_url'];
            $this->system->setCookie('MEMBER',$info['secstr'],null);
            $this->system->setCookie('UNAME',$info['uname'],null);
            $this->system->setCookie('MLV',$info['member_lv_id'],null);
            $GLOBALS['runtime']['member_lv'] = $info['member_lv_id'];
            $this->system->setCookie('CUR',$info['cur'],null);
            $this->system->setCookie('LANG',$info['lang'],null);
            $oCart = $this->system->loadModel('trading/cart');
            $cartCookie = $oCart->getCart();
            $oPassport = $this->system->loadModel('member/passport');
            if($_COOKIE['CART_COUNT']){    //Cookie 购物车数量
                //cookie中有商品
                $oCart->checkMember($info);
                $cartDb = $oCart->getCart();
                if($_COOKIE['CART_COUNT']){  
                    if($this->system->mkUrl('cart','checkout')==$url){
                        $url = $this->system->mkUrl('cart','index');    
                    }//DB 购物车数量
                    $this->nowredirect('success',$this->system->mkUrl('cart','merge',array(1)).'?forward='.urlencode($url),'');
                }else{
                    $oCart->save('all', $cartCookie);
                    $this->system->setcookie($oCart->cookiesName,'');
                    if ($oPassport->forward){
                        $loginfo = $oPassport->login($info['member_id'],$url);
                        $this->header.=$loginfo;
                    }
                    else{
                        if ($_POST['forward']){
                            $loginfo = $oPassport->login($info['member_id'],$url);
                            $this->header.=$loginfo;
                        }
                    }
                    $this->nowredirect('success',$url,'');
                }
            }else{

                $oCart->checkMember($info);
                $cartDb = $oCart->getCart();
                //$loginfo = $oPassport->login($info['member_id'],$url);
                //$this->header.=$loginfo;
                if ($oPassport->forward){
                        $loginfo = $oPassport->login($info['member_id'],$url);
                        $this->header.=$loginfo;
                }
                else{
                    if ($_POST['forward']){
                        $url='';
                    }
                    $loginfo = $oPassport->login($info['member_id'],$url);
                    $this->header.=$loginfo;                    
                }
                if(!$url){
                    $url = $this->system->mkUrl('index');
                }
                $this->nowredirect('success',$url,'');
            }
        }else{            
            $this->nowredirect('failed',base64_encode(str_replace(array('+','/','='),array('_',',','~'),$_POST['ref_url'])),'The username or password is invalid, please modify');
        }
    }


    function ssoSignin(){
        $oPassport = $this->system->loadModel('member/passport');
        return $oPassport->ssoSignin();
    }

    function setLoginCookie($info){
        if($info){
            $option = time()+60*60*24*30;//null;
        }else{
            $option = mktime() - 1000;
        }
        $this->system->setCookie('MEMBER',$info['secstr'],$option);
        $this->system->setCookie('UNAME',$info['uname'],$option);
        $this->system->setCookie('MLV',$info['member_lv_id'],$option);
        $GLOBALS['runtime']['member_lv'] = $info['member_lv_id'];
        $this->system->setCookie('CUR',$info['cur'],$option);
        $this->system->setCookie('LANG',$info['lang'],$option);
        return true;
    }

    function callback($type){
        $oPassport = $this->system->loadModel('member/passport');
        $info = $oPassport->ssoSignin($type);
        $this->_succ = true;
        switch($_GET['action']){
            case 'login':
                if(!$info){
                    header("location: ".$_GET['forward']);
                    exit;
                }
                $this->setLoginCookie($info);
                header("location: ".$_GET['forward']);
                exit;
            case 'logout':
                $this->setLoginCookie($info);
                header("location: ".$_GET['forward']);
                exit;
        }
    }
    
    function getMsg(){
        return array(
            'login'=>array('succ'=>'Successful sign in','fail'=>'Sign in failed'),
            'logout'=>array('succ'=>'Successful sign out','fail'=>'Sign out failed')
        );
    }
    
    function checkusername($uname='',$passwd='',$forward='',$uid='',$email=''){ 
         $account = $this->system->loadModel("member/account");
         $data=array(
            "uname"=>$uname,
            "passwd"=>$passwd,
            "email"=>$email
         );
         $oCart = $this->system->loadModel('trading/cart');
         $cartCookie = $oCart->getCart();
         if($_REQUEST['forward']) $url = $_REQUEST['forward'].DEFAULT_INDEX;
         else $url = $this->system->request['base_url'].DEFAULT_INDEX;
         $row =  $account->getMemberPluginUser($uname);
         if (!$row){
            $row=$account->createUserFromPluin($data,$message,$uid);
         }
         $oMsg = $this->system->loadModel('resources/msgbox');
         $row['unreadmsg'] = $oMsg->getNewMessageNum($row['member_id']);
         $this->system->setCookie('MEMBER',$row['secstr'],null);
         $this->system->setCookie('UNAME',$row['uname'],null);
         $this->system->setCookie('MLV',$row['member_lv_id'],null);
         $GLOBALS['runtime']['member_lv'] = $row['member_lv_id'];
         $this->system->setCookie('CUR',$row['cur'],null);
         $this->system->setCookie('LANG',$row['lang'],null);
         $oPassport = $this->system->loadModel('member/passport');
         $loginfo = $oPassport->login($row['member_id'],$url);
         $this->header.=$loginfo;
         $oCart->checkMember($row);
         if ($_COOKIE['CART_COUNT']){
            $cartCookie = $oCart->getCart();
            $this->splash('success',$this->system->mkUrl('cart','merge',array(1)).'?forward='.urlencode($url),
                    __('<h1>Login successful. We found that your shopping cart also save the last purchase of goods. </h1>
                    <div style="border:1px solid #efefef;padding:6px; margin:5px;width:auto;line-height:22px; ">
                    1. <a class="lnk" href="'.$this->system->mkUrl('cart','merge',array(0)).'?forward='.urlencode($url).'">Empty the last purchase the goods, retaining this</a>
                    <br>
                    2. <a  class="lnk" href="'.$this->system->mkUrl('cart','merge',array(1)).'?forward='.urlencode($url).'">Will be the purchase of goods and the last combined</a>
                    <br>
                    3. <a  class="lnk" href="'.$this->system->mkUrl('cart','merge',array(2)).'?forward='.urlencode($url).'">To retain the last purchase of goods, empty this time</a></div>'),'',60);
         }else{
            $oCart->save('all', $cartCookie);
         }
         $this->splash('success',$url,__('Successful sign in'),'',1);
         exit;
         $this->_succ=true;
    }
}
?>
