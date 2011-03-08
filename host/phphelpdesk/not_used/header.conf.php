<?php // HTML HEADER INFO ?>
<html>
<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <? if ($whattodo == "viewjobs") { ?>
<META HTTP-EQUIV="Refresh" CONTENT="<?echo $g_refreshtime;?>;URL=
<?echo $g_base_url;?>/index.php?whattodo=viewjobs">
  <? } ?>
  <title><?echo $g_title;?></title>
<?php if ($g_enable_javascript==1) { ?>

<? } ?>
<link rel='stylesheet' type='text/css' href='css/main.css'>
<script type='text/javascript' src='javascript/menu9.js'></script>

</head>
<body class = "main" topmargin=0 leftmargin=0>
<? /*
<body class "main" background="<?echo $html_background;?>" bgcolor="<?echo $html_body_color;?>" 
      link=<?echo $html_link;?> vlink=<?echo $html_vlink;?> alink=<?echo $html_alink;?> 
      topmargin=0 leftmargin=0> */

if( isset($rpt) ) return; ?>
<table class = "maintable" cellspacing=0 cellpadding=0> <!-- main table begins-->
  <tr>
    <td class ="maintablecell" width="100%"> 
    <!-- Header Table begins -->
      <table border ="1" class = "header_table" cellspacing=0 cellpadding=0>
        <tr>
          <td style = "padding:5px;" width="26%">
            <center><b><font size=4><?echo $g_title;?></font></b><br>
            <br><?echo $l_version?> <?echo  $version;?></center>
			  </td>
<?PHP
//////////////////////////////////////////////////////////////
// Start setup for status information in the header

include("includes/functions.inc.php");

if (isset($authentication)) {
	
  print "          <td style = 'padding:5px;' width=\"50%\"><b>$l_status:</b> $status<br>\n";
  print "            <b>$l_username:</b> $user<br>\n";
  if ($g_dept_or_comp == 0) {
    print "            <b>$l_departments:</b> ";
  }
  else {
    print "            <b>$l_companies:</b> ";
  }
  include("includes/connect.inc.php"); 
  $query = "SELECT d_name FROM userdepartments ";
  $query .= "WHERE s_user=\"$user\" order by d_name;";
  $mysql_result = query($query);
  $needcommaflag = 0;
  for ($i=0;$row = mysql_fetch_row($mysql_result); $i++) {
    $departments[$i] = "$row[0]";
    if ($needcommaflag == 1) { 
      print ", ";
    }
    print "$departments[$i]";
    $needcommaflag = 1;
  } 
  print "            <br>\n";
  print "            <b>$l_laston:</b> $laston</font></td>\n";
}
else {
  print "          <td style = 'padding:5px;' width=\"50%\"><b>$l_status</b> n/a<br>\n";
  print "            <b>$l_username</b> n/a<br>\n";
  print "            <b>$l_departments</b> n/a<br>\n";
  print "            <b>$l_laston</b> n/a</td>\n";
}
echo "<td style = 'padding:5px;'>";
	$ticktarray = ticketSummary(); 
   print "$l_opentickets: $ticktarray[0]<br>\n";
   print "$l_assignadoausario: $ticktarray[1]\n</td>"  

// End setup for status information in the header
//////////////////////////////////////////////////////////////
?>
        </tr>
        </table> <!-- close header table tag -->
        </td></tr><!-- close cell for header from main table -->
<tr><td>
<table width = "100%">
<tr>
	<td width ="10%">&nbsp;</td>
	<td width ="90%"><div id='mnuMarker' class='marker'></div></td>
</tr>
</table>
</td></tr>        
