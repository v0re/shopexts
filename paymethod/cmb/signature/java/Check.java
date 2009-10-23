class Check
{
	public static void main(String args[])
	{
		try{
			String str = args[0];
			cmb.netpayment.Security p = new cmb.netpayment.Security("./public.key");
		//	System.out.println(str);
			byte[] sig = str.getBytes();
			for(int i = 0 ; i < sig.length ; i++)
			{
//				System.out.println(sig[i]);
			}
			//System.out.println(sig);
			System.out.println(p.checkInfoFromBank(sig));
		}catch(Exception e)
		{
			System.out.println("false");
		}
	}
}
