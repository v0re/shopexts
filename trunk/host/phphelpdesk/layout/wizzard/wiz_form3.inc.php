<html>
<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title><? echo $l_title_report; ?> III/V</title>
  <LINK REL=StyleSheet HREF="css/wizzard.css" TYPE="text/css" MEDIA=screen>
  </head>
<body class="main" >
<form action="wiz_start.php" method="POST" target="_self">
<input type="hidden" name="lstDepartment" value="<? echo $lstDepartment ?>">
<input type="hidden" name="lstRequest" value="<? echo $lstRequest; ?>">
<br><br>
   
  <table align="center" width="90%" >
    <tr> 
      <td colspan="5" height="19" align="center"> <h1><? echo $l_titel_wizzard2, $lstDepartment;?></h1></td>
    </tr>
    <tr> 
      <td width="7%"><? echo "$l_firstname";?> </td>
      <td width="18%"> <input  class="input" type="text" name="txtUserFirstName" size="20" > 
      </td>
      <td colspan="3">
		 <div class="errortxt"><?php if ($add_d_err[1]==1){echo "$l_verifyfirstname"; }?>
		</div>
	  </td>
    </tr>
    <tr> 
      <td><? echo "$l_lastname";?> </td>
      <td><input  class="input" type="text" name="txtUserLastName" size="20"></td>
      <td colspan="3"> <div class="errortxt"><?php if ($add_d_err[2]==1){echo "$l_verifylastname"; }?>
		</div>
		</td>
    </tr>
    <tr> 
      <td><? echo $l_telephonenumberextension;?></td>
      <td><input  class="input" type="text" name="txtUserTelephone" size="20"></td>
      <td width="24%">&nbsp;</td>
      <td width="5%"><?echo $l_location?></td>
      <td width="46%"><input  class="input" type=text size=20 name="txtLocation"></td>
    </tr>
    <tr> 
      <td><? echo $l_emailaddress;?></td>
      <td><input  class="input" type="text" name="txtUserEmail" size="20"></td>
      <td colspan="3"><div class="errortxt"><?php if ($add_d_err[3]==1){echo "$l_verifyemail"; }?>
		</div>
	  </td>
    </tr>
    <tr> 
      <td><? echo $l_computerid;?></td>
      <td colspan="2"><input class="input"  type="text" name="txtComputerid" size="20"></td>
      <td><? echo $l_num_personal;?></td>
      <td><input  class="input" type="text" name="txtStaffid" size="20"></td>
    </tr>
    <tr> 
      <td><? echo $l_area;?></td>
      <td><input  class="input" type="text" name="txtArea" size="20"></td>
      <td>&nbsp;</td>
      <td><?echo $l_dateopened;?></td>
      <td><? echo $readable_date,"  ", $readable_time;?></td>
    </tr>
    <tr> 
      <td colspan="5"> <center>
          <input  class="input" type="Submit" name="cmdNext03" value="<? echo $l_next; ?>">
        </center></td>
    </tr>
    <tr> 
      <td colspan ="5"><center>
          <a href="javascript:history.back()"><?echo $l_back;?></a></center></td>
    </tr>
  </table>
      </form>
      </body>
      </html>