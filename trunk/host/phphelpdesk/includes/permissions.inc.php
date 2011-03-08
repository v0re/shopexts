<?php  //GET USER PERMISSIONS

include("includes/functions.inc.php");

//Get user access permissions
$query = "SELECT * FROM security ";
$query .= "WHERE s_user='$user';";
$mysql_result = query($query);

$row = mysql_fetch_row($mysql_result);
$s_email = $row[5];
$s_register_new_tickets = $row[6];
$s_authorize_tickets = $row[7];
$s_assign_tickets = $row[8];
$s_update_tickets = $row[9];
$s_delete_tickets = $row[10];
$s_open_closed_tickets = $row[11];
$s_view_unauthorized_tickets = $row[12];
$s_view_department_tickets = $row[13];
$s_add_categories = $row[14];
$s_delete_categories = $row[15];
$s_add_departments = $row[16];
$s_delete_departments = $row[17];
$s_manage_users = $row[18];
$s_pref_viewall = $row[19];
$s_add_parts = $row[20];
$s_isroot = $row[21];
$s_pref_viewjobs_first = $row[22];
$s_generate_reports = $row[23];
$s_ismanager = $row[24];
if (!isset($whattodo) && $s_pref_viewjobs_first == 1) {
  $whattodo="viewjobs";
}


//Get department access permissions

if ($s_isroot == 1) {
  $query = "SELECT * FROM department order by d_name;";
  $mysql_result = query($query);
  while ($row = mysql_fetch_row($mysql_result)) {
    $query2 = "SELECT d_name FROM userdepartments ";
    $query2 .= "WHERE s_user=\"$user\" order by d_name;";
    $mysql_result2 = query($query2);
    $dontadd = 0;
    while ($row2 = mysql_fetch_row($mysql_result2)) {
   	if ($row[0] == $row2[0]) {
		$dontadd = 1;
      	}
    }
    if ($dontadd == 0) {
	$newdepartment = addslashes($row[0]);
  	$query3 = "INSERT INTO userdepartments ";
  	$query3 .= "VALUES (\"\", \"$user\", \"$newdepartment\");";
	$mysql_result3 = query($query3);
    }
  }
}

$query = "SELECT d_name FROM userdepartments ";
$query .= "WHERE s_user=\"$user\" order by d_name;";
$mysql_result = query($query);
for ($i=0;$row = mysql_fetch_row($mysql_result); $i++) {
  $departments[$i] = "$row[0]";
}


?>
