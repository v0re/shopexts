<?php
error_reporting(0);

require("class.HttpDown.php");


define("PAGEURL","http://www.cmbchina.com/cmb2005web/cmbaspxbin/rate/realtimefxrate.aspx");
define("LANG",'cn');
define("COLNUM",7);

//include_once("shopex/pluginswidgets/include/class.HttpDown.php");

global $forex_table;



//表头
 $aTblHeader = array(
0=>'交易币',
1=>'交易币单位',
2=>'基本币',
3=>'中间价',
4=>'卖出价',
5=>'现汇买入价',
6=>'现钞买入价'
);


//货币名称
 $aMoneyList = array(
1=>'港币',
2=>'澳大利亚元',
3=>'美元',
4=>'欧元',
5=>'加拿大元',
6=>'英镑',
6=>'日元',
8=>'新加坡元',
9=>'瑞士法郎'
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
$aTemp[$i] = $aTblHeader[$i];
}

for($i=1;$i<10;$i++){
$aTemp[$i*7] = $aMoneyList[$i];
$j=$i*7+2;
$aTemp[$j]="人民币";
}
//按COLNUM切分
$aRent = array_chunk($aTemp,COLNUM);
for($i=0;$i<10;$i++){
for($j=0;$j<7;$j++){
echo $aRent[$i][$j];

}}
 //$forex_table =  make_table($aRent);
//echo $forex_table;
//file_put_contents("default.html",$forex_table); 


//注入模板引擎

//$t->set_var("forex_table",$forex_table);
$aTmp=array();
  foreach($aRent as $key=>$val){
            $aTmp[$key]['A'] = $val[0];
			$aTmp[$key]['B'] = $val[1];

            $aTmp[$key]['C'] = $val[2];

            $aTmp[$key]['D'] = $val[3];

            $aTmp[$key]['E'] = $val[4];

			$aTmp[$key]['F'] = $val[5];

			$aTmp[$key]['G'] = $val[6];


			}
			//for($i=0;$i<10;$i++)
			//{
			 //echo $aTmp[$i]['A'];
			 //}
 //echo $aTmp[]['A'];









?>