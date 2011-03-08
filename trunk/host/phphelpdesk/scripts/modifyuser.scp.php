<?php        //MODIFY USER SCRIPT

include("includes/functions.inc.php");

// if user has not been choosen do this, else show modify user stuff
if ($s_manage_users==1) {
if (!isset($lstModifyUser) &&
          !isset($cmdSelectDepartments) &&
          !isset($cmdAdd) &&
          !isset($cmdDelete) &&
          !isset($cmdFinish) &&
          !isset($cmdNext)) { //first screen (1/3) choose user;
?>
<BR>
<form method=POST action="<? echo $g_base_url;?>/index.php?whattodo=modifyuser">
  <TABLE CELLSPACING=0 CELLPADDING=3 BORDER=1 width="50%" align="center">
    <tr>
      <th width="100%" valign=top align=left>
      <? echo $g_title;?> - <? echo $l_selectusertomodify?>
      </th>
    </tr>
    <tr>
      <td width="100%" align=left>
        <TABLE CELLSPACING=0 CELLPADDING=3 BORDER=0 width="100%">
        <tr>
          <td width="50%" valign=middle align=right>
            <b><? echo $l_selectuser?></b>
          </td>
          <td width="50%" valign=middle>
            <select size=1 name="lstModifyUser">
<?php  // print each row from the security table into an HTML table
    $query = "SELECT s_user FROM security ORDER BY s_user;";
    $mysql_result = query($query);
    while($row = mysql_fetch_row($mysql_result)) {
    /*  $useralreadyfound = 0;
      for ($i=0; $i < sizeof($departments); $i++) {
        $query1 = "SELECT s_user, d_name ";
        $query1 .= "FROM userdepartments ";
        $query1 .= "WHERE d_name='$departments[$i]' ";
        $query1 .= "AND s_user='$row[0]' ORDER BY s_user;";
        $mysql_result1 = query($query1);
        while($row1 = mysql_fetch_row($mysql_result1)) {
          if ($useralreadyfound == 0) {
            print "        <option value=\"$row[0]\">$row[0]</option>\n";
            $useralreadyfound = 1;
            }
        }
      }*/ //declarated this part above to be able to edit also the users to whom there 
      //is no department assigned.
         print "        <option value=\"$row[0]\">$row[0]</option>\n";      
    }
?>
            </select>
          </td>
          <td valign=middle>
                <input type=submit value="<? echo $l_selectthisuser?>" name="cmdSelectUser">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</FORM>
<?php
}
elseif ((isset($cmdSelectDepartments)) ||
          (isset($cmdAdd)) ||
          (isset($cmdDelete)) ||
          (isset($cmdFinish))) {
    if(isset($cmdSelectDepartments)) {
      $query = "UPDATE security SET ";
      $query .= "s_firstname='$txtFirstname', ";
      $query .= "s_lastname='$txtLastname', ";
      $query .= "s_password='$txtPassword', ";
      $query .= "s_email='$txtEmail' ";
      $query .= "WHERE s_user='$txtUsername';";
      $mysql_result = query($query);
      if ($mysql_result) { }

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
      $query .= "s_add_parts='$u_add_parts', ";
      $query .= "s_generate_reports='$u_generate_reports', ";
      $query .= "s_ismanager='$u_ismanager' ";
      $query .= "WHERE s_user='$txtUsername';";
      $mysql_result = query($query);

      // Print results
      print "<BR>";
      if ($mysql_result) {
        print "<div class=successtxt>";
        print "<BR>$l_pleasechoosethedepts\n";
        print "<BR></div>\n";
      }
      else {
        $query = "SELECT s_user FROM security ";
        $query .= "WHERE s_user='$txtUsername';";
        $mysql_result = query($query);
        print "<div class=errortxt><B>$l_error</B>";
        if ($mysql_result) {
          print " $txtUsername $l_isalreadyauser<br>\n";
          include("scripts/adduser.scp.php");
          exit;
        }
        else {
          print " $txtUsername $l_userhasnotbeenupdated<br>\n";
          print "<BR></div>\n";
        }
        exit;
      }
    }
    // IF User Selects more departments to be added to the user departments list
    if (isset($cmdAdd)) {
      for ($i=0; $i<sizeof($cboAllDepartments); $i++) {
        $query = "SELECT d_name FROM userdepartments ";
        $query .= "WHERE s_user='$txtUsername';";
        $mysql_result = query($query);
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
      print "<div class=successtxt><BR><BR>\n";
      print "$txtUsername $l_userhasbeenupdated<BR><BR></div>\n";
      $exitCreateUser = 1;
    }
    if ($exitCreateUser != 1) {
?>
<form method=POST action="<? echo $g_base_url?>/index.php?whattodo=modifyuser">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=80% align="center">
  <tr>
    <th width="100%" valign=center align=left>
      <? echo $g_title;?> - <? echo $l_selectdepartments?>
    </th>
  </tr>
  <tr>
    <td width="100%" align=left>
      <form method=POST action="<? echo $g_base_url?>/index.php?whattodo=modifyuser">
      <table CELLSPACING=0 CELLPADDING=3 border=0 width=100%>
        <tr>
          <input type=hidden name="txtUsername" value="<?echo $txtUsername;?>">
          <input type=hidden name="lstModifyuser" value="<?echo $lstModifyuser;?>">
        </tr>
        <tr>
          <td width="25%" align="center">
            <b><?echo $l_alldepartments?></b>
          </td>
          <td align="center" valign="middle" colspan="2">&nbsp;</td>
          <td width="25%">
            <b><? echo $l_useravailabledepartments?></b>
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
		<? echo $l_userthisbuttontoadd?>
	</td>
          <td align="center" valign="middle" width="16%" bgcolor="#FFCC66">
            <input type="submit" name="cmdAdd" value="<?echo $l_add?> &gt;&gt;">
          </td>
          <td width="26%" rowspan="2">
            <select name="cboUserDepartments[]" size="15" multiple>
<?
$query = "SELECT d_name FROM userdepartments ";
$query .= "WHERE s_user='$txtUsername'";
$mysql_result = query($query);
while ($row = mysql_fetch_row($mysql_result)){
  print "              <option value=\"$row[0]\">$row[0]</option>\n";
}
?>
            </select>
          </td>
        </tr>
        <tr>
          <td align="left" valign="middle" width="33%" bgcolor="#FFFFCC">
            <?echo $l_userthisbuttontodelete?>
          </td>
          <td align="center" valign="middle" width="16%" bgcolor="#FFFFCC">
            <input type="submit" name="cmdDelete" value="&lt;&lt; <?echo $l_delete?>">
          </td>
        </tr>
        <tr>
          <td align="center" valign="middle" colspan="3">
            <? echo $l_clickfinishmessage?>
          </td>
          <td align="center" valign="middle">
            <input type="submit" name="cmdFinish" value="<? echo $l_finish?>">
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
<?php }
  }
  elseif (isset($cmdNext)) {
    if (($txtUsername == "") ||
            ($txtPassword == "") ||
            ($txtPassword != $txtVerifiedPassword)) {
      ?>
      <div class="errortxt"><? echo "$l_error $l_atleastusername"; ?> <BR><BR>
   <? } 
?>

<form method=POST action="<? echo $g_base_url?>/index.php?whattodo=modifyuser">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=60% align="center">
  <tr>
    <th width="100%" valign=center align=left>
      <? echo $g_title;?> - <? echo $l_selectpermissions?> </th>
  </tr>
  <tr>
    <input type=hidden name="txtUsername" value="<?echo $txtUsername;?>">
    <input type=hidden name="txtPassword" value="<?echo $txtPassword;?>">
    <input type=hidden name="txtFirstname" value="<?echo $txtFirstname;?>">
    <input type=hidden name="txtLastname" value="<?echo $txtLastname;?>">
    <input type=hidden name="txtEmail" value="<?echo $txtEmail;?>">
    <input type=hidden name="lstModifyUser" value="<?echo $txtUsername;?>">
    <input type=hidden name="AddUser" value="Add">
  </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0 width="100%" cellspacing=0 cellpadding=1>
        <tr>
          <td width="50%" valign=center align=right>
          </td>
          <td width="25%" valign=center align=center>
            <b><? echo $l_grant?><BR><? echo $l_privileges?></b>
          </td>
          <td width="25%" valign=center align=center>
            <b><? echo $l_dontgrant?></b><BR><? echo $privileges?>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_registernewtickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_register_new_tickets==1) {
            $registergrant="checked";
          }
          else {
            $registerdontgrant="checked";
          }
          ?>
            <input type="radio" name="u_register_new_tickets" value="1" <?echo $registergrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_register_new_tickets" value="0" <?echo $registerdontgrant?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_authorizetickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_authorize_tickets==1) {
            $authorizegrant="checked";
          }
          else {
            $authorizedontgrant="checked";
          }
          ?>
            <input type="radio" name="u_authorize_tickets" value="1" <?echo $authorizegrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_authorize_tickets" value="0" <?echo $authorizedontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_assigntickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_assign_tickets==1) {
            $assigngrant="checked";
          }
          else {
            $assigndontgrant="checked";
          }
          ?>
            <input type="radio" name="u_assign_tickets" value="1" <?echo $assigngrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_assign_tickets" value="0" <?echo $assigndontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_updatetickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_update_tickets==1) {
            $updategrant="checked";
          }
          else {
            $updatedontgrant="checked";
          }
          ?>
            <input type="radio" name="u_update_tickets" value="1" <?echo $updategrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_update_tickets" value="0" <?echo $updatedontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_deletetickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_delete_tickets==1) {
            $deletegrant="checked";
          }
          else {
            $deletedontgrant="checked";
          }
          ?>
            <input type="radio" name="u_delete_tickets" value="1" <?echo $deletegrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_delete_tickets" value="0" <?echo $deletedontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_openclosedtickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_open_closed_tickets==1) {
            $openclosedgrant="checked";
          }
          else {
            $opencloseddontgrant="checked";
          }
          ?>
            <input type="radio" name="u_open_closed_tickets" value="1" <?echo $openclosedgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_open_closed_tickets" value="0" <?echo $opencloseddontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_viewunauthorizedtickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_view_unauthorized_tickets==1) {
            $viewunauthorizedgrant="checked";
          }
          else {
            $viewunauthorizeddontgrant="checked";
          }
          ?>
            <input type="radio" name="u_view_unauthorized_tickets" value="1" <?echo $viewunauthorizedgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_view_unauthorized_tickets" value="0" <?echo $viewunauthorizeddontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_viewdepartmenttickets?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_view_department_tickets==1) {
            $viewdepartmentgrant="checked";
          }
          else {
            $viewdepartmentdontgrant="checked";
          }
          ?>
            <input type="radio" name="u_view_department_tickets" value="1" <?echo $viewdepartmentgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_view_department_tickets" value="0" <?echo $viewdepartmentdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_addrequests?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_add_requests==1) {
            $addrequestsgrant="checked";
          }
          else {
            $addrequestsdontgrant="checked";
          }
          ?>
            <input type="radio" name="u_add_requests" value="1" <?echo $addrequestsgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_add_requests" value="0" <?echo $addrequestsdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_deleterequests?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_delete_requests==1) {
            $deleterequestsgrant="checked";
          }
          else {
            $deleterequestsdontgrant="checked";
          }
          ?>
            <input type="radio" name="u_delete_requests" value="1" <?echo $deleterequestsgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_delete_requests" value="0" <?echo $deleterequestsdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_adddepartments?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_add_departments==1) {
            $adddeptsgrant="checked";
          }
          else {
            $adddeptsdontgrant="checked";
          }
          ?> <input type="radio" name="u_add_departments" value="1" <?echo $adddeptsgrant;?>> </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_add_departments" value="0" <?echo $adddeptsdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <?echo $l_deletedepartments?> 
          </td>
          <td width="25%" valign=center align=center>
          <?
          if ($u_delete_departments==1) {
            $deldeptsgrant="checked";
          }
          else {
            $deldeptsdontgrant="checked";
          }
          ?>
            <input type="radio" name="u_delete_departments" value="1" <?echo $deldeptsgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_delete_departments" value="0" <?echo $deldeptsdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <? echo $l_manageusers?> 
          </td>
          <?
          if ($u_manage_users==1) {
            $manageusersgrant="checked";
          }
          else {
            $manageusersdontgrant="checked";
          }
          ?>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_manage_users" value="1" <?echo $manageusersgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_manage_users" value="0" <?echo $manageusersdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <? echo $l_manageparts?> 
          </td>
          <?
          if ($u_add_parts==1) {
            $addpartsgrant="checked";
          }
          else {
            $addpartsdontgrant="checked";
          }
          ?>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_add_parts" value="1" <?echo $addpartsgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_add_parts" value="0" <?echo $addpartsdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <? echo $l_runreports?> 
          </td>
          <?
          if ($u_generate_reports==1) {
            $generatereportsgrant="checked";
          }
          else {
            $generatereportsdontgrant="checked";
          }
          ?>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_generate_reports" value="1" <?echo $generatereportsgrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_generate_reports" value="0" <?echo $generatereportsdontgrant;?>>
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
             <? echo $l_isamanager?> 
          </td>
          <?
          if ($u_ismanager==1) {
            $ismanagergrant="checked";
          }
          else {
            $ismanagerdontgrant="checked";
          }
          ?>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_ismanager" value="1" <?echo $ismanagergrant;?>>
          </td>
          <td width="25%" valign=center align=center>
            <input type="radio" name="u_ismanager" value="0" <?echo $ismanagerdontgrant;?>>
          </td> </tr>
		<tr><td align="right" colspan="3">
      <input type="submit" value="<?echo $l_next?> >>" name="cmdSelectDepartments">
       </td> </tr>
      </table>
      </td>
  </tr>
</table>
</form>

<?php
  }
  elseif (isset($cmdSelectUser)) {
    // Find out what the selected user has.
    $query = "SELECT * FROM security WHERE s_user='$lstModifyUser';";
    $mysqlresult = query($query);
    $row = mysql_fetch_row($mysqlresult);
    $s_user = $row[0];
    $s_firstname = $row[1];
    $s_lastname = $row[2];
    $s_password = $row[3];
    $s_timestamp_laston = $row[4];
    $s_email = $row[5];
    $u_register_new_tickets = $row[6];
    $u_authorize_tickets = $row[7];
    $u_assign_tickets = $row[8];
    $u_update_tickets = $row[9];
    $u_delete_tickets = $row[10];
    $u_open_closed_tickets = $row[11];
    $u_view_unauthorized_tickets = $row[12];
    $u_view_department_tickets = $row[13];
    $u_add_requests = $row[14];
    $u_delete_requests = $row[15];
    $u_add_departments = $row[16];
    $u_delete_departments = $row[17];
    $u_manage_users = $row[18];
    $u_pref_viewall = $row[19];
    $u_add_parts = $row[20];
    $u_isroot = $row[21];
    $u_pref_viewjobs_first = $row[22];
    $u_generate_reports = $row[23];
    $u_ismanager = $row[24];
    
?>
<form method=POST action="<? echo $g_base_url?>/index.php?whattodo=modifyuser">
<table CELLSPACING=0 CELLPADDING=5 border=1 width=50% align="center">
  <tr>
    <th width="100%" valign=center align=left>
      <? echo $g_title;?> - <? echo $l_modifyuserinformation ?>
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
            <? echo $s_user; ?>
            <input type=hidden name=txtUsername value="<?echo $s_user;?>">
            <input type=hidden name=u_register_new_tickets value="<?echo $u_register_new_tickets;?>">
            <input type=hidden name=u_authorize_tickets value="<?echo $u_authorize_tickets;?>">
            <input type=hidden name=u_assign_tickets value="<?echo $u_assign_tickets;?>">
            <input type=hidden name=u_update_tickets value="<?echo $u_update_tickets;?>">
            <input type=hidden name=u_delete_tickets value="<?echo $u_delete_tickets;?>">
            <input type=hidden name=u_open_closed_tickets value="<?echo $u_open_closed_tickets;?>">
            <input type=hidden name=u_view_unauthorized_tickets value="<?echo $u_view_unauthorized_tickets;?>">
            <input type=hidden name=u_view_department_tickets value="<?echo $u_view_department_tickets;?>">
            <input type=hidden name=u_add_requests value="<?echo $u_add_requests;?>">
            <input type=hidden name=u_delete_requests value="<?echo $u_delete_requests;?>">
            <input type=hidden name=u_add_departments value="<?echo $u_add_departments;?>">
            <input type=hidden name=u_delete_departments value="<?echo $u_delete_departments;?>">
            <input type=hidden name=u_manage_users value="<?echo $u_manage_users;?>">
            <input type=hidden name=u_add_parts value="<?echo $u_add_parts;?>">
            <input type=hidden name=u_generate_reports value="<?echo $u_generate_reports;?>">
            <input type=hidden name=u_ismanager value="<?echo $u_ismanager;?>">
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right><? echo $l_Password ?>
          </td>
          <td width="50%">
            <input type=password name="txtPassword" size=16 value="<? echo $s_password;?>">
         </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right><? echo $l_verifypassword?>
          </td>
          <td width="50%">
            <input type=password name="txtVerifiedPassword" size=16 value="<? echo $s_password;?>">
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
            <? echo $l_firstname?>
          </td>
          <td width="50%">
            <input type=text name="txtFirstname" size=16 value="<? echo $s_firstname;?>">
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
            <? echo $l_lastname?></td>
          <td width="50%">
            <input type=text name="txtLastname" size=16 value="<? echo $s_lastname;?>">
          </td>
        </tr>
        <tr>
          <td width="50%" valign=center align=right>
            <? echo $l_emailaddress?>
          </td>
          <td width="50%">
            <input type=text name="txtEmail" size=20 value="<?echo $s_email;?>">
          </td></tr>
          <tr><td colspan="2" align="right">
      <input type="submit" value="<?echo $l_next?> >>" name="cmdNext">
      </table>
      </td>
  </tr>
</table>
</form>
<?
  }
}
else {
  print "<CENTER><BR><B>$l_error:</B> <I>Add User was selected and you do <B>not</B> \n";
  print "have administrative privileges!</I><BR><BR>\n";
  print "<I>PLEASE CHOOSE ANOTHER CATEGORY</I><BR><BR></CENTER>\n";
}
?>