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
			int ret = client.initialize("C:\\bocommjava\\ini\\B2CMerchant.xml"); //�ô���ֻ�����һ��
			
			if (ret != 0) 
			{                                                                 //��ʼ��ʧ��
				System.out.print("��ʼ��ʧ��,������Ϣ��" + client.getLastErr());
				return;
			}		
			com.bocom.netpay.b2cAPI.NetSignServer nss = new com.bocom.netpay.b2cAPI.NetSignServer();
			String	merchantDN = BOCOMSetting.MerchantCertDN;
			nss.NSSetPlainText(sourceMsg.getBytes("GBK"));
			byte bSignMsg [] = nss.NSDetachedSign(merchantDN);
			if (nss.getLastErrnum() < 0) {
				System.out.print("ERROR:�̻���ǩ��ʧ��");
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