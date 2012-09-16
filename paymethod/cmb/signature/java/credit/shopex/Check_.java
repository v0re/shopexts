class Check
{
    //把数字签名的“十六进制的字符串”，还原为byte数组
    public static byte[] getSignBytes(String strSig)
    {
        int iLength=strSig.length();
        if (iLength % 2 == 1) return null;
        byte[] ba = new byte[iLength / 2];
        for (int i=0; i<iLength; i+=2)
        {
            ba[i/2] = (byte)Integer.parseInt(strSig.substring(i,i+2),16);
        }
        return ba;
    }
    
    public static boolean checkSign(cmb.netpayment.Security pay,String strText, String strSig)
    {
        byte baText[];
        try
        {
            baText = strText.getBytes("GB2312");
        }
        catch(Exception e)
        {
            return false;
        }

        return pay.CheckSign(baText,getSignBytes(strSig));
    }

	public static void main(java.lang.String[] args)
	{
        String str = args[0];
		String certfile = args[1];
		
		cmb.netpayment.Security pay;
        try
        {
			pay = new cmb.netpayment.Security(certfile);
        }
        catch (Exception e)
        {
            System.out.println("public.key not found");
            return;
        }
        System.out.println(checkSign(pay,str));
	}
}
