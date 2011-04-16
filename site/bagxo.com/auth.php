<?php

function is_page_allow(){
    $str = $_SERVER['QUERY_STRING'];
    if(strstr($str,"passport-signup")){
        return false;
    }
    if(preg_match("/passport-.*-login/",$str)){
        $_COOKIE['sw_login_fail'] = true;
        return false;
    }
    if(strstr($str,"passport")){
        return true;
    }
    return false;
}

if( strstr($_SERVER['HTTP_REFERER'],"bagxo")){
    $sw_forward = $_SERVER['HTTP_REFERER'];
}else{
    $sw_forward = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

if(!$_COOKIE['S']['UNAME'] && !is_page_allow()  ){

?><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>The Best Designer Handbags, Fashion Luxury Handbags, Famous Handbags, Wholesale handbags</title>
</head>

<form class="loginform" action="./index.php?passport-verify.html" method="post">
    <div class="form">   <input type="hidden" value="" name="forward">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tbody><tr><td colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>
                <tr><td width="3%">&nbsp;</td><td width="97%"><div align="left" style="color: rgb(255, 102, 102); font-weight: bold;">Already a customer?</div></td></tr>
            </tbody></table></td></tr>
            <tr><th width="9%"><div align="left"><i>*</i>username：</div></th><td width="91%"><input tabindex="1" id="in_login" required="true" class="inputstyle _x_ipt" name="login" autocomplete="off"></td></tr>
            <tr><th><div align="left"><i>*</i>password：</div></th><td><input type="password" vtype="password" tabindex="2" id="in_passwd" required="true" class="inputstyle _x_ipt" name="passwd" autocomplete="off"></td></tr><tr><th></th><td><input type="image" src="./statics/btn-login.gif" tabindex="4" value="" class="memlogin_btn"><input type="hidden" value="" name="forward">            </td></tr><tr><th></th><td><a href="index.php?passport-lost.html" style="margin-left: 6px; color: rgb(237, 112, 126);">Forget the code？</a></td></tr></tbody></table></div><input type="hidden" value="./" name="ref_url">
            <input type=hidden name="forward" value="<?php echo $sw_forward; ?>">
        <div style="display:<?php echo $_COOKIE['sw_login_fail'] ? "true" : "none";  $_COOKIE['sw_login_fail']=false;?>"> The username or password is invalid, please modify<br/>Need more help? Contact vipBagXO@gmail.com</div>        
</form>

<?php   
 exit;
}

?>
