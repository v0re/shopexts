 import com.bocom.netpay.b2cAPI.*;
 
 class bocomm_sign
 {
	 static public void main(String argv[])
	 {
		try
		{
			String configFile = argv[0];
			String sourceMsg = argv[1];
			if( sourceMsg == "" || configFile == "")
			{
				return;
			}
			BOCOMB2CClient client = new BOCOMB2CClient();
			int ret = client.initialize(configFile);
			
			if (ret != 0) 
			{                                                               
				System.out.print("initialize fail : " + client.getLastErr());
				return;
			}		
			com.bocom.netpay.b2cAPI.NetSignServer nss = new com.bocom.netpay.b2cAPI.NetSignServer();
			String	merchantDN = BOCOMSetting.MerchantCertDN;
			nss.NSSetPlainText(sourceMsg.getBytes("GBK"));
			byte bSignMsg [] = nss.NSDetachedSign(merchantDN);
			if (nss.getLastErrnum() < 0) {
				System.out.print("ERROR:NSDetachedSign fail");
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