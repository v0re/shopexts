<?php // PHP Helpdesk - View Tickets - Add Event
include("includes/functions.inc.php");
if ($lstParts != "noparts") {              
  if ($txtQuantity == "" || $txtQuantity == "0") {
    $txtQuantity = 1;
  }
  $query = "SELECT p_id, p_description, p_stock_quantity FROM parts ";
  $query .= "WHERE p_id='$lstParts';";
  $mysqlresult = query($query);
  $row = mysql_fetch_row($mysqlresult);
  $PartID = $row[0];
  $PartDescription = $row[1];
  $AvailableQuantity = $row[2];
  $tmpDescription = $txtDescription;
  $txtDescription = "($txtQuantity) $PartID - $PartDescription $l_added<BR>\n";
  $txtDescription .= $tmpDescription;
	  $NewQuantity = $AvailableQuantity - $txtQuantity;
      $query = "INSERT INTO ticketparts ";
      $query .= "VALUES ('','$t_id', '$PartID', '$txtQuantity');";
      $mysqlresult = query($query);
      if ($mysqlresult) {
      }
      else {
        print "$l_error Part not added<BR>\n";
        print mysql_error($mysqlresult);
      } 
      $query = "UPDATE parts SET p_stock_quantity='$NewQuantity' ";
	  $query .= "WHERE p_id='$PartID';";
	  $mysqlresult = query($query);
}// end if not part mmmt
if ($txtDescription == "") {
  $txtDescription = "(re)Assigned Ticket/Added Hours";
}
$txtDescription = addslashes("$txtDescription");
if (settype($txtDuration, "double")) {
  $query = "INSERT INTO events ";
  $query .= "(t_id, e_description, e_duration, s_user, e_status, e_assignedto) ";
  $query .= "VALUES ('$t_id', '$txtDescription', '$txtDuration', '$s_user', ";
  $query .= "'OPEN', '$lstAssignedto');";
  $mysqlresult = query($query);
  if ($mysqlresult) {
  }
  else {
   print "$l_event $l_no $l_added<BR>\n";
   print "Mysql error: " . mysql_error($mysqlresult);
   print "<BR>";
  }
}
else {
  if (!isset($dontinsert)) {
    print "<CENTER>Please press your back button and make sure that the ";
    print "duration is a correct number (eg. 100.56)\n";
    print "Duration is currently: " . $txtDuration;
	print "</CENTER>\n";
  }
}
?>
