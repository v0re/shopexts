<?php
function widget_change(&$setting,&$system){
error_reporting(0);
require("class.HttpDown.php");
define("PAGEURL","http://www.cmbchina.com/cmb2005web/cmbaspxbin/rate/realtimefxrate.aspx");
define("LANG",'cn');
define("COLNUM",7);
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

 $aMoneyList = array(
1=>'港币',
2=>'澳大利亚元',
3=>'美元',
4=>'欧元',
5=>'加拿大元',
6=>'英镑',
7=>'日元',
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
for ($i = 0; $i < $aTDNodeList->length; $i++) {
$aTemp[] = $aTDNodeList->item($i)->nodeValue;

}
for($i=0;$i<COLNUM;$i++){
$aTemp[$i] = $aTblHeader[$i];
}

for($i=1;$i<10;$i++){
$aTemp[$i*7] = $aMoneyList[$i];
$j=$i*7+2;
$aTemp[$j]="人民币";
}
$aRent = array_chunk($aTemp,COLNUM); 
 foreach($aRent as $key=>$val){
            $aTmp[$key]['one'] = $val[0];
			$aTmp[$key]['two'] = $val[1];
            $aTmp[$key]['three'] = $val[3];
			}
return $aTmp;
}
 
?>
