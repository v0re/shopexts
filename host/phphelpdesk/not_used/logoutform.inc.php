<form method=POST action="<?echo $g_base_url;?>/index.php">
<input type=hidden name="logout" value="YES">
<table border=0 width = "150" cellpadding=0 cellspacing=0> 
<!-- Begins the Logoutform Table -->
	<tr>
	<td align = "center">
	<input type=submit value="<? echo $l_logout?>" name="cmdEnter">
	</td>
	</tr>
</table> <!-- Ends the logoutform table-->
</form>