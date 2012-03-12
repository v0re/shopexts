import java.io.*;
import java.util.*; 
import cn.com.infosec.icbc.ReturnValue;
 
 class test_icbc_sign
 {
	 static public void main(String argv[])
	 {
		try
		{
		    
			String priCertFile = argv[0];
			String password = argv[1];
			String tranData = argv[2];
			
			if(  password == "" || tranData == ""  || priCertFile == "" )
			{
				return;
			}
			
			System.out.println("test priCertFile:"+priCertFile);
			System.out.println("test password:"+password);
			System.out.println("test tranData:"+tranData);
					
            byte[] byteSrc = tranData.getBytes();
            char[] keyPass = password.toCharArray();
            
            FileInputStream in2 = new FileInputStream(priCertFile);
            byte[] bkey = new byte[in2.available()];
            in2.read(bkey);
            in2.close();
            
            
            byte[] sign =ReturnValue.sign(byteSrc,byteSrc.length,bkey,keyPass);
            if (sign==null) {
                System.out.println("return null,please make sure private file and password are correct");
                return;
            }            
            byte[] EncSign = ReturnValue.base64enc(sign);
            String SignMsgBase64=new String(EncSign).toString();
			System.out.println("test bkey:"+new String(bkey).toString());  
			System.out.print("test <message>");
			System.out.print(SignMsgBase64);
			System.out.print("</message>");
		
		}
		catch(Exception ex)
		{
			System.out.println(ex.getMessage());
		}
	 }
 }