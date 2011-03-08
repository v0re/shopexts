<div id="loginbox" style="background:#1B4F7E; border:0px"></div>
<div class="loginname"><? echo $l_username ?>:</div>
<div class="password"><? echo $l_password ?>:</div>
<form method=POST action="<?echo $g_base_url;?>/index.php">
	<input type=hidden name="status" value="Logged In">
	<div style ="position:absolute;top:265px; left:20px; z-index:2;">
	<input type=text name="txtUsername" size=12></div>
	<div style ="position:absolute;top:310px; left:20px; z-index:2;">
	<input type=password name="txtPassword" size=12></div>
	<div style ="position:absolute;top:310px; left:108px; z-index:4;">
	<input type="image" src="images/login_button.jpg">
	</div>
</form>	
	<div class = "errortxt" align = "right" style ="position:absolute;top:180px; left:180px; z-index:2;">
	<? echo $wronginfomsg;?>
	</div>
