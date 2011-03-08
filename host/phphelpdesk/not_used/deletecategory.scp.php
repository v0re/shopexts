<?php	// DELETE  CATEGORY SCRIPT 
include("includes/functions.inc.php");

if ($s_delete_categories == 1) {
  if (sizeof($departments) < 2) {
    $lstDepartment = $departments[0];
  }
  if (isset($lstDepartment)) {
    if(isset($lstCurrentCategories)) {
      $query = "DELETE FROM category ";
      $query .= "WHERE c_department='$lstDepartment' AND c_name='$lstCurrentCategories';";

      $mysql_result = query($query);

      // Print results
      print "<BR>";
      if ($mysql_result) {
        print "$lstCurrentCategories $l_userhasbeendeleted<BR>\n";
        print "<BR>\n";
      }
      else {  
        print "<B>ERROR:</B>";
        print " $lstCurrentCategories $l_wasnotdeleted<br>\n";
        print "<BR>\n";
      }
    }
    else {

?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=deletecategory">
<div align=center>
<center>
<table BACKGROUND="<?echo $html_table_background;?>" BGCOLOR="<?echo $html_table_bgcolor;?>" 
       BORDERCOLOR="<?echo $html_table_bordercolor;?>" BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>" 
       BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>" CELLSPACING=0 CELLPADDING=3  border=1 width=60%>
  <tr>
    <td width="100%" valign=center bgcolor="#000080" align=left>
      <p align=left><font color="#FFFFFF"><b><?echo $g_title;?> - <?echo $l_deletecategoryform?></b></font>
    </td>
  </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0 width="100%" cellspacing=0 cellpadding=0>
        <tr>
	  <td width=10%>
	    <input type=hidden value="<?echo $lstDepartment;?>" name="lstDepartment">
	  </td>
          <td width="30%" valign=center align=right>
 	    <font size=3><?echo $l_currentcategories?></font>
          </td>
          <td width="60%">
	   <select size=1 name="lstCurrentCategories">
<?php
    //////////////////////////////////////////////////////////////////////
    // query the database and input all info from categories into the listbox
    $query = "SELECT c_name FROM category WHERE c_department='$lstDepartment' "
             . " ORDER BY c_name;";
    $mysql_result = query($query);
    while ($row = mysql_fetch_row($mysql_result)) {
      if ($row[0]	!= "$l_default_category_name") {
        print "                  <option value=\"$row[0]\">$row[0]</option>\n";}
    }
?>
           </select>
    	  </td>
	</tr>
      </table>
      <p align=center><center>
      <input type=submit value="<?echo $l_deletethiscategory?> <?echo $lstDepartment;?>" name="cmdDeletecategory">
    </td></center>
  </tr>
</table>
</center>
</div>
</form>
<?
    }
  }
  else { 
?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=deletecategory">
<div align=center>
<center>
<table BACKGROUND="<?echo $html_table_background;?>" BGCOLOR="<?echo $html_table_bgcolor;?>" 
       BORDERCOLOR="<?echo $html_table_bordercolor;?>" BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>" 
       BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>" CELLSPACING=0 CELLPADDING=3  border=1 width=60%>
  <tr>
    <td width="100%" valign=center bgcolor="#000080" align=left>
      <p align=left><font color="#FFFFFF"><b><?echo $g_title;?> - <?echo $l_deletecategoryform?></b></font>
   </td>
 </tr>
 <tr>
   <td width="100%" align=left>
     <table border=0 width="100%" cellspacing=0 cellpadding=0>
       <tr>
         <td width="40%" valign=center align=right><?if ($g_dept_or_comp == 0) { print "$l_department:"; } else { print "$l_company:"; } ?> 
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
     <p align=center><center>
     <input type=submit value="<?echo $l_choosethisdepartment?>" name="cmdChooseDepartment">
    </td></center>
  </tr>
</table>
</center>
</div>
</form>

<?
  }
}
else { 
  print "<CENTER>You do not have privileges to delete categories!</CENTER>";
}
?>
