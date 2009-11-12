<?php


define('T2TFILE', 'index.html');
define('PRJNAME', 'ststm');


#
$hhc = new hhc;
$hhc->setT2tFile(T2TFILE);
$hhc->setProjectName(PRJNAME);
$hhc->setHhcFile();
$hhc->output();
#
$hhp = new hhp;
$hhp->setT2tFile(T2TFILE);
$hhp->setProjectName(PRJNAME);
$hhp->setChmFile();
$hhp->setHhpFile();
$hhp->setHhcFile();
$hhp->output();





class hhp{
	var $_t2tfile;
	var $_projectname;
	var $_chmfile;
	var $_hhcfile;
	var $_hhpfile;

	var $_filelist;

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

	function setHhpFile(){
		$this->_hhpfile = $this->_projectname.".hhp";
	}

		
	function setHhcFile(){
		$this->_hhcfile = $this->_projectname.".hhc";
	}

	function _getFileList(){
		$this->_filelist = '';
		$d = dir("html");
		while (false !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') continue;
			$this->_filelist .= 'html\\'.$entry."\r\n";
		}
		$d->close();
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
		return file_put_contents($this->_hhpfile,$this->_conf);
	}

	function output(){

		$this->_getFileList();
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

	function setProjectName($name){
		$this->_projectname = $name;
	}
	
	function setHhcFile(){
		$this->_hhcfile = $this->_projectname.".hhc";
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
							$toc = $toc = substr($page['attri'],1);
							$this->_html .= "<LI> <OBJECT type=\"text/sitemap\">
									<param name=\"Name\" value=\"".$page['value']."\">
									<param name=\"Local\" value=\"html\\".$toc.".html\">
								</OBJECT>";

								$this->_makePage($toc);
						}
						$this->_html .= "</UL>";
					}
				}
				$this->_html .= "</UL>";
			}
		}
		$this->_html .= "</UL>";
	}



	function _makePage($toc){		
		if(!file_exists('html')){
			mkdir('html');
		}
		$content  = $this->_getT2tHeader();
		$content .= $this->_getPageByToc($toc);
		$content .= $this->_getT2tFooter();
		$filename = "html\\".$toc.".html";
		file_put_contents($filename,$content);

		return true;
	}

	function _getPageByToc($toc){
		$nexttoc = $this->_getNextToc($toc);
		$pattern = '/<A NAME="'.$toc.'"><\/A>([\s\S]+)<A NAME="'.$nexttoc.'">/';
		$content = file_get_contents($this->_t2tfile);
		if(preg_match($pattern,$content,$match)){
			return $match[1];
		}

		return false;
	}

	function _getNextToc($toc){
		if(preg_match('/(.+)(\d)/',$toc,$match)){
			return $match[1].(intval($match[2]) + 1);
		}

		return false;
	}

	function _getT2tHeader(){
		$content = file_get_contents($this->_t2tfile);
		if(preg_match('/[\s\S]+<BODY>/',$content,$match)){
			return $match[0];
		}
	}

	function _getT2tFooter(){
		return "</BODY></HTML>";
	}

	function _wfile(){
		$this->_html = iconv('UTF-8','GBK',$this->_html);
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