<?PHP   // Search

if( isset( $doSearch ) ) {
    //heading("Search results for $searchText", 0);
    $result = query("SELECT * FROM ticket ORDER BY t_id;");
    
    ?>
     <TABLE
      BGCOLOR="<?echo $html_table_bgcolor;?>"
      BORDERCOLOR="<?echo $html_table_bordercolor;?>"
      BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
      BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
      CELLSPACING=0 CELLPADDING=5
      BORDER=1 ALIGN=CENTER>
      <tr><td BGCOLOR="#000080" COLSPAN=6>
       <p align=left><font color="#FFFFFF"><b>
       <? echo "$g_title - $l_search_results  $searchText;" ?>
       </b></font>
     </td></tr>
     <tr>
		<th><? echo $l_ticketid ?></th><th><? echo $l_category ?></th><th><? echo $l_summary ?></th>
		<th><? echo $l_detail ?></th><th><? echo $l_ticketregisterd ?></th><th><? echo $l_closeticket ?></th>
     </tr>
    <?
    
    while( $row = mysql_fetch_row( $result ) ) {
        for( $x=0; $x < count($row); $x++ ) {
            if( preg_match( "/$searchText/i", $row[$x] ) )
	        if( $prev != $row[0] ) {
		        $link = "<a href=\"$g_base_url/index.php?whattodo="
			    . "viewjobs&t_id=$row[0]\">"
			    . "$row[0]</a>";
		    if( $row[2] == "" ) $row[2] = "[NULL]";
		    if( !$row[6] )      $row[6] = "[OPEN]";
		    for( $x=1; $x < count( $row ); $x++ )
		        $row[$x] = stripslashes( $row[$x] );
                    //start addition by joe 7-17-01
                    if ($html_alt_color==$html_alt_color2) {
                      $html_alt_color = $html_alt_color1;
                    }
                    else {
                      $html_alt_color = $html_alt_color2;
                    }
                    print "<TR bgcolor=\"".$html_alt_color."\"\n";
                    if ($g_enable_javascript==1) {
                      print "class=m1 onClick=\"location.href='";
                      print $g_base_url."/index.php?whattodo=viewjobs&";
                      for ($j=0; $lstChooseCompany[$j] != NULL; $j++) {
                        $thiscompany = addslashes($lstChooseCompany[$j]);
                        print "lstChooseCompany[$j]=$thiscompany&";
                      }
                      print "t_id=$row[0]'\"\n";
                      print "onMouseOut=\"style.backgroundColor=''; style.border='0 solid black';\"\n";
                      print "onMouseOver=\"style.backgroundColor='".$html_highlight_color."';";
                      print "style.cursor='hand'; style.border='0 solid #CCCCCC'\"\n";
                    }
                    print ">\n";
                    //end addition by joe 7-17-01
                    print "<td><center>$link</center></td><td>$row[1]</td><td>$row[9]</td>";
		    print "<td>$row[2]</td><td>$row[5]</td><td>$row[6]</td>";
                    print "</tr>\n";
	            $prev = $row[0];
		}
	}
    }
    echo "</table>";
}

else {
//heading("Search - This feature is under developement", 0);
?>
     <TABLE
      BGCOLOR="<?echo $html_table_bgcolor;?>"
      BORDERCOLOR="<?echo $html_table_bordercolor;?>"
      BORDERCOLORLIGHT="<?echo $html_table_bordercolorlight;?>"
      BORDERCOLORDARK="<?echo $html_table_bordercolordark;?>"
      CELLSPACING=0 CELLPADDING=5
      BORDER=1 ALIGN=CENTER>
      <tr><td BGCOLOR="#000080" COLSPAN=3>
       <p align=left><font color="#FFFFFF"><b>
       <? echo "$g_title - $l_searchtitel"; ?> </b></font>
     </td></tr>

<tr><td>
 <table>
  <tr><td>
   <form action="<? echo "$g_base_url/index.php?whattodo=search"; ?>" method=post>
   <? /* <em>WARNING: This feature is under developement</em><p>*/ ?>
   <? echo "$l_searchfor" ?> <input type=text name=searchText size=40>
   <br>
   <input type=submit>
   <input type=hidden name=doSearch>
   </form>
  </td></tr>
 </table>
</td></tr>
</table>
<? 
}
?>
