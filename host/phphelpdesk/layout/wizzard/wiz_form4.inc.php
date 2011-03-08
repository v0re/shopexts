<html>
<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title><? echo $l_title_report; ?> IV/V</title>
  <LINK REL=StyleSheet HREF="css/wizzard.css" TYPE="text/css" MEDIA=screen>
  </head>
<body class="main" topmargin=70 leftmargin=0>
<form action="wiz_start.php" method="POST" target="_self">
<input type="hidden" name="lstDepartment" value="<?echo $lstDepartment;?>">
<input type="hidden" name="txtUserFirstName" value="<?echo $txtUserFirstName;?>">
 <input type="hidden" name="txtUserLastName" value="<?echo $txtUserLastName;?>">
<input type="hidden" name="txtUserTelephone" value="<?echo $txtUserTelephone;?>">
<input type="hidden" name="txtLocation" value="<?echo $txtLocation;?>">
<input type="hidden" name="txtUserEmail" value="<?echo $txtUserEmail;?>">
<input type="hidden" name="txtStaffid" value="<?echo $txtStaffid;?>">
<input type="hidden" name="txtComputerid" value="<?echo $txtComputerid;?>">
<input type="hidden" name="lstRequest" value="<?echo $lstRequest ;?>">
<table align="center" width="90%">
      <tr>
      <td align ="center" colspan="2"><h1><? echo $l_titel_wizzard3;?></h1></td>
      </tr>
      <tr>
      <td><?echo $l_shortsummary;?> </td>
      <td><input  class="input" type="Text" name="txtSummary" value = "<? echo $lstRequest  ?>" size="50" maxlength="50"></td>
      </tr>
      <tr>
      <td><?echo $l_detail;?> </td>
      <td><textarea  class="input" name="txtDetail" cols="50" rows="8"></textarea> </td>
      </tr>
      <tr>
      <td><?echo $l_proposed_solution;?> </td>
      <td><textarea  class="input" name="txtProposedSolution" cols="50" rows="8"></textarea> </td>
		</tr>
		<tr>
		<td colspan ="2">
  <table style="border: 0px;" width="100%" cellspacing=0 cellpadding=0>
      <tr>
		<td><? echo "<b>$l_priority&nbsp;</b>"; ?> </td>
      <td width="25%" bgcolor="<?echo $html_priority_low;?>">
      <input type=radio value="0" name="optPriority"><b><?echo $l_low?></b>
      </td>
      <td width="25%" bgcolor="<?echo $html_priority_normal;?>">
      <input type=radio value="1" name="optPriority" checked><b><?echo $l_normal?></b>
      </td>
      <td width="25%" bgcolor="<?echo $html_priority_high;?>">
      <input type=radio value="2" name="optPriority"><b><?echo $l_high?></b>
      </td>
      <td width="25%" bgcolor="<?echo $html_priority_urgent;?>">
      <input type=radio value="3" name="optPriority"><b><?echo $l_urgent?></b>
      </td></tr></table>
	  </td></tr>
      <tr>
      <td colspan="2" align ="center"><input  class="input" type="Submit" name="cmdNext04" value="<? echo $l_next; ?>"></td>
	  </tr>
      <tr> <td colspan ="2"><center><a href="javascript:history.back()"><?echo $l_back;?></a></center></td></tr>
      </tr>

</td>
</tr>
</table>
</form>