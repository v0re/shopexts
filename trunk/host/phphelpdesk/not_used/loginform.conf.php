<?php	//Main Login Form 
?>
 <script type="text/javascript">
function open_wizz() {
<!--
F1 = window.open("wiz_start.php","Fenster1","width=710,height=550,left=100,top=100");
}
//-->

</script>

<P><br>
<form method=POST action="<?echo $g_base_url;?>/index.php">
  <input type=hidden name="status" value="Logged In">
	<center>
	<?
	if (isset($login)) {
	?>
    <table border=0 width=300 cellpadding=2 cellspacing=0 bgcolor="#C0C0C0">
      <tr>
        <td width=125>
          <p align=right><?php echo $l_username;?> 
	</td>
        <td width=175>
	  <input type=text name="txtUsername" size=10>
	</td>
      </tr>
      <tr>
        <td width=125>
          <p align=right><?php echo $l_password;?> 
	  </td>
        <td width=175>
	  <input type=password name="txtPassword" size=16>
	</td>
      </tr>
      <tr>
        <td width=300 colspan=2>
         <p align=center>
	 <center><input type=submit value="<? echo $l_enter?> <?echo $g_title?>" name="cmdEnter"></center>
        </td>
      </tr>
    </table>
	<? } ?>
    </center>
  <table border=0 align=center>
    <tr><td>
    <font size=6><b> 
<? //  changes here since wizzard added;
//if ($g_dept_or_comp == 0) {
//  echo $l_whatdepartmentareyouin;
//}
//else {
//  echo $l_whatcompanyareyouin;
//}
echo $l_welcometothe;

?></b></font>
    <BR><BR>
	<ul>
	  <li>
        <font size=5>
          <a href="<?echo $g_base_url;?>/index.php?login=goto"><?echo $l_login?></a>
		</font>
      </li>
<?  
//##############################################################################
//# Below you will see the word "sales" twice.  Once for the txtUsername and
//# once for the txtPassword.  You will need to make a new link for each department
//# or company that you have.  This way, when they login, they can just click a
//# link and they can add a service request.  By the way, for each department or
//# company, you will need a username/password created for them.
//##############################################################################


//##############################################################################
//# START LOGIN LINKS FOR EACH DEPARTMENT/COMPANY
//##############################################################################
?>
      <li>
        <font size=5>
  <a href="javascript:open_wizz()"><? echo $l_wizzardsname;?></a>
        </font>
      </li>      
<?
//##############################################################################
//# END LOGIN LINKS FOR EACH DEPARTMENT/COMPANY
//##############################################################################
?>
	 </ul>
	</td></tr></table>
  </div>
  <p> 
</form>
<p><br>
