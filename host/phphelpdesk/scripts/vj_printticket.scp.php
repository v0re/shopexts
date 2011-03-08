<?php  //PHP Helpdesk - View Tickets - Show a Printable Version

include("includes/functions.inc.php");

$query = "SELECT * FROM ticket ";
$query .= "WHERE ticket.t_id='$t_id';";
$mysqlresult = query($query);
$row = mysql_fetch_row($mysqlresult);
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

$query = "SELECT SUM(e_duration) FROM events WHERE t_id='$t_id';";
$mysqlresult = query($query);
$row = mysql_fetch_row($mysqlresult);

$e_duration = $row[0];

?>
<FORM>
<TABLE width=100%>
  <TR>
    <Th>
	  <?echo $g_title;?> - <?echo $l_ticketid?> #<?echo $t_id;?>
	</Th>
  </TR>
  <TR>
    <TD>
      <TABLE CELLSPACING=0 border=1 width=100%>
        <TR>
          <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
            <B><?echo $l_manager?> </B>
          </TD>
          <TD  bgcolor="<? echo $html_alt_color1; ?>" colspan=3>
            <B><? echo "$l_lastname $l_firstname"; ?></B> <? echo $t_userlastname;?>,
           <? echo $t_userfirstname;?> <BR>
            <B><? echo $l_phonenumber?></B> <? echo $t_usertelephone;?>  <BR>
            <B><? echo $l_emailaddress?></B>
            <? echo $t_useremail;?>
          </TD>
        </TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>"  align=right>
		    <B><? echo $l_department?> </B>
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>" >
		    <? echo $t_department;?>
		  </TD>
		  <TD bgcolor="<? print $html_highlight_color; ?>"align=right>
		     <B><?echo $l_networkpropertiescorrect?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
            <input type=textbox width=20>
		  </TD>
		</TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><? echo $l_requests; ?> </B>  
		  </TD>
		  <TD  bgcolor="<? echo $html_alt_color1; ?>">
		    <? echo $t_request;?>
		  </TD>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_antivirus?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
            <input type=textbox width=20>
		  </TD>
		</TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_shortsummary?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
		    <?
		    $t_summary = stripslashes($t_summary);
		    echo $t_summary;
		    ?>
		  </TD>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_runantivirus?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
            <input type=textbox width=20>
		  </TD>
		</TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_detail?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
		    <?
		    $t_detail = stripslashes($t_detail);
		    echo $t_detail;
		    ?>
		  </TD>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_removenonworkrelatedprograms?></B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
            <input type=textbox width=20>
		  </TD>
		</TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_location?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
		    <?
		    $t_location = stripslashes($t_location);
		    echo $t_location;
		    ?>
		  </TD>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_runscandisk?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
            <input type=textbox width=20>
		  </TD>
		</TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_priority?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
		    <?
			if ($t_priority == 0) {
			  print "$l_low";
			}
			elseif ($t_priority == 1) {
			  print "$l_normal";
			}
			elseif ($t_priority == 2) {
			  print "$l_high";
			}
			elseif ($t_priority == 3) {
			  print "$l_urgent";
			}
			?>
		  </TD>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_authorizingsignature?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>">
            <input type=textbox width=20>
		  </TD>
		</TR>
	    <TR>
		  <TD bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_openedby?> </B>  
		  </TD>
		  <TD bgcolor="<? echo $html_alt_color1; ?>" COLSPAN=3>
		    <?echo $t_user;?>
		  </TD>
		</TR>
		<tr>
		  <td bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_time?> </B>  
		  </td>
		  <td bgcolor="<? echo $html_alt_color1; ?>" align=left COLSPAN=3>
		    <?
			$query = "DELETE FROM tmpeid;";
			$mysqlresult = query($query);
			$query = "INSERT INTO tmpeid SELECT MAX(e_id) FROM events GROUP BY t_id;";
			$mysqlresult = query($query);

		    $query = "SELECT events.t_id, events.e_id, SUM(e_duration), e_assignedto ";
			$query .= "FROM ticket, events ";
		    $query .= "WHERE ticket.t_id=events.t_id ";
	        $query .= "AND ticket.t_id='$t_id' ";
	        $query .= "GROUP BY e_assignedto;";
	        $mysql_result = query($query);
			print "<table border=0 width=100%>\n";
			print "<tr>\n";
			print "<td width=20%></td><td width=20%></td><td width=20%></td>\n";
			print "<td align=left>\n";
	        print "<B>$l_technician</B>";
			print "</td>\n";
			print "<td align=right>\n";
	        print "<B>$l_hours</B>";
			print "</td>\n";
			print "</tr>\n";
	        while ($row = mysql_fetch_row($mysql_result)) {
			  if ($row[2] > 0) {
			    print "<tr>\n";
			    print "<td width=20%></td><td width=20%></td><td width=20%></td>\n";
			    print "<td align=left>\n";
			    print "$row[3]";
			    print "</td>\n";
			    print "<td align=right>\n";
			    print "$row[2]";
			    print "</td>\n";
			    print "</tr>\n";
	            $totalhours += $row[2];
			  }
	        }
			print "</tr>\n";
            print "<tr>\n";
			print "<td COLSPAN=5 align=right>\n";
			print "<B><font color=red>$l_totaltime = $totalhours </font> <B>\n";
			print "</td>\n";
            print "</tr>\n";
			print "</table>\n";
	        ?>
	      </td>
	    </tr>
		<? if ($g_include_parts_management == 1) { ?>
		<tr>
		  <td bgcolor="<? print $html_highlight_color; ?>" align=right>
		     <B><?echo $l_parts?></B>  
		  </td>
		  <td bgcolor="<? echo $html_alt_color1; ?>" align=left COLSPAN=3>
		    <?
		    $query = "SELECT t_id, p_id, SUM(p_quantity) FROM ticketparts ";
	        $query .= "WHERE t_id='$t_id' GROUP BY p_id;";
	        $mysql_result = query($query);
			print "<table border=0 width=100%>\n";
		  print "<tr>\n";
		  print "<td align=left>\n";
	          print "<B>$l_quantity</B>";
		  print "</td>\n";
		  print "<td align=left>\n";
	          print "<B>$l_partid</B>";
		  print "</td>\n";
		  print "<td align=left>\n";
	          print "<B>$l_partdescription</B>";
		  print "</td>\n";
		  print "<td align=right>\n";
	          print "<B>$l_unitprice</B>";
		  print "</td>\n";
		  print "<td align=right>\n";
	          print "<B>$l_extendedprice</B>";
		  print "</td>\n";
		  print "</tr>\n";
	        while ($row = mysql_fetch_row($mysql_result)) {
	          $query2 = "SELECT p_id, p_description, p_price FROM parts WHERE p_id='$row[1]'";
	          $mysql_result2 = query($query2);
	          $row2 = mysql_fetch_row($mysql_result2);
		  if ($row[2] > 0) {
			    print "<tr>\n";
			    print "<td align=left>\n";
				print "($row[2])";
				print "</td>\n";
			    print "<td align=left>\n";
			    print "$row2[0]";
			    print "</td>\n";
			    print "<td align=left>\n";
			    print "$row2[1]";
			    print "</td>\n";
			    print "<td align=right>\n";
				$unitprice = $row2[2];
			    print "$$unitprice";
			    print "</td>\n";
			    print "<td align=right>\n";
			    $extendedprice = $row2[2] * $row[2];
				print "$$extendedprice";
			    print "</td>\n";
			    print "</tr>\n";
	            $totalprice += $row[2] * $row2[2];
			  }
	        }
			print "</tr>\n";
            print "<tr>\n";
			print "<td COLSPAN=5 align=right>\n";
			print "<B><font color=red>$l_totalprice = $g_currency $totalprice </font><B>\n";
			print "</td>\n";
            print "</tr>\n";
			print "</table>\n";
	        ?>
	      </td>
	    </tr>
	    <? } ?>
	  </TABLE>
	</TD>
  </TR>
</TABLE>
</FORM>
<TABLE width=100%>
  <TR bgcolor="<? print $html_highlight_color; ?>">
    <TD align=center>
       
        <B><?echo $l_time?></B>
       
    </TD>
    <TD align=center>
       
        <B><?echo $l_event?></B>
       
    </TD>
    <TD align=center>
       
        <B><?echo $l_duration?></B>
       
    </TD>
    <TD align=center>
       
        <B><?echo $l_reassignedto?></B>
       
    </TD>
  </TR> 
<?
$query = "SELECT t_id, e_description, ";
$query .= "e_timestamp, e_duration, s_user, e_status, e_assignedto ";
$query .= "FROM  events ";
$query .= "WHERE t_id='$t_id' ORDER BY e_timestamp;";
$mysqlresult = query($query);
while ($row = mysql_fetch_row($mysqlresult)) {
  print "<TR bgcolor=$html_alt_color1; >\n";
  print "  <TD valign=center>\n";
  $date_var=$row[2];
  $YY = "$date_var[2]$date_var[3]";
  $MM = "$date_var[4]$date_var[5]";
  $DD = "$date_var[6]$date_var[7]";
  $hh = "$date_var[8]$date_var[9]";
  $mm = "$date_var[10]$date_var[11]";
  print "    $MM/$DD/$YY $hh:$mm\n";
  print "  </TD\n>";
  print "  <TD valign=center>\n";
  $thisevent = stripslashes($row[1]);
  print "    $thisevent\n";
  print "  </TD>\n";
  print "  <TD valign=center>\n";
  print "    $row[3]\n";
  print "  </TD>\n";
  print "  <TD valign=center>\n";
  print "    $row[6]\n";
  print "  </TD>\n";
  print "</TR>\n";
}
?>
</TABLE>
</CENTER>
