<? /* since we have to put all the background first, we have to build the page according to the layers which follow*/ ?>
<!-- for the admin area: print a smaller header on top -->
<table style="position:absolute;top:0;left:0;width:100%;" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td style="width:800px;" background="images/img_admin/cachito.gif"><img src="images/header_admin.jpg">
    </td>
	    <td style="height:87px;" background="images/img_admin/plecacontexto.gif"></td>
		</td>
  </tr>
</table>
<div class="title"><? print $g_title; ?></div>
<div class="version"><? print "$l_version: $version"; ?></div>
<?  include ("layout/elements/mainmenu.inc.php"); ?>
<!-- Now get all the background elements on the screen first all the table since blanck cells would kill the stuff which goes over it-->
<!-- now position design elements over the tables -->
<div id="greenline2" style="background:#7CBD62; border:0px"></div> 
<div class="logoutbox"> <img src="images/img_admin/menu.gif" ></div>
<div style="position:absolute; top:228px; left:13px; z-index:1;" > <img src="images/menu2a.gif" ></div>
<div style="position:absolute; top:261px; left:13px; width:167px; height:180px; z-index:1; background:#7CBD62; border:0px;"></div>
<div style="position:absolute; top:423px; left:13px; width:167px; height:170px;  z-index:1; background:#1B4F7E; border:0px"></div>
<!-- here the photos and news which go on the page -->
