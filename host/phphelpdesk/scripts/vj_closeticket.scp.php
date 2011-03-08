<?php // PHP Helpdesk - View Tickets - Close Ticket

include("includes/functions.inc.php");

if ($s_update_tickets == 1) {

  $query = "INSERT INTO events ";
  $query .= "(t_id, e_description, e_duration, s_user, e_status, e_assignedto) ";
  $query .= "VALUES ('$t_id', 'Ticket CLOSED', '0.00', '$s_user', 'CLOSED', '$user');";
  $mysqlresult = query($query);
  if ($mysqlresult) {
    $current_date = date("YmdHis");
    $query = "UPDATE ticket SET t_timestamp_closed='$current_date' ";
    $query .= "WHERE t_id='$t_id';";
    $mysqlresult = query($query);
    if ($mysqlresult) {
      // Email the contacts email address
      if (!empty($t_useremail)) {

	$readable_date = $current_date[4].$current_date[5]."/".$current_date[6].$current_date[7]."/".$current_date[2].$current_date[3];
	$readable_time = $current_date[8].$current_date[9].":".$current_date[10].$current_date[11];
            
	$mailto = "$t_useremail"; 
        $mailsubject = "$g_title - Ticket #$t_id has been closed!";
        $mailbody = "$t_userfirstname,\n\n";
        $mailbody .= "Below is a copy of the original ticket information that you submitted.  The ";
	$mailbody .= "problem has been fixed.  If you are not satisfied with the resolution, please ";
	$mailbody .= "contact your supervisor.";
        $mailbody .= "\n\n------------------------------------------------------------------------\n";
        $mailbody .= "Company:  $lstDepartment\n";
        $mailbody .= "Category: $lstCategory\n";
        $mailbody .= "Summary:  $txtSummary\n";
        $mailbody .= "Detail:   $txtDetail\n";
        $mailbody .= "Location: $txtLocation\n";
        $mailbody .= "\n";
        $mailbody .= "Completed on ".$readable_date." ".$readable_time." by \"$user.\"";
        $mailbody .= "\n------------------------------------------------------------------------";
        $mailheader = "From: $user@$g_mailservername";

        $mailbody = stripslashes($mailbody);
        mail($mailto, $mailsubject, $mailbody, $mailheader);
        print "<center>A receipt was sent to $t_userfirstname $t_userlastname<BR></center>\n";

      }

    }
    else {
      print "<BR>Changes to ticket table was NOT successful";
      print "<BR>Mysql Error is: ". mysql_error($mysqlresult);
    }
  }
  else {
    print "<BR>Changes to events were NOT successful";
    print "<BR>Mysql Error is: ". mysql_error($mysqlresult);
  }

}
?>
