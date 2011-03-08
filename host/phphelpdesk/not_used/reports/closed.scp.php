<?php	// PHPHelpdesk - Reporting Features 

include("includes/functions.inc.php");

if ($s_generate_reports == 1) { 		//check that they have rights
?>

<div align=center>
<center>
<table BACKGROUND="<?echo $i_table_background;?>" BGCOLOR="<?echo $html_table_bgcolor;?>"
       BORDERCOLOR="<?echo $html_table_bordercolor;?>" BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
       BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>" CELLSPACING=0 CELLPADDING=3 border=1 width=70%>
  <tr>
    <td width="100%" valign=center bgcolor="#000080" align=left colspan=2>
      <p align=left><font color="#FFFFFF"><b><?echo $g_title;?> - Reporting Features
      <?
        if (isset($cmdGenerateReport)) {
	  print " - ".$lstSelectDepartment;
	}
      ?>
      </b></font>
    </td>
  </tr>
  <tr>
    <td width="100%" align=left colspan=2>
      <table border=0 width="100%" cellspacing=0 cellpadding=0>
        <tr>
          <td width="60%" align=center valign=middle colspan=2>
          <p align=left>
          1) Select the 
<? if($g_dept_or_comp == 0) {
     print "department";
   }
   else {
	print "company";
   }
?>
 that you would like to run a report for.<BR>
          2) Input the Beginning Date and End Date in the format 2001-02-23.<BR>
          3) Run the report.<BR>
          </p>
          <form method=POST action="<?echo
$g_base_url;?>/index.php?whattodo=reports/closed&rpt=1">
          <select size=1 name="lstSelectDepartment">
            <option value="All" selected>All Departments</option>
<?
  // Get all the departments/companies
  $query ="SELECT * FROM department;";
  $mysql_result = query($query);
  while ($row = mysql_fetch_row($mysql_result)) {
    print "<option value=\"$row[0]\">$row[0]</option>";
  }
?>
          </select>
          Start Date: <input type=text name="txtStartDate" size=10>
	  <? if($g_enable_javascript==1) { ?>
	  <input name="b1" type="button" value="..." onClick="javascript:pedirFecha(txtStartDate,'Start Date');">
	  <? } ?>
          End Date: <input type=text name="txtEndDate" size=10>
	  <? if($g_enable_javascript==1) { ?>
	  <input name="b2" type="button" value="..." onClick="javascript:pedirFecha(txtEndDate,'End Date');">
	  <? } ?>
          <BR><input type=submit value="Generate This Report" name="cmdGenerateReport">
          </form>
          </td>
        </tr>
	<tr><td colspan=1><td></tr>
      </table>
    </td>
  </tr>
</table>
</center>
</div>

<?
  if (isset($cmdGenerateReport)) {
    include("scripts/generatereport.scp.php");
  }
}
  else {
    print "<CENTER><BR><B>ERROR:</B> <I>Reporting features were selected\n";
    print " and you do <B>not</B> have privileges to view them!</I><BR><BR>\n";
    print "<I>PLEASE CHOOSE ANOTHER OPTION</I><BR><BR></CENTER>\n";
  }
?>
