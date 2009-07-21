<?php
/**
* 
* @version  4.6
* @author   ShopEx.cn <develop@shopex.cn>
* @url		http://www.shopex.cn/
* @since    PHP 4.3
* @copyright ShopEx.cn
*
**/
if (!defined("ISSHOP"))
{
	Header("Location:../index.php");
	exit;
}

$action = $_POST["shopex_action"];

$shopex_pay_type = $_POST["shopex_pay_type"];
$shopex_encoding = $_POST["shopex_encoding"];
if($shopex_encoding=="")
{
	header("Content-Type: text/html; charset=UTF-8");
}
else
{
	header("Content-Type: text/html; charset=".$shopex_encoding);
}
$req_type = ($shopex_pay_type == "XPAY")?"GET":"POST";
?>
<form name=payform action="<?php echo $action;?>" method="<?php echo $req_type;?>">
<?php
foreach($_POST as $k=>$v)
{
	if($shopex_pay_type == 'GSPAY'){
		$k = urldecode($k);
		$v = urldecode($v);
	}
	if(substr($k,0,7)!="shopex_"&&$k!="submit"&&$k!="send"&&$k!="gOo")
	{
		if(substr($v,0,19)=="local_shopex_encode") $v = base64_decode(substr($v,19));
		$str =  "<input type=\"hidden\" name=\"{$k}\" value=\"".$v."\" />";
		echo $str;
	}
}
?>
</form>
Please wait....
<script type="text/javascript">
<!--
   payform.submit();
//-->
</script><TABLE>
<TR>
	<TD></TD>
</TR>
<TR>
	<TD></TD>
</TR>
</TABLE>