<?php exit(); ?>a:2:{s:5:"value";s:2232:"<form class="form loginform" action="<?php echo $this->_vars['callback']; ?>" method="post" id='loginBar'>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <th><i>*</i>用户名：</th>
            <td><?php echo $this->ui()->input(array('name' => "uname",'class' => "inputstyle",'required' => "true",'id' => "uname",'tabindex' => "1",'value' => "{$this->_vars['loginName']}"));?><a style="margin:0 0 0 6px;" href="<?php echo $this->_vars['singup_url']; ?>">立即注册</a></td>
        </tr>
        <tr>
            <th><i>*</i>密码：</th>
            <td><?php echo $this->ui()->input(array('name' => "password",'class' => "inputstyle",'type' => "password",'required' => "true",'id' => "password",'tabindex' => "2"));?><a style="margin:0 0 0 6px;" href="<?php echo $this->_vars['lost_url']; ?>">忘记密码</a></td>
      </tr>
       <?php if( $this->_vars['show_varycode'] ){ ?>
        <tr>
          <th><i>*</i>验证码：</th>
          <td><span id='verifyCodebox'><?php echo $this->ui()->input(array('type' => "number",'required' => "true",'size' => "4",'maxlength' => "4",'name' => "verifycode",'id' => "iptlogin",'tabindex' => "3"));?>
             <span class='verifyCode' style="display:none"><img id="membervocde" src="#" border="1" /><a href="javascript:changeimg('membervocde')">&nbsp;看不清楚?换个图片</a>
             </span>
             </span>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <th></th>
          <td><input class="actbtn btn-login" type="submit" value="登录" tabindex="4" />
              <input type="hidden" name="forward" value="<?php echo $this->_vars['forward']; ?>">
            </td>
        </tr>
    </table>
</form>

<?php if( $this->_vars['show_varycode'] ){ ?>
<script>

$$('#loginBar input').addEvent('focus',function(){
      if($(this.form).retrieve('showvcode',false))return;
      changeimg('membervocde');
      $('verifyCodebox').getElements('span').show();
      $(this.form).store('showvcode',true);
});

function changeimg(id){
	$(id).set('src','<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_passport",'act' => "gen_vcode")); ?>#'+$time());
}
</script>
<?php } ?>";s:6:"expire";i:0;}