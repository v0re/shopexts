<html>
<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title><? echo $l_title_report; ?> I/V</title>
  <LINK REL=StyleSheet HREF="css/wizzard.css" TYPE="text/css" MEDIA=screen>
 </head>
<body class="main">
<form action="wiz_start.php" method="POST" target="_self">
<table width="75%" align="center">
          <tr> <td width="100%" height="19">
            <h1 align="center"><? echo $l_titel_wizzard1;?></td></h1>
          </tr>
          <tr><td width="100%" height="19">
            <p align="center"><? echo $l_welcometothe_wizz; // create personal headline
                                  if ($user == "wizzard"){ echo "Wizzardasd"; }
                                  else {echo "<b>", $s_firstname," ", $s_lastname, "</b>"; }
                                  ?><br>
            <? echo $l_itisimportant; ?>
            </td>
          </tr>
          <tr> <td width="100%" height="10">
            <h1 align="center"><? echo $l_selectrequestcategory;?></td></h1>
          </tr>
      <?php
		$query = "SELECT rc_name FROM requestcategories";
      $mysql_result = query($query);
      for ($i=0;$category_choose = mysql_fetch_row($mysql_result); $i++) {
        if ($i==0) {
        ?>
            <tr> <td><input type="Radio" name="lstRequestCategory"
                    value="<? echo $category_choose[0]; ?>" checked>  <? echo $category_choose[0]; ?>
            </td></tr>
         <?} else { ?>
            <tr> <td><input type="Radio" name="lstRequestCategory"
                    value="<? echo $category_choose[0]; ?>">  <? echo $category_choose[0]; ?>
            </td></tr>
        <?  } // end of if clause
      }  // end of for loop ?>
      <tr> <td><center><input class="input" type="Submit" name="cmdNext01" value="<? echo $l_next; ?>"></center></td></tr>
      </table>
      </form>
 </body>
</html>