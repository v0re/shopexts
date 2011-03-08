<?php	//PHP Helpdesk - PREFERENCES
include("includes/functions.inc.php");
if (isset($cmdSavePreferences)) {
  $query = "UPDATE security SET ";
  $query .= "s_pref_viewall='$u_pref_viewall', ";
  $query .= "s_pref_viewjobs_first='$u_pref_viewjobs_first' ";
  $query .= "WHERE s_user='$user';";
  $mysql_result = query($query);
  if ($mysql_result) {
    print "<center> $l_prev_saved </center>";
  }
  else {
    print "MYSQL $l_error ".mysql_error("$mysql_result");
  }
}
?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=preferences">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=60% align="center">
  <tr>
    <th>
      <? echo $g_title;?> - <? echo $l_userpreferences?>
    </th>
  </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0 width="100%" cellspacing=0 cellpadding=3>
        <tr>
	  <td width="50%" valign=center align=right>
	  </td>
	  <td width="25%" valign=center align=center>
	   <b><? echo $l_yes?></b>
	  </td>
	  <td width="25%" valign=center align=center>
	    <b><? echo $l_no?></b>
	  </td>
	</tr>
	<tr>
      <td width="50%" valign=center align=right>
	    <? echo $l_showalltickets?> 
	  </td>
	  <td width="25%" valign=center align=center>
	  <?
	  if ($s_pref_viewall == "1") {
	    $viewallyes = "checked";
	  }
	  else {
		$viewallno = "checked";
	  }
      ?>
	    <input type="radio" name="u_pref_viewall" value="1" <?echo $viewallyes;?>>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_pref_viewall" value="0" <?echo $viewallno;?>>
	  </td>
    </tr>
	<tr>
      <td width="50%" valign=center align=right>
	    <? echo $l_viewjobsfirst?>
	  </td>
	  <td width="25%" valign=center align=center>
	  <?
	  if ($s_pref_viewjobs_first == "1") {
	    $viewjobsfirstyes = "checked";
	  }
	  else {
		$viewjobsfirstno = "checked";
	  }
      ?>
	    <input type="radio" name="u_pref_viewjobs_first" value="1" <?echo $viewjobsfirstyes;?>>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_pref_viewjobs_first" value="0" <?echo $viewjobsfirstno;?>>
	  </td>
    </tr>
	<tr>
	<td colspan="3" align="center">
	  <input type="submit" value="<?echo $l_savechanges?>" name="cmdSavePreferences">
	</td>
  </tr>
  <tr><td COLSPAN=3><hr><br>
   <?
   echo "<center><a href=$g_base_url/index.php?whattodo=chpw>";
   echo "$l_changepwd</a></center><p>";
   ?>
 </td></tr>
</table>
</td>
</tr>
</table>
</form>

