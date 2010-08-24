<?php exit(); ?>a:2:{s:5:"value";s:2621:"<form class="passport-form" action="<?php echo $this->_vars['callback']; ?>" method="post">
	<ul>
		<li><label for="uname">用户名：</label><input class="inputstyle" type="text" required="true" name="uname" id="uname" value="<?php echo $this->_vars['pam_passport_basic_uname']; ?>"/></li>
		<li style="padding-left:9px"><label for="password">密&nbsp;码：</label><input class="inputstyle" type="password" name="password" id="password" onkeypress="detectCapsLock(event);" value=""/>
		<?php if( $this->_vars['type']=="member" ){ ?><a href="<?php echo kernel::router()->gen_url(array('app' => 'b2c','ctl' => 'site_passport','act' => 'lost')); ?>">忘记密码</a><?php } ?>
			<div class="info-tip" id="caps-info">
			<div class="info-tip-arrow">&nbsp;</div>
			Caps Lock键已开启,<br />密码输入是大写状态!
		</div>
		</li>
		<?php if( $this->_vars['show_varycode'] ){ ?>
        <li><label for="verifycode">验证码：</label><input maxlength="4" autocomplete="off" class="inputstyle inputstyle-short" name="verifycode" id="verifycode" value=""  />
     <img id="shopadminvocde" src="index.php?app=desktop&ctl=passport&act=gen_vcode&<?php echo time(); ?>" align="top" class="verifycodeimg"><a href="javascript:changeimg('shopadminvocode')">&nbsp;看不清楚?换个图片</a></li>
		<?php } ?>
		<li class="colspan">
			<input type="checkbox" name="remember" value="true" <?php if( $_COOKIE['pam_passport_basic_uname'] ){ ?>checked=true<?php } ?> >
				<label for="remember">保存用户名、密码</label>
			</li>
		<li class="colspan"> 
			   <input class="btn-login" type="submit" value="登陆" /> 
		</li>
	</ul>
</form>

<?php if( $this->_vars['error_info'] ){ ?>
<div class="error-info">
	<?php echo $this->_vars['error_info']; ?>
</div>
<?php } ?>

<script>
var capsTip = document.getElementById('caps-info');  

function detectCapsLock(e){  
   valueCapsLock  =  e.keyCode ? e.keyCode:e.which;
   valueShift  =  e.shiftKey ? e.shiftKey:((valueCapsLock  ==   16 ) ? true : false );  

    if (((valueCapsLock >=   65   &&  valueCapsLock  <=   90 )  &&   ! valueShift)   
    || ((valueCapsLock >=   97   &&  valueCapsLock  <=   122 )  &&  valueShift))   
       capsTip.style.visibility  =  'visible';  
    else   
       capsTip.style.visibility  =  'hidden';  
}  
</script>

<?php if( $this->_vars['show_varycode'] ){ ?>
<script>
function changeimg(id){
	var timestamp = Date.parse(new Date());
		document.getElementById('shopadminvocde').src='index.php?app=desktop&ctl=passport&act=gen_vcode#'+timestamp;
		
    
}
</script>
<?php } ?>";s:6:"expire";i:0;}