<?php	// ADD DEPARTMENT SCRIPT
include("includes/functions.inc.php");
$veri_err=0;
if ($s_add_departments == 1) {  //check if user has permissions
	if (isset($cmdAdddepartment)) {  //when form is posted
	if (!isset($txtDepartmentKey)||($txtDepartmentname == NULL)) { // must be set since it will be used for the $et_ids
		$veri_err=1;$add_d_err[1]=1;
		}
	//check if department/company is not already there, 
	//since there would be a foult since (table, has not aut_incerease id)
	else {
    $query = "SELECT d_name, d_depkey FROM department";
    $mysql_result = query($query);
    while($row = mysql_fetch_row($mysql_result)) {
		 if ($txtDepartmentname==$row[0]) { $veri_err=1;$add_d_err[2]=1;	}
		 if ($txtDepartmentKey==$row[1]) { $veri_err=1;$add_d_err[3]=1;	}			
	 }//end while loop	
	} //end else part 
	if (!isset($txtDepartmentname) || ($txtDepartmentname == NULL)){
	 $veri_err=1;$add_d_err[4]=1;	}			
	if($veri_err==0) { //now go ahead.
    $txtDepartmentKey = addslashes($txtDepartmentKey);
    $txtDepartmentname = addslashes($txtDepartmentname);
    $query = "INSERT INTO department (d_name, d_depkey) VALUES ('$txtDepartmentname', '$txtDepartmentKey');";
    $mysql_result = query($query);
    // Print results
    print "<BR>";
	 $txtDepartmentKey = stripslashes($txtDepartmentKey);
    $txtDepartmentname = stripslashes($txtDepartmentname);
    if ($mysql_result) {
      print "<div class=successtxt>$txtDepartmentname ($txtDepartmentKey) $l_wasadded</div>\n";
    }
    else {  
      print "<div class=errortxt><B>ERROR:</B>";
      print " $txtDepartmentname ($txtDepartmentKey) $l_wasnotadded<br>\n";
      print "<BR></div>\n";
    }
	 // add the user which has the department added to the department
    $txtDepartmentname = addslashes($txtDepartmentname);
    $query = "INSERT INTO userdepartments (s_user, d_name) ";
    $query .= "VALUES ('$user', '$txtDepartmentname');";
    $mysql_result = query($query);
    // Print results
    $txtDepartmentname = stripslashes($txtDepartmentname);
    if ($mysql_result) {
			print "<div class=successtxt>$user $l_added</div>\n";
    }
    else {  
      print "<BR>\n";
    }//from mysql
	 // create now standart request category
    $query = "INSERT INTO requestcategories (rc_department, rc_name) ";
    $query .= "VALUES ('$txtDepartmentname', '$txtDepartmentname');";
    $mysql_result = query($query);
    if ($mysql_result) {
			print "<div class=successtxt>$l_default_requestcat_added</div>";}
	 // create now standart request 
    $query = "INSERT INTO request (r_department, r_name, r_category) ";
    $query .= "VALUES ('$txtDepartmentname', '$l_default_category_name', '$txtDepartmentname');";
    $mysql_result = query($query);
    if ($mysql_result) {
			print "<div class=successtxt>$l_default_request_added</div>";}
			
  }//close if clause if al is correct and dep added.
  }// close  form is posted 
if ((!isset($cmdAdddepartment)) || ($veri_err==1)) { //start form here.
?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=adddepartment">
<table align="center" CELLSPACING=0 CELLPADDING=3 border=1 width=70%>
  <tr>
    <th width="100%" valign=center bgcolor="#000080" align=left>
		<? echo $g_title;?> - <? if ($g_dept_or_comp == 0) { print "$l_adddepartmentform"; } else { print "$l_addcompanyform";} ?></th>
    </tr>
    <tr>
      <td width="100%" align=left>
        <table border=0 width="100%" cellspacing=0 cellpadding=3>
          <tr>
            <td width="35%" valign=center align=right>
	     <? if ($g_dept_or_comp == 0) { print "$l_newdepartmentname"; } else { print "$l_newcompanyname";} ?> 
		    </td>
            <td width="30%">
		      <input type=text name="txtDepartmentname" size=25>
		    </td>
            <td width="35%" valign=center align=left>   
				<div class="errortxt">
	      <?php if ($add_d_err[1]==1){echo "$l_givedepname";}
			      if ($add_d_err[2]==1){echo "$l_depalreadyexists";}
	       ?>
			</div>
	    </td>
          </tr>
          <tr>
            <td width="35%" valign=center align=right>
	      <? if ($g_dept_or_comp == 0) { print "$l_newdepartmentkey:"; } else { print "$l_newcompanykey:";} ?> 
	    </td>
            <td width="30%">
	      <input type=text name="txtDepartmentKey" size=25>
			</td>
          <td width="35%" valign=center align=left>   
		 <div class="errortxt">
	      <?php if ($add_d_err[4]==1){echo "$l_givedepkey";}
			      if ($add_d_err[3]==1){echo "$l_depkeyalreadyexists";}
	       ?>
			</div>	       
	    </td> </tr>
          <tr><td valign=center align=right><?if ($g_dept_or_comp == 0) { print "$l_currentdepartments"; } else { print "$l_currentcompanies";} ?>
	    </td>
            <td colspan="2" >
	      <select size=1 name="lstDepartment">
<?php
/////////////////////////////////////////////////////////////////////////
// query the database and input all information into the department list

$query = "SELECT d_name FROM department;";
$mysql_result = query($query);
while($row = mysql_fetch_row($mysql_result)) {
  print "                  <option value=\"$row[0]\">$row[0]</option>\n";
}
?>
     </select>
	    </td></tr>
		<tr><td colspan="3" align="center">
		<input type=submit value="<?echo $l_add?>" name="cmdAdddepartment">
	    </td></tr>
        </table>
	    </td></tr>
    </table>
</form>

<?php
  }
} // end check permissions
else { // display if no permissions
  print "<CENTER><BR><B>$l_error</B> <I>Add Department was selected\n";
  print " and you do <B>not</B> have root privileges!</I><BR><BR>\n";
  print "<I>PLEASE CHOOSE ANOTHER OPTION</I><BR><BR></CENTER>\n";
}
?>
