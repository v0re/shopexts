<?php //PHP Helpdesk - View Tickets - Choose Company?>
<form method=POST action="<?echo $g_base_url;?>/index.php?whattodo=viewjobs">
<div align=center>
<center>
<table BGCOLOR="#FFFFFF" BORDERCOLOR="#000000"
    BORDERCOLORLIGHT="#000000" BORDERCOLORDARK="#000000"
    CELLSPACING=0 CELLPADDING=5 border=1 width="60%">
  <tr>
    <td width="100%" valign=top bgcolor="#000080" align=left>
      <p align=left><font color="#FFFFFF"><b><?echo $g_title?> - <?echo $l_choosecompany?></b></font>
    </td>
  </tr>
  <tr>
    <td width="100%" align=left>
      <table BACKGROUND="" BGCOLOR="#FFFFFF"
        BORDERCOLOR="#000000" BORDERCOLORLIGHT="#000000"
        BORDERCOLORDARK="#000000" CELLSPACING=0 CELLPADDING=5 border=0 width="100%">
        <tr>
          <td width="30%" valign=middle align=right>
            <b><?echo $l_pleasechoosecompanytoviewtickets?></b>
          </td>
          <td width="40%" valign=middle>
            <center><select size=5 name="lstChooseCompany[]" multiple>
	    <?
	    for ($i=0; $i < sizeof($departments); $i++) {
              print "              <option value=\"$departments[$i]\">$departments[$i]</option>\n";
	    }
	    ?>
            </select></center>
          </td>
          <td width="33%" valign=middle>
    	    <p align=center><input type=submit value="<?echo $l_continue?>" name="cmdSelectCompany">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
