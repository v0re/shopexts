import java.lang.*;

public class s1
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
        cmb.netpayment.Security pay;
        try
        {
			pay = new cmb.netpayment.Security("public.key");
        }
        catch (Exception e)
        {
            System.out.println("public.key文件找不到？");
            return;
        }
        System.out.println(checkSign(pay,
                "<$Head$><$Version$>1.0.0.0</$Version$></$Head$><$Body$><$ResultFlag$>Y</$ResultFlag$><$MchMchNbr$>888801</$MchMchNbr$><$MchBllNbr$>000000</$MchBllNbr$><$MchBllDat$>20080908</$MchBllDat$><$MchBllTim$>140130</$MchBllTim$><$TrxTrxCcy$>156</$TrxTrxCcy$><$TrxBllAmt$>100.00</$TrxBllAmt$><$TrxPedCnt$>3</$TrxPedCnt$><$MchNtfPam$>中文的参数abc，和更多的英文</$MchNtfPam$><$CrdBllNbr$>0000000000</$CrdBllNbr$><$CrdBllRef$>REFaaaaaaa</$CrdBllRef$><$CrdAutCod$>000000</$CrdAutCod$></$Body$>",
                "5CF26349F50ECFA9EEC46ABC93B0D9F8038B06DDB5C016522187DECDF17B9166A78AA67F685CE4EC4E2D8F05A8C60BE8056181F0AAE3A2845EC1F922B8C474CC"));
	}
}
