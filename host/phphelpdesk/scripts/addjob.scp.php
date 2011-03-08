<?php	//view jobs script

include("includes/functions.inc.php");

if (isset($cmdAddjob) && ($txtSummary != "")) {

  $current_date = date("ymdHi");		//set timestamp

  $txtDetail1 = addslashes($txtDetail);
  $txtSummary1 = addslashes($txtSummary);
  $txtLocation1 = addslashes($txtLocation);
  $txtUserFirstName1 = addslashes($txtUserFirstName);
  $txtUserLastName1 = addslashes($txtUserLastName);
  $txtUserTelephone1 = addslashes($txtUserTelephone);
  $txtUserEmail1 = addslashes($txtUserEmail);
  $txtcomputerid1 = addslashes($txtcomputerid);
  $txtstaffid1 = addslashes($txtstaffid);
  $txtproposedsolution1 = addslashes($txtproposedsolution);

  // Insert ticked into the tickets table
  $et_id = getnextticketid($Company); //(get extended ticket id);
  $query = "INSERT INTO ticket";
  $query .= " (t_id, t_category, t_detail, t_priority, t_user, ";
  $query .= "t_timestamp_opened, t_department, t_location, t_summary, ";
  $query .= "t_userfirstname, t_userlastname, t_usertelephone, t_useremail, ";
  $query .= "t_computerid, t_staffid, t_proposedsolution, t_et_id)";
  $query .= " VALUES (NULL, '$lstCategory', '$txtDetail1', '$optPriority', '$user', ";
  $query .= "'$current_date', '$Company', '$txtLocation1', '$txtSummary1', ";
  $query .= "'$txtUserFirstName1', '$txtUserLastName1', '$txtUserTelephone1', '$txtUserEmail1', ";
  $query .= "'$txtcomputerid', '$txtstaffid', '$txtproposedsolution', '$et_id');";
  $mysql_result = query($query);

  if ($mysql_result) {
      
    $readable_date = $current_date[2].$current_date[3]."/".$current_date[4].$current_date[5]."/".$current_date[0].$current_date[1];
    $readable_time = $current_date[6].$current_date[7].":".$current_date[8].$current_date[9];

    $mailto = "$txtUserEmail"; 
    $mailsubject = "$g_title - $l_wehavereceivedyourrequest";
    $mailbody = "$txtUserFirstName,\n\n";
    $mailbody .= "$l_belowisacopyoftheticket  $l_ithasbeenadded\n";
    $mailbody .= "\n\n------------------------------------------------------------------------\n";
    if ($g_dept_or_comp == 0) {
      $mailbody .= "$l_department:  $Company\n";
    }
    else {
      $mailbody .= "$l_company:  $Company\n";
    }
    $mailbody .= "$l_category: $lstCategory\n";
    $mailbody .= "$l_summary:  $txtSummary\n";
    $mailbody .= "$l_detail:   $txtDetail\n";
    $mailbody .= "$l_location $txtLocation\n";
    $mailbody .= "\n";
    $mailbody .= "$l_senton ".$readable_date." ".$readable_time." $l_usingusername \"$user.\"";
    $mailbody .= "\n------------------------------------------------------------------------";
    if ($g_domainmailfrom == "") {
      $mailheader = "From: $user@$g_mailservername";
    }
    else {
      $mailheader = "From: $user@$g_domainmailfrom";
    }

    $mailbody = stripslashes($mailbody);
    if (!empty($txtUserEmail)) {
      mail($mailto, $mailsubject, $mailbody, $mailheader);
    }
    if ($s_email != NULL) {
      mail($s_email, $mailsubject, $mailbody, $mailheader);
    }
    print "<center>$l_areceiptwassent $txtUserFirstName $txtUserLastName<BR></center>\n";

    print "<center>$l_yourservicerequest<BR>\n";
	print "$l_atechnician</center>\n";
    // Add an event for this ticket
    if (isset($lstAssignedto)) {
      $query = "INSERT INTO events ";
      $query .= "(t_id, e_description, s_user, e_status, e_assignedto) ";
      $query .= "VALUES (LAST_INSERT_ID(), 'Ticket Authorized', ";
      $query .= "'$user', 'OPEN', '$lstAssignedto');";
      $mysql_result = query($query);
      if ($mysql_result) {
        print "<center>$l_eventwasadded<BR></center>";
      }
      else { 
        print "<center>$l_error $l_jobnotadded<BR></center>";
	print "MySQL Error is:  ".mysql_errno() . " " . mysql_error();
      }
    }
    else {
      $query = "INSERT INTO events ";
      $query .= "(t_id, e_description, s_user, e_status, e_assignedto) ";
      $query .= "VALUES (last_insert_id(), 'Ticket Registered', ";
      $query .= "'$user', 'REGISTERED', 'Not Assigned');";
      $mysql_result = query($query);
    }
    if ($optMailuser == "yes") {		//Mail User

      $queryemail = "SELECT s_email FROM security ";
      $queryemail .= "WHERE s_user='$lstAssignedto';";
      $mysql_result = query($queryemail);
      $row = mysql_fetch_row($mysql_result);

      $mailto = "$row[0]"; 
      $mailsubject = "$g_title - $l_summary: $txtSummary - $l_priority: $optPriority";
      $mailbody = "$txtSummary";
      $mailbody .= "\n\n";
      $mailbody .= "$txtDetail";
      $mailbody .= "\n\n";
      $mailbody .= "$l_senton $current_date $l_from $user";
      $mailheader = "From: $user@$g_mailservername";

      mail($mailto, $mailsubject, $mailbody, $mailheader);
      print "<center>$l_mailwassent $lstAssignedto<BR></center>\n";
 
    }
    print "<BR>\n";
  }
  else {
    print "<B>$l_error:</B>";
    print " $l_jobnotadded<br>\n";
    print "<BR>\n";
  }          
}
else {
  // If the user logged in with a username that has only one department for their departments
  // array in includes/permissions.inc.php, or they have already chosen a department from their
  // list, then only show the categories for that department.
  if (sizeof($departments) < 2) {
    $lstChooseCompany = $departments[0];
  }
  if ((sizeof($departments) < 2) || ($department_already_chosen == 1)) { 
    // Display the form with the department and catogies for only that department ?>	  
    <form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=addjob">
    <div align=center>
    <center>
    <table BGCOLOR="<?echo $html_table_bgcolor;?>" BORDERCOLOR="<?echo $html_table_bordercolor;?>"
           BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>" BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
           CELLSPACING=0 CELLPADDING=5 border=1 width="80%">
      <tr>
        <td width="100%" valign=top bgcolor="#000080" align=left>
          <p align=left><font color="#FFFFFF"><b><?echo $g_title;?> - <?echo $l_addjobform?></b></font>
        </td>
      </tr>
      <tr>
        <td width="100%" align=left>
          <table BACKGROUND="<?echo $i_table_background;?>" BGCOLOR="<?echo $html_table_bgcolor;?>"
                 BORDERCOLOR="<?echo $html_table_bordercolor;?>" BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
                 BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>" CELLSPACING=0 CELLPADDING=5 border=0 width="100%">
          <tr>
            <td width="100%" valign=middle align=center colspan=3>
	      <font size=5>
	        <i><?echo $l_itisimportant?></i>
              </font>
	    </td>
	  </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><?echo $l_firstname?></b>
          </td>
          <td width="75%" valign=middle>
            <input type=text size=15 name="txtUserFirstName">
            &nbsp;&nbsp;&nbsp;&nbsp;<b><?echo $l_lastname?></b>
            <input type=text size=28 name="txtUserLastName">
          </td>
	</tr>
	<tr>
          <td width="25%" valign=middle align=right>
            <b><?echo $l_telephonenumberextension?></b>
          </td>
          <td width="75%" valign=middle>
            <input type=text size=15 name="txtUserTelephone">
            &nbsp;&nbsp;&nbsp;&nbsp;<b><?echo $l_emailaddress?></b>
            <input type=text size=25 name="txtUserEmail">
          </td>
        </tr>
                <tr>
        	<td colspan = "2"> <?// added this part (4 lines) (cheitkamp) ?>
            <? echo "<b>$l_num_personal:</b>"; ?> &nbsp;&nbsp 
            &nbsp;&nbsp;&nbsp;&nbsp; <input type=text size=15 name="txtstaffid">
			</td>
		  </tr>	
	<tr>
          <td width="25%" valign=middle align=right>
            <b><?
		if($g_dept_or_comp == 0) {
		   echo $l_department;
		}
		else {
		   echo $l_company;
		}
               ?>:</b>
          </td>
          <td width="75%" valign=middle>
	    <?echo $lstChooseCompany;?>
	    <input type=hidden name="Company" value="<?echo $lstChooseCompany;?>">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
          <b><?echo $l_category?>: </b> 
          </td>
          <td width="75%" valign=middle>
            <select size=1 name="lstCategory">
    <?
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from categories into the listbox
    $query = "SELECT c_name FROM category WHERE c_department=" .
             "'$lstChooseCompany' ORDER BY c_name;";
    $mysql_result = query($query);
    while ($row = mysql_fetch_row($mysql_result)) {
      if ($row[0] == "Other" || $row[0] == "General") {
        print "                  <option value=\"$row[0]\" selected>$row[0]</option>\n";
      }
      else {
        print "                  <option value=\"$row[0]\">$row[0]</option>\n";
      }
    }
    ?>
            </select>
			<? //added by cheitkamp ?>	
           &nbsp; &nbsp;&nbsp; <b><?echo $l_computerid; ?></b> 
           &nbsp; &nbsp;&nbsp; <input type=text size=10 name="txtcomputerid">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><?echo $l_shortsummary?></b> 
          </td>
          <td width="75%" valign=middle>
            <input type=text size=45 name="txtSummary">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=top align=right>
            <b><?echo $l_detail?></b>
          </td>
          <td width="75%">
            <textarea rows=5 name="txtDetail" cols=40 wrap></textarea>
          </td>
        </tr>
        <tr> 
          <td width="25%" valign=top align=right>
            <b><?echo $l_proposed_solution?></b>
          </td>
          <td width="75%">
            <? 
            /* Next textarea used to be rows=5 and cols=43 */ 
            // added this part (cheitkamp) ?>
            <textarea rows=7 name="txtproposedsolution" cols=48 wrap></textarea>
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><?echo $l_location?></b> 
          </td>
          <td width="75%" valign=middle>
            <input type=text size=20 name="txtLocation">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><?echo $l_priority?></b> 
          </td>
          <td width="75%" valign=middle>
            <table border=0 width="100%" cellspacing=0 cellpadding=0>
              <tr>
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
	        </td>
              </tr>
            </table>
          </td>
        </tr>
	<?if (($s_authorize_tickets == 1) && ($s_assign_tickets == 1)) {?>
        <tr>
          <td width="15%" valign=middle align=right>
            <b><?echo $l_assignedto?></b>
          </td>
          <td width="75%" valign=middle>
	    <select size=1 name="lstAssignedto">
      
      <?
      ///////////////////////////////////////////////////////////////////////////
      // query the database and input security names from your dept. into the listbox
      $query = "SELECT userdepartments.s_user, security.s_update_tickets ";
      $query .= "FROM userdepartments, security ";
      $query .= "WHERE userdepartments.d_name='$lstChooseCompany' ";
      $query .= "AND security.s_update_tickets='1'";
      $query .= "AND security.s_user = userdepartments.s_user";
      $mysql_result = query($query);
      while ($row = mysql_fetch_row($mysql_result)) {
	if ($row[0] == "authorized") {
          print "                  <option value=\"$row[0]\" selected>$row[0]</option>\n";
	}
	else {
          print "                  <option value=\"$row[0]\">$row[0]</option>\n";
	}
      }
      ?>
            </select>
	    <B> &nbsp;&nbsp;<?echo $l_mailto?> </B> &nbsp;&nbsp;
            <input type=radio value="yes" name="optMailuser"><b><?echo $l_yes?> </b>&nbsp;
            <input type=radio checked value="no" name="optMailuser"><b><?echo $l_no?> </b>
          </td>
        </tr>
	<?}?>
      </table>
      <p align=center><center><input type=submit value="<?echo $l_addjob?>" name="cmdAddjob">
      <input type=reset value="<?echo $l_clearform?>" name="cmdClear"></center>
    </td>
  </tr>
</table>
</center>
</div>
</form>

<?
  }
  else {
    ///////////////////////////////////////////////////////////////////////////
    // Display the form with all the departments that are available to the current user
    ?>
    <form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=addjob&department_already_chosen=1">
    <div align=center>
    <center>
    <table BGCOLOR="<?echo $html_table_bgcolor;?>" BORDERCOLOR="<?echo $html_table_bordercolor;?>"
           BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>" BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
           CELLSPACING=0 CELLPADDING=5 border=1 width="60%">
      <tr>
        <td width="100%" valign=top bgcolor="#000080" align=left>
          <p align=left><font color="#FFFFFF"><b><?echo $g_title;?> - <? if($g_dept_or_comp == 0) {echo $l_choosedepartment;} else { echo $l_choosecompany; } ?></b></font>
        </td>
      </tr>
      <tr>
        <td width="100%" align=left>
          <table BACKGROUND="<?echo $i_table_background;?>" BGCOLOR="<?echo $html_table_bgcolor;?>"
                 BORDERCOLOR="<?echo $html_table_bordercolor;?>" BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
                 BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>" CELLSPACING=0 CELLPADDING=5 border=0 width="100%">
          <tr>
          <td width="30%" valign=middle align=right>
            <b>
<?
if ($g_dept_or_comp==0) {
	echo "$l_department:";
}
else {
	echo "$l_company:";
}
?>
	</b> 
          </td>
          <td width="40%" valign=middle>
            <center><select size=1 name="lstChooseCompany">
    <?
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from companies that this
    // user has access to into the listbox
    $query = "SELECT d_name FROM userdepartments WHERE s_user='$user';";
    $mysql_result = query($query);
    for ($i=0;$i<sizeof($departments);$i++) {
      print "              <option value=\"$departments[$i]\">$departments[$i]</option>\n";
    }
    ?>
            </select></center>
	  </td>
          <td width="3330%" valign=middle>
	    <p align=center><input type=submit value="<?echo $l_continue?>" name="cmdContinue">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?
  }
}
?>
