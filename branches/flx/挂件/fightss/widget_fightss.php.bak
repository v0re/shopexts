<?php
 function widget_fightss(&$setting,&$system){
  error_reporting(0);
  require("class.HttpDown.php");
  define("PAGEURL","http://www.cmbchina.com/cmb2005web/cmbaspxbin/rate/realtimefxrate.aspx");
  define("LANG",'cn');
  define("COLNUM",7);


//表头
$aTblHeader = array(
array('en'=>'Currency Name','cn'=>'交易币'),
array('en'=>'Trading Unit','cn'=>'交易币单位'),
array('en'=>'Base Currency','cn'=>'基本币'),
array('en'=>'Middle Rate','cn'=>'中间价'),
array('en'=>'Selling Rate','cn'=>'卖出价'),
array('en'=>'Buying Rate','cn'=>'现汇买入价'),
array('en'=>'Cash Buying Rate','cn'=>'现钞买入价')
);


//货币名称
 $aMoneyList = array(
array('en'=>'HKD','cn'=>'港币'),
array('en'=>'AUD','cn'=>'澳大利亚元'),
array('en'=>'USD','cn'=>'美元'),
array('en'=>'EUR','cn'=>'欧元'),
array('en'=>'CAD','cn'=>'加拿大元'),
array('en'=>'GBP','cn'=>'英镑'),
array('en'=>'JPY','cn'=>'日元'),
array('en'=>'SGD','cn'=>'新加坡元'),
array('en'=>'CHF','cn'=>'瑞士法郎')
);

$wget = new HttpDown;
//打开页面
$wget->OpenUrl(PAGEURL);
$html = $wget->GetHtml();


$doc = new DOMDocument("1.0","UTF-8");
$doc->loadHTML($html);
$node = $doc->getElementById('ExrateDataGrid');


$aTDNodeList = $node->getElementsByTagName("td");


$aTemp = array();
for ($i = 0; $i <= $aTDNodeList->length; $i++) {
$aTemp[] = $aTDNodeList->item($i)->nodeValue;
}
//替换表头，前7个元素
for($i=0;$i<COLNUM;$i++){
$aTemp[$i] = $aTblHeader[$i][LANG];
}
$aRent = array_chunk($aTemp,COLNUM);
for($i=0;$i<count($aMoneyList);$i++){
   $arr[0][$i]=$aMoneyList[$i]['cn'];
}

$arr=array("123"=>"交易币","456"=>"中间价");
$arr1[]=$arr;
for($i=0;$i<10;$i++){
$arr=array("123"=>$aMoneyList[$i]['cn'],"456"=>$aRent[$i+1][3]);
$arr1[]=$arr;
}

//for($j=0;$j<count($arr1);$j++){
    //echo $arr1[$j]['123']."<br />";
	//echo $arr1[$j]['456']."<br />";
    //}

 foreach($arr1 as $key=>$val){
	      $aTmp[$key]['a'] = $val['123'];
          $aTmp[$key]['b'] = $val['456'];
}

//$t->set_var("arr",$arr);
  return $aTmp;
}
           
  
?>
