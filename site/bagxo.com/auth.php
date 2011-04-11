<?php

if(!$_COOKIE['S']['UNAME'] && !strstr($_SERVER['QUERY_STRING'],"passport")){

?>

<form class="loginform" action="./index.php?passport-verify.html" method="post">
    <div class="form">   <input type="hidden" value="" name="forward">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tbody><tr><td colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>
                <tr><td width="3%">&nbsp;</td><td width="97%"><div align="left" style="color: rgb(255, 102, 102); font-weight: bold;">Already a customer?</div></td></tr>
            </tbody></table></td></tr>
            <tr><th><div align="left"><i>*</i>username：</div></th><td><input tabindex="1" id="in_login" required="true" class="inputstyle _x_ipt" name="login" autocomplete="off"></td></tr>
            <tr><th><div align="left"><i>*</i>password：</div></th><td><input type="password" vtype="password" tabindex="2" id="in_passwd" required="true" class="inputstyle _x_ipt" name="passwd" autocomplete="off"></td></tr><tr><th></th><td><input type="image" src="./statics/btn-login.gif" tabindex="4" value="" class="memlogin_btn"><input type="hidden" value="" name="forward">            </td></tr><tr><th></th><td><a href="index.php?passport-lost.html" style="margin-left: 6px; color: rgb(237, 112, 126);">Forget the code？</a></td></tr></tbody></table></div><input type="hidden" value="./" name="ref_url"></form>

<?php   
 exit;
}

?>
