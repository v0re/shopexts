 import com.bocom.netpay.b2cAPI.*;
 
 class bocomm_sign
 {
	 static public void main(String argv[])
	 {
		try
		{
			String sourceMsg = argv[0];
			if( sourceMsg == "")
			{
				return;
			}
			BOCOMB2CClient client = new BOCOMB2CClient();
			int ret = client.initialize("C:\\bocommjava\\ini\\B2CMerchant.xml"); //该代码只需调用一次
			
			if (ret != 0) 
			{                                                                 //初始化失败
				System.out.print("初始化失败,错误信息：" + client.getLastErr());
				return;
			}		
			com.bocom.netpay.b2cAPI.NetSignServer nss = new com.bocom.netpay.b2cAPI.NetSignServer();
			String	merchantDN = BOCOMSetting.MerchantCertDN;
			nss.NSSetPlainText(sourceMsg.getBytes("GBK"));
			byte bSignMsg [] = nss.NSDetachedSign(merchantDN);
			if (nss.getLastErrnum() < 0) {
				System.out.print("ERROR:商户端签名失败");
				return;
			}
			String signMsg = new String(bSignMsg, "GBK");
			System.out.print("<message>");
			System.out.print(signMsg);
			System.out.print("</message>");
		}
		catch(Exception ex)
		{
			System.out.println(ex.getMessage());
		}
	 }
 }