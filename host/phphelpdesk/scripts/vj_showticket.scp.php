<?php //PHP Helpdesk - View Tickets - Show Ticket
include("includes/functions.inc.php");
    $query = "SELECT * from ticket ";
    $query .= "WHERE ticket.t_id='$t_id';";
    $mysql_result = query($query);
    $row = mysql_fetch_row($mysql_result);
    $t_id = $row[0];
    $t_request = $row[1]; 
    $t_detail = $row[2];
    $t_priority = $row[3];
    $t_user = $row[4];
    $t_timestamp_opened = $row[5];
    $t_timestamp_closed = $row[6];
    $t_department = $row[7];
    $t_location = $row[8];
    $t_summary = $row[9];
    $t_userfirstname = $row[10];
    $t_userlastname = $row[11];
    $t_usertelephone = $row[12];
    $t_useremail = $row[13];
    $t_computerid = $row[14];
    $t_staffid = $row[15]; //cheitkamp
    $t_proposedsolution = $row[16]; //cheitkamp
    $t_et_id = $row[17]; //cheitkamp
    // Display the form with the department and catogies for only that department ?>	  
    <form method=POST action="<? echo $g_base_url;?>/index.php?whattodo=viewjobs">
    <table CELLSPACING=0 CELLPADDING=5 border=1 width="80%" align="center">
      <tr>
        <th width="100%" valign=top align=left>
          <? echo $g_title;?> - <? echo $l_viewjobdetailsform?> <? echo $l_ticketid?> #<? echo $t_et_id;?>
        </th>
      </tr>
      <tr>
        <td width="100%" align=left>
          <table CELLSPACING=0 CELLPADDING=5 border=0 width="100%">
          <tr>
            <td width="25%" valign=middle align=right>
    <?
    for ($i=0; $i < sizeof($lstChooseCompany);$i++) {
      print "<input type=hidden name=lstChooseCompany[$i] value=$lstChooseCompany[$i]>\n";
    }
    ?>
            <input type=hidden name=t_id value="<? echo $t_id;?>">
	    </td>
            <td width="75%" valign=middle>
	    </td>
	  </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><? echo $l_firstname?></b> 
          </td>
          <td width="75%" valign=middle>
	    <?
	      $t_userfirstname = stripslashes($t_userfirstname);
	      echo $t_userfirstname;
	    ?>
            <input type=hidden name=t_userfirstname value="<? echo $t_userfirstname;?>">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><? echo $l_lastname?></b>&nbsp;&nbsp;&nbsp;&nbsp; 
	    <?
	      $t_userlastname = stripslashes($t_userlastname);
	      echo $t_userlastname;
	    ?>
            <input type=hidden name=t_userlastname value="<? echo $t_userlastname;?>">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><? echo $l_telephonenumberextension?></b> 
          </td>
          <td width="75%" valign=middle>
	    <?
	      $t_usertelephone = stripslashes($t_usertelephone);
	      echo $t_usertelephone;
	    ?>
            <input type=hidden name=t_usertelephone value="<? echo $t_usertelephone;?>">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><? echo $l_emailaddress?></b>&nbsp;&nbsp;&nbsp;&nbsp; 
	    <?
	      $t_useremail = stripslashes($t_useremail);
	      echo $t_useremail;
	    ?>
            <input type=hidden name=t_useremail value="<? echo $t_useremail;?>">
          </td>
        </tr>
          <tr>
          <td width="25%" valign=middle align=right>
            <b>
<?
if ($g_dept_or_comp== 0) {
	echo $l_department;
}
else {
	echo $l_company;
}
?>
	   </b>
          </td>
          <td width="75%" valign=middle>
            <select size=1 name="lstDepartment">
    <?
    for ($i=0; $i < sizeof($departments);$i++) {
      if ($departments[$i] == $t_department) {
        print "                  <option value=\"$departments[$i]\" selected>$departments[$i]</option>\n";
      }
      else {
        print "                  <option value=\"$departments[$i]\">$departments[$i]</option>\n";
      }
    }
    ?>
            </select>         
          </td>
        </tr>
        <tr>
        	<td colspan = "2"> 
             <input type=hidden name=t_staffid value="<? echo $t_staffid;?>">
            &nbsp;&nbsp;&nbsp; <? echo "<b>$l_num_personal:</b>&nbsp;&nbsp;$t_staffid"; ?>
			</td>
		  </tr>	
        <tr>
          <td width="25%" valign=middle align=right>
          <b><? echo $l_request?></b> 
          </td>
          <td width="75%" valign=middle>
            <select size=1 name="lstRequest">
    <?
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from categories into the listbox
    $query = "SELECT r_name FROM request WHERE r_department='$t_department'"
             . " ORDER BY r_name;";
    $mysql_result = query($query);
    while ($row = mysql_fetch_row($mysql_result)) {
      if ($row[0] == $t_request) {
        print "                  <option value=\"$row[0]\" selected>$row[0]</option>\n";
      }
      else {
        print "                  <option value=\"$row[0]\">$row[0]</option>\n";
      }
    }
    ?>
            </select>
          <b><? echo $l_computerid?></b> 
	    <? $t_computerid = stripslashes($t_computerid);?>
            <input type=text size=15 name="txtComputerID" value="<? echo $t_computerid;?>">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><? echo $l_shortsummary?></b> 
          </td>
          <td width="75%" valign=middle>
	    <?$t_summary = stripslashes($t_summary);?>
            <input type=text size=49 name="txtSummary" value="<?echo $t_summary;?>">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=top align=right>
            <b><? echo $l_detail?></b>
          </td>
          <td width="75%">
	    <? $t_detail = stripslashes($t_detail);?>
            <textarea rows=6 name="txtDetail" cols=38 wrap><? echo $t_detail;?></textarea>
          </td>
        </tr>
        <tr>
          <td width="25%" valign=top align=right>
            <b><? echo $l_proposed_solution?></b>
          </td>
          <td width="75%">
            <? $t_proposedsolution = stripslashes($t_proposedsolution);?>
            <textarea rows=7 name="txtProposedSolution" cols=38 wrap><? echo $t_proposedsolution;?></textarea>
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><? echo $l_location?></b> 
          </td>
          <td width="75%" valign=middle>
	    <? $t_location = stripslashes($t_location);?>
            <input type=text size=20 name="txtLocation" value="<? echo $t_location;?>">
          </td>
        </tr>
        <tr>
          <td width="25%" valign=middle align=right>
            <b><? echo $l_priority?></b> 
          </td>
          <td width="75%" valign=middle>
            <table border=0 width="100%" cellspacing=0 cellpadding=0>
              <tr>
                <td width="25%" bgcolor="<?echo $html_priority_low;?>">
		<?if ($t_priority=="0") {?>
                  <input type=radio value="0" name="optPriority" checked><b><? echo $l_low?></b>
		<?} else {?>
                  <input type=radio value="0" name="optPriority"><b><? echo $l_low?></b>
		<?}?>
	        </td>
                <td width="25%" bgcolor="<?echo $html_priority_normal;?>">
		<? if ($t_priority=="1") {?>
                  <input type=radio value="1" name="optPriority" checked><b><? echo $l_normal?></b>
		<?} else {?>
                  <input type=radio value="1" name="optPriority"><b><? echo $l_normal?></b>
		<?}?>
	        </td>
                <td width="25%" bgcolor="<?echo $html_priority_high;?>">
		<? if ($t_priority=="2") {?>
                  <input type=radio value="2" name="optPriority" checked><b><? echo $l_high?></b>
		<? } else {?>
                  <input type=radio value="2" name="optPriority"><b><? echo $l_high?></b>
		<? }?>
	        </td>
                <td width="25%" bgcolor="<?echo $html_priority_urgent;?>">
		<? if ($t_priority=="3") {?>
                  <input type=radio value="3" name="optPriority" checked><b><? echo $l_urgent?></b>
		<? } else { ?>
                  <input type=radio value="3" name="optPriority"><b><? echo $l_urgent?></b>
		<?}?>
	        </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td width="15%" valign=middle align=right>
            <b><? echo $l_openedby?></b>
          </td>
          <td width="75%" valign=middle>
	    <? echo $t_user;?>
	  </td>
	</tr>
        <tr>
          <td width="15%" valign=middle align=right>
            <b><? echo $l_totaltime?></b>
          </td>
          <td width="75%" valign=middle>
      <?
      ///////////////////////////////////////////////////////////////////////////
      // query the database and input security names from your dept. into the listbox
	  $query = "SELECT SUM(e_duration) FROM events WHERE t_id='$t_id';";
      $mysql_result = query($query);
      $row = mysql_fetch_row($mysql_result);
	  print "<B><FONT color=#FF0000>$row[0] $l_hours</FONT></B>";
      ?>
          </td>
        </tr>

		<?php if ($g_include_parts_management == 1) { 
		// add this if part mmt is desired?>
        <tr>
          <td width="15%" valign=middle align=right>
            <b><? echo $l_priceforparts?></b>
          </td>
          <td width="75%" valign=middle>
      <?
      ///////////////////////////////////////////////////////////////////////////
      // query the database and sum the quantity of the parts put into the events.
	  $query = "SELECT t_id, p_id, SUM(p_quantity) FROM ticketparts ";
	  $query .= "WHERE t_id='$t_id' GROUP BY p_id;";
      $mysql_result = query($query);
      while ($row = mysql_fetch_row($mysql_result)) {
	    $query2 = "SELECT p_price FROM parts WHERE p_id='$row[1]'";
		$mysql_result2 = query($query2);
		$row2 = mysql_fetch_row($mysql_result2);
			  $totalprice += $row[2] * $row2[0];
	  }
	    print "<B><FONT color=#FF0000>$g_currency $totalprice</FONT></B>";
      ?>
          </td>
        </tr>
       <? } // end this part if the part mmt part is desired ?>
      </table>
      <? if ($s_update_tickets) {?>
        <input type=submit value="<? echo $l_savechanges?>" name="cmdSaveChanges">
        <input type=submit value="<? echo $l_closeticket?>" name="cmdCloseTicket">
      <? }?>
        <input type=submit value="<? echo $l_showprintableversion?>" name="cmdPrintTicket">
      <? if ($s_delete_tickets) {?>
       <input type=submit value="<?php echo $l_deleteticket;//cheitkamp ?>" name="cmdDeleteTicket">
      <? }?>
    </td>
  </tr>
</table>
<BR><BR>
<TABLE width=80%>
  <TR>
    <TH align=center><? echo $l_time?></TH>
    <TH align=center><? echo $l_event?></TH>
    <TH align=center><? echo $l_duration?></TH>
    <TH align=center><? echo $l_reassignedto?></TH>
  </TR>
  <?
  $query = "SELECT t_id, e_description, ";
  $query .= "e_timestamp, e_duration, s_user, e_status, e_assignedto ";
  $query .= "FROM  events ";
  $query .= "WHERE t_id='$t_id' ORDER BY e_timestamp;";
  $mysqlresult = query($query);
  while ($row = mysql_fetch_row($mysqlresult)) {
    print "<TR bgcolor=#eeeeee>\n";
    print "  <TD valign=center align=center>\n";
    $date_var=$row[2];
    $YY = "$date_var[2]$date_var[3]";
    $MM = "$date_var[4]$date_var[5]";
    $DD = "$date_var[6]$date_var[7]";
    $hh = "$date_var[8]$date_var[9]";
    $mm = "$date_var[10]$date_var[11]";
    print "    $MM/$DD/$YY &nbsp;&nbsp;&nbsp; $hh:$mm\n"; 
    print "  </TD>\n";
    print "  <TD valign=center>\n";
    $thisevent = stripslashes($row[1]);
    print "$thisevent\n";
    print "  </TD>\n";
    print "  <TD valign=center>\n";
    print "    $row[3]\n";
    print "  </TD>\n";
    print "  <TD valign=center>\n";
    print "    $row[6]\n";
    print "  </TD>\n";
    print "</TR>\n";
  }
  if ($s_update_tickets=="1") {?>
    <form method=POST action="<? echo $g_base_url;?>/index.php?whattodo=viewjobs">
    <TR bgcolor=#eeeeee>
    <TD valign=center align=center>
    <?
    $date_var= date("YmdHis");
    $YY = "$date_var[2]$date_var[3]";
    $MM = "$date_var[4]$date_var[5]";
    $DD = "$date_var[6]$date_var[7]";
    $hh = "$date_var[8]$date_var[9]";
    $mm = "$date_var[10]$date_var[11]";
    print "    $MM/$DD/$YY &nbsp;&nbsp;&nbsp; $hh:$mm\n"; 
    ?>
    </TD>
    <TD align=center>
		<?php if ($g_include_parts_management == 1) { 
		// add this if part mmt is desired?>		
      <input type=textbox size=2 name=txtQuantity>
      <select name=lstParts>
	    <option selected value="noparts"><?echo $l_selectpartsfromthislist?></option>
        <?php
        $query = "SELECT p_id, p_description FROM parts ";
		$query .= "ORDER BY p_id;";
        $mysqlresult = query($query);
        while ($row = mysql_fetch_row($mysqlresult)) {
          print "        <option value=\"$row[0]\">$row[0] &nbsp;&nbsp;&nbsp;$row[1]</option>\n";
        }
        ?>
      </select><BR>
      <? } // end of the part if part mmt is desired ?>
      <textarea name=txtDescription rows=3 cols=28 wrap></textarea>
    </TD>
    <TD align=center>
      <input type=text name=txtDuration size=6 value="000.00">
    </TD>
    <TD valign=center align=center>
      <select name=lstAssignedto> 
    <?
    if ($s_assign_tickets == "1") {
      $query = "SELECT s_user FROM userdepartments ";
      $query .= "WHERE d_name='$t_department';";
      $mysqlresult = query($query);
      while ($row = mysql_fetch_row($mysqlresult)) {
		if ($row[0] == $user) {
          print "<option value=\"$row[0]\" selected>$row[0]</option>\n";
		}
		else {
          print "<option value=\"$row[0]\">$row[0]</option>\n";
		}
      }
    }
    else {
      print "<option value=\"$row[7]\">$row[7]</option>\n";
    }
    ?>
      </select>
    </TD>
    <TD>
	  <input type=hidden value="<?echo $t_id;?>" name="t_id">
      <input type=submit value="<?echo $l_addevent?>" name="cmdAddEvent">
    </TD>
    </TR>
    </FORM>
  <? }  ?>
  
</TABLE><br><br>


