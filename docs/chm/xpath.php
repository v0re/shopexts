<?php

$doc = new DOMDocument("1.0","UTF-8");
$doc->loadHTMLFile('E:\\shopexts\\trunk\\docs\\t2t\\index.html');
$xp = new DOMXPath($doc);
$xpathString = "//html/body/*[@class='toc']/ol/li/a";
$nodes = $xp->query($xpathString);
$i = 0;
foreach ($nodes as $node) {
	$attri = $node->attributes->item(0)->nodeValue;
	$value = $node->nodeValue;
	$topics[$i]['attri'] = $attri;
	$topics[$i]['value'] = $value;
	$i++;
}

//print_r($topics);
foreach($topics as $key=>$topic){
	$xpathString = "//html/body/*[@class='toc']/ol/li/a[@href='".$topic['attri']."']/parent::*/ul/li/a";
	
	$nodes = $xp->query($xpathString);
	
	$ii = 0;
	foreach ($nodes as $node) {
		$attri = $node->attributes->item(0)->nodeValue;
		$value = $node->nodeValue;
		$subjects[$ii]['attri'] = $attri;
		$subjects[$ii]['value'] = $value;
		$ii++;
	}
	$topics[$key]['children'] = $subjects;
}

var_export( $topics );

/*
	$xpathString = "//html/body/*[@HREF='{$attri}']/ul/li/a";
	$nodes_ii = $xp->query($xpathString);
	$ii = 0;
	foreach ($nodes_ii as $node) {
		$attri = $node->attributes->item(0)->nodeValue;
		$value = $node->nodeValue;
		$ret[$i]['child'][$ii]['attri'] = $attri;
		$ret[$i]['child'][$ii]['value'] = $value;
	}
//$domNodeList = xpathExtension::getNodes($doc, $xpathString);
//foreach($domNodeList as $domNode){
//   echo $domNode->nodeName;
//}
*/
?>