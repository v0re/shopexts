<?PHP  // Security Audit report

include("includes/functions.inc.php");

$fields = array (
"User ID",
"First Name",
"Last Name",
"Password",
"Last On",
"E-Mail",
"Add Job",
"Auth Job",
"Assign Job",
"Update Job",
"Del Job",
"Reopen Job",
"View Unauth Job",
"View Dept. Job",
"Add Cat",
"Delete Cat",
"Add Dept.",
"Del Dept.",
"Manage Users",
"Pref. View All",
"Add Parts",
"Is Root",
"Pref. View Jobs",
"Run Rprts",
"Is Manager"
);

$skipFields = array (
3
);


if( !isset( $cmdRunReport ) ) {
?>
<TABLE
 BGCOLOR="<?echo $html_table_bgcolor;?>"
 BORDERCOLOR="<?echo $html_table_bordercolor;?>"
 BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
 BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
 CELLSPACING=0 CELLPADDING=5
 BORDER=1 ALIGN=CENTER>
	    
<tr><td BGCOLOR="#000080" colspan=25>
<p align=left><font color="#FFFFFF"><b>
<? echo "$g_title"; ?>  - Security Audit - Options</b></font>
</td></tr>
<tr><td>
What fields do you want included in the report?<p>
<form action="<? echo "$g_base_url"; ?>/index.php?whattodo=reports/security" 
 method=post>
<table>
<?
  for($x=0; $x<count($fields); $x++) {
      if( !in_array( $x, $skipFields ) ) {
          print "<tr><td>$fields[$x]</td><td><input type=checkbox checked ";
          print " name=cb$x></tr>";
      }
  }

?>
</table>
<input type=submit value="Run This Report">
<input type=hidden name=cmdRunReport>
</form>
</td></tr></table>

<?
}
else {

if( $cb0 != "on" ) array_push( $skipFields, 0);
if( $cb1 != "on" ) array_push( $skipFields, 1);
if( $cb2 != "on" ) array_push( $skipFields, 2);
if( $cb3 != "on" ) array_push( $skipFields, 3);
if( $cb4 != "on" ) array_push( $skipFields, 4);
if( $cb5 != "on" ) array_push( $skipFields, 5);
if( $cb6 != "on" ) array_push( $skipFields, 6);
if( $cb7 != "on" ) array_push( $skipFields, 7);
if( $cb8 != "on" ) array_push( $skipFields, 8);
if( $cb9 != "on" ) array_push( $skipFields, 9);
if( $cb10 != "on" ) array_push( $skipFields, 10);
if( $cb11 != "on" ) array_push( $skipFields, 11);
if( $cb12 != "on" ) array_push( $skipFields, 12);
if( $cb13 != "on" ) array_push( $skipFields, 13);
if( $cb14 != "on" ) array_push( $skipFields, 14);
if( $cb15 != "on" ) array_push( $skipFields, 15);
if( $cb16 != "on" ) array_push( $skipFields, 16);
if( $cb17 != "on" ) array_push( $skipFields, 17);
if( $cb18 != "on" ) array_push( $skipFields, 18);
if( $cb19 != "on" ) array_push( $skipFields, 19);
if( $cb20 != "on" ) array_push( $skipFields, 20);
if( $cb21 != "on" ) array_push( $skipFields, 21);
if( $cb22 != "on" ) array_push( $skipFields, 22);
if( $cb23 != "on" ) array_push( $skipFields, 23);
if( $cb24 != "on" ) array_push( $skipFields, 24);
if( $cb25 != "on" ) array_push( $skipFields, 25);

?>

<TABLE
 BGCOLOR="<?echo $html_table_bgcolor;?>"
 BORDERCOLOR="<?echo $html_table_bordercolor;?>"
 BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
 BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
 CELLSPACING=0 CELLPADDING=5
 BORDER=1 ALIGN=CENTER>
	    
<tr><td BGCOLOR="#000080" colspan=25>
<p align=left><font color="#FFFFFF"><b>
<? echo "$g_title"; ?>  - Security Audit</b></font>
</td></tr>

<?

// Print field headings in report
print "<tr>";
for( $x = 0; $x < count($fields); $x++ ) {
    if( in_array( $x, $skipFields ) ) continue;
    print "<th><font size=2>$fields[$x]</font></th>";
}
print "</tr>\n";

$result = query("SELECT * FROM security ORDER BY s_user;");
while( $row = mysql_fetch_row( $result ) ) {
    print "<tr>";
    for( $x=0; $x < mysql_num_fields($result); $x++ ) {
        //if( $x == 1 || $x == 2 || $x == 3 || $x == 4 || $x == 5 || $x == 22)
	//    continue;
	if( in_array( $x, $skipFields ) ) continue;
        $t = $row[$x];
	if( $t == "" )  $t = "NULL";
	if( $x >= 6 && $t == 0 )   $t = "N";
	if( $x >= 6 && $t == 1 )   $t = "Y";
        print "<td>$t</td>";
    }
    print "</tr>";
}

?>

</table>

<?
}
?>

