import java.io.*;
import java.net.*;
import java.security.*;
import com.bocom.netpay.b2cAPI.*;


public class bocommd {
	 private static int port=9555, maxConnections=0;
	// Listen for incoming connections and handle them
	public static void main(String[] args) {
		int i=0;

		try{
			ServerSocket listener = new ServerSocket(port);
			Socket server;

			while((i++ < maxConnections) || (maxConnections == 0)){
				doComms connection;
				server = listener.accept();
				doComms conn_c= new doComms(server);
				Thread t = new Thread(conn_c);
				t.start();
			}
		} catch (IOException ioe) {
			System.out.println("IOException on socket listen: " + ioe);
			ioe.printStackTrace();
		}
	}
}

class doComms implements Runnable {
	private Socket server;
	private String line,input;

	doComms(Socket server) {
		this.server=server;
	}

	public void run () {		
		try {
			// Get input from the client
			DataInputStream in = new DataInputStream (server.getInputStream());
			PrintStream out = new PrintStream(server.getOutputStream());
			String op,message,signedmessage,certfile;

			if((line = in.readLine()) != null && !line.equals(".") && line.length() > 5) {		
				//op = line.substring(0,4);
				//message = line.substring(5);
				String[] args = line.split("%");
				op = args[0];	
				
				if(op.equals("sign")){
					message = args[1].trim();
					certfile = args[2].trim();
					signedmessage = this.sign(message,certfile);
					out.print(signedmessage);
				}

				if(op.equals("veri")){
					String src =  args[1].trim();
					String signed = args[2].trim();
					certfile = args[3].trim();
					int vcode = this.verify(src,signed,certfile);
					out.print(vcode);
				}
			
			}
			server.close();
		} catch (IOException ioe) {
			System.out.println("IOException on socket listen: " + ioe);
			ioe.printStackTrace();
		}
	}

	private String sign(String message, String certfile) {
		//System.out.println("working on: " + message);
		String signMsg = "";
		try
		{
	
			if( message == "" )
			{
				return "wrong argument";
			}
			BOCOMB2CClient client = new BOCOMB2CClient();
			int ret = client.initialize(certfile);			
			if (ret != 0) 
			{
				System.out.print("load config file fail: " + client.getLastErr());
				return client.getLastErr();
			}
			com.bocom.netpay.b2cAPI.NetSignServer nss = new com.bocom.netpay.b2cAPI.NetSignServer();
			String	merchantDN = BOCOMSetting.MerchantCertDN;
			nss.NSSetPlainText(message.getBytes("GBK"));
			byte bSignMsg [] = nss.NSDetachedSign(merchantDN);
			if (nss.getLastErrnum() < 0) {
				System.out.print("ERROR:merchant sign fail");
				return "ERROR:merchant sign fail";
			}
			signMsg = new String(bSignMsg, "GBK");
			
		}
		catch(Exception ex)
		{
			System.out.println(ex.getMessage());
		}

		return signMsg;
	}

	private int verify(String src, String signed, String certfile){

		if( src == "" || signed == "" )
		{
			return -10;
		}
		BOCOMB2CClient client = new BOCOMB2CClient();
		int ret = client.initialize(certfile);			
		if (ret != 0) 
		{
			System.out.print("load config file fail: " + client.getLastErr());
			return -11;
		}
		com.bocom.netpay.b2cAPI.NetSignServer nss = new com.bocom.netpay.b2cAPI.NetSignServer();
        nss.NSDetachedVerify(signed.getBytes(), src.getBytes());
        int veriyCode = nss.getLastErrnum();

		return veriyCode;
    
	}
}