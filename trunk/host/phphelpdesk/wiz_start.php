<?PHP // the Wizzard script is again a bit large so I splitted it into 4 includes. 
// These are found in the "layout/wizzard" folder and are named wiz_form1.inc.php,
// wiz_form2.inc.php,wiz_form3.inc.php wiz_form4.inc.php and wiz_mail.inc.php
header("Cache-control: private"); //IE 6 enable to use the back command (javaScript). 
// get the initals since it is not called by index.php we init all things for the new window.
include("config/general.conf.php");
include("config/db.conf.php");
include("config/html.conf.php");
include("includes/functions.inc.php");
include("includes/permissions.inc.php");   //inical what do to =viewjobs if permission and choosen pref.
$user = "assistente"; // set a user that it is clear the ticket created comes from 
						//the wizzard (in case there will be another form in the admin section
// Load language file.  Always load english, so if a variable is
// missing from the translation file, at least the user will see
// the english version instead of nothing.
include("languages/english.lang.php");
if( $g_language != "english" && isset( $g_language ) )
    include("languages/$g_language.lang.php");
// Vars init treated in the edit and new mode
$current_date = date("ymdHi");                //set timestamp
$readable_date = $current_date[2].$current_date[3]."/".$current_date[4].$current_date[5]."/".$current_date[0].$current_date[1];
$readable_time = $current_date[6].$current_date[7].":".$current_date[8].$current_date[9];

//*************************************************
// get the main thing to run
//*************************************************
if (isset($cmdNext01)) { // THE FIRST PART IS IN THE ELSE PART BELOW
    unset($cmdNext01);
	// determine now to which department the selected category/request belongs
    // alright now we get the second part from from the first form  on the screen
    include ("layout/wizzard/wiz_form2.inc.php");
} 
// THE FIRST PART IS IN THE ELSE PART BELOW
elseif (isset($cmdNext02)) { // Second Dialog
      // the first and the second screen serve only to determine the departments which the request should be send. 
	// determine now to which department the selected category/request belongs
    $query = "SELECT r_department FROM request WHERE r_name = '$lstRequest ';";
    $mysql_result = query($query);
    if ($mysql_result) {
    $lstDepartment = mysql_fetch_row($mysql_result);  }	
	$lstDepartment = $lstDepartment[0];
    // alright now we get the second fomr on the screen
    include ("layout/wizzard/wiz_form3.inc.php");
} // that was the second part

elseif (isset($cmdNext03)){ // Third dialog
// verification for the third part should be done here.
	if ($txtUserFirstName == NULL) {$add_d_err[1] =1;  $veri_err=1;}
	if ($txtUserLastName == NULL) {$add_d_err[2] =1;$veri_err=1;}
	if ($txtUserEmail == NULL)  {$add_d_err[3] =1;$veri_err=1;}	
	if ($veri_err==0) {		// finally the third part is printed if right otherwise the same page again
			include ("layout/wizzard/wiz_form4.inc.php"); 
		} else {	
			include ("layout/wizzard/wiz_form3.inc.php"); 
		}
}// end third dialog
elseif (isset($cmdNext04)){ // Fourth Dialog
// get the verification of the 3rd part
// and update the tables
?>
<html>
<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title><? echo $l_title_report; ?> V/V</title>
  <LINK REL=StyleSheet HREF="css/wizzard.css" TYPE="text/css" MEDIA=screen>
  </head>
<body class="main">
    <table align="center">
      <tr>
      <td>
<?PHP
  // Insert ticked into the tickets table
		$et_id = getnextticketid($lstDepartment); //(get extended ticket id);
      $query = "INSERT INTO ticket";
      $query .= " (t_id, t_request, t_detail, t_priority, t_user, ";
      $query .= "t_timestamp_opened, t_department, t_location, t_summary, ";
      $query .= "t_userfirstname, t_userlastname, t_usertelephone, t_useremail, ";
      $query .= "t_computerid , t_staffid, t_proposedsolution, t_et_id)";
      $query .= " VALUES (NULL, '$lstRequest', '$txtDetail', '$optPriority', '$user', ";
      $query .= "'$current_date', '$lstDepartment', '$txtLocation', '$txtSummary', ";
      $query .= "'$txtUserFirstName', '$txtUserLastName', '$txtUserTelephone', '$txtUserEmail',";
      $query .= "'$txtStaffid', '$txtComputerid', '$txtProposedSolution',  '$et_id');";
    $mysqlresult = query($query);
    if ($mysql_result) {
		   $t_id = mysql_insert_id();
         //sends eMails and inserts the events.
		 include ("layout/wizzard/wiz_mail.inc.php");
      } // end the clause for good Mysql result
      else {
			print "<B>$l_error:</B>";
            print " $l_jobnotadded<br>\n";
            print "<BR>\n";
      }// closer clause for false sql result
      ?>
      </td>
      </tr>
      </table>
      </body>
      </html>
      <?PHP
} // so we are done
else { // here goes the first screen with the personal entries
      // the first and the second screen serve only to determine the departments which the request should be send. 
	  // get departments from the DB. This is done from the dept table since everybody
      // should be able to send request to the accorind departments
	  
        include("includes/connect.inc.php");
        $query = "SELECT d_name FROM department;";
        $mysql_result = query($query);
        for ($i=0;$row = mysql_fetch_row($mysql_result); $i++) {
          $departments_choose[$i] = "$row[0]";
          }
      include ("layout/wizzard/wiz_form1.inc.php");

} // end of the first part with persnal data
?>