<? // User Activity and Stats Report

heading("User Activity Report", 1);

include("includes/functions.inc.php");

if( isset($cmdGenerateReport) ) {
    ?>

    <TABLE
     BGCOLOR="<?echo $html_table_bgcolor;?>"
     BORDERCOLOR="<?echo $html_table_bordercolor;?>"
     BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
     BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
     CELLSPACING=0 CELLPADDING=5
     BORDER=1 ALIGN=CENTER>
    <tr><td BGCOLOR="#000080" COLSPAN=3>
    <p align=left><font color="#FFFFFF"><b>
    <? echo "$g_title"; ?>  - Number of Logs Opened By Each User</b></font>
    </td></tr>

    <?
    if( !$txtStartDate ) {
        $txtStartDate = "1950-01-01";
    }
    if( !$txtEndDate ) {
        $txtEndDate = "2010-01-01";
    }
    print "<b>Report from $txtStartDate to $txtEndDate</b><p>";
    
    // Add 24 hours to end date
    $end = strtotime( $txtEndDate );
    $end += 60*60*24;
    $txtEndDate = date( "Y-m-d", $end );
    
    $records = array();
        
    $query = "SELECT t_id, t_user, t_timestamp_opened FROM ticket WHERE " .
             " t_timestamp_opened >= '$txtStartDate' AND " .
	     " t_timestamp_opened <= '$txtEndDate' ORDER BY t_user;";
    $result = query($query);
    $opened = 1;
    $prev = "";

    while( $row = mysql_fetch_row($result) ) {
        $t_id = $row[0];
        $t_user = $row[1];
        if( $prev == $t_user ) {
            $opened++;
        }
        else {
            if( $prev != "" && $opened > 0)
	        $records["$prev"] = $opened;
            $prev = $t_user;
	    $opened = 1;
        }
    }
    if( $prev != "" && $opened > 0 ) {
        $records[$prev] = $opened;
    }

    arsort( $records );
    reset( $records );

    print "<tr><th>Rank</th><th>User</th><th># Logs</th></tr>\n";
    $count = 0;
    while( list($key,$val) = each( $records ) )  
        print "<tr><td>" . ++$count . "</td><td>$key</td><td>$val</td></tr>\n";
    
    print "</table>\n";

?>

    <p>
     <TABLE
     BGCOLOR="<?echo $html_table_bgcolor;?>"
     BORDERCOLOR="<?echo $html_table_bordercolor;?>"
     BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
     BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
     CELLSPACING=0 CELLPADDING=5
     BORDER=1 ALIGN=CENTER>
    <tr><td BGCOLOR="#000080" COLSPAN=3>
    <p align=left><font color="#FFFFFF"><b>
    <? echo "$g_title"; ?>  - Number of Logs Closed By Each User</b></font>
    </td></tr>

<?
    $query = "SELECT e_id, t_id, e_assignedto, e_status, e_timestamp " .
             " FROM events ORDER BY t_id, e_id;";
    $result = query($query);
    $prev_t_id = 0;
    $prev_e_assignedto = "";
    $prev_e_status = "";
    $prev_e_timestamp = "";
    $records = array();

    $unix_start_date = strtotime($txtStartDate);
    $unix_end_date = strtotime($txtEndDate); 
    while( $row = mysql_fetch_row($result) ) {
        $e_id = $row[0];
        $t_id = $row[1];
        $e_assignedto = $row[2];
        $e_status = $row[3];
	$e_timestamp = $row[4];
    
        if( $prev_t_id != $t_id ) {
            if( $prev_e_status == "CLOSED" ) {
		$unix_timestamp = timestampToUnix( $prev_e_timestamp );
		if( $unix_timestamp >= $unix_start_date &&
		    $unix_timestamp <= $unix_end_date ) {
 		    $records[$prev_e_assignedto]++;
		}
  	    }
        }
        $prev_t_id = $t_id;
        $prev_e_assignedto = $e_assignedto;
        $prev_e_status = $e_status;
	$prev_e_timestamp = $e_timestamp;
    }
    if( $prev_e_status == "CLOSED" ) {
        $unix_timestamp = timestampToUnix( $prev_e_timestamp );
	if( $unix_timestamp >= $unix_start_date &&
	    $unix_timestamp <= $unix_end_date ) {
 	    $records[$prev_e_assignedto]++;
	 }
     }
    
    arsort( $records );
    reset( $records );

    print "<tr><th>Rank</th><th>User</th><th># Logs</th></tr>\n";
    $count = 0;
    while( list($key,$val) = each( $records ) )
        if( !empty($key) ) 
            print "<tr><td>" . ++$count . "</td><td>$key</td><td>$val</td></tr>\n";
    
    print "</table>\n";
}

else {   // First time viewing the screen, show form for parameters 
  ?>
     <TABLE
     BGCOLOR="<?echo $html_table_bgcolor;?>"
     BORDERCOLOR="<?echo $html_table_bordercolor;?>"
     BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
     BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
     CELLSPACING=0 CELLPADDING=5
     BORDER=1 ALIGN=CENTER>
    <tr><td BGCOLOR="#000080">
    <p align=left><font color="#FFFFFF"><b>
    <? echo "$g_title"; ?>  - User Status/Activity Report - Options</b></font>
    </td></tr>
    <tr><td><center>
  <?
    print "\n<form action=\"$g_base_url/index.php?" .
          "whattodo=$whattodo&rpt=1\" method=post>\n";
    print "Select report parameters:<p>";
    print "<em>Leave dates blank for all</em><br>\n";
    print "Start Date: \n";
    print "<input type=text name=txtStartDate size=10>\n";
    if($g_enable_javascript==1) {
        print "<input name=\"b1\" type=\"button\" value=\"...\" onClick=";
        print "\"javascript:pedirFecha(txtStartDate,'Start Date');\">";
    }
    print "<br>End Date: \n";
    print "<input type=text name=txtEndDate size=10>\n";
    if($g_enable_javascript==1) {
        print "<input name=\"b1\" type=\"button\" value=\"...\" onClick=";
        print "\"javascript:pedirFecha(txtEndDate,'End Date');\">";
    }
    print "<input type=hidden name=cmdGenerateReport>\n";
    print "<p><input value=\"Generate Report\" type=submit></form>\n";
    print "</td></tr></table>\n";
}


?>
