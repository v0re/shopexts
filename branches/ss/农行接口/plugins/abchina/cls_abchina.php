<?php
/*
MerchantPayment
*/
function GBK2UTF8($gbkStr)
{
	return iconv("GBK","UTF-8",$gbkStr);
}

class MerchantPayment{
	protected  
		$address,
		$tOrderNo,
		$tOrderDesc,
		$tOrderDate,
		$tOrderTime,
		$tOrderAmountStr,
		$tOrderURL,
		$tProductType,
		$tPaymentType,
		$tNotifyType,
		$tResultNotifyURL,
		$tMerchantRemarks,
		$tPaymentLinkType,
		$tOrderItems;
		
	private $res;
/*	public function __construct($address,$tOrderNo,$tOrderDesc,$tOrderDate,$tOrderTime,$tOrderAmountStr,$tOrderURL,$tProductType,$tPaymentType,$tNotifyType,$tResultNotifyURL,$tMerchantRemarks,$tPaymentLinkType)
	{
		$this->address = $address;
		$this->tOrderNo = $tOrderNo;
		$this->tOrderDesc = $tOrderDesc;
		$this->tOrderDate = $tOrderDate;
		$this->tOrderTime = $tOrderTime;
		$this->tOrderAmountStr = $tOrderAmountStr;
		$this->tOrderURL = $tOrderURL;
		$this->tProductType = $tProductType;
		$this->tPaymentType = $tPaymentType;
		$this->tNotifyType = $tNotifyType;
		$this->tResultNotifyURL = $tResultNotifyURL;
		$this->tMerchantRemarks = $tMerchantRemarks;
		$this->tPaymentLinkType = $tPaymentLinkType;
	}
*/
	public function __construct($address,$merchantPaymentRequest)
	{
		$this->address = $address;
		$this->tOrderNo = $merchantPaymentRequest->tOrderNo;
		$this->tOrderDesc = $merchantPaymentRequest->tOrderDesc;
		$this->tOrderDate = $merchantPaymentRequest->tOrderDate;
		$this->tOrderTime = $merchantPaymentRequest->tOrderTime;
		$this->tOrderAmountStr = $merchantPaymentRequest->tOrderAmountStr;
		$this->tOrderURL = $merchantPaymentRequest->tOrderURL;
		$this->tProductType = $merchantPaymentRequest->tProductType;
		$this->tPaymentType = $merchantPaymentRequest->tPaymentType;
		$this->tNotifyType = $merchantPaymentRequest->tNotifyType;
		$this->tResultNotifyURL = $merchantPaymentRequest->tResultNotifyURL;
		$this->tMerchantRemarks = $merchantPaymentRequest->tMerchantRemarks;
		$this->tPaymentLinkType = $merchantPaymentRequest->tPaymentLinkType;
		$this->tOrderItems=$merchantPaymentRequest->tOrderItems;
	}	
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantPayment_php($this->tOrderNo,$this->tOrderDesc,$this->tOrderDate,$this->tOrderTime,$this->tOrderAmountStr,$this->tOrderURL,$this->tProductType,$this->tPaymentType,$this->tNotifyType,$this->tResultNotifyURL,$this->tMerchantRemarks,$this->tPaymentLinkType,$this->tOrderItems);
		$this->res = (array)$result;
		return $this->generateResponse();
	}

	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$paymentURL = $this->res[0];
		}
		$merchantPaymentResult = new MerchantPaymentResult($paymentURL,$this->res[1],$this->res[2],$this->res[3]);
		return $merchantPaymentResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>PaymentURL:".$this->res[0]."</br>";
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".$this->res[2]."</br>"); 
			print("<br>Error Message:".$this->res[3]."</br>");
		}	
	}
}
class MerchantPaymentRequest
{
	public
		$tOrderNo,
		$tOrderDesc,
		$tOrderDate,
		$tOrderTime,
		$tOrderAmountStr,
		$tOrderURL,
		$tProductType,
		$tPaymentType,
		$tNotifyType,
		$tResultNotifyURL,
		$tMerchantRemarks,
		$tPaymentLinkType,
		$tOrderItems;
	public function __construct($tOrderNo,$tOrderDesc,$tOrderDate,$tOrderTime,$tOrderAmountStr,$tOrderURL,$tProductType,$tPaymentType,$tNotifyType,$tResultNotifyURL,$tMerchantRemarks,$tPaymentLinkType,$tOrderItems)
	{
		$this->tOrderNo = $tOrderNo;
		$this->tOrderDesc = $tOrderDesc;
		$this->tOrderDate = $tOrderDate;
		$this->tOrderTime = $tOrderTime;
		$this->tOrderAmountStr = $tOrderAmountStr;
		$this->tOrderURL = $tOrderURL;
		$this->tProductType = $tProductType;
		$this->tPaymentType = $tPaymentType;
		$this->tNotifyType = $tNotifyType;
		$this->tResultNotifyURL = $tResultNotifyURL;
		$this->tMerchantRemarks = $tMerchantRemarks;
		$this->tPaymentLinkType = $tPaymentLinkType;
		$this->tOrderItems = $tOrderItems;
	}	
}
class MerchantPaymentResult{
	public $paymentURL,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($paymentURL,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->paymentURL=$paymentURL;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}




/*
MerchantQueryOrder
*/
class MerchantQueryOrder{
	protected $address,$tOrderNo,$tQueryType;
	private $res;
/*
	public function __construct($address,$tOrderNo,$tQueryType)
	{
		$this->address = $address;
		$this->tOrderNo = $tOrderNo;
		$this->tQueryType = $tQueryType;
	}
*/
	public function __construct($address,$merchantQueryOrderRequest)
	{
		$this->address = $address;
		$this->tOrderNo = $merchantQueryOrderRequest->tOrderNo;
		$this->tQueryType = $merchantQueryOrderRequest->tQueryType;
	}
	public function outputString()
	{
		$strlen = 1024;
        $str = '';   
        for($i = 0; $i<$strlen; $i++){   
            $str .= 'd';   
        } 
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));	   
		$result = $client->outputStrings($str);
		$res = (array)$result;
		print("result:".$result."~~~~~~~~~~~~\n");
		print("res:".$res[100]."~~~~~~~~~~~~\n");
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantQueryOrder_php($this->tOrderNo,$this->tQueryType);
		$this->res = (array)$result;

		//print "<br>Request : \n".htmlspecialchars($client->__getLastRequest())."</br>";
		//print "<br>Response : \n".htmlspecialchars($client->__getLastResponse())."</br>";
		
		return $this->generateResponse();
	}
	
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$orderItems = array();
			$count = count($this->res[0][9]);
			for ($i = 0; $i < $count; $i++) 
			{	
				$orderItems[$i] = new OrderItem($this->res[0][9][$i][0],iconv("UTF-8", "GBK",$this->res[0][9][$i][1]),iconv("UTF-8", "GBK",$this->res[0][9][$i][2]),iconv("UTF-8", "GBK",$this->res[0][9][$i][3]));
			}
			$order = new Order(iconv("UTF-8", "GBK",$this->res[0][0]),iconv("UTF-8", "GBK",$this->res[0][1]),iconv("UTF-8", "GBK",$this->res[0][2]),iconv("UTF-8", "GBK",$this->res[0][3]),iconv("UTF-8", "GBK",$this->res[0][4]),iconv("UTF-8", "GBK",$this->res[0][5]),iconv("UTF-8", "GBK",$this->res[0][6]),iconv("UTF-8", "GBK",$this->res[0][7]),iconv("UTF-8", "GBK",$this->res[0][8]),$orderItems);
		}
		$merchantQueryOrderResult = new MerchantQueryOrderResult($order,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantQueryOrderResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>OrderAmount:".iconv("UTF-8", "GBK",$this->res[0][0])."</br>";
			print "<br>OrderDate:".iconv("UTF-8", "GBK",$this->res[0][1])."</br>";
			print "<br>OrderDesc:".iconv("UTF-8", "GBK",$this->res[0][2])."</br>";
			print "<br>OrderNo:".iconv("UTF-8", "GBK",$this->res[0][3])."</br>";
			print "<br>OrderStatus:".iconv("UTF-8", "GBK",$this->res[0][4])."</br>";
			print "<br>OrderTime:".iconv("UTF-8", "GBK",$this->res[0][5])."</br>";
			print "<br>OrderURL:".iconv("UTF-8", "GBK",$this->res[0][6])."</br>";		
			print "<br>PayAmount:".iconv("UTF-8", "GBK",$this->res[0][7])."</br>";		
			print "<br>RefundAmount:".iconv("UTF-8", "GBK",$this->res[0][8])."</br>";		
			$count = count($this->res[0][9]);
			for ($i = 0; $i < $count; $i++) 
			{			
				print "<br>ProductID:".$this->res[0][9][$i][0]."</br>";
				print "<br>ProductName:".iconv("UTF-8", "GBK",$this->res[0][9][$i][1])."</br>";
				print "<br>Qty:".iconv("UTF-8", "GBK",$this->res[0][9][$i][2])."</br>";
				print "<br>UnitPriceλ۸:".iconv("UTF-8", "GBK",$this->res[0][9][$i][3])."</br>";
			}
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}

class MerchantQueryOrderRequest
{
	public $tOrderNo,$tQueryType;
	public function __construct($tOrderNo,$tQueryType)
	{
		$this->tOrderNo = $tOrderNo;
		$this->tQueryType = $tQueryType;
	}
	
}

class MerchantQueryOrderResult{
	public $order,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($order,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->order=$order;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}
class Order
{
	public $OrderAmount,$OrderDate,$OrderDesc,$OrderNo,$OrderStatus,$OrderTime,$OrderURL,$PayAmount,$RefundAmount,$OrderItems;
	public function __construct($OrderAmount,$OrderDate,$OrderDesc,$OrderNo,$OrderStatus,$OrderTime,$OrderURL,$PayAmount,$RefundAmount,$OrderItems)
	{
		$this->OrderAmount=$OrderAmount;
		$this->OrderDate=$OrderDate;
		$this->OrderDesc=$OrderDesc;
		$this->OrderNo=$OrderNo;
		$this->OrderStatus=$OrderStatus;
		$this->OrderTime=$OrderTime;
		$this->OrderURL=$OrderURL;
		$this->PayAmount=$PayAmount;
		$this->RefundAmount=$RefundAmount;
		$this->OrderItems=$OrderItems;
	}	
}
class OrderItem
{
	public $ProductID,$ProductName,$Qty,$UnitPrice;
	public function __construct($ProductID,$ProductName,$Qty,$UnitPrice)
	{
		$this->ProductID=$ProductID;
		$this->ProductName=$ProductName;
		$this->Qty=$Qty;
		$this->UnitPrice=$UnitPrice;
	}
}
/*
MerchantVoidPayment
*/
class MerchantVoidPayment
{
	protected  
		$address,$tOrderNo;		
	private $res;	
/*
	public function __construct($address,$tOrderNo)
	{
		$this->address = $address;
		$this->tOrderNo = $tOrderNo;
	}
*/
	public function __construct($address,$merchantVoidPaymentRequest)
	{
		$this->address = $address;
		$this->tOrderNo = $merchantVoidPaymentRequest->tOrderNo;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantVoidPayment_php($this->tOrderNo);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$voidPaymentResponseDetail = new voidPaymentResponseDetail($this->res[0][0],$this->res[0][1],$this->res[0][2],$this->res[0][3],$this->res[0][4],$this->res[0][5],$this->res[0][6]);
		}
		$merchantVoidPaymentResult = new MerchantVoidPaymentResult($voidPaymentResponseDetail,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantVoidPaymentResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>BatchNo:".$this->res[0][0]."</br>";
			print "<br>HostDate:".$this->res[0][1]."</br>";
			print "<br>HostTime:".$this->res[0][2]."</br>";
			print "<br>OrderNo:".$this->res[0][3]."</br>";
			print "<br>PayAmount:".$this->res[0][4]."</br>";
			print "<br>TrxType:".$this->res[0][5]."</br>";
			print "<br>VoucherNo:".$this->res[0][6]."</br>";		
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}
class MerchantVoidPaymentRequest
{
	public $tOrderNo;
	public function __construct($tOrderNo)
	{
		$this->tOrderNo = $tOrderNo;
	}

}
class MerchantVoidPaymentResult
{	
	public $voidPaymentResponseDetail,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($voidPaymentResponseDetail,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->voidPaymentResponseDetail=$voidPaymentResponseDetail;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}
class VoidPaymentResponseDetail
{
	public $BatchNo,$HostDate,$HostTime,$OrderNo,$PayAmount,$TrxType,$VoucherNo;
	public function __construct($BatchNo,$HostDate,$HostTime,$OrderNo,$PayAmount,$TrxType,$VoucherNo)
	{
		$this->BatchNo = $BatchNo;
		$this->HostDate = $HostDate;
		$this->HostTime = $HostTime;
		$this->OrderNo = $OrderNo;
		$this->PayAmount = $PayAmount;
		$this->TrxType = $TrxType;
		$this->VoucherNo = $VoucherNo;
	}
}

/*
MerchantRefund
*/
class MerchantRefund
{
	protected  
		$address,$tOrderNo,$tTrxAmount;		
	private $res;
/*	
	public function __construct($address,$tOrderNo,$tTrxAmount)
	{
		$this->address = $address;
		$this->tOrderNo = $tOrderNo;
		$this->tTrxAmount = $tTrxAmount;
	}
*/
	
	public function __construct($address,$merchantRefundRequest)
	{
		$this->address = $address;
		$this->tOrderNo = $merchantRefundRequest->tOrderNo;
		$this->tTrxAmount = $merchantRefundRequest->tTrxAmount;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantRefund_php($this->tOrderNo,$this->tTrxAmount);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$merchantRefundResponseDetail = new MerchantRefundResponseDetail($this->res[0][0],$this->res[0][1],$this->res[0][2],$this->res[0][3],$this->res[0][4],$this->res[0][5],$this->res[0][6]);
		}
		$merchantRefundResult = new MerchantRefundResult($merchantRefundResponseDetail,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantRefundResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>BatchNo:".$this->res[0][0]."</br>";
			print "<br>HostDate:".$this->res[0][1]."</br>";
			print "<br>HostTime:".$this->res[0][2]."</br>";
			print "<br>OrderNo:".$this->res[0][3]."</br>";
			print "<br>TrxAmount:".$this->res[0][4]."</br>";
			print "<br>TrxType:".$this->res[0][5]."</br>";
			print "<br>VoucherNo:".$this->res[0][6]."</br>";		
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}
class MerchantRefundRequest
{
	public $tOrderNo,$tTrxAmount;
	public function __construct($tOrderNo,$tTrxAmount)
	{
		$this->tOrderNo = $tOrderNo;
		$this->tTrxAmount = $tTrxAmount;
	}
}
class MerchantRefundResult
{
	public $merchantRefundResponseDetail,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($merchantRefundResponseDetail,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->merchantRefundResponseDetail=$merchantRefundResponseDetail;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}
class MerchantRefundResponseDetail
{
	public $BatchNo,$HostDate,$HostTime,$OrderNo,$TrxAmount,$TrxType,$VoucherNo;
	public function __construct($BatchNo,$HostDate,$HostTime,$OrderNo,$TrxAmount,$TrxType,$VoucherNo)
	{
		$this->BatchNo = $BatchNo;
		$this->HostDate = $HostDate;
		$this->HostTime = $HostTime;
		$this->OrderNo = $OrderNo;
		$this->TrxAmount = $TrxAmount;
		$this->TrxType = $TrxType;
		$this->VoucherNo = $VoucherNo;
	}
}

/*
MerchantTrxSettle
*/
class MerchantTrxSettle
{
	protected  
		$address,$tSettleDate;		
	private $res;	
/*	
	public function __construct($address,$tSettleDate)
	{
		$this->address = $address;
		$this->tSettleDate = $tSettleDate;
	}
*/
	public function __construct($address,$merchantTrxSettleRequest)
	{
		$this->address = $address;
		$this->tSettleDate = $merchantTrxSettleRequest->tSettleDate;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantTrxSettle_php($this->tSettleDate);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$settleItems = array();
			$count = count($this->res[0][6]);
			for ($i = 0; $i < $count; $i++) 
			{	
				$settleItems[$i] = "Record-".$i."=".$this->res[0][6][$i];
			}
			$merchantTrxSettleDetail = new MerchantTrxSettleDetail($this->res[0][0],$this->res[0][1],$this->res[0][2],$this->res[0][3],$this->res[0][4],$this->res[0][5],$settleItems);
		}
		$merchantTrxSettleResult = new MerchantTrxSettleResult($merchantTrxSettleDetail,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantTrxSettleResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>SettleDate:".$this->res[0][0]."</br>";
			print "<br>SettleType:".$this->res[0][1]."</br>";
			print "<br>NumOfPayments:".$this->res[0][2]."</br>";
			print "<br>SumOfPayAmount:".$this->res[0][3]."</br>";
			print "<br>NumOfRefunds:".$this->res[0][4]."</br>";
			print "<br>SumOfRefundAmount:".$this->res[0][5]."</br>";
			$count = count($this->res[0][6]);
			for ($i = 0; $i < $count; $i++) 
			{		
				print("<br>Record-".$i."=".$this->res[0][6][$i]."</br>");
			}			
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}
class MerchantTrxSettleRequest
{
	public $tSettleDate;
	public function __construct($tSettleDate)
	{
		$this->tSettleDate = $tSettleDate;
	}
}

class MerchantTrxSettleResult
{
	public $merchantTrxSettleDetail,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($merchantTrxSettleDetail,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->merchantTrxSettleDetail=$merchantTrxSettleDetail;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}

class MerchantTrxSettleDetail
{
	public $SettleDate,$SettleType,$NumOfPayments,$SumOfPayAmount,$NumOfRefunds,$SumOfRefundAmount,$settleItems;
	public function __construct($SettleDate,$SettleType,$NumOfPayments,$SumOfPayAmount,$NumOfRefunds,$SumOfRefundAmount,$settleItems)
	{
		$this->SettleDate = $SettleDate;
		$this->SettleType = $SettleType;
		$this->NumOfPayments = $NumOfPayments;
		$this->SumOfPayAmount = $SumOfPayAmount;
		$this->NumOfRefunds = $NumOfRefunds;
		$this->SumOfRefundAmount = $SumOfRefundAmount;
		$this->settleItems = $settleItems;
	}
}
/*
MerchantTrxSettleByHour
*/
class MerchantTrxSettleByHour
{
	protected  
		$address,$tSettleDate,$tSettleStartHour,$tSettleEndHour;		
	private $res;	
/*	
	public function __construct($address,$tSettleDate,$tSettleStartHour,$tSettleEndHour)
	{
		$this->address = $address;
		$this->tSettleDate = $tSettleDate;
		$this->tSettleStartHour = $tSettleStartHour;
		$this->tSettleEndHour = $tSettleEndHour;
	}
*/	
	public function __construct($address,$merchantTrxSettleByHourRequest)
	{
		$this->address = $address;
		$this->tSettleDate = $merchantTrxSettleByHourRequest->tSettleDate;
		$this->tSettleStartHour = $merchantTrxSettleByHourRequest->tSettleStartHour;
		$this->tSettleEndHour = $merchantTrxSettleByHourRequest->tSettleEndHour;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantTrxSettleByHour_php($this->tSettleDate,$this->tSettleStartHour,$this->tSettleEndHour);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$settleItems = array();
			$count = count($this->res[0][6]);
			for ($i = 0; $i < $count; $i++) 
			{	
				$settleItems[$i] = "Record-".$i."=".$this->res[0][6][$i];
			}
			$merchantTrxSettleByHourDetail = new MerchantTrxSettleByHourDetail($this->res[0][0],$this->res[0][1],$this->res[0][2],$this->res[0][3],$this->res[0][4],$this->res[0][5],$settleItems);
		}
		$merchantTrxSettleByHourResult = new MerchantTrxSettleByHourResult($merchantTrxSettleDetail,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantTrxSettleByHourResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>SettleDate:".$this->res[0][0]."</br>";
			print "<br>SettleType:".$this->res[0][1]."</br>";
			print "<br>NumOfPayments:".$this->res[0][2]."</br>";
			print "<br>SumOfPayAmount:".$this->res[0][3]."</br>";
			print "<br>NumOfRefunds:".$this->res[0][4]."</br>";
			print "<br>SumOfRefundAmount:".$this->res[0][5]."</br>";
			$count = count($this->res[0][6]);
			for ($i = 0; $i < $count; $i++) 
			{		
				print("<br>Record-".$i."=".$this->res[0][6][$i]."</br>");
			}			
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}
class MerchantTrxSettleByHourRequest
{
	public $tSettleDate,$tSettleStartHour,$tSettleEndHour;
	public function __construct($tSettleDate,$tSettleStartHour,$tSettleEndHour)
	{
		$this->tSettleDate = $tSettleDate;
		$this->tSettleStartHour = $tSettleStartHour;
		$this->tSettleEndHour = $tSettleEndHour;
	}
}

class MerchantTrxSettleByHourResult
{
	public $merchantTrxSettleByHourDetail,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($merchantTrxSettleByHourDetail,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->merchantTrxSettleByHourDetail=$merchantTrxSettleByHourDetail;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}

class MerchantTrxSettleByHourDetail
{
	public $SettleDate,$SettleType,$NumOfPayments,$SumOfPayAmount,$NumOfRefunds,$SumOfRefundAmount,$settleItems;
	public function __construct($SettleDate,$SettleType,$NumOfPayments,$SumOfPayAmount,$NumOfRefunds,$SumOfRefundAmount,$settleItems)
	{
		$this->SettleDate = $SettleDate;
		$this->SettleType = $SettleType;
		$this->NumOfPayments = $NumOfPayments;
		$this->SumOfPayAmount = $SumOfPayAmount;
		$this->NumOfRefunds = $NumOfRefunds;
		$this->SumOfRefundAmount = $SumOfRefundAmount;
		$this->settleItems = $settleItems;
	}
}


/*
IdentityVerify
*/
class IdentityVerify
{
	protected  
		$address,$tResultNotifyURL,$tBankCardNo,$tCertificateType,$tCertificateNo,$tRequestDate,$tRequestTime,$request;		
	private $res;
/*	
	public function __construct($address,$tResultNotifyURL,$tBankCardNo,$tCertificateType,$tCertificateNo,$tRequestDate,$tRequestTime)
	{
		$this->address = $address;
		$this->tResultNotifyURL = $tResultNotifyURL;
		$this->tBankCardNo = $tBankCardNo;
		$this->tCertificateType = $tCertificateType;
		$this->tCertificateNo = $tCertificateNo;
		$this->tRequestDate = $tRequestDate;
		$this->tRequestTime = $tRequestTime;
	}
*/
	public function __construct($address,$request)
	{
		$this->address = $address;
		$this->tResultNotifyURL = $request->tResultNotifyURL;
		$this->tBankCardNo = $request->tBankCardNo;
		$this->tCertificateType = $request->tCertificateType;
		$this->tCertificateNo = $request->tCertificateNo;
		$this->tRequestDate = $request->tRequestDate;
		$this->tRequestTime = $request->tRequestTime;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->identityVerify_php($this->tResultNotifyURL,$this->tBankCardNo,$this->tCertificateType,$this->tCertificateNo,$this->tRequestDate,$this->tRequestTime);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$identityVerifyURL = $this->res[0];
		}
		$identityVerifyResult = new IdentityVerifyResult($identityVerifyURL,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $identityVerifyResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>VerifyURL:".$this->res[0]."</br>";
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}
class IdentityVerifyRequest
{
	public  
		$tResultNotifyURL,$tBankCardNo,$tCertificateType,$tCertificateNo,$tRequestDate,$tRequestTime,$request;	
	public function __construct($tResultNotifyURL,$tBankCardNo,$tCertificateType,$tCertificateNo,$tRequestDate,$tRequestTime)
	{
		$this->tResultNotifyURL = $tResultNotifyURL;
		$this->tBankCardNo = $tBankCardNo;
		$this->tCertificateType = $tCertificateType;
		$this->tCertificateNo = $tCertificateNo;
		$this->tRequestDate = $tRequestDate;
		$this->tRequestTime = $tRequestTime;
	}
}
class IdentityVerifyResult{
	public $identityVerifyURL,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($identityVerifyURL,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->identityVerifyURL=$identityVerifyURL;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}

/*
MerchantBatchSend
*/
class MerchantBatchSend
{
	protected  
		$address,$SerialNumber;		
	private $res;
/*	
	public function __construct($address,$SerialNumber)
	{
		$this->address = $address;
		$this->SerialNumber = $SerialNumber;
	}
*/	
	public function __construct($address,$MerchantBatchSendRequest)
	{
		$this->address = $address;
		$this->SerialNumber = $MerchantBatchSendRequest->SerialNumber;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantBatchSend_php($this->SerialNumber);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$merchantBatchSendResDetail =  new MerchantBatchSendResDetail($this->res[0][0],$this->res[0][1],$this->res[0][2]);
		}
		$merchantBatchSendResult = new MerchantBatchSendResult($merchantBatchSendResDetail,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantBatchSendResult;
	}
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>SendTime:".$this->res[0][0]."</br>";
			print("<br>SerialNumber:".$this->res[0][1]."</br>");
			print("<br>TrxType:".$this->res[0][2]."</br>");
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$this->res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$this->res[3])."</br>");
		}	
	}
}
class MerchantBatchSendRequest
{
	public $SerialNumber;
	public function __construct($SerialNumber)
	{
		$this->SerialNumber = $SerialNumber;
	}
}
class MerchantBatchSendResult
{
	public $merchantBatchSendResDetail,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($merchantBatchSendResDetail,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->merchantBatchSendResDetail=$merchantBatchSendResDetail;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}
class MerchantBatchSendResDetail
{
	public $SendTime,$SerialNumber,$TrxType;
	public function __construct($SendTime,$SerialNumber,$TrxType)
	{
		$this->SendTime = $SendTime;
		$this->SerialNumber = $SerialNumber;
		$this->TrxType = $TrxType;
	}
}
/*
MerchantQueryBatch
*/
class MerchantQueryBatch
{
	protected  
		$address,$SerialNumber;
	private $res;
/*		
	public function __construct($address,$SerialNumber)
	{
		$this->address = $address;
		$this->SerialNumber = $SerialNumber;
	}
*/	
	public function __construct($address,$merchantQueryBatchRequest)
	{
		$this->address = $address;
		$this->SerialNumber = $merchantQueryBatchRequest->SerialNumber;
	}
	public function invoke()
	{
		$client=new SoapClient($this->address, array("style"=> SOAP_RPC,"use"=> SOAP_ENCODED, "trace"=>0,"exceptions" =>0));
		$client->soap_defencoding='utf-8';
		$client->decode_utf8=false;
		$result = $client->merchantQueryBatch_php($this->SerialNumber);
		$this->res = (array)$result;
		return $this->generateResponse();
	}
	private function generateResponse()
	{
		if($this->res[1]==TRUE)
		{
			$batchItems = array();
			$count = count($this->res[0][4]);
			for ($i = 0; $i < $count; $i++) 
			{	
				$batchItems[$i] = new BatchItem($this->res[0][4][$i][0],$this->res[0][4][$i][1],$this->res[0][4][$i][2]);
			}
			$merchantQueryBatchResDetail = new MerchantQueryBatchResDetail($batchItems,$this->res[0][0],$this->res[0][1],$this->res[0][2],$this->res[0][3]);
		}
		$merchantQueryBatchResult = new MerchantQueryBatchResult($merchantQueryBatchResDetail,$this->res[1],iconv("UTF-8", "GBK",$this->res[2]),iconv("UTF-8", "GBK",$this->res[3]));
		return $merchantQueryBatchResult;
	}
	
	public function showResult()
	{
		if($this->res[1]==TRUE)
		{
			print("<br>Sucess!!!"."</br>");
			print "<br>SerialNumber:".$this->res[0][0]."</br>";
			print("<br>RefundAmount:".$this->res[0][1]."</br>");
			print("<br>RefundCount:".$this->res[0][2]."</br>");
			print("<br>BatchStatus:".$this->res[0][3]."</br>");
			$count = count($this->res[0][4]);
			for ($i = 0; $i < $count; $i++) 
			{		
				print("<br>OrderItem".$i.":"."</br>");
				print("<br>OrderNo:".$this->res[0][4][$i][0]."</br>");
				print("<br>RefundAmount:".$this->res[0][4][$i][1]."</br>");
				print("<br>OrderStatus:".$this->res[0][4][$i][2]."</br>");
			}
		}
		else
		{
			print("<br>Failed!!!"."</br>");
			print("<br>return code:".iconv("UTF-8", "GBK",$res[2])."</br>"); 
			print("<br>Error Message:".iconv("UTF-8", "GBK",$res[3])."</br>");
		}
		
	}
}
class MerchantQueryBatchRequest
{
	public $SerialNumber;
	public function __construct($SerialNumber)
	{
		$this->SerialNumber = $SerialNumber;
	}
}
class MerchantQueryBatchResult
{
	public $merchantQueryBatchResDetail,$isSucess,$returnCode,$ErrorMessage;
	public function __construct($merchantQueryBatchResDetail,$isSucess,$returnCode,$ErrorMessage)
	{
		$this->merchantQueryBatchResDetail=$merchantQueryBatchResDetail;
		$this->isSucess=$isSucess;
		$this->returnCode=$returnCode;
		$this->ErrorMessage=$ErrorMessage;
	}
}
class MerchantQueryBatchResDetail
{
	public $batchItems,$SerialNumber,$RefundAmount,$RefundCount,$BatchStatus;
	public function __construct($batchItems,$SerialNumber,$RefundAmount,$RefundCount,$BatchStatus)
	{
		$this->batchItems = $batchItems;
		$this->SerialNumber = $SerialNumber;
		$this->RefundAmount = $RefundAmount;
		$this->RefundCount = $RefundCount;
		$this->BatchStatus = $BatchStatus;
	}
}
class BatchItem
{
	public $OrderNo,$RefundAmount,$OrderStatus;
	public function __construct($OrderNo,$RefundAmount,$OrderStatus)
	{
		$this->OrderNo = $OrderNo;
		$this->RefundAmount = $RefundAmount;
		$this->OrderStatus = $OrderStatus;
	}
}

?>