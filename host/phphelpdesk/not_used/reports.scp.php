<?PHP   // Reporting main menu

include("includes/functions.inc.php");

?>

<TABLE
  BGCOLOR="<?echo $html_table_bgcolor;?>"
  BORDERCOLOR="<?echo $html_table_bordercolor;?>"
  BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
  BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
  CELLSPACING=0 CELLPADDING=5
  BORDER=1 ALIGN=CENTER>

 <tr><td BGCOLOR="#000080">
  <p align=left><font color="#FFFFFF"><b>
   <? echo "$g_title"; ?>  - Reporting Options</b></font>
  </td></tr>
  <tr><td>
  <TABLE BORDER=0>
  <tr><td>

<?			
echo "Please select from the following reports:<p>\n";
echo "<table border=0 align=center cellpadding=5>\n";
addTableRow( "reports/closed", "Closed Log Status Report",
             "The original report.  You can specify a date range." );
addTableRow( "reports/user", "User activity report",
             "Statistics about each user, such as who is opening logs " .
	     "and who is closing them." );
addTableRow( "reports/responsetime", "Respone Time", 
             "Technician Response Time" );
addTableRow( "reports/security", "Security Audit", 
             "Security Audit Report.  Shows all users and what options " .
	     "they have." );
echo "</table>\n";

function addTableRow( $whattodo, $linkText, $descText ) {
    global $g_base_url;
    
    echo "<tr><td><a href=\"$g_base_url/index.php?whattodo=$whattodo\">";
    echo "$linkText</a>";
    echo "</td><td>$descText</td></tr>\n";
}

?>
</td></tr></table></td></tr></table>

