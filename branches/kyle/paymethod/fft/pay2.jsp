<%@ page contentType="text/html; charset=gb2312" language="java" import="java.util.*,com.huateng.crypto.rsa.RSATool"%>
<%@ page import="com.shfft.service.utils.*"%>
<%

//String gateWayPubKey = "e:\\key\\merchant\\pri.key";
String merPriKey = "/home/weblogic/gatekey/merchant/pri.key";

java.util.Date today=new java.util.Date();

String todayYear=Integer.toString(today.getYear()+1900);

String todayMonth=Integer.toString(today.getMonth()+1);

String todayDay=Integer.toString(today.getDate());

todayMonth=todayMonth.length()==2?todayMonth:"0"+todayMonth;

todayDay=todayDay.length()==2?todayDay:"0"+todayDay;

//String todayHour=Integer.toString(today.getHour());

String testFlw=todayYear+todayMonth+todayDay+Integer.toString(today.getHours())+Integer.toString(today.getMinutes())+Integer.toString(today.getSeconds());

String orderId=request.getParameter("orderID");

String totalPrice=request.getParameter("totalPrice");



String reqOrgID="222222222222222";
//String reqOrgID="333333333333333";

//String reqDate=todayYear+todayMonth+todayDay;
String reqDate = "20040808000000";

String reqTransFlowNo=testFlw;

String payAmount=totalPrice;

String orderID=orderId;

String payInd="0";

String returnURL="/gateway/test/payResult.jsp";

String sort="0";

String billOrgID="888880002102900";

String billBar="123";

String codeType="";

String code="";

String totalRows="0";

String times="00";

String period=todayYear+todayMonth;

String resultShowType="0";
String curCode="156";
String transType="A0160"; //支付交易
String version="00";//版本号
String txsubcode="0";

/**/





String orderString=version+transType+txsubcode+reqOrgID+reqDate+reqTransFlowNo+orderID+curCode+payAmount+"2sfft007wang8w8ch8";

String sign="";



//RSATool rsatool = new RSATool();

//rsatool.setPrivateKey(RSATool.JAVARSA, merPriKey);

////byte[] a = new byte[] {1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0};+sort+billOrgID+billBar+codeType+code+payAmount

//byte[] a = orderString.getBytes();

//byte[] b = rsatool.genSign(a);

//sign = com.huateng.util.ConvertUtil.toHex(b);

 sign = Md5Encrypt.md5(orderString);

out.print("<p>原始字符串："+orderString);

out.print("<p>签名字符串："+sign);



%>

<style type="text/css">

<!--

.unnamed1 {

	font-size: 13px;

}

-->

</style>

<html>

<body id='allContent' style='visibility:visible' onload="thinking()">

<form id="submitForm" action="/gateway/transForwardAll.jsp" method="post">

  <table width="498" border="0" align="center" cellpadding="2" cellspacing="1" class="unnamed1">

    <tr>

      <td width="157" bgcolor="#f3eff7"> <div align="right">版本号</div></td>

      <td width="100" bgcolor="#eff3f7"><div align="center">VERSION</div></td>

      <td width="225" bgcolor="#eff3f7"><div align="left">

          <input name="VERSION" type="text" value="<%=version%>">

        </div></td>

    </tr>
    <tr>

      <td width="157" bgcolor="#f3eff7"> <div align="right">交易类别</div></td>

      <td width="100" bgcolor="#eff3f7"><div align="center">TXCODE</div></td>

      <td width="225" bgcolor="#eff3f7"><div align="left">

          <input name="TXCODE" type="text" value="<%=transType%>">

        </div></td>

    </tr>
<tr>
      <td width="157" bgcolor="#f3eff7"> <div align="right">分类交易代码</div></td>
      <td width="100" bgcolor="#eff3f7"><div align="center">TXSUBCODE</div></td>
      <td width="225" bgcolor="#eff3f7"><div align="left">
          <input name="TXSUBCODE" type="text" value="<%=txsubcode%>">
        </div></td>
    </tr>

	<tr>

      <td width="157" bgcolor="#f3eff7"> <div align="right">请求机构代码</div></td>

      <td width="100" bgcolor="#eff3f7"><div align="center">MERCHANTID</div></td>

      <td width="225" bgcolor="#eff3f7"><div align="left">

          <input name="MERCHANTID" type="text" value="<%=reqOrgID%>">

        </div></td>

    </tr>

    <tr>

      <td bgcolor="#f3eff7"><div align="right">请求方交易日期</div></td>

      <td bgcolor="#eff3f7"><div align="center">TRANSDATE</div></td>

      <td bgcolor="#eff3f7"><div align="left">

          <input name="TRANSDATE" type="text" value="<%=reqDate%>">

        </div></td>

    </tr>

    <tr>

      <td bgcolor="#f3eff7"><div align="right">请求方交易流水号</div></td>

      <td bgcolor="#eff3f7"><div align="center">TRANSFLW</div></td>

      <td bgcolor="#eff3f7"><div align="left">

          <input name="TRANSFLW" type="text" value="<%=reqTransFlowNo%>">

        </div></td>

    </tr>

     <tr>

      <td bgcolor="#f3eff7"><div align="right">支付币别</div></td>

      <td bgcolor="#eff3f7"><div align="center">CURCODE</div></td>

      <td bgcolor="#eff3f7"><div align="left">

          <input name="CURCODE" type="text" value="<%=curCode%>">

        </div></td>

    </tr>
   <tr>

      <td bgcolor="#f3eff7"><div align="right">支付金额</div></td>

      <td bgcolor="#eff3f7"><div align="center">AMOUNT</div></td>

      <td bgcolor="#eff3f7"><div align="left">

          <input name="AMOUNT" type="text" value="<%=payAmount%>">

        </div></td>

    </tr>

    <tr>

      <td bgcolor="#f3eff7"><div align="right">订单号</div></td>

      <td bgcolor="#eff3f7"><div align="center">ORDERID</div></td>

      <td bgcolor="#eff3f7"><div align="left">

          <input name="ORDERID" type="text" id="orderID" value="<%=orderID%>">

        </div></td>

    </tr>

    <tr>


    <tr>

      <td bgcolor="#f3eff7"><div align="right">账单个数</div></td>

      <td bgcolor="#eff3f7"><div align="center">TOTROW</div></td>

      <td bgcolor="#eff3f7"><input name="TOTROW" type="text" id="totalRows" value="<%=totalRows%>"></td>

    </tr>

    <tr>

      <td bgcolor="#f3eff7"><div align="right">返回URL</div></td>

      <td bgcolor="#eff3f7"><div align="center">RTNURL</div></td>

      <td bgcolor="#eff3f7"><div align="left">

          <input name="RTNURL" type="text" id="returnURL" value="<%=returnURL%>">

        </div></td>

    </tr>

    <tr>

      <td bgcolor="#f3eff7"><div align="right">原始串</div></td>

      <td colspan="2" bgcolor="#eff3f7"> <div align="center">

          <textarea name="orderString" cols="40" rows="5" id="orderString"><%=orderString%></textarea>

        </div></td>

    </tr>

    <tr>

      <td bgcolor="#f3eff7"><div align="right">签名串</div></td>

      <td colspan="2" bgcolor="#eff3f7"> <div align="center">

          <textarea name="SIGN" cols="40" rows="5" id="sign"><%=sign%></textarea>

        </div></td>

    </tr>

  </table>

<p align="center">

  <input name="Submit" type="submit"   class="unnamed1" value="确定" onClick="f_Submit()"></p>

  <!--input name="Submit2" type="button" value="隐藏" onclick="hideText()"-->


<p>

</p>

</form>

</body>

</html>

<script>

/*

function hideText(){

	if(allContent.style.visibility=='hidden')

		allContent.style.visibility='visible';

	else

		allContent.style.visibility='hidden';

}

*/

function thinking(){

	if(allContent.style.visibility=='hidden')

		submitForm.submit();

}

function f_Submit(){

	//alert(document.all.reqTransFlowNo.value);

	//document.submitForm.submit();

}

</script>

