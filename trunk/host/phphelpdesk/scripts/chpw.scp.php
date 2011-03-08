<?php
include("includes/connect.inc.php");

if( isset( $go ) ) {
    $r = query("SELECT s_password FROM security WHERE s_user='$user'");
    $row = mysql_fetch_row($r);
    if( $new1 == $new2 ) {
        if( $row[0] == $old ) {
	    query("UPDATE security SET s_password='$new1' WHERE " .
	          "s_user='$user'");
            echo "<div class=successtxt>$l_pwdchanged</div>";
	}
	else {
	    echo "<div class=errortxt>$l_currentpwdincorrect</div>";
	}
    }
    else {
        echo "<div class = errortxt>$newpwdnomatch</div>";
    }
}
else {
?>
<TABLE  CELLSPACING=0 CELLPADDING=5  BORDER=1 ALIGN=CENTER>
 <tr><th>
	<b> <? echo "$g_title - $l_changepwd"; ?> </b>
 </th></tr>
 <tr><td>
 <TABLE BORDER=0>
  <tr><td><? echo $l_changepwdforuser ?><b><? echo "$user"; ?></b></td></tr>
  <form action=<? echo "$g_base_url/index.php?whattodo=chpw"; ?>  method=post>
  <tr><td><? echo $l_currentpwd ?></td><td><input type=password name=old></tr>
  <tr><td><? echo $l_newpwd ?></td><td><input type=password name=new1></tr>
  <tr><td><? echo $l_confirmpwd ?></td><td><input type=password name=new2></tr>
  <tr><td align=center colspan=2><input type=submit name=go value="<? echo $l_changepwdacction ?>">
  </tr>
  </form>
 </TABLE>
</td></tr>
</TABLE>


<? } ?>
