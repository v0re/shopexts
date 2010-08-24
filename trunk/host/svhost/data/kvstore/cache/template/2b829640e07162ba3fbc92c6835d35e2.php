<?php exit(); ?>a:2:{s:5:"value";s:2662:"<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>POSSPORTS - LOGIN</title>
<link rel="shortcut icon" href="../favicon.gif" type="image/gif" />
<link href="<?php echo $this->app->res_url; ?>/framework.css" rel="stylesheet" type="text/css"  /> 
<link href="<?php echo $this->app->res_url; ?>/login/login.css" rel="stylesheet" type="text/css"  />
</head>
<body>
<div class="signup-contant clearfix">
 <div class="signup-contant-main ">
 <div class="logo clearfix"><h3><?php echo app::get('desktop')->getConf('logo'); ?><span><?php echo app::get('desktop')->getConf('logo_desc'); ?></span></h3></div>
 <div class="signup">
  <div class="signup-left"></div>
  <div class="signip-main">
  <div class="ifram">
<div class="publicize">
	<iframe name="publicize-iframe" frameborder="0" scrolling="no" height="100%" width="100%" allowTransparency="true"   src="http://top.shopex.cn/ecos/welcome.php" >	
	</iframe> 
 </div>
  </div>
  <div class="window">
   <div class="window-tabsingle">
    <ul>
     <li class="window-tab-current">
     <div class="window-tabcurrent-leftsingle"></div>
     <div class="window-tabcurrent-midsingle"><?php if($this->_vars['passports'])foreach ((array)$this->_vars['passports'] as $this->_vars['item']){  echo $this->_vars['item']['name'];  } ?></div>
     <div class="window-tabcurrent-rightsingle"></div>
     </li>
     <!--  <li class="window-tab-unselected">高级登录</li>
     <li class="window-tab-unselected">淘宝登录</li>--> 
    </ul>
   </div>
   <div class="window-main">
        <?php if($this->_vars['passports'])foreach ((array)$this->_vars['passports'] as $this->_vars['item']){ ?>
			<div class="passport-bd"><?php echo $this->_vars['item']['html']; ?></div>
		<?php } ?>
   </div>
  </div>
  </div>
  <div class="signup-right"></div>
 </div>
 </div>
   <div class="inverted-image" id="inverted-image">&nbsp;</div>
   <div class="copyright">Copyright &copy; 2003-<script>document.write(new Date().getFullYear());</script> ShopEx. All rights reserved.</div>
</div> 
<script>
	
    (function(el){
     	     	if (window.ActiveXObject&&!window.XMLHttpRequest) { el.style.cssText="background:none;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $this->app->res_url; ?>/login/signup_31.png')"; 
     			}
     			return this;
     	  })(document.getElementById('inverted-image'));

</script>
</body>
</html>
";s:6:"expire";i:0;}