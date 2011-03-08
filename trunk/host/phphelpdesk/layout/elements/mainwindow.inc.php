<!-- Table for the main window when logged in. -->
<table style="position:absolute;top:162px; left:0px;height:181px;width:100%;z-index:1;" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td style="width:190px;" bgcolor="#FFFFFF">&nbsp;</td>
    <td>
<?php
	if (isset($authentication)) {
	  if (isset($whattodo)) {
	    include("scripts/$whattodo.scp.php");
	  }
	  else {
	    print "<CENTER><BR>$l_welcometothe<BR>\n";
	    print "$l_pleasechoose</CENTER><BR><BR>\n";
	  }
	}
	else {
	  if (isset($wronginfomsg)) { print "$wronginfomsg\n"; }
	}
    ?>
  </td></tr>
</table>