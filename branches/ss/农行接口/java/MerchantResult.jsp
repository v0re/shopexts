<%@ page contentType="text/html; charset=utf-8" %>
<%@ page import = "com.hitrust.trustpay.client.b2c.*" %>
<% response.setHeader("Cache-Control", "no-cache"); %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>农行网上支付平台-支付结果</title>
</head>

<body>
<CENTER>支付结果<br>
<%
//1、取得MSG参数，并利用此参数值生成支付结果对象
PaymentResult tResult = new PaymentResult(request.getParameter("MSG"));

//2、判断支付结果状态，进行后续操作
if (tResult.isSuccess()) {
	String rUrl = "<form method=post action='http://www.968111.com/plugins/payment/pay.abchina.php' name='result' id='result'>" +
		"<input type='hidden' name='TrxType' value='" + tResult.getValue("TrxType") + "' />" +
		"<input type='hidden' name='OrderNo' value='" + tResult.getValue("OrderNo") + "' />" +
		"<input type='hidden' name='Amount' value='" + tResult.getValue("Amount") + "' />" +
		"<input type='hidden' name='BatchNo' value='" + tResult.getValue("BatchNo") + "' />" +
		"<input type='hidden' name='VoucherNo' value='" + tResult.getValue("VoucherNo") + "' />" +
		"<input type='hidden' name='HostDate' value='" + tResult.getValue("HostDate") + "' />" +
		"<input type='hidden' name='HostTime' value='" + tResult.getValue("HostTime") + "' />" +
		"<input type='hidden' name='MerchantRemarks' value='" + tResult.getValue("MerchantRemarks") + "' />" +
		"<input type='hidden' name='PayType' value='" + tResult.getValue("PayType") + "' />" +
		"<input type='hidden' name='NotifyType' value='" + tResult.getValue("NotifyType") + "' />" +
		"<input type='hidden' name='State' value='9' />" +
	"</form><script language='javascript'>document.result.submit();</script>";
	out.println(rUrl);
}
else {
	String rUrl = "<form method=post action='http://www.968111.com/plugins/payment/pay.abchina.php' name='result' id='result'>" +
		"<input type='hidden' name='ReturnCode' value='" + tResult.getValue("ReturnCode") + "' />" +
		"<input type='hidden' name='getErrorMessage' value='" + tResult.getValue("getErrorMessage") + "' />" +
		"<input type='hidden' name='State' value='8' />" +
	"</form><script language='javascript'>document.result.submit();</script>";
	out.println(rUrl);
}
%>
</body>
</html>