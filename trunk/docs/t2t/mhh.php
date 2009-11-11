<?php


define('T2TFILE', 'index.html');
define('PRJNAME', 'ststm');


$hhp = new hhp;
$hhp->setT2tFile(T2TFILE);
$hhp->setProjectName(PRJNAME);
$hhp->setChmFile();
$hhp->setHhcFile();
$hhp->setHhpFile();
$hhp->output();


class hhp{
	var $_t2tfile;
	var $_projectname;
	var $_chmfile;
	var $_hhcfile;
	var $_hhpfile;

	var $_conf;
	

	function setT2tFile($name){
		$this->_t2tfile = $name;
	}

	function setProjectName($name){
		$this->_projectname = $name;
	}

	function setChmFile(){
		$this->_chmfile = $this->_projectname.".chm";
	}

	function setHhcFile(){
		$this->_hhcfile = $this->_projectname.".hhc";
	}

	function setHhpFile(){
		$this->_hhpfile = $this->_projectname.".hhp";
	}

	function _make(){
		$this->_conf = <<<EOF
[OPTIONS]
Compatibility=1.1 or later
Compiled file={$this->_chmfile}
Contents file={$this->_hhcfile}
Default topic=html\index.html
Display compile progress=Yes
Language=0x804 Chinese (PRC)


[FILES]
{$this->_filelist}
EOF;

	}

	function _wfile(){
		if(file_exists($this->_hhpfile)){
			unlink($this->_hhpfile);
		}
		return file_put_contents($this->_hhpfile,$this->_conf);
	}

	function output(){
		#
		$hhc = new hhc;
		$hhc->setT2tFile($this->_t2tfile);
		$hhc->setOutFileName($this->_hhcfile);
		$hhc->output();
		#
		$this->_make();
		$this->_wfile();

		return true;
	}
}

class hhc{

	var $_ct;
	var $_hhcfile;	
	var $_t2tfile;
	var $_html;

	function setT2tFile($name){
		$this->_t2tfile = $name;
	}
	
	function setOutFileName($name){
		$this->_hhcfile = $name;
	}
	
	function _getContentTree(){
		$doc = new DOMDocument("1.0","UTF-8");
		$doc->loadHTMLFile($this->_t2tfile);
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

		$this->_ct = $topics;

		return true;
	}

	function _makeHeader(){
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

	function _makeFooter(){
		$this->_html .= <<<EOF

</BODY></HTML>
EOF;
	}

	function _makeContent(){
		$this->_html .= "<UL>";
		foreach($this->_ct as $topic){
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

	function _wfile(){
		if(file_exists($this->_hhcfile)){
			unlink($this->_hhcfile);
		}
		return file_put_contents($this->_hhcfile,$this->_html);
	}

	function output(){
		$this->_getContentTree();
		$this->_makeHeader();
		$this->_makeContent();
		$this->_makeFooter();
		$this->_wfile();

		return true;
	}

}
	

?>