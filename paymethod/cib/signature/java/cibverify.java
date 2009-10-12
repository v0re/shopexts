import com.crypt.Des.*;
/**
 * <p>Title: </p>
 * <p>Description: test DESUTIL</p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: </p>
 * @author cyq
 * @version 1.0
 */

public class cibverify {
    public static void main(String[] args)
    {
		
        if (args.length != 3) {
            System.out.print("-1");
        }

		String mac_key = args[0];
        String content = args[1];
		String mac = args[2];

		boolean flag = DesUtil.checkMac(mac_key,content,mac);

		if (flag == true){
			System.out.print("1");
		}else{			
			System.out.print("0");    
		}
    }
}