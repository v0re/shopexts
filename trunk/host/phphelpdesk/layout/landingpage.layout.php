<!--  main flash heading -->
<? /* since we have to put all the background first, we have to build the page according to the layers which follow*/ ?>
<!-- Main HEADING  -->
<table style="position:absolute;top:0;left:0;width:100%;" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td style="width:800px;" background="images/cachito.gif"><img src="images/header_landing.jpg" width="800" height="143"></td>
    <td style="height:142px;" background="images/cachito.gif"></td>
  </tr>
  <tr> 
    <td style="width:800px;" background="images/gris.gif"><img src="images/barragris.gif" width="800" height="34"></td>
    <td style="height:34px;" background="images/gris.gif"></td>
  </tr>
</table>
<div class="title"><? print $g_title; ?></div>
<div class="version"><? print "$l_version: $version"; ?></div>
<!-- Now get all the background elements on the screen first all the table since blanck cells would kill the stuff which goes over it-->
<table style="position:absolute;top:363;height:12px;width:100%;z-index:1" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td style="width:127px;" bgcolor="#FFFFFF"></td>
    <td style="width:580px;" background="images/cachito1.gif"></td>
    <td background="images/cachito1.gif"></td>
  </tr>
</table>
<table style="position:absolute;top:182px; left:0px;height:181px;width:100%;z-index:1;" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td style="width:180px;" bgcolor="#FFFFFF">&nbsp;</td>
    <td style="width:619px;" background="images/foto.gif">&nbsp;</td>
    <td background="images/gris1.gif">&nbsp;</td>
  </tr>
</table>
<table style="position:absolute;top:374px; left:0px;height:33px;width:100%;z-index:1;" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td style="width:180px;" bgcolor="#FFFFFF">&nbsp;</td>
    <td style="width:350px;" background="images/fondonoticias1.gif">&nbsp;</td>
    <td background="images/grisazul.gif">&nbsp;</td>
  </tr>
</table>
<!-- the blue line  under new-->
<table style="position:absolute;top:465px; left:0px;height:36px;width:100%;z-index:1;" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td style="width:180px;" bgcolor="#FFFFFF">&nbsp;</td>
    <td background="images/cachito2.gif">&nbsp;</td>
  </tr>
</table>
<!-- now position design elements over the tables -->
<div id="greenline" style="background:#7CBD62; border:0px"></div> 
<div id="shortgreenline" style="background:#7CBD62; border:0px"></div>
<div class="sidemenu1"> <img src="images/menu1b.gif" ></div>
<div class="sidemenu2"> <img src="images/menu2a.gif" ></div>
<div id="greenmenubox" style="background:#7CBD62; border:0px"></div>
<div id="bluemenubox" style="background:#1B4F7E; border:0px"></div>
<!-- here the photos and news which go on the page -->
<div class="noticias"><? print $l_news; ?></div>
<div class="fotomotiv1"><img src="images/fot1.gif" ></div>
<div class="fotomotiv2"><img src="images/fot1.gif" ></div>
<div class="fotomotiv3"><img src="images/fot3.gif" ></div>
<div class="fotomotiv4"><img src="images/fot2.gif" ></div>
<?PHP include ("layout/elements/footer.inc.php"); // print the footer ?>