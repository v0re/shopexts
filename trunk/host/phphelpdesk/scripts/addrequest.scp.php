<?php	// ADD request SCRIPT based on the old category source 
		// by cheitkamp
include("includes/functions.inc.php");
if ($s_add_categories == 1) { // evntually to be changed.
  if (sizeof($departments) < 2) {
    $lstDepartment = $departments[0];
  }
  if (isset($lstDepartment)) {
  	if (isset($cmdAddrequest)) {
    if(isset($txtRequestname) && ($txtRequestname != NULL) && (isset($lstCurrentRequests) || $txtnewCategory != NULL)) {
			if ($txtnewCategory != NULL) { $txtRequestCategory=$txtnewCategory;}
			else { $txtRequestCategory=$lstCurrentRequests; }
		//insert request into DB
      $query = "INSERT INTO request (r_department, r_name, r_category) ";
      $query .= "VALUES (\"$lstDepartment\", \"$txtRequestname\", \"$txtRequestCategory\");";
      $mysql_result = query($query);
      // Print results
      print "<BR>";
      if ($mysql_result) {
  		  $r_id = mysql_insert_id();
        print "<div class=successtxt>$txtRequestname $l_wasadded</div><BR>\n";
        print "<BR>\n";
      }  else {  
        print "<div class=errortxt><B>ERROR:</B>";
        print "$txtRequestname $l_wasnotadded<br>\n";
        print "<BR></div>\n";
      }    // end mysql result
		if ($txtnewCategory != NULL) { //insert new request category into DB 
      	$query = "INSERT INTO requestcategories (rc_id, rc_r_id, rc_department, rc_name) ";
      	$query .= "VALUES (NULL, '$r_id','$lstDepartment',  \"$txtRequestCategory\");";
      	$mysql_result = query($query);
      	// Print results
      	print "<BR>";
      	if ($mysql_result) {
        		print "<div class=successtxt>$txtRequestname $l_wasadded</div><BR>\n";
        		print "<BR>\n";
      	}  else {  
        		print "<div class=errortxt><B>ERROR:</B>";
        		print " $txtRequestname $l_wasnotadded<br>\n";
        		print "<BR></div>\n";
      	} //end mysql result
		} // end insert request category
		} // end insert request 
		else { // give answers to the verificacion
				print "<div class=errortxt><B>ERROR: </B>";
        		print "$l_requesterrortext $l_wasnotadded<br>\n";
        		print "<BR></div>\n";
		}  
		} // CLOSE (isset($cmdAddrequest))
// print form
?>
<table width="60%" align="center" cellpadding="5" border="1">
  <tr>
    <th width="100%" valign=center align=left>
	<? echo $g_title;?> - <? echo $l_addrequestform?>
	</th>
  </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0  width="100%" cellspacing=5 cellpadding=3>
		<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=addrequest">
		<tr>
          <td width="50%" align="center">
 	    <? echo $l_requestname ?>
          </td>
          <td colspan = 2 width="50%">
 	    <input type=text name="txtRequestname" size=30>
    	  </td>
        </tr>
		<tr>
			<td colspan="2" width="50%" align="center"><input type=hidden value="<?echo $lstDepartment;?>" name="lstDepartment">
 	    	<? echo $l_requestcategories ?></td>
			<td align="center"><? echo $l_addrequestcategory ?></td>
		</tr>
        <tr>
       <td colspan="2" width="30%" valign=center align=center>
	   <select size=1 name="lstCurrentRequests">
<?
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from Request Categorys into the listbox
    $query = "SELECT rc_name FROM requestcategories ";
    $query .= "WHERE rc_department='$lstDepartment' ORDER BY rc_name;";
    $mysql_result = query($query);
    while ($row = mysql_fetch_row($mysql_result)) {
        print "                  <option value=\"$row[0]\">$row[0]</option>\n";
	  
    }
?>
           </select>
    	  </td>
		<td align="center"><input type="text" name="txtnewCategory" size="10" maxlength="100">     
		</td>
		</tr><tr>
        <td colspan="3" width="60%" align="center">
       		<input type=submit value="<?echo $l_addthisrequest?> " name="cmdAddrequest">
         </td>        
		</form>
	</tr>
      </table>
    </td>
  </tr>
</table>
<?php
   } else { // select from the differnt deparments
?>
<form method=POST action="<? echo $g_base_url; ?>/index.php?whattodo=addrequest">
<table border="1" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th width="100%" valign=center  align=left>
      <?echo $g_title;?> - <?echo $l_addrequestform?>
   </th>
 </tr>
 <tr>
   <td width="100%" align=left>
     <table border=0 width="100%" cellspacing=3 cellpadding=3>
       <tr>
         <td width="40%" valign=center align=right><?if ($g_dept_or_comp == 0) { print "$l_department: "; } else { print "$l_company:"; } ?> 
	 </td>
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
	 </td>
       </tr>
     </table>
	 </td></tr>
	 <tr><td align="center">
<?
if ($g_dept_or_comp == 0) { ?>
     <input type=submit value="<?echo $l_choosethisdepartment?>" name="cmdChooseDepartment">
<?
}
else { ?>
     <input type=submit value="<?echo $l_choosethiscompany?>" name="cmdChooseDepartment">
<?
}
?>
    </td>
  </tr>
</table>
</form>

<?
  } // close the first page which is executed  if there are more then 1 department. 
} // close main section which is executed when the user has priveliges
else {  
  print "<CENTER>You do not have privileges to add Requests!</CENTER>";
}
?>
