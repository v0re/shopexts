<?php	//PHP Helpdesk - notifications
// script to manage the eMail notifications which are send by the report wizzard;
// by cheitkamp
include("includes/functions.inc.php");
// the departments have been already loaded into the header. 
if (isset($cmdSaveSettings)) {
//foreach ($HTTP_POST_VARS as $var => $value) {
//echo "$var = $value<br>\n";       }
	  unset($cmdSaveSettings);
	  for ($i=0; $i < sizeof($departments);$i++) {
  		$query = "UPDATE department SET ";
		$query .= "d_email_notification1='$lstAssignedto1[$i]', ";
		$query .= "d_email_notification2='$lstAssignedto2[$i]' ";
		$query .= "WHERE d_name='$departments[$i]';";
		$mysql_result = query($query);
		if ($mysql_result) {
				$okay=TRUE;
		  }
		  else {
		    print "MYSQL $l_error ".mysql_error("$mysql_result");
		  }
	} // close for loop
	// now write the category data into the DB.HTTP_POST_VARS:catassignedto[$i][$c_i] 	
   
    for ($d_i=0;$d_i < sizeof($catassignedto);$d_i++) {
		$query = "SELECT r_name FROM request ";
		$query .= "WHERE r_department='$departments[$d_i]';";
		$mysql_result = query($query);
		for ($i=0;$row = mysql_fetch_row($mysql_result); $i++) {
			$request[$i] = "$row[0]";}
 		for ($c_i=0;$c_i < sizeof($catassignedto[$d_i]);$c_i++){
			$assign_to_this = $catassignedto[$d_i][$c_i]; // had to add this var since it seems that in '' or "" it isnt accepted!
			$query = "UPDATE request SET ";
			$query .= "r_assignto = '$assign_to_this' "; 
			$query .= "WHERE r_department='$departments[$d_i]' AND r_name = '$request[$c_i]';";
		$mysql_result = query($query);
		if ($mysql_result) {
				$okay=TRUE;
		  }
		  else {
		    print "MYSQL $l_error ".mysql_error("$mysql_result");
		  }
		}//end for-loop requests
	}//end for loop for departments
   if ($okay==1){ print "<div class=successtxt>$l_prev_saved</div>";}
} // close if clause	
else { 
?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=notifications">
<table 	CELLSPACING=0 CELLPADDING=5 border=1 width=80% align="center">
  <tr><th>
     <? echo $g_title;?> - <?echo $l_notifications_titel ?>
    </th> </tr>
  <tr>
    <td width="100%" align=left>
      <table border=0 width="100%" cellspacing=0 cellpadding=5>
          <tr> 
            <td colspan ="4" align="center"> <?php echo "$l_notifications_explain"; ?> 
            </td>
          </tr>
          <tr> 
            <td align= center bgcolor="<? echo $html_highlight_color; ?>"><b><? echo "$l_department/$l_requests";?></b></td>
            <td align= left bgcolor="<? echo $html_highlight_color; ?>"><b><? echo $l_assignto;?></b></td>
            <td align= center bgcolor="<? echo $html_alt_color1; ?>"><b><? echo $l_notifications_email1;?></b></td>
            <td align= center bgcolor="<? echo $html_alt_color2; ?>"><b><? echo $l_notifications_email2;?></b></td>
          </tr>
          <tr> 
            <td colspan =2 bgcolor="<? echo $html_highlight_color; ?>">&nbsp;</td>
            <td bgcolor="<? echo $html_alt_color1; ?>">&nbsp;</td>
            <td bgcolor="<? echo $html_alt_color2; ?>">&nbsp;</td>
          </tr>
          <?PHP // start the loop to print department names and select forms to chosse assingment
		   for ($i=0; $i < sizeof($departments);$i++) {
				// read data from db for editing mode.
				$query =  "SELECT d_email_notification1, d_email_notification2 FROM department ";
				$query .= "WHERE d_name = '$departments[$i]';";
      		$mysqlresult_selects = query($query);
      		$select_this = mysql_fetch_row($mysqlresult_selects);
    		?>
          <tr> 
            <td bgcolor="<? echo $html_highlight_color; ?>" colspan = 2 align=left> 
              <b><? echo $departments[$i]; ?></b> </td>
            <td width="21%" bgcolor="<? echo $html_alt_color1; ?>" align=center> 
              <select name=<?echo "lstAssignedto1[$i]";?> >
                <?PHP
    		    print "<option value=\"none\">none</option>\n";
				$query = "SELECT s_user FROM userdepartments ";
		      $query .= "WHERE d_name='$departments[$i]';";
      		$mysqlresult = query($query);
      		while ($row = mysql_fetch_row($mysqlresult)) {
					if ($row[0] == $select_this[0]) {
	      		    print "<option value=\"$row[0]\" selected>$row[0]</option>\n";
					}
					else {
	      		    print "<option value=\"$row[0]\">$row[0]</option>\n";
					}
		  		} // end for loop for departments
		    ?>
              </select> </td>
            <td width="25%" bgcolor="<? echo $html_alt_color2; ?>" align=center> 
              <select name=<?echo "lstAssignedto2[$i]";?> >
                <?PHP
    		    print "<option value=\"none\">none</option>\n";
		      $query = "SELECT s_user FROM userdepartments ";
		      $query .= "WHERE d_name='$departments[$i]';";
      		$mysqlresult = query($query);
      		while ($row = mysql_fetch_row($mysqlresult)) {
				if ($row[0] == $select_this[1]) {
      		    print "<option value=\"$row[0]\" selected>$row[0]</option>\n";
				}
				else {
      		    print "<option value=\"$row[0]\">$row[0]</option>\n";
				}
      		} // end while loop
		    ?>
              </select> </td>
          </tr>
          <tr> 
            <?PHP // beginn listing requests
		  	$query = "SELECT r_name, r_assignto FROM request ";
		  	$query .=" WHERE r_department ='$departments[$i]';";
      	$mysqlresult_request = query($query);  		
   	   for ($c_i=0; $request = mysql_fetch_row($mysqlresult_request);$c_i++) { ?>
            <td bgcolor="<? echo $html_highlight_color; ?>" width="31%" align=left> 
              <? echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$request[0]; ?> </td>
            <td bgcolor="<? echo $html_highlight_color; ?>" width="23%" align=left> 
              <select name=<?echo "catassignedto[$i][$c_i]";?> >
                <?PHP
    		    print "<option value=\"none\">none</option>\n";
		      $query = "SELECT s_user FROM userdepartments ";
		      $query .= "WHERE d_name='$departments[$i]';";
      		$mysqlresult = query($query);
      		while ($c_row = mysql_fetch_row($mysqlresult)) {
				if ($c_row[0] == $request[1]) {
      		    print "<option value=\"$c_row[0]\" selected>$c_row[0]</option>\n";
				}
				else {
      		    print "<option value=\"$c_row[0]\">$c_row[0]</option>\n";
				} 
				} // und while loop
			?>
              </select> </td>
            <td width="21%" align=left bgcolor="<? echo $html_alt_color1; ?>">&nbsp;</td>
            <td width="25%" align=left bgcolor="<? echo $html_alt_color2; ?>">&nbsp;</td>
          </tr>
          <?PHP		   
		   } // end listing of requests
		  	?>
          <?	} // end of listing departments
    		?>
          <tr> 
            <td colspan ="4" align="right"> <input type="submit" value="<?echo $l_savechanges?>" name="cmdSaveSettings"> 
            </td>
          </tr>
        </table>
	</td>
 </tr>
</table>
 </form>
<?PHP } // close if cmdSaveSettings ?>

