<?php exit(); ?>a:2:{s:5:"value";s:2480:"<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"site/passport/login.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
</td>
<td>
<input type='hidden' name='from_minipassport' value='1' >
  <div class="RegisterWrap">
      <div class="form">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <!--<td colspan="2"><h4>已注册用户，请登录</h4></td>-->
            <td class='row-span' rowspan='<?php if( $this->_vars['valideCode'] ){ ?>5<?php }else{ ?>4<?php } ?>'>
               <div class='span-auto' style='width:160px; text-align:left;'><h4 style="padding-top:0;">还不是会员?</h4></div><div class='span-auto close' style='width:25px'>X</div>
               <div class='clear'></div>
               <ul class="list fast-login">
                 <li><span>没有账号？</span><a href='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_passport",'act' => "signup")); ?>' class="actbtn btn-newregister">立即注册</a></li>
                 <?php if( $this->_vars['guest_enabled']=='true' ){ ?>  <!--非会员购买-->
                 <li><span>您还可以...</span><a class="actbtn btn-buynow" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_cart,'act' => checkout)); ?>" onclick="Cookie.set('S[ST_ShopEx-Anonymity-Buy]', 'true');$(this).getParent('.dialog').retrieve('chain',$empty)();return false;" >无需注册直接快读购买</a></li>
                 <?php } ?>
               <ul>
            </td>
         </tr>
        </table>
     </div>
  </div>
</td></tr></table>
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
   
   }('mini-loginbuyform');
</script>
<?php } ?>

";s:6:"expire";i:0;}