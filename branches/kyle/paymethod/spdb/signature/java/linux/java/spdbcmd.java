
public class spdbcmd {
    public static void main(String[] args)
    {

		String action = args[0];
		String plain = args[1];
		if(action.equals("sign")){
			String mac = com.csii.payment.client.core.MerchantSignVerify.merchantSignData_ABA(plain);
			System.out.print(mac);    
		}

		if(action.equals("verify")){
			String sign = args[2];
			boolean ret = com.csii.payment.client.core.MerchantSignVerify.merchantVerifyPayGate_ABA(sign,plain);
			System.out.print(ret);    
		}
		
    }
}