<? // PHP HElpdesk - Add Parts ?>

<?if (!isset($cmdAddPart) && !isset($cmdDeletePart) && !isset($cmdEditPart)) {?>
<TABLE width=100%>
  <TR BGCOLOR=#000080>
    <TD align=center>
      <FONT color=#FFFFFF>
	    <B><?echo $l_partnumber?></B>
      </FONT>
    </TD>
    <TD align=center>
      <FONT color=#FFFFFF>
        <B><?echo $l_description?></B>
      </FONT>
    </TD>
    <TD align=center>
      <FONT color=#FFFFFF>
        <B><?echo $l_price?></B>
      </FONT>
    </TD>
    <TD align=center>
      <FONT color=#FFFFFF>
	    <B><?echo $l_stockquantity?></B>
	  </FONT>
	</TD>
	<TD>
	</TD>
  </TR>
  <TR bgcolor=#eeeeee>
    <form method=POST action="<?echo $g_base_url?>/index.php?whattodo=addparts">
    <TD valign=center>
	  <?
	   if (isset($editpart)) { 
	     $query = "SELECT * FROM parts WHERE p_id='$editpart';";
	     $mysqlresult = query($query);
		 $row = mysql_fetch_row($mysqlresult);
		 if (!$mysqlresult) {
           print "$l_cantfindpartid $editpart !";
		 }
	  ?>
      <center><?echo $editpart;?><input type=hidden name=p_id size=20 value="<?echo $editpart;?>"></center>
	  <? }
	     else { ?>
      <center><input type=text name=txtPartNumber size=20 value="<?echo $l_mustbeunique?>"></center>
	  <? } ?>
    </TD>
    <TD align=center>
	  <? if (isset($editpart)) { ?>
      <input type=text name=txtDescription size=30 value="<?echo $row[0];?>">
	  <? }
	     else { ?>
      <input type=text name=txtDescription size=30>
	  <? } ?>
    </TD>
    <TD align=center>
	  <? if (isset($editpart)) { ?>
      <input type=text name=txtPrice size=6 value="<?echo $row[1];?>">
	  <? }
	     else { ?>
      <input type=text name=txtPrice size=6 value="000.00">
	  <? } ?>
    </TD>
    <TD valign=center align=center>
	  <? if (isset($editpart)) { ?>
       <?echo $l_currrentquantity?> <?echo $row[2];?><BR>
	  <?echo $l_addthisquantity?> 
      <input type=text name=txtQuantity size=3 value="0">
	  <? }
	     else { ?>
      <input type=text name=txtQuantity size=3 value="000">
	  <? } ?>
  	</TD>
    <TD valign=center align=center>
	  <? if (isset($editpart)) { ?>
	  <center><input type=submit value="<?echo $l_save?>" name="cmdEditPart"></center>
	  <? }
	     else { ?>
	  <center><input type=submit value="<?echo $l_add?>" name="cmdAddPart"></center>
	  <? } ?>
	</TD>
    </FORM>
  </TR>
  <?
  $query = "SELECT * FROM parts ORDER BY p_id;";
  $mysqlresult = query($query);
  while ($row = mysql_fetch_row($mysqlresult)) {
  ?>
  <TR bgcolor=#eeeeee>
    <form method=POST action="<?echo $g_base_url?>/index.php?whattodo=addparts">
    <TD valign=center>
	  <CENTER><a href="<?echo $g_base_url?>/index.php?whattodo=addparts&editpart=<?echo $row[3];?>"><?echo $row[3]?></a></CENTER>
    </TD>
    <TD align=center>
	  <CENTER><a href="<?echo $g_base_url?>/index.php?whattodo=addparts&editpart=<?echo $row[3];?>"><?echo $row[0]?></a></CENTER>
    </TD>
    <TD align=center>
	  <CENTER><?echo $row[1]?></CENTER>
    </TD>
    <TD valign=center align=center>
	  <CENTER><?echo $row[2]?></CENTER>
  	</TD>
    <TD valign=center align=center>
     <input type=hidden value="<?echo $row[3]?>" name=p_id>
     <center><input type=submit value="<?echo $l_delete?>" name="cmdDeletePart">
     <BR><input type=checkbox value=1 name=chkDelete><?echo $l_imsure?></center>
    </TD> 
    </FORM>
  </TR>
  <? } ?>
</table>
<? 
}
elseif (isset($cmdAddPart)) {
  $query = "SELECT p_id FROM parts;";
  $mysqlresult = query($query);
  while ($row = mysql_fetch_row($mysqlresult)) {
    if ($row[0] == $txtPartNumber) {
      $partfound = $row[0];
    }
  }
  if (isset($partfound)) {
    print "<CENTER>$l_parthasalreadybeenused  ";
    print "$l_pleasechooseadifferentpart</CENTER>\n";
  }
  else {
    $txtPartNumber = addslashes($txtPartNumber);
    $query = "INSERT INTO parts ";
	$query .= "VALUES ('$txtDescription', '$txtPrice', '$txtQuantity', '$txtPartNumber');";
	$mysqlresult = query($query);
	if ($mysqlresult) {
	}
	else {
	  print "$txtPartNumber $l_wasnotadded: <BR>\n";
	  mysql_error($mysqlresult);
	}
  }
  unset($cmdAddPart);
  include("scripts/addparts.scp.php");
} 

elseif (isset($cmdDeletePart)) {
  if ($chkDelete) {
    $query = "DELETE FROM parts ";
    $query .= "WHERE p_id='$p_id';";
    $mysqlresult = query($query);
    if ($mysqlresult) {
    }
    else {
      print "$l_partwasnotdeleted <BR>\n";
      print mysql_error($mysqlresult);
    }
  }
  else {
	print "<CENTER>$l_pleasecheckimsure \n";
  }
  unset($cmdDeletePart);
  include("scripts/addparts.scp.php");
}

elseif (isset($cmdEditPart)) {
  $query = "SELECT p_stock_quantity FROM parts WHERE p_id='$p_id';";
  $mysqlresult = query($query);
  $row = mysql_fetch_row($mysqlresult);
  $txtQuantity += $row[0];
  $query = "UPDATE parts ";
  $query .= "SET p_description='$txtDescription', p_price='$txtPrice', ";
  $query .= "p_stock_quantity='$txtQuantity' WHERE p_id='$p_id'";
  $mysqlresult = query($query);
  if (!$mysqlresult) {
    print "$txtPartNumber $l_wasnotadded <BR>\n";
    mysql_error($mysqlresult);
  }
  unset($cmdEditPart);
  include("scripts/addparts.scp.php");
}
?>
