<?php	// ADD request SCRIPT based on the old category source 
		// by cheitkamp
include("includes/functions.inc.php");
if ($s_delete_categories == 1) { // check if user has the right
  if (sizeof($departments) < 2) {
    $lstDepartment = $departments[0];
  }
  if (isset($lstDepartment)) {
	 if (isset($cmdDeleterequestcategory)) { // if the user likes to delete a category
		// check if the category is empty
		$query = "SELECT * FROM request WHERE r_category = '$lstCurrentRequestCategories'";
      $mysql_result = query($query);
      if (mysql_affected_rows() < 1) { // when empty then delete
      $query = "DELETE FROM requestcategories ";
      $query .= "WHERE rc_department='$lstDepartment' AND rc_name='$lstCurrentRequestCategories';";
      $mysql_result = query($query);
      // Print results
      print "<BR>";
      if ($mysql_result) {
        print "<div class=successtxt> $lstCurrentRequestCategories $l_userhasbeendeleted<BR></div>\n";
        print "<BR>\n";
      }
      else {  
        print "<div class=errortxt>ERROR: ";
        print " $lstCurrentRequests $l_wasnotdeleted<br>\n";
        print "<BR></div>\n";
      }
	 } else {
	 echo "<div class=errortxt> $l_requestcategorynotempty</div>";
	 }
	 } // end delete category part
	 
    elseif(isset($lstCurrentRequests)) {
      $query = "DELETE FROM request ";
      $query .= "WHERE r_department='$lstDepartment' AND r_name='$lstCurrentRequests';";
      $mysql_result = query($query);
      // Print results
      print "<BR>";
      if ($mysql_result) {
        print "<div class=successtxt>$lstCurrentRequests $l_userhasbeendeleted<BR></div>\n";
        print "<BR>\n";
      }
      else {  
        print "<div class=errortxt>ERROR: ";
        print " $lstCurrentRequests $l_wasnotdeleted<br>\n";
        print "<BR></div>\n";
      }
    } ?>
<table CELLSPACING="0" CELLPADDING="3"  border=1 width="60%" align="center"> 
<form method=POST action="<? echo $g_base_url;?>/index.php?whattodo=deleterequest">
  <tr>
    <th width="100%" valign="center" align="left">
      <? echo $g_title;?> - <? echo $l_deleterequestform?>
    </th>
  </tr>
  <tr> <td width="100%">
      <table width="100%" cellspacing="0" cellpadding="5" align="center">
       <tr><td align="right">
		<? echo $l_currentrequests ?><input type=hidden value="<?echo $lstDepartment;?>" name="lstDepartment">
          </td>
          <td align="left">
	   <select size=1 name="lstCurrentRequests">
<?php
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from requests into the listbox
    $query = "SELECT r_name FROM request WHERE r_department='$lstDepartment' "
             . " ORDER BY r_name;";
    $mysql_result = query($query);
    while ($row = mysql_fetch_row($mysql_result)) {
      if ($row[0]	!= "$l_default_request_name") {
        print "                  <option value=\"$row[0]\">$row[0]</option>\n";}
    }
?>
           </select>
    	  </td>
	</tr>
	<tr><td colspan="2" align="center">
      <input type=submit value="<?echo $l_deletethisrequest?>" name="cmdDeleterequest">
	</form>
    </td>
	</tr>
	<tr><td>
		<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=deleterequest">
		<tr><td align="center">
     <input type=hidden value="<?echo $lstDepartment;?>" name="lstDepartment">
    <select size=1 name="lstCurrentRequestCategories">
<?
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from Request Categorys into the listbox
    $query = "SELECT rc_name FROM requestcategories ";
    $query .= "WHERE rc_department='$lstDepartment'";
    $mysql_result = query($query);
    while ($row = mysql_fetch_row($mysql_result)) {
        print "                  <option value=\"$row[0]\">$row[0]</option>\n";
	  
    }
?>
         </select>		
		</td>
		<td align="left">
		<input type=submit value="<?echo $l_deletethisrequestcategory?>" name="cmdDeleterequestcategory">
		</td>
		</tr>
		</form>
</table>
<?
    }
  else { 
?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=deleterequest">
<table CELLSPACING=0 CELLPADDING=3  border=1 width=60% align="center">
  <tr>
    <th width="100%" valign=center bgcolor="#000080" align=center>
     <? echo $g_title; ?> - <? echo $l_deleterequestform?>
   </th>
 </tr>
 <tr>
   <td width="100%" align=left>
     <table border=0 width="100%" cellspacing=0 cellpadding=5>
       <tr>
         <td width="40%" valign=center align=center><?if ($g_dept_or_comp == 0) { print "$l_department:"; } else { print "$l_company:"; } ?> 
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
	 </td> </tr>
     </table>
    </td> </tr>
	<tr><td align="center">
     <input type=submit value="<?echo $l_choosethisdepartment?>" name="cmdChooseDepartment">
    </td>
  </tr>
</table>
</form>
<?  }
}
else { 
  print "<CENTER>You do not have privileges to delete requests!</CENTER>";
}
?>
