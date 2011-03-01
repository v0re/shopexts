<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: global.func.php 4461 2010-01-24 16:17:18Z anjel $
*/

function is_login()
{
    global $user;
	if(empty($user["user_name"]))
	{
	    header("location:login.php");
	}
}
function get_qijia($keywords)
{
     global $db,$zz_config;
	 $row=$db->get_one("select * from kuaso_zz_set_keywords where keywords='".$keywords."'");
	 if(empty($row))
	 {
	    $qijia=$zz_config["default_point"];
	 }
	 else
	 {
	    $qijia=$row["price"];
	 }
	 return $qijia;
}
function headhtml()
{
   global $user,$zz_user_group_array;
   $filename=basename($_SERVER['SCRIPT_NAME']);
   $action=$_REQUEST["action"];
?>
        <div class="head">
        	<div class="head-wrapper">
        		<a href="#" id="Logo" class="logo"><img width="133px" height="43px" src="images/logo.gif" title="<?php echo $config["name"];?>�ƹ�" alt="<?php echo $config["name"];?>�ƹ�" /></a>
        		<div id="RightTopNav" class="right-top-nav">
        			<strong><?php echo $user["user_name"];?></strong>
        			<!--<span id="BridgeHi">����:<?php echo $zz_user_group_array[$user["user_group"]];?></span>-->
        			
        			<a href="login.php?action=logou">�˳�</a>
        			<div id="ContactMenu">
        				<div id="BridgeService"></div>
        				<div><span class="block"><a id="RightNavMyPromoAdvLink" target="_blank" href="#">������Ϣ</a></span></div>
        				<div><span class="block"><a id="RightNavMessToBdLink" target="_blank" href="#">����</a></span></div>
        			</div>
        			<iframe id="ContactMenuMask" class="contact-mask"></iframe>
        		</div>
        		<div id="HelpTip" class="right-bottom-nav"></div>
        		<div id="MainNavigation" class="mainnav">
        			<span<?php if($filename=="index.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="./">��ҳ</a>
        			<i></i></span>
        			<span<?php if($filename=="manage.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="manage.php">�ƹ����</a>
        			<i></i></span>
					<span<?php if($filename=="union.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="union.php">���˴���</a>
        			<i></i></span>
					<span<?php if($filename=="account.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="account.php">�˻���Ϣ</a>
        			<i></i></span>
					<span<?php if($filename=="getpoints.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="getpoints.php">��λ�û���</a>
        			<i></i></span>
					<span<?php if($filename=="website.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="website.php">��վ����</a>
        			<i></i></span>
        			<!--<span<?php if($filename=="reports.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="reports.php">����</a>
        			<i></i></span>
					
					<span<?php if($filename=="tool.php"){echo " class=\"current\"";}?>><u></u>
        				<a href="tool.php">����</a>
        			<i></i></span>-->

        		</div>
        	</div>
		<?php
		if($filename=="account.php")
		{
		?>
				<div id="SubNavigation" class="subnav">
		<div class="subnav-wrapper">

			<span<?php if($action==""){echo " class=\"current\"";}?>><i></i><u></u><s></s><b></b><a href="<?php echo $filename;?>" target="_self">�����趨</a></span>
			<span class="split">|</span>
			
			
			<span<?php if($action=="epw"){echo " class=\"current\"";}?>><i></i><u></u><s></s><b></b><a href="?action=epw" target="_self">�޸�����</a></span>
			<span class="split"></span>
	
		</div>
	</div>
		<?php
		}
		?>
		<?php
		if($filename=="manage.php")
		{
		?>
				<div id="SubNavigation" class="subnav">
		<div class="subnav-wrapper">

			<span<?php if($action==""){echo " class=\"current\"";}?>><i></i><u></u><s></s><b></b><a href="<?php echo $filename;?>" target="_self">��ӹؼ���</a></span>
			<span class="split">|</span>
			
			
			
	
		</div>
	</div>
		<?php
		}
		?>
        	<div class="head-bottom"></div>
        </div>
<?php
}
?>
<?php
function foothtml()
{
    global $config;
?>
	</div>
        <div id="MaskDiv" class="maskDivNoColor"></div>
        <div class="foot"><?php echo $config['copyright'];?>
		&nbsp;&nbsp;<a target="_blank" href="<?php echo $config["url"];?>">Powered by Kuaso</a>
		</div>

	</body>
</html>
<?php
}
?>
