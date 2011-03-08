<?php // PHP Helpdesk - View Tickets - Save Changes

include("includes/functions.inc.php");

    $txtLocation = addslashes("$txtLocation");
    $txtDetail = addslashes("$txtDetail");
    $query = "UPDATE ticket SET ";
    $query .= "t_department='$lstDepartment', t_request='$lstRequest', ";
    $query .= "t_summary='$txtSummary', t_detail='$txtDetail', ";
    $query .= "t_location='$txtLocation', t_priority='$optPriority',";
    $query .= "t_proposedsolution = '$txtProposedSolution'"; //cheitkamp 
    if ($txtComputerID != "") {
      $query .= ", t_computerid='$txtComputerID'";
    }
    $query .= " WHERE t_id='$t_id';";
    $mysqlresult = query($query);
    if ($mysqlresult) {
    }
    else {
      print "Changes were NOT successful";
      print "<BR>Mysql Error is:  mysql_error($mysqlresult)";
    }
?>
