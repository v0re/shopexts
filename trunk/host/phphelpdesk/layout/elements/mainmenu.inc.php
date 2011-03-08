
<table style="position:absolute; top:86px; left:0px;height:60px;width:100%;z-index:1;" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="middel" style="width:800px;" background="images/barragris_doble.gif" >
<table border="0" width="100%" cellpadding="1" cellspacing="0">
<tr>
	<td align="center"><form name="form1" method="post" action="<? echo $g_base_url;?>/index.php?whattodo=viewjobs">
		<input name="whattodo" type="hidden" value="viewjobs">
        <input type="submit" name="Submit" value="<? echo $l_viewjobs; ?>">
      </form></td>
	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="adddepartment">
        <input type="submit" name="Submit" value="<? echo $l_adddepartment; ?>">
      </form></td>
	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="deletedepartment">
        <input type="submit" name="Submit" value="<? echo $l_deletedepartment; ?>">
      </form></td>
  	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="addrequest">
        <input type="submit" name="Submit" value="<? echo $l_addrequest; ?>">
      </form></td>
   	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="deleterequest">
        <input type="submit" name="Submit" value="<? echo $l_deleterequest; ?>">
      </form></td>	 
	  </tr>
	  <tr>
  	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="adduser">
        <input type="submit" name="Submit" value="<? echo $l_adduser; ?>">
      </form></td>	  
	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="modifyuser">
        <input type="submit" name="Submit" value="<? echo $l_modifyuser; ?>">
      </form></td>
	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="deleteuser">
        <input type="submit" name="Submit" value="<? echo $l_deleteuser; ?>">
      </form></td>
   	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="notifications">
        <input type="submit" name="Submit" value="<? echo $l_notifications; ?>">
      </form></td>
   	<td align="center"><form name="form1" method="post" action="">
		<input name="whattodo" type="hidden" value="preferences">
        <input type="submit" name="Submit" value="<? echo $l_preferences; ?>">
      </form></td>
</tr>
</table>	
</td>
	<td background="images/gris_doble.gif"  >&nbsp;</td>
  </tr>
</table>
