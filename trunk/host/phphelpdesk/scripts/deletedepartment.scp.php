<?php	// DELETE DEPARTMENT SCRIPT
include("includes/functions.inc.php");
if ($s_delete_departments == 1) { // check rights
  if(isset($lstDepartment)) {
    //Delete the department from the department table
    $query = "DELETE FROM department WHERE d_name='$lstDepartment';";
    $mysql_result = query($query);
    // Print results
    print "<BR>";
    $lstDepartment = stripslashes($lstDepartment);
    if ($mysql_result) {
      print "<div class=successtxt>$lstDepartment $l_userhasbeendeleted<BR>\n";
      print "<BR></div>\n";
    }
    else {  
      print "<div class=errortxt><B>$l_error</B>";
      print " $lstDepartment $l_wasnotdeleted <br>\n";
      print "<BR></div>\n";
    }
    // Now delete the requests and request categories belonging to the department+
    $query = "DELETE FROM request WHERE r_department=\"$lstDepartment\";";
    $mysql_result = query($query);
    $query = "DELETE FROM requestcategories WHERE rc_department=\"$lstDepartment\";";
    $mysql_result = query($query);
    print "<BR>";
    if ($mysql_result) {
      print "<div class=successtxt>$lstDepartment $l_userhasbeendeleted<BR>\n";
      print "<BR></div>\n";
    }
    else {  
      print "<div class=errortxt><B>$l_error</B>";
      print " $lstDepartment $l_wasnotdeleted<br>\n";
      print "<BR></div>\n";
    } 
    // Now delete this department from the userdepartments table
    $query = "DELETE FROM userdepartments WHERE d_name=\"$lstDepartment\";";
    $mysql_result = query($query);
    print "<BR>";
    if ($mysql_result) {
      print "<div class=successtxt>$lstDepartment $l_userhasbeendeleted<BR>\n";
      print "<BR></div>\n";
    }
    else {  
      print "<div class=errortxt><B>$l_error</B>";
      print " $lstDepartment $l_wasnotdeleted<br>\n";
      print "<BR></div>\n";
    }
  }
  else {
?>
<form method=POST action="<? echo $g_base_url;?>/index.php?whattodo=deletedepartment">
<table CELLSPACING=0 CELLPADDING=3 border=1 width=50% align="center">
  <tr>
    <th width="100%" valign=center bgcolor="#000080" align=left>
	<? echo $g_title;?> - <? if ($g_dept_or_comp == 0) { print "$l_deletedepartmentform"; } else { print "$l_deletecompanyform";} ?> </th>
    </tr> <tr>  <td width="100%" align=left>
        <table border=0 width="100%" cellspacing=0 cellpadding=5 align="center">
          <tr>
            <td width="40%" valign=center align=right><? if ($g_dept_or_comp == 0) { print "$l_selectdepartment"; } else { print "$l_selectcompany";} ?>:	    </td>
            <td width="60%" valign=middle>
	      <select size=1 name="lstDepartment">
<?
/////////////////////////////////////////////////////////////////////////
// query the database and input all information into the department list

for ($i=0; $i < sizeof($departments); $i++) {
  print "                  <option value=\"$departments[$i]\">$departments[$i]</option>\n";
}
?>
      </select>
	    </td></tr>
        </table>
	    </td></tr>
		<tr><td align="center">
		<input type=submit value="<?echo $l_delete?>" name="cmdDeletedepartment">
		</td> </tr>
    </table>
</form>
<?
  }
}
else {
  print "<CENTER><BR><B>$l_error</B> <I>Delete Department was selected\n";
  print " and you do <B>not</B> have these privileges!</I><BR><BR>\n";
  print "<I>PLEASE CHOOSE ANOTHER OPTION</I><BR><BR></CENTER>\n";
}
?>
