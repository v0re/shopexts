class Check
{
	public static void main(String args[])
	{
		try{
			String str = args[0];
			String certfile = args[1];
			cmb.netpayment.Security p = new cmb.netpayment.Security(certfile);
			byte[] sig = str.getBytes();
			System.out.println(p.checkInfoFromBank(sig));
		}catch(Exception e)
		{
			System.out.println("false");
		}
	}
}
