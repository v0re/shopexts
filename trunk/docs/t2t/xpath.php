<?php


$hhc = new hhc;
$t = getContentTree();
$hhc->setContentTree($t);
$hhc->setOutFileName('test.hhc');
$hhc->output();


function getContentTree(){
	$doc = new DOMDocument("1.0","UTF-8");
	//$doc->loadHTMLFile('E:\\shopexts\\trunk\\docs\\t2t\\index.html');
	$doc->loadHTMLFile('index.html');
	$xp = new DOMXPath($doc);
	$xpathString = "//html/body/*[@class='toc']/ol/li/a";
	$nodes = $xp->query($xpathString);
	$i = 0;
	#获取第一级
	foreach ($nodes as $node) {
		$attri = $node->attributes->item(0)->nodeValue;
		$value = $node->nodeValue;
		$topics[$i]['attri'] = $attri;
		$topics[$i]['value'] = $value;
		$i++;
	}
	#获取第二级
	foreach($topics as $t_key=>$topic){
		$xpathString = "//html/body/*[@class='toc']/ol/li/a[@href='".$topic['attri']."']/parent::*/ul/li/a";	
		$nodes = $xp->query($xpathString);
		
		$i = 0;
		foreach ($nodes as $node) {
			$attri = $node->attributes->item(0)->nodeValue;
			$value = $node->nodeValue;
			$subjects[$i]['attri'] = $attri;
			$subjects[$i]['value'] = $value;
			$i++;
		}
		$topics[$t_key]['children'] = $subjects;
		unset($subjects);
	}
	#获取第三级
	foreach($topics as $t_key=>$topic){
		$subjects = $topic['children'];
		if(is_array($subjects))
		foreach($subjects as $s_key=>$subject){
			$xpathString = "//html/body/*[@class='toc']/ol/li/a[@href='".$topic['attri']."']/parent::*/ul/li/a[@href='".$subject['attri']."']/parent::*/ul/li/a";	
			$nodes = $xp->query($xpathString);
			$i = 0;
			foreach ($nodes as $node) {
				$attri = $node->attributes->item(0)->nodeValue;
				$value = $node->nodeValue;
				$pages[$i]['attri'] = $attri;
				$pages[$i]['value'] = $value;
				$i++;
			}
			$topics[$t_key]['children'][$s_key]['children'] = $pages;
			unset($pages);
		}
	}

	return $topics;
}

class hhc{

	var $ct;
	var $outfile;		
	var $_html;

	function setContentTree($ct){
		$this->ct = $ct;
	}

	function setOutFileName($name){
		$this->outfile = $name;
	}

	function makeHeader(){
		$this->_html = <<<EOF
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<HTML>
<HEAD>
<meta name="GENERATOR" content="Microsoft&reg; HTML Help Workshop 4.1">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<!-- Sitemap 1.0 -->
</HEAD><BODY>
<OBJECT type="text/site properties">
	<param name="Window Styles" value="0x800025">
</OBJECT>
EOF;
	}

	function makeFooter(){
		$this->_html .= <<<EOF

</BODY></HTML>
EOF;
	}

	function makeContent(){
		$this->_html .= "<UL>";
		foreach($this->ct as $topic){
			$this->_html .= "<LI> <OBJECT type=\"text/sitemap\">
			<param name=\"Name\" value=\"".$topic['value']."\">
			</OBJECT>";
			$subjects = $topic['children'];
			if(is_array($subjects)){
				$this->_html .= "<UL>";		
				foreach($subjects as $subject){
					$this->_html .= "<LI> <OBJECT type=\"text/sitemap\">
					<param name=\"Name\" value=\"".$subject['value']."\">
					</OBJECT>";
					$pages = $subject['children'];
					if(is_array($pages)){
						$this->_html .= "<UL>";		
						foreach($pages as $page){
							$this->_html .= "<LI> <OBJECT type=\"text/sitemap\">
									<param name=\"Name\" value=\"".$page['value']."\">
									<param name=\"Local\" value=\"html\\".$page['attri'].".html\">
								</OBJECT>";
						}
						$this->_html .= "</UL>";
					}
				}
				$this->_html .= "</UL>";
			}
		}
		$this->_html .= "</UL>";
	}

	function wfile(){
		file_put_contents($this->outfile,$this->_html);
	}

	function output(){
		$this->makeHeader();
		$this->makeContent();
		$this->makeFooter();
		$this->wfile();
	}
}
	

?>