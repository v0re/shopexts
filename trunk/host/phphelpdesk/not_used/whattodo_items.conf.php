<?php	//WHAT TO DO ITEMS AT THE TOP OF THE HTML BODY

if( isset( $rpt ) ) return;  // Don't print menu on reports..
// Check user permissions
include("includes/permissions.inc.php");
//$s_register_new_tickets = 1;
//$s_view_department_tickets =0;
//$s_manage_users =0;
//$s_add_parts = 0;
//$s_add_departments = 0;	
//$s_delete_departments =0;
?>
 

<BR><center>[

<!-- The table is for when the menu downgrades -->
<?PHP
if ($s_register_new_tickets == 1) {
  print "<div id='i11' class='mItem'><a class ='m' href=\'$g_base_url/index.php?whattodo=addjob\'>$l_addjob</a>\n'</div>";
}

if ($s_view_department_tickets == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=viewjobs\">$l_viewjobs</a>\n";

if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=search\">";
  print "$l_search</a>\n";
}


if ($s_manage_users == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=adduser\">$l_adduser</a>\n";
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=modifyuser\">";
  print "$l_modifyuser</a>\n";
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=deleteuser\">";
  print "$l_deleteuser</a>\n";
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=notifications\">";
  print "$l_notifications</a>\n";
}

if ($s_add_categories == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=addrequest\">";
  print "$l_addrequest</a>\n";
}

if ($s_delete_categories == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=deleterequest\">";
  print "$l_deleterequest</a>\n";
}

if ($s_add_departments == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=adddepartment\">";
  if ($g_dept_or_comp == 0) {
    print "$l_adddepartment</a>\n";
  }
  else {
    print "$l_addcompany</a>\n";
  }
}

if ($s_delete_departments == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=deletedepartment\">";
  if ($g_dept_or_comp == 0) {
    print "$l_deletedepartment</a>\n";
  }
  else {
    print "$l_deletecompany</a>\n";
  }
}

if ($s_add_parts == 1 && $g_include_parts_management == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=addparts\">";
  print "$l_addparts</a>\n";
}

if ($s_generate_reports == 1) {
  if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
  print "<a href=\"$g_base_url/index.php?whattodo=reports\">";
  print "$l_reports</a>\n";
}

if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
print "<a href=\"$g_base_url/index.php?whattodo=preferences\">";
print "$l_preferences</a>\n";

//since not implemented and localized cut out.
//if ($pipeflag == 1) { print " | "; } else { $pipeflag = 1; }
//print "<a href=\"$g_base_url/index.php?whattodo=help/main\">";
//print "$l_help</a>\n";


?>
| <a href="<?echo $g_base_url;?>/index.php?logout=true"><?echo $l_logout?></a>
]</center><br><br>
