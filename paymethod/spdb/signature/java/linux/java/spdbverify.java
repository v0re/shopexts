/**
 * <p>Title: </p>
 * <p>Description: test DESUTIL</p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: </p>
 * @author cyq
 * @version 1.0
 */

public class spdbverify {
    public static void main(String[] args)
    {

		String sign = args[0];
		String plain = args[1];

		System.out.println(sign);
		System.out.println(plain);
   		
        boolean ret = com.csii.payment.client.core.MerchantSignVerify.merchantVerifyPayGate_ABA(sign,plain);
        System.out.print(ret);     
    }
}