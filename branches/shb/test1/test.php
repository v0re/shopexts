
<?php
error_reporting(0);

require("class.HttpDown.php");


define("PAGEURL","http://www.cmbchina.com/cmb2005web/cmbaspxbin/rate/realtimefxrate.aspx");
define("LANG",'cn');
define("COLNUM",7);

//include_once("shopex/pluginswidgets/include/class.HttpDown.php");







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

$aRent = array_chunk($aTemp,COLNUM);
$a= iconv("gb2312","UTF-8",$aRent[1][2]);
echo $a;
//echo $aRent[1][2];
//按COLNUM切分
















?>