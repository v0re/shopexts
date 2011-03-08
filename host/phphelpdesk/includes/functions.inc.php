<?php   // Global Functions

if( isset( $FUNCTIONS ) ) {
    return;
}

$FUNCTIONS=1;

include("includes/connect.inc.php");

/*
 *  showSummary - Display current open tickets and open tickets for
 *                the current user.
 */  
function ticketSummary() {
   global $user, $mysql_link;
   $openTickets = 0;
   $userTickets = 0;
   
   $query = "SELECT events.e_id, events.t_id, events.e_status, " .
            "events.e_assignedto, ticket.t_id FROM events,ticket " .
            " WHERE events.t_id = ticket.t_id ORDER BY " .
	    " events.t_id, events.e_id;";
   $result = query( $query );

   $row = mysql_fetch_row( $result );

   $prev_e_id = $row[0];  
   $prev_t_id = $row[1];  
   $prev_e_status = $row[2];  
   $prev_e_assignedto = $row[3];

   $done = 0;
   while( !$done  ) {
       $row = mysql_fetch_row( $result );
       if( !$row ) { 
           $done = 1;
       }
       
       $e_id = $row[0];  
       $t_id = $row[1];  
       $e_status = $row[2];  
       $e_assignedto = $row[3];

       if( $t_id != $prev_t_id ) {
           if( $prev_e_status == "OPEN" ) {
	       $openTickets++;
	       if( $prev_e_assignedto == $user && isset($user) )
	           $userTickets++;
	   }
       }
       
       $prev_e_id = $e_id;
       $prev_t_id = $t_id;
       $prev_e_status = $e_status;
       $prev_e_assignedto = $e_assignedto;
   }
	$ticketarray[0]=$openTickets; //stuff the values into an array to hand themo over to the calling procedure
	$ticketarray[1]=$userTickets;
   return $ticketarray;
}


/*
 *  query( $queryString ) Execute query on MySQL and report any errors
 *
 *  If $debug flag is set in config/general.conf.php, some debugging 
 *  information is printed such as the SQL statement sent to the back end.
 */
function query( $q ) {
    global $mysql_link;
    global $debug;

    if( $debug >= 1 )
        echo "<br>Executed SQL:  <b>$q</b>";

    $result = mysql_query( $q, $mysql_link );
	
    unset( $error );
    $error = mysql_error( $mysql_link );
    if( $error )
        echo "<br><b>SQL ERROR: </b>$error<br>";

    if( $debug >= 1 )
    echo " (" . (0+mysql_affected_rows($mysql_link)) . " rows affected.)<br>";
	 return $result; 
}


/*
 * heading( $headingText )
 *
 * This function prints a heading on the page.  This is intended to
 * make a consistant heading style for all pages within the helpdesk.
 */
function heading( $text, $center ) {
    if( $center ) print "<center>";
    print "<hr><h1>$text</h1><hr>";
    if( $center ) print "</center>";
}


/*
 * timestampToUnix( $timestamp ) 
 *
 * Converts a MySQL timestamp to a unix integer date.
 */
function timestampToUnix( $t ) {
    $YYYY = $MM = $DD = $hh = $mm = 0;
    
    $YYYY = "$t[0]$t[1]$t[2]$t[3]";
    $MM = "$t[4]$t[5]";
    $DD = "$t[6]$t[7]";
    $hh = "$t[8]$t[9]";
    $mm = "$t[10]$t[11]";
    
    return strtotime("$YYYY-$MM-$DD $hh:$mm:00");
}
// this function is for determining the next ticket id.
function getnextticketid($for_department) {
// find out about last
	$old_id=0;
	$query = "SELECT t_et_id FROM ticket WHERE t_department = '$for_department' ";
	$query .= "ORDER BY t_et_id DESC;";
	$mysqlresult = query($query);
	$row = mysql_fetch_row($mysqlresult);
	list ($depcode,$curr_id) = split('[-]',$row[0]);
	if ($curr_id == NULL) { // in case it is the first ticket for the department
		$et_id = 0;
		// get the dep_key
		$query = "SELECT d_depkey FROM department WHERE d_name = '$for_department';";
		$mysqlresult = query($query);
		$key_row = mysql_fetch_row($mysqlresult);
		$depcode =$key_row[0];		
	}
	else { $et_id = $curr_id; }
	//echo "row: $row[0],depcore:$depcode,curid: $curr_id, etid:$et_id";
	$et_id++;	
	$et_id = "$depcode-$et_id";
return ($et_id);
}


?>
