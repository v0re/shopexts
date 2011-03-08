<?PHP
				$assigned=0;
				// build eMail.
            $readable_date = $current_date[2].$current_date[3]."/".$current_date[4].$current_date[5]."/".$current_date[0].$current_date[1];
            $readable_time = $current_date[6].$current_date[7].":".$current_date[8].$current_date[9];
            $mailto = "$txtUserEmail";
            //$mailsubject = "$g_title - $l_wehavereceivedyourrequest";
            $mailbody = "$txtUserFirstName,\n\n";
            $mailbody .= "$l_belowisacopyoftheticket  $l_ithasbeenadded\n";
            $mailbody .= "\n\n------------------------------------------------------------------------\n";
            if ($g_dept_or_comp == 0) {
                        $mailbody .= "$l_department:  $lstdepartment\n";
            }
             else {
                         $mailbody .= "$t_department:  $Company\n";
            }
            $mailbody .= "$l_request: $lstRequest\n";
            $mailbody .= "$l_summary:  $txtSummary\n";
            $mailbody .= "$l_detail:   $txtDetail\n";
            $mailbody .= "$l_proposed_solution:  $txtProposedSolution\n";
            $mailbody .= "$l_num_personal:   $txtStaffid\n";
            $mailbody .= "$l_computerid   $txtComputerid\n";
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
				//get the information for the additiona eMails from the database.
				// get the information about the assignment, add event, send eMail
				$assign_query = "SELECT s_email, s_user, r_assignto, s_firstname, ";
				$assign_query .= "s_lastname FROM security, request ";
				$assign_query .= "WHERE s_user = r_assignto ";
				$assign_query .= "AND r_name = '$lstRequest';";
            $mysql_result = query($assign_query);
            $assign_row = mysql_fetch_row($mysql_result);
            if(($assign_row[2]!="none") && (mysql_affected_rows()!=0)){
            	// Add an event for the ticketm assigninf it to the user defines in "notifications";
					$query = "INSERT INTO events ";
					$query .= "(t_id, e_description, s_user, e_status, e_assignedto) ";
               		$query .= "VALUES ('$t_id', '$l_ticketregisterd', ";
               $query .= "'wizzard', 'OPEN', '$assign_row[1]');";
               $mysql_result = query($query);
               if ($mysql_result) {
								$assigned=1;
                        }
                        else {
                        print "<center>$l_error $l_jobnotadded<BR></center>";
                        print "MySQL Error is:  ".mysql_errno() . " " . mysql_error();
                        }
					// finish add event for the person wo whom the ticket is assigned
					//send mail to person to whom the ticket is assigned
               $mailto = $mailto_assigned;
               $mailsubject = "Responsable: $g_title - $l_summary: $txtSummary - $l_priority: $optPriority";
               //$mailbody .= "$txtSummary"; use first eMail body
               $mailheader = "From: $user@$g_mailservername";
					$assignedto = $assign_row[0];
               // this mail goes to the technican;
               mail($assignedto, $l_eMailsubject_technican, $mailbody, $mailheader);
             } // end send mail to person to whom the ticket is assigned
	           // end the else part where the ticket is assigned and the eMail send.
				if ($assigned==0) { // add an event that the ticket is not assigned
            $query = "INSERT INTO events ";
            $query .= "(t_id, e_description, s_user, e_status, e_assignedto) ";
            $query .= "VALUES (LAST_INSERT_ID(), '$l_ticketregisterd', "; //changed cheitkamp ($l_ticketregisterd)
            $query .= "'$user', 'REGISTERED', '$l_notassigned');";
            $mysql_result = query($query);
            } // finish not assigned event 
				// now we send the eMails
				// get the information for the additional eMails
				$notify_query = "SELECT s_email, d_email_notification1 FROM security, department ";
				$notify_query .= "WHERE s_user = d_email_notification1 ";
				$notify_query .= "AND d_name = '$lstdepartment';";
            $mysql_result = query($notify_query);
            $row = mysql_fetch_row($mysql_result);
            if ($row[1]!="none") {
                   // this eMail goes to the addiotnal notification I
                   mail($row[0], $l_eMailsubject_addtional_notification, $mailbody, $mailheader);
            	}
				$notify_query = "SELECT s_email, d_email_notification1 FROM security, department ";
				$notify_query .= "WHERE s_user = d_email_notification1 ";
				$notify_query .= "AND d_name = '$lstdepartment';";
            $mysql_result = query($notify_query);
            $row = mysql_fetch_row($mysql_result);
            if ($row[1]!="none") {
                   // this eMail goes to the addiotnal notification II
                   mail($row[0], $l_eMailsubject_addtional_notification, $mailbody, $mailheader);
            	}
 			// send the user eMail
            if (!empty($txtUserEmail)) {
					$mailto = "$txtUserEmail";
					// this EMail goes now to the reporting user in case he/she specified an eMail address
					mail($mailto, $l_eMailsubject_reporting_user, $mailbody, $mailheader);
            }
			// now print the infromation what has een done on the screen.
         print "<b><h3><center>$l_yourservicerequest<BR></h3></b><br>\n";
         print "<b>$l_atechnician</center></b>\n";
        	if ($assigned == 1){
		        	print "<center>$l_ticketresponsible $assign_row[3], $assign_row[4]<BR></center>\n";
		        	print "<center>$l_emailresponsbile $assign_row[0]<BR></center>\n";
					print "<center>$l_mailwassent $assignedto<BR><BR></center>\n";
					}
		  if (!empty($txtUserEmail)) 	{
		  print "<center>$l_areceiptwassent $txtUserEmail<BR></center>\n";}
      	print "<center><i>$l_numticket $et_id<BR></i></center>\n";  
?>