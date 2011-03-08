<?php //PHP Helpdesk - VIEW JOBS - View Tickets
include("includes/functions.inc.php");
    // select certain items from the ticket table
    if ($orderby == "t_summary") {
      if (isset($tmp_t_summary)) {
        $queryorderby = " order by t_summary, t_timestamp_opened;";
      }
      else {
        $queryorderby = " order by t_summary DESC, t_timestamp_opened;";
      }
    }
    elseif ($orderby == "t_request") {
      if (isset($tmp_t_request)) {
        $queryorderby = " order by t_request, t_timestamp_opened;";
      }
      else {
        $queryorderby = " order by t_request DESC, t_timestamp_opened;";
      }
    }
    elseif ($orderby == "e_status") {
      if (isset($tmp_e_status)) {
        $queryorderby = " order by e_status, t_timestamp_opened;";
      }
      else {
        $queryorderby = " order by e_status DESC, t_timestamp_opened;";
      }
    }
    elseif ($orderby == "t_department") {
      if (isset($tmp_t_department)) {
        $queryorderby = " order by t_department, t_timestamp_opened;";
      }
      else {
        $queryorderby = " order by t_department DESC, t_timestamp_opened;";
      }
    }
    elseif ($orderby == "t_user") {
      if (isset($tmp_t_user)) {
        $queryorderby = " order by e_assignedto, t_timestamp_opened;";
      }
      else {
        $queryorderby = " order by e_assignedto DESC, t_timestamp_opened;";
      }
    }
    elseif ($orderby == "t_id") {
      if (isset($tmp_t_id)) {
        $queryorderby = " ORDER BY t_id, t_timestamp_opened;";
      }
      else {
        $queryorderby = " ORDER BY t_id DESC, t_timestamp_opened;";
      }
    }
    elseif ($orderby == "t_timestamp_opened") {
      if (isset($tmp_t_timestamp_opened)) {
        $queryorderby = " order by t_timestamp_opened;";
      }
      else {
        $queryorderby = " order by t_timestamp_opened DESC;";
      }
    }
    else {
      if (isset($tmp_t_priority)) {
        $queryorderby = " ORDER BY t_priority, t_timestamp_opened;";
      }
      else {
        $queryorderby = " ORDER BY t_priority DESC, t_timestamp_opened;";
      }
    }
    // print first portion of the HTML table
    print "<CENTER>";
    if ($viewall == "viewall") {
      print "<a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	  for ($i=0; $lstChooseCompany[$i] != NULL; $i++) 
	  {
	    print "$lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  print "viewall=viewopen\">$l_viewonlyopenedtickets</a>\n";
	  $viewallquery = "";
	}
    else {
      print "<a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
      for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
        print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
      }
      print "viewall=viewall\">$l_viewalltickets</a>\n";
      $viewallquery = " AND (e_status='OPEN' || e_status='REGISTERED')";
      //$viewallquery = " AND (e_status='OPEN')";
    }
?>
<BR><BR>
<table border="0" cellpadding="2" align="center">
  <TR>
    <Th>
	  <?php
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
      for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
        print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
      }
      if (isset($tmp_t_id)) {
        unset($tmp_t_id);
        print "orderby=t_id&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_ticketid</font></a></b>\n";
      }
      else {
        print "orderby=t_id&viewall=$viewall&tmp_t_id=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_ticketid</font></a></b>\n";
      }
      ?>
    </Th>
	<th  align=center>

	  <?
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
      for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	    print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  if (isset($tmp_t_summary)) {
	    unset($tmp_t_summary);
	    print "orderby=t_summary&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_summary</font></a></b>\n";
	  }
	  else {
	    print "orderby=t_summary&viewall=$viewall&tmp_t_summary=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_summary</font></a></b>\n";
	  }
	  ?>
	</Th>
    <Th  align=center>

	  <?
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	  for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	    print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  if (isset($tmp_t_request)) {
	    unset($tmp_t_request);
   	    print "orderby=t_request&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_requests</font></a></b>\n";
	  }
	  else {
	    print "orderby=t_request&viewall=$viewall&tmp_t_request=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_requests</font></a></b>\n";
	  }
	  ?>
	</Th>
    <Th  align=center>

	  <?
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	  for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	    print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  if (isset($tmp_e_status)) {
	    unset($tmp_e_status);
	    print "orderby=e_status&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_status</font></a></b>\n";
	  }
	  else {
	    print "orderby=e_status&viewall=$viewall&tmp_e_status=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_status</font></a></b>\n";
	  }
	  ?>
	</Th>
	<Th  align=center>
	  <?
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	  for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	    print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  if (isset($tmp_t_department)) {
	    unset($tmp_t_department);
	    if ($g_dept_or_comp == 0) {
	      print "orderby=t_department&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_department</font></a></B>\n";
	    }
	    else {
	      print "orderby=t_department&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_company</font></a></B>\n";
   	    }
	  }
	  else {
	    if ($g_dept_or_comp == 0) {
	      print "orderby=t_department&viewall=$viewall&tmp_t_department=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_department</font></a></B>\n";
	    }
	    else {
	      print "orderby=t_department&viewall=$viewall&tmp_t_department=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_company</font></a></B>\n";
   	    }
	  }
	  ?>
    </Th>
	<Th align=center>

	  <?
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	  for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	    print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  if (isset($tmp_t_user)) {
	    unset($tmp_t_user);
	    print "orderby=t_user&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_assignedto</font></a></B>\n";
	  }
	  else {
	    print "orderby=t_user&viewall=$viewall&tmp_t_user=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_assignedto</font></a></B>\n";
	  }
	  ?>
	</Th>
	<Th align=center>

	  <?
	  print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	  for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	    print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	  }
	  if (isset($tmp_t_priority)) {
	    unset($tmp_t_priority);
	    print "orderby=t_priority&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_priority</font></a></B>\n";
	  }
	  else {
	    print "orderby=t_priority&viewall=$viewall&tmp_t_priority=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_priority</font></a></B>\n";
	  }
	  ?>
	  </Th>
	  <Th align=center>

	    <?
	    print "<B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	    for ($i=0; $lstChooseCompany[$i] != NULL; $i++) {
	      print "lstChooseCompany[$i]=$lstChooseCompany[$i]&";
	    }
	    if (isset($tmp_t_timestamp_opened)) {
	      unset($tmp_t_timestamp_opened);
	      print "orderby=t_timestamp_opened&viewall=$viewall\"><font color=\"".$html_table_header_fontcolor."\">$l_dateopened</font></a></B>\n";
	    }
	    else {
	      print "orderby=t_timestamp_opened&viewall=$viewall&tmp_t_timestamp_opened=DESC\"><font color=\"".$html_table_header_fontcolor."\">$l_dateopened</font></a></B>\n";
	    }
	    ?>
	  </Th>
	</TR>
	</div>
	<?php
	// select all events for each ticket id and only display the information for the last
	// event joining it with the other information.  I had to do a MySQL query and
	// then take that query and put it into an array.  I then searched through the array
	// to see if the ticket was already in there.  If it was, I updated that array item
	// and continued until the query is finished.  I had to do this because I couldn't
	// get the correct SQL to do it :(  If someone finds a way, please let me know

	// DELETE ALL FROM tmpeid
	$query = "DELETE FROM tmpeid;";
	$mysqlresult = query($query);
	
	// Insert the maximum event id's for each ticket into tmpeid
    $query = "INSERT INTO tmpeid SELECT MAX(e_id) FROM events GROUP BY t_id;";
	$mysqlresult = query($query);

    // Select the ticket, event, and tmpeid so that the tables are joined correctly
	$query = "SELECT events.t_id, t_summary, t_request, ";
	$query .= "t_user, t_priority, t_timestamp_opened, t_department, ";
	$query .= "e_status, e_assignedto, e_timestamp, events.e_id, ticket.t_et_id ";
	$query .= "FROM ticket, events, tmpeid ";
	$query .= "WHERE ticket.t_id=events.t_id";
	$query .= " AND events.e_id=tmpeid.e_id";
	$query .= "$viewallquery";
	$query .= " AND (";
	for ($i=0; $i < sizeof($lstChooseCompany); $i++) {
	  $query .= "t_department='$lstChooseCompany[$i]'";
	  if ($i+1 < sizeof($lstChooseCompany)) {
	    $query .= " OR ";
	  }
	}
	$query .= ")$queryorderby";
	$mysqlresult = query($query);
	if (!$mysqlresult) {
      print "MYSQL ERROR: " . mysql_error($mysqlresult);
	}
	else {
	  //print the rows
	  while ($row = mysql_fetch_row($mysqlresult)) {
	    if ($html_alt_color==$html_alt_color2) {
	      $html_alt_color = $html_alt_color1;
	    }
	    else {
	      $html_alt_color = $html_alt_color2;
            }	
	    print "<TR bgcolor=\"".$html_alt_color."\"\n";
            if ($g_enable_javascript==1) {
              print "class=m1 onClick=\"location.href='";
              print $g_base_url."/index.php?whattodo=viewjobs&";
              for ($j=0; $lstChooseCompany[$j] != NULL; $j++) {
		$thiscompany = addslashes($lstChooseCompany[$j]);
                print "lstChooseCompany[$j]=$thiscompany&";
              }
              $t_summary = stripslashes($row[1]);
              print "t_id=$row[0]'\"\n";
              print "onMouseOut=\"style.backgroundColor=''; style.border='0 solid black';\"\n";
              print "onMouseOver=\"style.backgroundColor='".$html_highlight_color."'; style.cursor='hand'; style.border='0 solid #CCCCCC'\"\n";
            }
	    print ">\n";
	    print "  <TD align=center>\n";
	    print "    <B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	    for ($j=0; $lstChooseCompany[$j] != NULL; $j++) {
	      print "lstChooseCompany[$j]=$lstChooseCompany[$j]&";
	    }
	    print "t_id=$row[0]\">$row[11]</a>\n";
	    print "  </TD>\n";
	    print "  <TD align=left>\n";
	    print "    <B><a href=\"".$g_base_url."/index.php?whattodo=viewjobs&";
	    for ($j=0; $lstChooseCompany[$j] != NULL; $j++) {
	      print "lstChooseCompany[$j]=$lstChooseCompany[$j]&";
	    }
	    $t_summary = stripslashes($row[1]);
	    print "t_id=$row[0]\">$t_summary</a>\n";
	    print "  </TD>\n";
	    print "  <TD  align=center>\n";
	    print "$row[2]\n";
	    print "  </TD>\n";
	    print "  <TD align=center>\n";
  	    print "$row[7]\n";
	    print "  </TD>\n";
	    print "  <TD  align=center>\n";	    
		 // now the department name
		 if ($g_dep_short_name == 1) {
			 $dep_query = "SELECT d_depkey FROM department WHERE d_name = '$row[6]'";
		 	 $mysqlresult_dep = query($dep_query);
		 	 if (!$mysqlresult_dep) {
   	   	 print "MYSQL ERROR: " . mysql_error($mysqlresult_dep);
			 }
			 $dep_row = mysql_fetch_row($mysqlresult_dep);
		    print "$dep_row[0]\n"; // short version
		 // short version
		 } else {
		    print "$row[6]\n"; // long version
	    }
	    print "  </TD>\n";
	    print "  <TD  align=center>\n";
	    print "$row[8]\n";
	    print "  </TD>\n";
	    // set the background color of the priority cell
	    if ($row[4] == "0") {
	      print "    <TD align=center bgcolor=\"".$html_priority_low."\"><i>$l_low</i></TD>\n";
	    }
	    elseif ($row[4] == "1") {
	      print "    <TD  align=center bgcolor=\"".$html_priority_normal."\"><i>$l_normal</i></TD>\n";
	    }
	    elseif ($row[4] == "2") {
	      print "    <TD  align=center bgcolor=\"".$html_priority_high."\"><i>$l_high</i></TD>\n";
	    }
	    elseif ($row[4] == "3") {
	      print "    <TD  align=center bgcolor=\"".$html_priority_urgent."\"><i><b>$l_urgent</b></i></TD>\n";
	    }
	    print "  <TD  align=center valign=center>\n";
	    print "$row[5]\n";
	    print "  </TD>\n";
	    print "</TR>\n";
  	  }
	}
    print "</TABLE>\n";
    print "<br>" . mysql_num_rows( $mysqlresult ) . " $l_numoftickets.";
?>
