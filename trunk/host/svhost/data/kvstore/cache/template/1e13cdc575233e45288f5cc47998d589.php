<?php exit(); ?>a:2:{s:5:"value";s:5295:"<?php if( $this->_vars['mini_passport'] ){ ?><div class="mini-dialog-close close">X</div><?php } ?>
<table width="100%">
	<tr>
		<td>
<form method="post" action='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_passport','act' => 'create','args01' => $this->_vars['next_url'])); ?>' class='signupform'>
    <div class="RegisterWrap">
      <h4>用户注册</h4>
      <div class="intro"><div class="customMessages"><!--register_message--></div></div>
      <div class="form">
        <input name="forward" type="hidden" value="<?php echo $this->_vars['options']['url']; ?>">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <th><i>*</i>用户名：</th>
            <td><input type="text" class="inputstyle _x_ipt" name="pam_account[login_name]" vtype="required" required="true" id="reg_user" maxlength="50" onchange="nameCheck(this)"><span></span></td>
          </tr>
          <tr>
            <th><i>*</i>密码：</th>
            <td><?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "pam_account[login_password]",'type' => "password",'required' => "true",'id' => "reg_passwd"));?></td>
          </tr>
          <tr>
            <th><i>*</i>确认密码：</th>
            <td><?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "pam_account[psw_confirm]",'type' => "password",'required' => "true",'id' => "reg_passwd_r"));?></td>
          </tr>
          <tr>
            <th><i>*</i>电子邮箱：</th>
            <td><input type="text" vtype="email" required="true" name="contact[email]" id="reg_email" class="inputstyle _x_ipt" />
           </td>
          </tr>
          <?php if( $this->app->getConf('site.register_valide') == 'true' ){ ?>
          <tr>
            <th><i>*</i>验证码：</th>
            <td><?php echo $this->ui()->input(array('type' => "number",'required' => "true",'size' => "4",'maxlength' => "4",'name' => "signupverifycode",'id' => "iptsingup"));?>
                <span class='verifyCode' style='display:none;'><img src="#" codesrc='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_passport",'act' => "verifyCode",'arg0' => "s")); ?>' border="1" /><a href="javascript:void(0)">&nbsp;看不清楚?换个图片</a>
               </span>
             </td>
          </tr>
          <?php } ?>
          <tr>
            <th></th>
            <td><label for="license" class="nof" style="width:auto; text-align:left; font-weight:normal;">
          <input type="checkbox" id="license" name="license" value="agree" checked="checked"/>
          我已阅读并同意 <a href="<?php echo kernel::router()->gen_url(array('app' => 'content','ctl' => site_article,'act' => index,'arg0' => 16)); ?>" id="terms_error" class="lnk" target='_blank'><span class="FormText" id="terms_error_sym">会员注册协议</span></a>和<a href="<?php echo kernel::router()->gen_url(array('app' => 'content','ctl' => site_article,'act' => index,'arg0' => 17)); ?>" id="privacy_error" class="lnk" target='_blank'><span class="FormText" id="privacy_error_sym">隐私保护政策</span></a>。
          </label></td>
          </tr>          
          <tr>
            <th></th>
            <td>
        <input class="actbtn btn-register" type="submit" value="注册新用户" />
        <input type="hidden" name="forward" value="<?php echo $this->_vars['forward']; ?>">
              </td>
          </tr>
        </table>
        <?php echo $this->_vars['redirectInfo']; ?> </div>

</div>
</form>
</td>
<?php if( $this->_vars['mini_passport'] ){ ?>
<td class="row-span">
	<br /><br /><br /><br /><br />
	已有帐号？现在<a class="link" href="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_passport",'act' => "login",'mini_passport' => 1)); ?>">登陆</a>
</td>
<?php } ?>
</tr>
</table>
<?php if( $this->_vars['valideCode'] ){ ?>
<script>
   void function(formclz){
         var vcodeBox = $E('.'+formclz+' .verifyCode');
         var vcodeImg  =vcodeBox.getElement('img');
         var refreshVcodeBtn  = vcodeBox.getElement('a').addEvent('click',function(e){
              e.stop();
              vcodeImg.src = vcodeImg.get('codesrc')+'?'+$time();
         });
         $$('.'+formclz+' input').addEvent('focus',function(){
             if (this.form.retrieve('showvcode',false))return;
             vcodeBox.show();
             refreshVcodeBtn.fireEvent('click',{stop:$empty});
             this.form.store('showvcode',true);
         });
            $E('.'+formclz+'').addEvent('submit',function(e){
                 var str=$("reg_user").value.trim();
                var len = 0;  
                for (var i = 0; i < str.length; i++) {  
                    str.charCodeAt(i) > 255? len += 2:len ++;  
                }             
                if(len<3)return false;
         });
   }('signupform');
</script>
<?php } ?>
<script>
function nameCheck(input){
  
  new Request.HTML({update:$(input).getNext(),data:'name='+encodeURIComponent(input.value=input.value.trim())}).post('<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => namecheck)); ?>');
}
</script>
";s:6:"expire";i:0;}