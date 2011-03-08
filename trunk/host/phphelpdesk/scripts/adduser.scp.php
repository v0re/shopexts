<?php	//add user script

include("includes/functions.inc.php");

if ($s_manage_users == 1) {
  if ((isset($cmdSelectDepartments)) || 
	  (isset($cmdAdd)) || 
	  (isset($cmdDelete)) || 
	  (isset($cmdFinish))) {
    if(isset($cmdSelectDepartments)) { 
      $query = "INSERT INTO security";
      $query .= " (s_user, s_firstname, s_lastname, s_password, s_email) ";
      $query .= " VALUES ('$txtUsername', '$txtFirstname', ";
      $query .= "'$txtLastname', '$txtPassword', '$txtEmail');";    
      $mysql_result = query($query);
      if ($mysql_result) { print "<div class=successtxt>$l_userinfoadded</div>"; }
      
      $query = "UPDATE security SET ";
      $query .= "s_register_new_tickets='$u_register_new_tickets', ";
      $query .= "s_authorize_tickets='$u_authorize_tickets', ";
      $query .= "s_assign_tickets='$u_assign_tickets', ";
      $query .= "s_update_tickets='$u_update_tickets', ";
      $query .= "s_delete_tickets='$u_delete_tickets', ";
      $query .= "s_open_closed_tickets='$u_open_closed_tickets', ";
      $query .= "s_view_unauthorized_tickets='$u_view_unauthorized_tickets', ";
      $query .= "s_view_department_tickets='$u_view_department_tickets', ";
      $query .= "s_add_requests='$u_add_requests', ";
      $query .= "s_delete_requests='$u_delete_requests', ";
      $query .= "s_add_departments='$u_add_departments', ";
      $query .= "s_delete_departments='$u_delete_departments', ";
      $query .= "s_manage_users='$u_manage_users', ";
      $query .= "s_pref_viewall='1', ";
      $query .= "s_add_parts='$u_add_parts', ";
      $query .= "s_isroot='0', ";
      $query .= "s_pref_viewjobs_first='1', ";
      $query .= "s_generate_reports='$u_generate_reports', ";
      $query .= "s_ismanager='$u_ismanager' ";
      $query .= "WHERE s_user='$txtUsername';";
      $mysql_result = query($query);

      // Print results
      print "<BR>";
      if ($mysql_result) {
        print "<div class=successtxt>$txtUsername $l_wassuccessfullyadded<BR>\n";
	print "<BR>$l_pleasechoosethedepts<br>\n";
        print "<BR></div>\n";
      }
      else {  
	$query = "SELECT s_user FROM security";
	$query .= "WHERE s_user='$txtUsername';";
	$mysql_result = query($query);
        print "<div class=errortxt><B>$l_error</B>";
	if ($mysql_result) {
	  print " $txtUsername $l_isalreadyauser";
	}
	else {
          print " $txtUsername $l_wasnotadded<br>\n";
          print "<BR></div>\n";
	}
	exit; 
      }
    }
    // IF User Selects more departments to be added to the user departments list
    if (isset($cmdAdd)) {
      for ($i=0; $i<sizeof($cboAllDepartments); $i++) {
        $query = "SELECT d_name FROM userdepartments ";
        $query .= "WHERE s_user='$txtUsername'";
        $mysql_result = query($query);
        while ($row = mysql_fetch_row($mysql_result)){
	    if ($row[0] == $cboAllDepartments[$i]) {
	      $dontaddflag = 1;
	    }
        }
	if ($dontaddflag != 1) {
          $query = "INSERT INTO userdepartments ";
          $query .= "(s_user, d_name) ";
          $query .= "VALUES ('$txtUsername','$cboAllDepartments[$i]');";
          $mysql_result = query($query);
	}
      }
    }
    if (isset($cmdDelete)) {
      for ($i=0; $i<sizeof($cboUserDepartments); $i++) {
	$query = "DELETE FROM userdepartments ";
	$query .= "WHERE s_user='$txtUsername' ";
	$query .= "AND d_name='$cboUserDepartments[$i]';";
	$mysql_result = query($query);
      }
    }
    if (isset($cmdFinish)) {
      print "<div class = successtxt><BR><BR>\n";
      print "$txtUsername $l_usersuccessfullycreated<BR><BR></div>\n";
      $exitCreateUser = 1;
    }
    if ($exitCreateUser != 1) {
?>
<form method=POST action="<? echo $g_base_url?>/index.php?whattodo=adduser">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=80% align="center">
  <tr>
    <th width="100%" valign=center align=left>
      <? echo $g_title;?> - <? echo $l_selectdepartments?>
    </th>
  </tr>
  <tr>
    <td width="100%">
      <form method=POST action="<?echo $g_base_url?>/index.php?whattodo=adduser">
      <table CELLSPACING=0 CELLPADDING=3 border=1 width=100% align="center">
        <tr>
          <input type=hidden name="txtUsername" value="<? echo $txtUsername; ?>">
        </tr>
        <tr> 
	  <td width="25%"> 
	    <p><b><? echo $l_alldepartments?></b></p>
	  </td>
          <td align="center" valign="middle" colspan="2">&nbsp;</td>
          <td width="25%"> 
            <p><b><?echo $l_useravailabledepartments?>
            </b></p>
          </td>
        </tr>
        <tr> 
          <td width="25%" rowspan="2"> 
            <select name="cboAllDepartments[]" size="15" multiple>
<?
for ($i=0; $i<sizeof($departments);$i++) {
  $dontshowdepartmentflag = 0;
  $query = "SELECT d_name FROM userdepartments ";
  $query .= "WHERE s_user='$txtUsername'";
  $mysql_result = query($query);
  while ($row = mysql_fetch_row($mysql_result)){
    if ($row[0] == $departments[$i]) {
      $dontshowdepartmentflag = 1;
    }
  }
  if ($dontshowdepartmentflag != 1) {
    print "              <option value=\"$departments[$i]\">$departments[$i]</option>\n";
  }
}
?>
            </select>
          </td>
          <td align="left" valign="middle" height="130" width="33%" bgcolor="#FFCC66"> 
             <div align="center"><font size="2"> 
		<?echo $l_userthisbuttontoadd?>
             </font></div>
          </td>
          <td align="center" valign="middle" width="16%" bgcolor="#FFCC66"> 
            <input type="submit" name="cmdAdd" value="<?echo $l_add?> &gt;&gt;">
          </td>
          <td width="26%" rowspan="2"> 
            <div align="center"> 
            <select name="cboUserDepartments[]" size="15" multiple>
<?php
$query = "SELECT d_name FROM userdepartments ";
$query .= "WHERE s_user='$txtUsername'";
$mysql_result = query($query);
while ($row = mysql_fetch_row($mysql_result)){
  print "              <option value=\"$row[0]\">$row[0]</option>\n";
}
?>
            </select>
            </div>
          </td>
        </tr>
        <tr> 
          <td align="left" valign="middle" width="33%" bgcolor="#FFFFCC"> 
            <div align="center"><font size="2"><?echo $l_userthisbuttontodelete?> 
            </font></div>
          </td>
          <td align="center" valign="middle" width="16%" bgcolor="#FFFFCC"> 
            <input type="submit" name="cmdDelete" value="&lt;&lt; <?echo $l_delete?>">
          </td>
        </tr>
        <tr> 
          <td align="center" valign="middle" colspan="3">
            <font size="2">
	    <?echo $l_clickfinishmessage?></font>
	  </td>
          <td align="center" valign="middle"> 
	    <input type="submit" name="cmdFinish" value="<?echo $l_finish?>">
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table><br><br><br>
<? }
  }
  elseif (isset($cmdNext)) {
    if (($txtUsername == "") || 
	    ($txtPassword == "") || 
	    ($txtPassword != $txtVerifiedPassword)) { ?>
<? echo "<div class = errortxt> $l_error $l_atleastusername </div>" ?><BR> 
<center><a href="<? echo $g_base_url?>/index.php?whattodo=adduser">Add user Page</a>.</center>
<? } else { ?>
<form method=POST action="<? echo $g_base_url?>/index.php?whattodo=adduser">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=60% align="center">
  <tr>
    <th width="100%" valign=center align=left>
      <? echo $g_title;?> - <? echo $l_selectpermissions?>
    </th>
  </tr>
  <tr>
    <input type=hidden name="txtUsername" value="<?echo $txtUsername;?>">
    <input type=hidden name="txtPassword" value="<?echo $txtPassword;?>">
    <input type=hidden name="txtFirstname" value="<?echo $txtFirstname;?>">
    <input type=hidden name="txtLastname" value="<?echo $txtLastname;?>">
    <input type=hidden name="txtEmail" value="<?echo $txtEmail;?>">
    <input type=hidden name="AddUser" value="Add">
  </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0 width="100%" cellspacing=0 cellpadding=0>
        <tr>
	  <td width="50%" valign=center align=right>
	  </td>
	  <td width="25%" valign=center align=center>
	    <? echo $l_grant?><BR><? echo $l_privileges?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <? echo $l_dontgrant?><BR><? echo $l_privileges?>
	  </td>
	</tr>
	<tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_registernewtickets ?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_register_new_tickets" value="1" checked>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_register_new_tickets" value="0">
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_authorizetickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_authorize_tickets" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_authorize_tickets" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_assigntickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_assign_tickets" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_assign_tickets" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	   <? echo $l_updatetickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_update_tickets" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_update_tickets" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_deletetickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_delete_tickets" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_delete_tickets" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_openclosedtickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_open_closed_tickets" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_open_closed_tickets" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	   <? echo $l_viewunauthorizedtickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_view_unauthorized_tickets" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_view_unauthorized_tickets" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_viewdepartmenttickets?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_view_department_tickets" value="1" checked>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_view_department_tickets" value="0">
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_addrequests?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_add_requests" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_add_requests" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_deleterequests?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_delete_requests" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_delete_requests" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_adddepartments?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_add_departments" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_add_departments" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_deletedepartments?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_delete_departments" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_delete_departments" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_manageusers?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_manage_users" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_manage_users" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_manageparts?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_add_parts" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_add_parts" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_runreports?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_generate_reports" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_generate_reports" value="0" checked>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    <? echo $l_isamanager?>
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_ismanager" value="1">
	  </td>
	  <td width="25%" valign=center align=center>
	    <input type="radio" name="u_ismanager" value="0" checked>
	  </td> </tr>
		<tr><td colspan="3" align="right">
      <input type="submit" value="<?echo $l_next?>" name="cmdSelectDepartments">
      </table>
      </td>
  </tr>
</table>
</form>

<?
}
  }
  else {
?>
<form method=POST action="<?echo $g_base_url?>/index.php?whattodo=adduser">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=50% align="center">
  <tr>
    <th width="100%" valign=center align=left>
      <? echo $g_title;?> - <? echo $l_userinformation?>
    </th>
  </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0 width="100%" cellspacing=0 cellpadding=3>
        <tr>
          <td width="50%" valign=center align=right>
		<? echo $l_Username?>
	  </td>
	  <td width="50%">
	    <input type=text name="txtUsername" size=10>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right><? echo $l_Password ?> 
	  </td>
          <td width="50%">
	    <input type=password name="txtPassword" size=16>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right><? echo $l_verifypassword?>
	  </td>
          <td width="50%">
	    <input type=password name="txtVerifiedPassword" size=16>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	    	<? echo $l_firstname?>
	  </td>
          <td width="50%">
	    <input type=text name="txtFirstname" size=15>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	     <?echo $l_lastname?></td>
          <td width="50%">
	    <input type=text name="txtLastname" size=15>
	  </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
	     <? echo $l_emailaddress?>
	  </td>
          <td width="50%">
	    <input type=text name="txtEmail" size=20>
	  </td></tr>
      </table>
 	  </td></tr>
	 <tr><td align="center">
      <input type="submit" value="<? echo $l_next?>" name="cmdNext">
      </td>
  </tr>
</table>
</form>
<?
  }
} // end if part "manage users";
else {
  print "<CENTER><BR><B>$l_error</B> <I>Add User was selected and you do <B>not</B> \n";
  print "have administrative privileges!</I><BR><BR>\n";
  print "<I>PLEASE CHOOSE ANOTHER Item</I><BR><BR></CENTER>\n";
}
?>
