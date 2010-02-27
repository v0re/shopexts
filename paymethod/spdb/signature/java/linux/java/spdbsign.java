/**
 * <p>Title: </p>
 * <p>Description: test DESUTIL</p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: </p>
 * @author cyq
 * @version 1.0
 */

public class spdbsign {
    public static void main(String[] args)
    {

		String content = args[0];
   		
        String mac = com.csii.payment.client.core.MerchantSignVerify.merchantSignData_ABA(content);
        System.out.print(mac);     
    }
}