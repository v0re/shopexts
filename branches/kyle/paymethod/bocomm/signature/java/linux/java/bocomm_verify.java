 import com.bocom.netpay.b2cAPI.*;
 
 class bocomm_verify
 {
	 static public void main(String argv[])
	 {
		try
		{
			String configFile = argv[0];
			String src = argv[1];
			String signed = argv[2];

			if( configFile == "" || src == "" || signed == "" )
			{
				System.out.print("<message>");
				System.out.print(-10);
				System.out.print("</message>");
			}

			BOCOMB2CClient client = new BOCOMB2CClient();
			int ret = client.initialize(configFile);			
			if (ret != 0) 
			{
				System.out.print("load config file fail: " + client.getLastErr());
				return ;
			}
			com.bocom.netpay.b2cAPI.NetSignServer nss = new com.bocom.netpay.b2cAPI.NetSignServer();
			nss.NSDetachedVerify(signed.getBytes(), src.getBytes());
			int veriyCode = nss.getLastErrnum();

			System.out.print("<message>");
			System.out.print(veriyCode);
			System.out.print("</message>");
		}
		catch(Exception ex)
		{
			System.out.println(ex.getMessage());
		}
	 }
 }