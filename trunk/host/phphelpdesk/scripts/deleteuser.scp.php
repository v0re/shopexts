<?php	//DELETE USER SCRIPT
include("includes/functions.inc.php");
// if user has not been choosen do this, else show modify user stuff
if ($s_manage_users==1) {
if (!isset($lstDeleteUser)) {?>
  <FORM method=POST action="<? echo $g_base_url;?>/index.php?whattodo=deleteuser">
<BR>
  <TABLE CELLSPACING=0 CELLPADDING=3 BORDER=1 align="center">
    <tr>
      <th width="100%" valign=top bgcolor="#000080" align=left>
       <? echo $g_title?> - <? echo $l_selectuser?>
      </th>
    </tr>
    <tr>
      <td width="100%" align=left>
        <TABLE CELLSPACING=0 CELLPADDING=3 BORDER=0 width="100%">
        <tr>
	  <td width="50%" valign=middle align=right>
	    <b><? echo $l_selectuser?></b> 
	  </td>
	  <td width="50%" valign=middle>
	    <select size=1 name="lstDeleteUser">
<?  // print each row from the security table into an HTML table
    $query = "SELECT s_user FROM security;";
    $mysql_result = query($query);
    while($row = mysql_fetch_row($mysql_result)) {
      $useralreadyfound = 0;
      for ($i=0; $i < sizeof($departments); $i++) {
        $query1 = "SELECT s_user, d_name ";
        $query1 .= "FROM userdepartments ";
        $query1 .= "WHERE d_name='$departments[$i]' ";
        $query1 .= "AND s_user='$row[0]';";
        $mysql_result1 = query($query1);
        while($row1 = mysql_fetch_row($mysql_result1)) {
          if ($useralreadyfound == 0) { 
            print "        <option value=\"$row[0]\">$row[0]</option>\n";
	    $useralreadyfound = 1;
  	  }
        }
      }
    }
?>
            </select>
          </td>
          <td valign=middle align=center>
    	    <input type=submit value="<?echo $l_selectthisuser?>" name="cmdSelectUser">
          </td>
        </tr>
      </table>
</FORM>
<?
}
elseif (isset($cmdSelectUser)) {?>
  <FORM method=POST action="<?echo $g_base_url;?>/index.php?whattodo=deleteuser">
	  <input type=hidden name=lstDeleteUser value="<? echo $lstDeleteUser;?>">
  	  <center><? echo $l_areyousureyouwanttodelete?> 
	  <input type=radio name="rdoAreYouSure" value=1><? echo $l_yes?> &nbsp;&nbsp;&nbsp;
	  <input type=radio name="rdoAreYouSure" value=0 checked><? echo $l_no?><BR><br>
	  <input type=submit value="<? echo $l_deleteuser?>" name="cmdDeleteUser"></center>
  </FORM>
<? }
elseif ($rdoAreYouSure == 1) {
  // update mysql with new information  
  if ($user!=$lstDeleteUser) {
  $query = "DELETE FROM security WHERE s_user='$lstDeleteUser';";
  $mysql_result = query($query);
  if ($mysql_result) {
    print "<div class=successtxt><BR><BR>$lstDeleteUser $l_userhasbeendeleted<BR></div>\n";  
    $query = "DELETE FROM userdepartments ";
    $query .= "WHERE s_user='$lstDeleteUser'";
    $mysql_result = query($query);
    if ($mysql_result) {
      print "<div class=successtxt><BR><BR>$lstDeleteUser $l_userhasbeendeleted<BR></div>\n";  
    }
  }
  else {
    print "<div class=errortxt><BR><BR><B>$l_error</B>$lstDeleteUser $l_wasnotdeleted</div>\n";
  }
} else {
    print "<div class=errortxt><BR><BR><B>$l_error</B>$l_dontdeleteyouself</div>\n"; }
}
}
?>
