<html>
<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title><? echo $l_title_report; ?> II/V</title>
  <LINK REL=StyleSheet HREF="css/wizzard.css" TYPE="text/css" MEDIA=screen>
  </head>
<body class="main" topmargin=70 leftmargin=0>
    <form action="wiz_start.php" method="POST" target="_self">
    <table width="75%" align="center">
          <tr> <td width="100%" height="19" align="center">
            <h1><? echo $l_titel_wizzard1;?></h1>
			</td></tr>
		  <tr><td align="center">
			<h3><? echo $l_selectrequest; ?></h3>
           </td></tr>
      <?php
		$query = "SELECT r_name, r_department FROM request ";
		$query .="WHERE r_category = '$lstRequestCategory'";
      $mysql_result = query($query);
      for ($i=0;$category_choose = mysql_fetch_row($mysql_result); $i++) {
        if ($i==0) {
        ?>
            <tr> <td><input type="Radio" name="lstRequest"
                    value="<? echo $category_choose[0]; ?>" checked>   <? echo "$category_choose[0] ( $lstRequestCategory )"; ?>
            </td></tr>
         <?} else { ?>
            <tr> <td><input type="Radio" name="lstRequest"
                    value="<? echo $category_choose[0]; ?>"> <? echo "$category_choose[0] ( $lstRequestCategory )"; ?>
            </td></tr>
        <?  } // end of if clause
      }  // end of for loop ?>
      <tr> <td><center><input  class="input" type="Submit" name="cmdNext02" value="<? echo $l_next; ?>"></center></td></tr>
      <tr> <td><center><a href="javascript:history.back()"><?echo $l_back;?></a></center></td></tr>
      </table>
      </div>
      </form>
      </body>
      </html>