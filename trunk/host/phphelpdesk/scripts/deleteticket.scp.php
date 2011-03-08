<?PHP  // Delete ticket

if( $s_delete_tickets==1 ) {

  if( isset( $confirmDelete ) ) {
    $r1 = query("DELETE FROM ticket WHERE t_id='$t_id';");
    if( mysql_affected_rows( $mysql_link ) > 0 )
        print "Deleted ticket $t_id<p>";
    else
        print "Failed to delete!";

    $r2 = query("DELETE FROM events WHERE t_id='$t_id';");
  }
  else {
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
  <? echo "$g_title"; ?>  - Delete Ticket</b></font>
</td></tr>
<tr><td>
<center>Are you sure you want to delete ticket # <? echo $t_id; ?> ?
<br><form action="<? echo "$g_base_url";
?>/index.php?whattodo=deleteticket&t_id=<?echo $t_id; ?>
" method=post>
<input type=submit name=confirmDelete value="Yes, Delete">
</form>
</center></td></tr></table>

  <?
  }
}
?>



