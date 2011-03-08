<?php	//RESPONSE TIME REPORT
include("includes/functions.inc.php");
include("includes/graphit.inc.php");
?>
<CENTER>

<?
if (isset($cmdGenerateReport)) {
  if (empty($txtStartDate)) {
    // This is the day that I fixed the close ticket event so it reflects 
    // the time that the ticket was closed.
    $txtStartDate = "2001-03-01";
  }
  // automatically set EndDate to todays date if it is empty
  if (empty($txtEndDate)) {
    //$tomorrow = date("d")+1;
    //$txtEndDate = date("Y-m-$tomorrow");
    $txtEndDate = date("Y-m-d");
  }
  $original_end_date = $txtEndDate;
  $enddate = strtotime($txtEndDate);
  $endyear = date("Y",$enddate);
  $endmonth = date("n",$enddate);
  $endday = date("d",$enddate);
  $endhour = date("H",$enddate);
  $endminute = date("i",$enddate);
  $tomorrow = mktime($endhour,$endminute,$endsecond,$endmonth,$endday+1,$endyear);
  $txtEndDate = date("Y-m-d", $tomorrow);

//HEADER INFORMATION FOR THE REPORT
?>
</TABLE>
<BR>
<TABLE border=0 width=100%>
<?
print "<TR bgcolor=#000000>\n";
print "  <TD align=center colspan=2><FONT color=white><B>Report Generated ".date("Y-m-d");
print " by ".$user." for ".$lstSelectDepartment;
print " between the dates of ".$txtStartDate." and ".$original_end_date;
print "</B></font>\n";
print "  </TD>\n";
print "</TR>\n";
?>
  <TR>
    <TD colspan=2>
    <TABLE border=0 width=100%>
  <?

//FIND TOTAL SERVICE REQUESTS
  $query = "SELECT COUNT(ticket.t_id) ";
  $query .="FROM ticket ";
  $query .="WHERE t_timestamp_opened>=\"$txtStartDate\" ";
  if ($lstSelectDepartment != "All") {
    $query .="AND t_department=\"$lstSelectDepartment\" ";
  }
  $query .="AND t_timestamp_closed<=\"$txtEndDate\" ";
  // Had to add these since the program wasn't adding 
  //timestamps for closed tickets correctly
  $query .="AND t_timestamp_closed!=\"0000-00-00\" ";
  $query .="AND t_timestamp_closed!=\"NULL\";";
  $mysql_result = query($query);
  $row = mysql_fetch_row($mysql_result);
  print "<TR><TD align=left>Total Service Calls:</TD><TD align=left>".$row[0]."</TD></TR>\n";
  


//FIND RESPONSE TIMES
  $query = "SELECT t_id ";
  $query .="FROM ticket ";
  $query .="WHERE t_timestamp_opened>=\"$txtStartDate\" ";
  $query .="AND t_timestamp_closed<=\"$txtEndDate\" ";
  if ($lstSelectDepartment != "All") {
    $query .="AND t_department=\"$lstSelectDepartment\" ";
  }
  $query .="AND t_timestamp_closed!=\"0000-00-00\" ";
  $query .="AND t_timestamp_closed!=\"NULL\";";
  $mysql_result = query($query);
  $count = 1;
  unset($max_response);
  unset($min_response);
  unset($avg_response);
  while ($row = mysql_fetch_row($mysql_result)) {
    $query2 = "SELECT e_id, t_id, e_timestamp ";
    $query2 .="FROM events ";
    $query2 .="WHERE t_id=\"$row[0]\" ";
    $query2 .="LIMIT 2;";
    $mysql_result2 = query($query2);
    $row2 = mysql_fetch_row($mysql_result2); //get submittal date
    $date_var = $row2[2]; //convert to date format
    $YYYY = "$date_var[0]$date_var[1]$date_var[2]$date_var[3]";
    $MM = "$date_var[4]$date_var[5]";
    $DD = "$date_var[6]$date_var[7]";
    $hh = "$date_var[8]$date_var[9]";
    $mm = "$date_var[10]$date_var[11]";
    $ticket_submittal = "$YYYY-$MM-$DD $hh:$mm";
    $row2 = mysql_fetch_row($mysql_result2); //get response date
    $date_var = $row2[2]; //convert to date format
    $YYYY = "$date_var[0]$date_var[1]$date_var[2]$date_var[3]";
    $MM = "$date_var[4]$date_var[5]";
    $DD = "$date_var[6]$date_var[7]";
    $hh = "$date_var[8]$date_var[9]";
    $mm = "$date_var[10]$date_var[11]";
    $tech_response = "$YYYY-$MM-$DD $hh:$mm";
  
    //convert timestamp to a computable date format
    $originalstart = $ticket_submittal;
    $startdate = strtotime($ticket_submittal);
    $startyear = date("Y",$startdate);
    $startmonth = date("n",$startdate);
    $startday = date("d",$startdate);
    $starthour = date("H",$startdate);
    $startminute = date("i",$startdate);
    $ticket_submittal = mktime($starthour,$startminute,$startsecond,$startmonth,$startday,$startyear);
    //$nextminute = mktime($starthour,$startminute+1,$startsecond,$startmonth,$startday,$startyear);
 
    //convert timestamp to a computable date format
    $original_response = $tech_response;
    $enddate = strtotime($tech_response);
    $endyear = date("Y",$enddate);
    $endmonth = date("n",$enddate);
    $endday = date("d",$enddate);
    $endhour = date("H",$enddate);
    $endminute = date("i",$enddate);
    $tech_response = mktime($endhour,$endminute,$endsecond,$endmonth,$endday,$endyear);

    $tmp_time = $ticket_submittal;
    $total_minutes = 0;
    //see how many minutes are between these dates
    for ($i=0; $tmp_time < $tech_response; $i++) {
      if ((date("H",$tmp_time) > $g_bus_start_hour)&&(date("H",$tmp_time) < $g_bus_stop_hour)) {  //make sure it is within business hours
        if (strtolower(date("l",$tmp_time)) == "saturday") {
          if ($g_include_saturdays = "1") {
            $total_minutes++;
          }
        }
        elseif (strtolower(date("l",$tmp_time)) == "sunday") {
          if ($g_include_sundays = "1") {
            $total_minutes++;
          }
        }
        else {
          $total_minutes++;
        }
      }
      $tmp_time = mktime($starthour+$i,$startminute,$startsecond,$startmonth,$startday,$startyear);
    }
    
    //calculate response times
    if ($total_minutes == "0") {
      $total_minutes = .15;
    }
    if (!isset($max_response)) { $max_response = $total_minutes; }
    if ($total_minutes > $max_response) {
      $max_response = $total_minutes;
    }
    if (!isset($min_response)) { $min_response = $total_minutes; }
    if ($total_minutes < $min_response) {
      $min_response = $total_minutes;
    }
    if (!isset($avg_response)) { $avg_response = $total_minutes;}
    else {
      $avg_response+= $total_minutes;
      $count++;
    }
  }
  //print "<BR>Min Response Time: ".round($min_response/60,2)." hours";
  //print "<BR>Max Response Time: ".round($max_response/60,2)." hours";
  //print "<BR>Avg Response Time: ".round(($avg_response/$count)/60,2)." hours";
  print "<TR><TD align=left>Min Response Time: </TD><TD align=left>".round($min_response,2)." hours</TD></TR>\n";
  print "<TR><TD align=left>Max Response Time: </TD><TD align=left>".round($max_response,2)." hours</TD></TR>\n";
  print "<TR><TD align=left>Avg Response Time: </TD><TD align=left>".round(($avg_response/$count),2)." hours</TD></TR>\n";


//FIND RESOLUTION TIMES
  $query = "SELECT t_id, t_timestamp_opened, t_timestamp_closed, t_category ";
  $query .="FROM ticket ";
  $query .="WHERE t_timestamp_opened>=\"$txtStartDate\" ";
  $query .="AND t_timestamp_closed<=\"$txtEndDate\" ";
  $query .="AND t_timestamp_closed!=\"0000-00-00\" ";
  if ($lstSelectDepartment != "All") {
    $query .="AND t_department=\"$lstSelectDepartment\" ";
  }
  $query .="AND t_timestamp_closed!=\"NULL\";";
  $mysql_result = query($query);
  $count = 1;
  $index = 0;
  unset($max_resolution);
  unset($min_resolution);
  unset($avg_resolution);
  while ($row = mysql_fetch_row($mysql_result)) {
  
    //convert timestamp to a computable date format
    $ticket_opened_orig = $row[1];
    $startdate = strtotime($row[1]);
    $startyear = date("Y",$startdate);
    $startmonth = date("n",$startdate);
    $startday = date("d",$startdate);
    $starthour = date("H",$startdate);
    $startminute = date("i",$startdate);
    $ticket_opened = mktime($starthour,$startminute,$startsecond,$startmonth,$startday,$startyear);
 
    //convert timestamp to a computable date format
    $ticket_closed_orig = $row[2];
    $enddate = strtotime($row[2]);
    $endyear = date("Y",$enddate);
    $endmonth = date("n",$enddate);
    $endday = date("d",$enddate);
    $endhour = date("H",$enddate);
    $endminute = date("i",$enddate);
    $ticket_closed = mktime($endhour,$endminute,$endsecond,$endmonth,$endday,$endyear);

    $tmp_time = $ticket_opened;
    $total_minutes = 0;
    //see how many minutes are between these dates
    for ($i=0; $tmp_time < $ticket_closed; $i++) {
      if ((date("H",$tmp_time) > $g_bus_start_hour)&&(date("H",$tmp_time) < $g_bus_stop_hour)) {  //make sure it is within business hours
        // check for weekends
        if (strtolower(date("l",$tmp_time)) == "saturday") {
          if ($g_include_saturdays = "1") {
            $total_minutes++;
          }
        }
        elseif (strtolower(date("l",$tmp_time)) == "sunday") {
          if ($g_include_sundays = "1") {
            $total_minutes++;
          }
        }
        else {
          $total_minutes++;
        }
      }
      $tmp_time = mktime($starthour+$i,$startminute,$startsecond,$startmonth,$startday,$startyear);
    }

// FIND CATEGORIES
    if (!isset($category_array)) {
      $category_array[0][0] = $row[3];
      $category_array[0][1] = 1;
    } 
    else {
      for ($j=0; $j<sizeof($category_array); $j++) {
        if ($row[3] == $category_array[$j][0]) {
          $category_array[$j][1] += 1;
          $already_taken_care_of = 1;
        } 
      }
      if ($already_taken_care_of != 1) {
        $category_array[$j][0] = $row[3];
        $category_array[$j][1] = 1;
      }
    }
    unset($already_taken_care_of);
    $index++; 

    //calculate resolution times
    if ($total_minutes == "0") { 
      $total_minutes = .15;
    }
    if (!isset($max_resolution)) { $max_resolution = $total_minutes; }
    if ($total_minutes > $max_resolution) {
      $max_resolution = $total_minutes;
    }
    if (!isset($min_resolution)) { $min_resolution = $total_minutes; }
    if ($total_minutes < $min_resolution) {
      $min_resolution = $total_minutes;
    }
    if (!isset($avg_resolution)) { $avg_resolution = $total_minutes;}
    else {
      $avg_resolution+= $total_minutes;
      $count++;
    }
  }
  //print "<BR>Min Resolution Time: ".round($min_resolution/60,2)." hours";
  //print "<BR>Max Resolution Time: ".round($max_resolution/60,2)." hours";
  //print "<BR>Avg Resolution Time: ".round(($avg_resolution/$count)/60,2)." hours";
  print "<TR><TD width=25% align=left>Min Resolution Time: </TD><TD align=left>".round($min_resolution,2)." hours </TD></TR>";
  print "<TR><TD width=25% align=left>Max Resolution Time: </TD><TD align=left>".round($max_resolution,2)." hours </TD></TR>";
  print "<TR><TD width=25% align=left>Avg Resolution Time: </TD><TD align=left>".round(($avg_resolution/$count),2)." hours </TD></TR>";

  print "</TABLE></TD></TR>";
  print "</TD></TR></TABLE>\n";
  print "<TABLE border=0 width=100%>\n";
  print "<TR bgcolor=#000000><TD align=left colspan=2>\n";
  print "  <B><font color=white>Categories</font></B>\n";
  print "</TD></TR>";
  print "</TABLE>\n";
  print "<TABLE border=0 width=100%>\n";
  print "<TR><TD valign=middle><TABLE border=0 width=100%>";
  for ($i=0; $i < sizeof($category_array); $i++) {
    $names[$i] = $category_array[$i][0];
    $values[$i] = round($category_array[$i][1]);
    $tmp_cat_total = round(($category_array[$i][1]/$index)*100, 2);
    print "<TR><TD align=left>".$category_array[$i][0].":</TD><TD align=left> ".$tmp_cat_total."% </TD></TR>";
  }
  print "</TABLE></TD>";
  print "<TD align=center valign=middle colspan=2>";
  graphit($names,$values,"Categories (values are in tickets)");
  unset($names);
  unset($values);
  unset($category_array);
  print "</TD>";
  print "</TR></TABLE>";
  print "<TABLE border=0 width=100%>\n";
  print "<TR bgcolor=#000000><TD align=left colspan=2>\n";
  print "  <B><font color=white>Computer ID's and Associated Tickets</font></B>\n";
  print "</TD></TR>";
  print "</TABLE>\n";
  print "<TABLE border=0 width=100%>\n";
  print "<TR><TD align=left valign=middle>\n";
  print "<TABLE border=0 width=100%>\n";
  $query = "SELECT DISTINCT t_computerid from ticket ";
  $query .="WHERE t_computerid!=\"\" ";
  if ($lstSelectDepartment != "All") {
    $query .="AND t_department=\"$lstSelectDepartment\" ";
  }
  $query .="AND t_timestamp_opened>=\"$txtStartDate\" ";
  $query .="AND t_timestamp_closed<=\"$txtEndDate\" ";
  $query .="AND t_timestamp_closed!=\"0000-00-00\" ";
  $query .="AND t_timestamp_closed!=\"NULL\";";
  $result = query($query);
  while ($row = mysql_fetch_row($result)) {
	  $query2 = "SELECT t_id FROM ticket ";
	  $query2 .="WHERE t_computerid=\"$row[0]\" ";
	  $query2 .="AND t_computerid!=\"\";";
	  $result2 = query($query2);
	  if ($whatcolor=="#F1FAFE") {
		  $whatcolor="#DEDEEE";
	  }
	  else {
		  $whatcolor="#F1FAFE";
	  }
          print "<TR bgcolor=\"$whatcolor\"><TD width=15%>\n";
	  print "".$row[0].": ";
          print "</TD><TD>\n";
	  while ($row2 = mysql_fetch_row($result2)) {
		  print "<A HREF=\"".$g_base_url."/index.php?whattodo=viewjobs&t_id=".$row2[0]."\">".$row2[0]."</A> &nbsp;&nbsp;\n";
	  }
          print "</TD></TR>\n";
  }
  print "</TD>\n";
  print "</TR>\n";
  print "</TABLE>\n";
}
else {
}
?>
