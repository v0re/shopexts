<?PHP 
$ip1= $_SERVER['REMOTE_ADDR']; 
if (!isset($status)) { 
// the status bar when not logged in 
?>
	<table width="100%" cellspacing="0" cellpadding="0">
        <tbody><tr>
          <td ><? echo "$l_contact" ?></td>
          <td ><? echo "$l_credits" ?></td>
          <td ><? echo "$l_ip_txt $ip1 " ?></td>
        </tr>
      </tbody></table>
<?PHP
} else { // the status bar when logged in?>
	<table width="625" cellspacing="0" cellpadding="0">
        <tbody><tr>
          <td width="50"><? echo "$l_contact" ?></td>
          <td width="121"><? echo "$l_credits" ?></td>
          <td width="214"><? echo "$l_ip_txt $ip1 " ?></td>
        </tr>
      </tbody></table>


<? } ?>

