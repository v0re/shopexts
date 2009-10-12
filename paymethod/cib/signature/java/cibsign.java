import com.crypt.Des.*;
/**
 * <p>Title: </p>
 * <p>Description: test DESUTIL</p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: </p>
 * @author cyq
 * @version 1.0
 */

public class cibsign {
    public static void main(String[] args)
    {

		String content = args[0];
        String mac_key = args[1];
		
        String mac = DesUtil.genMac(mac_key,content);
        System.out.print(mac);     
    }
}